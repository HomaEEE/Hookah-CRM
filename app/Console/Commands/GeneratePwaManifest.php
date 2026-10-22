<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GeneratePwaManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pwa:manifest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dynamic PWA manifest for admin panel based on environment variables and available icons';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $appName = config('app.name', 'Noire CRM');
        $appUrl = rtrim(config('app.url', 'https://noire.ee'), '/');
        $shortName = env('PWA_SHORT_NAME') ?: $appName;
        $description = env('PWA_DESCRIPTION') ?: ('Учет финансов '.$appName);
        $startUrl = env('PWA_START_URL') ?: '/admin/';
        $scope = env('PWA_SCOPE') ?: '/admin/';

        $pwaDir = public_path('assets/imgs/pwa');
        $iconsDir = $pwaDir.'/icons';

        if (! File::isDirectory($iconsDir)) {
            File::makeDirectory($iconsDir, 0755, true, true);
        }

        // Generate resized icons from base icon if GD is available
        $sourceIcon = $pwaDir.'/favicon-crm.png';
        if (! File::exists($sourceIcon)) {
            $sourceIcon = $pwaDir.'/favicon-touch.png';
        }

        $standardSizes = [48, 72, 96, 128, 144, 152, 192, 384, 512];

        if (File::exists($sourceIcon) && extension_loaded('gd')) {
            $this->generateIconsFromSource($sourceIcon, $iconsDir, $standardSizes);
        }

        // Discover all available icons
        $icons = [];

        // 1. Scan icons in assets/imgs/pwa/icons
        if (File::isDirectory($iconsDir)) {
            $iconFiles = File::files($iconsDir);
            foreach ($iconFiles as $file) {
                $ext = strtolower($file->getExtension());
                if (! in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'])) {
                    continue;
                }

                $sizeStr = null;
                $mime = 'image/'.($ext === 'svg' ? 'svg+xml' : $ext);

                if ($ext !== 'svg') {
                    $imgSize = @getimagesize($file->getPathname());
                    if ($imgSize) {
                        $sizeStr = $imgSize[0].'x'.$imgSize[1];
                        $mime = $imgSize['mime'] ?? $mime;
                    }
                }

                if (! $sizeStr && preg_match('/(\d+)x(\d+)/', $file->getFilename(), $matches)) {
                    $sizeStr = $matches[1].'x'.$matches[2];
                }

                $icons[] = [
                    'src' => 'assets/imgs/pwa/icons/'.$file->getFilename(),
                    'sizes' => $sizeStr ?: 'any',
                    'type' => $mime,
                    'purpose' => 'maskable any',
                ];
            }
        }

        // 2. If no subfolder icons found, fallback to icons in assets/imgs/pwa
        if (empty($icons) && File::isDirectory($pwaDir)) {
            $baseFiles = File::files($pwaDir);
            foreach ($baseFiles as $file) {
                $ext = strtolower($file->getExtension());
                if (! in_array($ext, ['png', 'svg', 'webp'])) {
                    continue;
                }

                $sizeStr = 'any';
                $mime = 'image/'.($ext === 'svg' ? 'svg+xml' : $ext);

                if ($ext !== 'svg') {
                    $imgSize = @getimagesize($file->getPathname());
                    if ($imgSize) {
                        $sizeStr = $imgSize[0].'x'.$imgSize[1];
                        $mime = $imgSize['mime'] ?? $mime;
                    }
                }

                $icons[] = [
                    'src' => 'assets/imgs/pwa/'.$file->getFilename(),
                    'sizes' => $sizeStr,
                    'type' => $mime,
                    'purpose' => 'maskable any',
                ];
            }
        }

        // Sort icons by dimension
        usort($icons, function ($a, $b) {
            $aSize = (int) explode('x', $a['sizes'])[0];
            $bSize = (int) explode('x', $b['sizes'])[0];

            return $aSize <=> $bSize;
        });

        $manifest = [
            'name' => $appName,
            'short_name' => $shortName,
            'start_url' => $startUrl,
            'scope' => $scope,
            'display' => 'standalone',
            'description' => $description,
            'lang' => config('app.locale', 'ru'),
            'dir' => 'auto',
            'orientation' => 'any',
            'icons' => $icons,
            'screenshots' => [
                [
                    'src' => 'https://www.pwabuilder.com/assets/screenshots/screen1.png',
                    'sizes' => '2880x1800',
                    'type' => 'image/png',
                    'description' => 'A screenshot of the home page',
                ],
            ],
            'related_applications' => [
                [
                    'platform' => 'windows',
                    'url' => '/admin',
                ],
            ],
            'prefer_related_applications' => false,
            'shortcuts' => [
                [
                    'name' => 'Добавить поставку',
                    'url' => '/admin/supplies',
                    'description' => 'Новая поставка',
                ],
                [
                    'name' => 'Добавить расход',
                    'url' => '/admin/expenses',
                    'description' => 'Новый расход',
                ],
                [
                    'name' => 'Добавить задачу',
                    'url' => '/admin/tasks',
                    'description' => 'Новая задача',
                ],
            ],
        ];

        $manifestPath = public_path('manifest-admin.json');
        File::put(
            $manifestPath,
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $this->info("PWA manifest generated at: {$manifestPath}");
        $this->line(" - Name: {$appName}");
        $this->line(" - Start URL: {$startUrl}");
        $this->line(' - Icons count: '.count($icons));

        return Command::SUCCESS;
    }

    /**
     * Generate standard icon sizes from base source image.
     */
    protected function generateIconsFromSource(string $sourcePath, string $targetDir, array $sizes): void
    {
        $srcImage = @imagecreatefrompng($sourcePath);
        if (! $srcImage) {
            return;
        }

        $srcWidth = imagesx($srcImage);
        $srcHeight = imagesy($srcImage);

        foreach ($sizes as $size) {
            $destFile = $targetDir."/icon-{$size}x{$size}.png";

            // Only generate if missing or source is newer
            if (File::exists($destFile) && filemtime($destFile) >= filemtime($sourcePath)) {
                continue;
            }

            $destImage = imagecreatetruecolor($size, $size);
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $size, $size, $transparent);

            imagecopyresampled(
                $destImage,
                $srcImage,
                0,
                0,
                0,
                0,
                $size,
                $size,
                $srcWidth,
                $srcHeight
            );

            imagepng($destImage, $destFile, 9);
            imagedestroy($destImage);
        }

        imagedestroy($srcImage);
    }
}
