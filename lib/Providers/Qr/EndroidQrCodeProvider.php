<?php

declare(strict_types=1);

namespace RobThree\Auth\Providers\Qr;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class EndroidQrCodeProvider implements IQRCodeProvider
{
    public $bgcolor;

    public $color;

    public $margin;

    public $errorcorrectionlevel;

    protected $endroid4 = false;

    public function __construct($bgcolor = 'ffffff', $color = '000000', $margin = 0, $errorcorrectionlevel = 'H')
    {
        $this->endroid4 = method_exists(QrCode::class, 'create');

        $this->bgcolor = $this->handleColor($bgcolor);
        $this->color = $this->handleColor($color);
        $this->margin = $margin;
        $this->errorcorrectionlevel = $this->handleErrorCorrectionLevel($errorcorrectionlevel);
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }

    public function getQRCodeImage(string $qrText, int $size): string
    {
        if (! $this->endroid4) {
            return $this->qrCodeInstance($qrText, $size)->writeString();
        }

        $writer = new PngWriter();
        return $writer->write($this->qrCodeInstance($qrText, $size))->getString();
    }

    protected function qrCodeInstance(string $qrText, int $size): QrCode
    {
        $qrCode = new QrCode($qrText);
        $qrCode->setSize($size);

        $qrCode->setErrorCorrectionLevel($this->errorcorrectionlevel);
        $qrCode->setMargin($this->margin);
        $qrCode->setBackgroundColor($this->bgcolor);
        $qrCode->setForegroundColor($this->color);

        return $qrCode;
    }

    private function handleColor(string $color): Color|array
    {
        $split = str_split($color, 2);
        $r = hexdec($split[0]);
        $g = hexdec($split[1]);
        $b = hexdec($split[2]);

        return $this->endroid4
            ? new Color($r, $g, $b, 0)
            : [
                'r' => $r,
                'g' => $g,
                'b' => $b,
                'a' => 0,
            ];
    }

    private function handleErrorCorrectionLevel(string $level): ErrorCorrectionLevelInterface|ErrorCorrectionLevel
    {
        return match ($level) {
            'L' => $this->endroid4 ? new ErrorCorrectionLevelLow() : ErrorCorrectionLevel::LOW(),
            'M' => $this->endroid4 ? new ErrorCorrectionLevelMedium() : ErrorCorrectionLevel::MEDIUM(),
            'Q' => $this->endroid4 ? new ErrorCorrectionLevelQuartile() : ErrorCorrectionLevel::QUARTILE(),
            default => $this->endroid4 ? new ErrorCorrectionLevelHigh() : ErrorCorrectionLevel::HIGH(),
        };
    }
}
