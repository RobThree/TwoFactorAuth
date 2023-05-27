<?php

declare(strict_types=1);

namespace RobThree\Auth\Providers\Qr;

// https://developers.google.com/chart/infographics/docs/qr_codes
class GoogleChartsQrCodeProvider extends BaseHTTPQRCodeProvider
{
    public function __construct(protected bool $verifyssl = false, public string $errorcorrectionlevel = 'L', public int $margin = 4, public string $encoding = 'UTF-8')
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
            'https://chart.googleapis.com/chart?chs=%1$sx%1$s&chld=%2$s&cht=qr&choe=%3$s&chl=%4$s',
            $size,
            urlencode(strtoupper($this->errorcorrectionlevel) . '|' . $this->margin),
            $this->encoding,
            rawurlencode($qrtext)
        );
    }
}
