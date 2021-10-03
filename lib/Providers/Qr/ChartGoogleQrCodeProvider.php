<?php

namespace RobThree\Auth\Providers\Qr;

// https://developers.google.com/chart/infographics/docs/qr_codes
class ChartGoogleQrCodeProvider extends BaseHTTPQRCodeProvider
{
    /** @var string */
    public $errorcorrectionlevel;

    /** @var int */
    public $margin;

    /** @var string */
    public $encoding;

    /**
     * @param string $errorcorrectionlevel
     * @param int $margin
     * @param string $encoding
     */
    public function __construct($errorcorrectionlevel = 'L', $margin = 4, $encoding = 'UTF-8')
    {
        $this->verifyssl = false;

        $this->errorcorrectionlevel = $errorcorrectionlevel;
        $this->margin = $margin;
        $this->encoding = $encoding;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return 'image/png';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getQRCodeImage($qrtext, $size)
    {
        return $this->getContent($this->getUrl($qrtext, $size));   
    }

    /**
     * @param string $qrtext the value to encode in the QR code
     * @param int|string $size the desired size of the QR code
     *
     * @return string file contents of the QR code
     */
    public function getUrl($qrtext, $size)
    {
        return 'https://chart.googleapis.com/chart'
            . '?chs=' . $size . 'x' . $size
            . '&chld=' . urlencode(strtoupper($this->errorcorrectionlevel) . '|' . $this->margin)
            . '&cht=' . 'qr'
            . '&chl=' . rawurlencode($qrtext);
    }
}
