<?php

namespace RobThree\Auth\Providers\QrHTML;

interface IQRCodeHTMLProvider
{
    public function getQRCodeHTML($qrtext, $size);
}
