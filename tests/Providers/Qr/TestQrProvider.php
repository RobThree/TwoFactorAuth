<?php

declare(strict_types=1);

namespace Tests\Providers\Qr;

use RobThree\Auth\Providers\Qr\IQRCodeProvider;

class TestQrProvider implements IQRCodeProvider
{
    public function getQRCodeImage(string $qrtext, int $size): string
    {
        return $qrtext . '@' . $size;
    }

    public function getMimeType(): string
    {
        return 'test/test';
    }
}
