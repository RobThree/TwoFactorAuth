<?php

declare(strict_types=1);

namespace RobThree\Auth\Providers\Qr;

use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class EndroidQrCodeWithLogoProvider extends EndroidQrCodeProvider
{
    protected $logoPath;

    protected $logoSize;

    /**
     * Adds an image to the middle of the QR Code.
     * @param string $path Path to an image file
     * @param array|int $size Just the width, or [width, height]
     */
    public function setLogo($path, $size = null)
    {
        $this->logoPath = $path;
        $this->logoSize = (array)$size;
    }

    public function getQRCodeImage(string $qrText, int $size): string
    {
        if (!$this->endroid4) {
            return $this->qrCodeInstance($qrText, $size)->writeString();
        }

        $logo = null;
        if ($this->logoPath) {
            if ($this->endroid6) {
                $logo = new Logo($this->logoPath, ...$this->logoSize);
            } else {
                $logo = Logo::create($this->logoPath);
                if ($this->logoSize) {
                    $logo->setResizeToWidth($this->logoSize[0]);
                    if (isset($this->logoSize[1])) {
                        $logo->setResizeToHeight($this->logoSize[1]);
                    }
                }
            }
        }
        $writer = new PngWriter();
        return $writer->write($this->qrCodeInstance($qrText, $size), $logo)->getString();
    }

    protected function qrCodeInstance(string $qrText, int $size): QrCode
    {
        $qrCode = parent::qrCodeInstance($qrText, $size);

        if (!$this->endroid4 && $this->logoPath) {
            $qrCode->setLogoPath($this->logoPath);
            if ($this->logoSize) {
                $qrCode->setLogoSize($this->logoSize[0], $this->logoSize[1] ?? null);
            }
        }

        return $qrCode;
    }
}
