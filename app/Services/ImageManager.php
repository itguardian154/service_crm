<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager as InterventionImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;

class ImageService
{
    protected InterventionImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new InterventionImageManager(
            Driver::class,
            autoOrientation: true
        );
    }

    public function rotateExistingImage(string $imagePath, string $direction = 'right'): bool
    {
        try {
            $disk = Storage::disk('public');

            if (!$disk->exists($imagePath)) {
                return false;
            }

            // Ambil file dari storage
            $content = $disk->get($imagePath);

            // Baca image dari binary
            $image = $this->imageManager->read($content);

            // Validasi arah rotasi
            $angle = match ($direction) {
                'right' => 90,
                'left'  => -90,
                default => 0,
            };

            if ($angle === 0) {
                return false;
            }

            // Rotate image
            $image->rotate($angle);

            // Encode ulang ke JPEG (quality 80)
            $encoded = $image->encode(new JpegEncoder(80));

            // Simpan kembali (overwrite)
            $disk->put($imagePath, (string) $encoded);

            return true;

        } catch (\Throwable $e) {
            Log::error('Image rotate failed', [
                'path' => $imagePath,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}