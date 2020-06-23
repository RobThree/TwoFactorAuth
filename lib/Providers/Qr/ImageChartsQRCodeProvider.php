<?php

namespace RobThree\Auth\Providers\Qr;

// https://image-charts.com
class ImageChartsQRCodeProvider extends BaseHTTPQRCodeProvider
{
    public $errorcorrectionlevel;
    public $margin;

    function __construct($verifyssl = false, $errorcorrectionlevel = 'L', $margin = 1)
    {
        if (!is_bool($verifyssl))
            throw new \QRException('VerifySSL must be bool');

        $this->verifyssl = $verifyssl;

        $this->errorcorrectionlevel = $errorcorrectionlevel;
        $this->margin = $margin;
    }

    public function getMimeType()
    {
        return 'image/png';
    }

    public function getQRCodeHTML($qrtext, $size)
    {
        return '<img class="qrCode" src="data:'
            . $this->getMimeType()
            . ';base64,'
            . base64_encode($this->getContent($this->getUrl($qrtext, $size)))
            . '" width='.$size.' height='.$size.'>';
    }

    public function getUrl($qrtext, $size)
    {
        return 'https://image-charts.com/chart?cht=qr'
            . '&chs=' . ceil($size/2) . 'x' . ceil($size/2)
            . '&chld=' . $this->errorcorrectionlevel . '|' . $this->margin
            . '&chl=' . rawurlencode($qrtext);
    }
}
