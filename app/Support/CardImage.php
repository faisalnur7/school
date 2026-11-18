<?php

namespace App\Support;

/**
 * Pre-fits an image to an exact box for PDF output.
 *
 * mPDF ignores CSS `object-fit`, so a photo or logo placed in a fixed box would be stretched.
 * Instead the image is cropped ("cover") or padded ("contain") with GD to the box's aspect ratio
 * and cached on disk. Falls back to the original file when GD or the source is unusable.
 */
class CardImage
{
    private const DPI = 200;

    /**
     * @param  string  $path     absolute path of the source image
     * @param  string  $mode     'cover' (crop to fill) or 'contain' (fit inside, transparent padding)
     * @return string absolute path of the fitted image (or $path unchanged)
     */
    public static function fit(string $path, float $widthCm, float $heightCm, string $mode = 'cover'): string
    {
        if (!is_file($path) || !extension_loaded('gd') || $widthCm <= 0 || $heightCm <= 0) {
            return $path;
        }

        $mode = $mode === 'contain' ? 'contain' : 'cover';
        $targetW = max(1, (int) round($widthCm / 2.54 * self::DPI));
        $targetH = max(1, (int) round($heightCm / 2.54 * self::DPI));

        $directory = storage_path('app/card-image-cache');
        $cached = $directory . '/' . md5($path . filemtime($path) . "|$targetW|$targetH|$mode") . '.png';
        if (is_file($cached)) {
            return $cached;
        }

        $info = @getimagesize($path);
        $source = match ($info[2] ?? null) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_GIF => @imagecreatefromgif($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
        if (!$source) {
            return $path;
        }

        $sourceW = imagesx($source);
        $sourceH = imagesy($source);
        $canvas = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 255, 255, 255, 127));
        imagealphablending($canvas, true);

        $scale = $mode === 'cover'
            ? max($targetW / $sourceW, $targetH / $sourceH)
            : min($targetW / $sourceW, $targetH / $sourceH);
        $drawW = max(1, (int) round($sourceW * $scale));
        $drawH = max(1, (int) round($sourceH * $scale));
        imagecopyresampled(
            $canvas, $source,
            (int) round(($targetW - $drawW) / 2), (int) round(($targetH - $drawH) / 2), 0, 0,
            $drawW, $drawH, $sourceW, $sourceH
        );

        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }
        $saved = @imagepng($canvas, $cached, 6);
        imagedestroy($source);
        imagedestroy($canvas);

        return $saved ? $cached : $path;
    }
}
