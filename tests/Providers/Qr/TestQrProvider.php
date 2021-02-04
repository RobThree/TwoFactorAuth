<?php

namespace Tests\Providers\Qr;

use RobThree\Auth\Providers\Qr\IQRCodeProvider;

class TestQrProvider implements IQRCodeProvider
{
    public function getQRCodeImage($qrtext, $size)
    {
        return $qrtext . '@' . $size;
    }

    public function getMimeType()
    {
        return 'test/test';
    }
}
