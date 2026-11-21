<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Prepares uploaded images for display and stores them safely.
 *
 *  - the upload is fully decoded and RE-ENCODED with GD, which strips any embedded
 *    payload/metadata (a polyglot "image" cannot survive this),
 *  - it is auto-rotated (EXIF), centre-cropped and resized to a fixed square,
 *  - it is saved on the public disk under a random UUID name (the client's
 *    file name is never used).
 */
class ImageService
{
    public function store(UploadedFile $file, string $directory, int $size): string
    {
        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($source === false) {
            throw new RuntimeException('The uploaded file could not be read as an image.');
        }

        $source = $this->fixOrientation($source, $file->getRealPath());

        $sw = imagesx($source);
        $sh = imagesy($source);

        // "cover" crop: scale so the shorter side fills the square, then crop the centre.
        $cropSide = min($sw, $sh);
        $srcX = (int) (($sw - $cropSide) / 2);
        $srcY = (int) (($sh - $cropSide) / 2);

        $canvas = imagecreatetruecolor($size, $size);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255)); // flatten transparency on white
        imagecopyresampled($canvas, $source, 0, 0, $srcX, $srcY, $size, $size, $cropSide, $cropSide);

        ob_start();
        imagejpeg($canvas, null, 85);
        $binary = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        $path = trim($directory, '/').'/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function fixOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $orientation = $exif['Orientation'] ?? 1;

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated ?: $image;
    }
}
