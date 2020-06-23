<?php

namespace RobThree\Auth\Providers\Qr;

interface IQRCodeProvider
{
    public function getQRCodeHTML($qrtext, $size);
}
