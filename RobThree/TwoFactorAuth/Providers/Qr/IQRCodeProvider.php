<?php

namespace RobThree\TwoFactorAuth\Providers\Qr;

interface IQRCodeProvider
{
	public function getQRCodeImage($qrtext, $size);
    public function getMimeType();
}