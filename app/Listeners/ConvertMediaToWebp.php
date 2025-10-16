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

            // Prepare processing path (handle HEIC/HEIF -> create intermediate JPEG if needed)
            $processingPath = $originalPath;
            $intermediatePath = null;
            $cleanupIntermediate = false;

            $isHeic = preg_match('/heif|heic/i', $media->mime_type) || preg_match('/\.heic$|\.heif$/i', $originalPath);

            if ($isHeic) {
                // create an intermediate JPEG for downstream processing
                $intermediatePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('heic_').'.jpg';

                // Prefer Imagick if it can read HEIC
                if (class_exists('Imagick')) {
                    try {
                        $he = new \Imagick($originalPath);
                        // Some Imagick builds require readImage; ensure it loads
                        // write intermediate as jpeg
                        $he->setImageFormat('jpeg');
                        $he->setImageCompressionQuality($quality);
                        $he->writeImage($intermediatePath);
                        $he->clear();
                        $he->destroy();
                        if (file_exists($intermediatePath)) {
                            $processingPath = $intermediatePath;
                            $cleanupIntermediate = true;
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Imagick HEIC conversion failed: '.$e->getMessage());
                    }
                }

                // If no intermediate yet, try CLI tools: heif-convert, magick, convert
                if (! $cleanupIntermediate) {
                    // try heif-convert
                    $escapedSrc = escapeshellarg($originalPath);
                    $escapedDst = escapeshellarg($intermediatePath);
                    $commands = [
                        "heif-convert $escapedSrc $escapedDst",
                        "magick $escapedSrc $escapedDst",
                        "convert $escapedSrc $escapedDst",
                    ];

                    foreach ($commands as $cmd) {
                        try {
                            exec($cmd.' 2>&1', $output, $exitCode);
                            if ($exitCode === 0 && file_exists($intermediatePath)) {
                                $processingPath = $intermediatePath;
                                $cleanupIntermediate = true;
                                break;
                            }
                        } catch (\Throwable $e) {
                            // ignore and try next
                        }
                    }
                }

                if ($processingPath === $originalPath) {
                    Log::warning("HEIC/HEIF conversion failed for: {$originalPath}");
                    // give up but do not delete original
                    if ($intermediatePath && file_exists($intermediatePath)) {
                        @unlink($intermediatePath);
                    }

                    return;
                }
            }

            // Temp output path (placed next to the processing file)
            $tempPath = $processingPath.'.tmp.'.$format;

            $conversionSucceeded = false;

            // Try Imagick first (apply EXIF orientation where possible)
            if (class_exists('Imagick')) {
                try {
                    $img = new \Imagick($processingPath);

                    // Try to get orientation from Imagick or EXIF and apply transforms
                    $orientation = null;
                    if (method_exists($img, 'getImageOrientation')) {
                        $orientation = $img->getImageOrientation();
                    } elseif (function_exists('exif_read_data')) {
                        $exif = @exif_read_data($processingPath);
                        $orientation = $exif['Orientation'] ?? null;
                    }

                    if ($orientation) {
                        switch ($orientation) {
                            case 2: // mirrored horizontally
                                if (method_exists($img, 'flopImage')) {
                                    $img->flopImage();
                                }
                                break;
                            case 3: // rotated 180
                                $img->rotateImage(new \ImagickPixel('none'), 180);
                                break;
                            case 4: // mirrored vertically
                                if (method_exists($img, 'flipImage')) {
                                    $img->flipImage();
                                }
                                break;
                            case 5: // mirrored horizontally then rotate 90 CW
                                if (method_exists($img, 'flopImage')) {
                                    $img->flopImage();
                                }
                                $img->rotateImage(new \ImagickPixel('none'), 90);
                                break;
                            case 6: // rotate 90 CW
                                $img->rotateImage(new \ImagickPixel('none'), 90);
                                break;
                            case 7: // mirrored horizontally then rotate 270 CW
                                if (method_exists($img, 'flopImage')) {
                                    $img->flopImage();
                                }
                                $img->rotateImage(new \ImagickPixel('none'), -90);
                                break;
                            case 8: // rotate 270 CW
                                $img->rotateImage(new \ImagickPixel('none'), -90);
                                break;
                        }
                    }

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
                    Log::warning("GD failed to create image resource from: {$processingPath}");

                    return;
                }

                // Apply EXIF orientation for GD if available
                $orientation = null;
                if (function_exists('exif_read_data')) {
                    $exif = @exif_read_data($processingPath);
                    $orientation = $exif['Orientation'] ?? null;
                }

                if ($orientation) {
                    switch ($orientation) {
                        case 2:
                            if (function_exists('imageflip')) {
                                imageflip($src, IMG_FLIP_HORIZONTAL);
                            }
                            break;
                        case 3:
                            $src = imagerotate($src, 180, 0);
                            break;
                        case 4:
                            if (function_exists('imageflip')) {
                                imageflip($src, IMG_FLIP_VERTICAL);
                            }
                            break;
                        case 5:
                            if (function_exists('imageflip')) {
                                imageflip($src, IMG_FLIP_HORIZONTAL);
                            }
                            $src = imagerotate($src, -90, 0);
                            break;
                        case 6:
                            $src = imagerotate($src, -90, 0);
                            break;
                        case 7:
                            if (function_exists('imageflip')) {
                                imageflip($src, IMG_FLIP_HORIZONTAL);
                            }
                            $src = imagerotate($src, 90, 0);
                            break;
                        case 8:
                            $src = imagerotate($src, 90, 0);
                            break;
                    }
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
