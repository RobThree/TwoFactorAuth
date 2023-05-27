<?php

declare(strict_types=1);

namespace RobThree\Auth\Providers\Qr;

/**
 * Use http://goqr.me/api/doc/create-qr-code/ to get QR code
 */
class QRServerProvider extends BaseHTTPQRCodeProvider
{
    public function __construct(protected bool $verifyssl = false, public string $errorcorrectionlevel = 'L', public int $margin = 4, public int $qzone = 1, public string $bgcolor = 'ffffff', public string $color = '000000', public string $format = 'png')
    {
    }

    public function getMimeType(): string
    {
        switch (strtolower($this->format)) {
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'svg':
                return 'image/svg+xml';
            case 'eps':
                return 'application/postscript';
        }
        throw new QRException(sprintf('Unknown MIME-type: %s', $this->format));
    }

    public function getQRCodeImage(string $qrtext, int $size): string
    {
        return $this->getContent($this->getUrl($qrtext, $size));
    }

    public function getUrl(string $qrtext, int $size): string
    {
        return sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=%1$sx%1$s&ecc=%2$s&margin=%3$s&qzone=%4$s&bgcolor=%5$s&color=%6$s&format=%7$s&data=%8$s',
            $size,
            strtoupper($this->errorcorrectionlevel),
            $this->margin,
            $this->qzone,
            $this->decodeColor($this->bgcolor),
            $this->decodeColor($this->color),
            strtolower($this->format),
            rawurlencode($qrtext)
        );
    }

    private function decodeColor(string $value): string
    {
        return vsprintf('%d-%d-%d', sscanf($value, '%02x%02x%02x'));
    }
}
