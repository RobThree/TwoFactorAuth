<?php

declare(strict_types=1);

namespace RobThree\Auth\Providers\Qr;

/**
 * Use https://image-charts.com to provide a QR code
 */
class ImageChartsQRCodeProvider extends BaseHTTPQRCodeProvider
{
    public function __construct(protected bool $verifyssl = false, public string $errorcorrectionlevel = 'L', public int $margin = 1)
    {
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }

    public function getQRCodeImage(string $qrtext, int $size): string
    {
        return $this->getContent($this->getUrl($qrtext, $size));
    }

    public function getUrl(string $qrtext, int $size): string
    {
        return sprintf(
            'https://image-charts.com/chart?cht=qr&chs=%1$dx%1$d&chld=%2$s|%3$s&chl=%4$s',
            ceil($size / 2),
            $this->errorcorrectionlevel,
            $this->margin,
            rawurlencode($qrtext)
        );
    }
}
