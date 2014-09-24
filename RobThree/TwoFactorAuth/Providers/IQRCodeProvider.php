<?php

namespace RobThree\TwoFactorAuth\Providers;

interface IQRCodeProvider
{
	public function getQRCodeImage($qrtext, $size);
    public function getMimeType();
}