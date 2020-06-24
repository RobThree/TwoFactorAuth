<?php

namespace RobThree\Auth\Providers\Qr;

// https://github.com/davidshimjs/qrcodejs
class QRJSProvider implements IQRCodeProvider
{
    public $errorcorrectionlevel;
    public $margin;
    public $qzone;
    public $bgcolor;
    public $color;
    public $format;

    function __construct($errorcorrectionlevel = 'L', $bgcolor = 'ffffff', $color = '000000')
    {
        $this->errorcorrectionlevel = $errorcorrectionlevel;
        $this->bgcolor = $bgcolor;
        $this->color = $color;
    }


    public function getQRCodeHTML($qrtext, $size)
    {
        return '<script src="js/qrcode.min.js"></script>
<div id="qrcode"></div>
<script type="text/javascript">
var qrcode = new QRCode(document.getElementById("qrcode"), {
  text: "'.$qrtext.'",
  width: '.$size.',
  height: '.$size.',
  colorDark : "#'.$this->bgcolor.'",
  colorLight : "#'.$this->color.'",
  correctLevel : QRCode.CorrectLevel.'.$this->errorcorrectionlevel.'
});
</script>';
    }

}
