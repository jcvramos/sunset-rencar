<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Comprime y almacena imágenes usando la extensión GD nativa de PHP.
 * - Redimensiona a un ancho máximo configurable (default 1600px)
 * - Convierte todo a JPEG con calidad configurable (default 80)
 * - No requiere dependencias externas (Intervention/Image, etc.)
 *
 * Ahorro típico: imagen móvil de 4-8MB → 200-500KB sin pérdida visual perceptible.
 */
class ImageService
{
    public static function compressAndStore(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $maxWidth = 1600,
        int $quality = 80,
    ): string {
        $mime = $file->getMimeType();

        // Archivos no-imagen pasan directos
        if (!str_starts_with($mime, 'image/')) {
            return $file->store($directory, $disk);
        }

        $source = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png'               => @imagecreatefrompng($file->getRealPath()),
            'image/gif'               => @imagecreatefromgif($file->getRealPath()),
            'image/webp'              => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file->getRealPath()) : null,
            default                   => null,
        };

        // Si GD no pudo, guardar original sin comprimir (no perdemos la foto)
        if (!$source) {
            return $file->store($directory, $disk);
        }

        // Respetar orientación EXIF en fotos de móvil
        if (function_exists('exif_read_data') && in_array($mime, ['image/jpeg', 'image/jpg'])) {
            $exif = @exif_read_data($file->getRealPath());
            if (!empty($exif['Orientation'])) {
                $source = self::applyExifOrientation($source, $exif['Orientation']);
            }
        }

        $origW = imagesx($source);
        $origH = imagesy($source);

        if ($origW > $maxWidth) {
            $newW = $maxWidth;
            $newH = (int) round($origH * ($maxWidth / $origW));
        } else {
            $newW = $origW;
            $newH = $origH;
        }

        $resized = imagecreatetruecolor($newW, $newH);

        // Para PNG/transparencia: relleno blanco (porque convertimos a JPG)
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefilledrectangle($resized, 0, 0, $newW, $newH, $white);

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        // Guardar como JPG
        Storage::disk($disk)->makeDirectory($directory);
        $relative = trim($directory, '/') . '/' . Str::random(24) . '.jpg';
        $fullPath = Storage::disk($disk)->path($relative);

        imagejpeg($resized, $fullPath, $quality);

        imagedestroy($source);
        imagedestroy($resized);

        return $relative;
    }

    /**
     * Borra una imagen del disco si existe.
     */
    public static function delete(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    private static function applyExifOrientation($img, int $orientation)
    {
        return match ($orientation) {
            3       => imagerotate($img, 180, 0),
            6       => imagerotate($img, -90, 0),
            8       => imagerotate($img, 90, 0),
            default => $img,
        };
    }
}
