<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class ConvertMediaToWebp
{
    /**
     * Handle the event.
     */
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        $media = $event->media;

        try {
            $originalPath = $media->getPath();

            if (! file_exists($originalPath)) {
                Log::warning("ConvertMediaToWebp: original file does not exist: {$originalPath}");

                return;
            }

            // Only process images
            if (! str_starts_with($media->mime_type, 'image/')) {
                return;
            }

            // Read params from media custom properties if provided
            $properties = is_array($media->custom_properties) ? $media->custom_properties : [];
            $format = strtolower($properties['format'] ?? ($properties['conversion'] ?? 'webp'));
            $targetWidth = (int) ($properties['width'] ?? ($properties['max_width'] ?? 1600));
            $quality = (int) ($properties['quality'] ?? 82);

            // Temp output path
            $tempPath = $originalPath.'.tmp.'.$format;

            $conversionSucceeded = false;

            // Try Imagick first
            if (class_exists('Imagick')) {
                try {
                    $img = new \Imagick($originalPath);
                    $width = $img->getImageWidth();
                    if ($width > $targetWidth && $targetWidth > 0) {
                        $height = $img->getImageHeight();
                        $newHeight = (int) floor($height * ($targetWidth / $width));
                        $img->resizeImage($targetWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
                    }
                    $img->setImageFormat($format);
                    if (method_exists($img, 'setImageCompressionQuality')) {
                        $img->setImageCompressionQuality($quality);
                    }
                    $img->writeImage($tempPath);
                    $img->clear();
                    $img->destroy();
                    $conversionSucceeded = file_exists($tempPath);
                } catch (\Throwable $e) {
                    Log::warning('Imagick conversion failed: '.$e->getMessage());
                    $conversionSucceeded = false;
                }
            }

            // Fallback to GD
            if (! $conversionSucceeded) {
                $contents = @file_get_contents($originalPath);
                if ($contents === false) {
                    Log::warning("Failed to read original image: {$originalPath}");

                    return;
                }

                $src = @imagecreatefromstring($contents);
                if ($src === false) {
                    Log::warning("GD failed to create image resource from: {$originalPath}");

                    return;
                }

                $width = imagesx($src);
                $height = imagesy($src);
                if ($width > $targetWidth && $targetWidth > 0) {
                    $newWidth = $targetWidth;
                    $newHeight = (int) floor($height * ($newWidth / $width));
                    $dst = imagescale($src, $newWidth, $newHeight, IMG_BILINEAR_FIXED);
                } else {
                    $dst = $src;
                }

                // Ensure true color for webp
                if (! imageistruecolor($dst)) {
                    imagepalettetotruecolor($dst);
                }

                $saveResult = false;
                switch ($format) {
                    case 'webp':
                        $saveResult = imagewebp($dst, $tempPath, $quality);
                        break;
                    case 'jpeg':
                    case 'jpg':
                        $saveResult = imagejpeg($dst, $tempPath, $quality);
                        break;
                    case 'png':
                        // quality in imagepng is compression level: 0 (no compression) to 9
                        $pngLevel = (int) round((100 - $quality) / 11.1111);
                        $pngLevel = max(0, min(9, $pngLevel));
                        $saveResult = imagepng($dst, $tempPath, $pngLevel);
                        break;
                    default:
                        // try webp as default
                        $saveResult = imagewebp($dst, $tempPath, $quality);
                }

                imagedestroy($dst);
                if ($dst !== $src) {
                    imagedestroy($src);
                }

                if (! $saveResult || ! file_exists($tempPath)) {
                    Log::warning('GD conversion failed or temp file missing for: '.$originalPath);

                    return;
                }
            }

            // Optimize with Spatie Image Optimizer if available
            if (class_exists('Spatie\\ImageOptimizer\\OptimizerChainFactory')) {
                try {
                    $optimizer = \Spatie\ImageOptimizer\OptimizerChainFactory::create();
                    $optimizer->optimize($tempPath);
                } catch (\Throwable $e) {
                    Log::warning('Image optimizer failed: '.$e->getMessage());
                }
            }

            // Verify temp file exists and has size
            if (! file_exists($tempPath) || filesize($tempPath) === 0) {
                Log::warning('Conversion produced empty or missing temp file: '.$tempPath);
                // Do not delete original
                if (file_exists($tempPath)) {
                    @unlink($tempPath);
                }

                return;
            }

            $newSize = filesize($tempPath);

            // Prepare new filename and path
            $oldFileName = $media->file_name;
            $newExtension = $format;
            $newFileName = pathinfo($oldFileName, PATHINFO_FILENAME).'.'.$newExtension;
            $newPath = dirname($originalPath).DIRECTORY_SEPARATOR.$newFileName;

            // Replace original atomically: move temp to new path, then delete original if different
            if (! @rename($tempPath, $newPath)) {
                Log::error("Failed to move converted file to destination: {$newPath}");
                // Clean up temp
                if (file_exists($tempPath)) {
                    @unlink($tempPath);
                }

                return;
            }

            // Only delete original if it's a different path/filename and exists
            if (file_exists($originalPath) && realpath($originalPath) !== realpath($newPath)) {
                @unlink($originalPath);
            }

            // Update media record
            $media->file_name = $newFileName;
            $media->mime_type = 'image/'.($newExtension === 'jpg' ? 'jpeg' : $newExtension);
            $media->size = $newSize;
            $media->save();

        } catch (\Throwable $e) {
            Log::error('ConvertMediaToWebp error: '.$e->getMessage());
        }
    }
}
