<?php

namespace TestsDependency;

use PHPUnit\Framework\TestCase;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\HandlesDataUri;

class BaconQRCodeTest extends TestCase
{
    use HandlesDataUri;

    public function testDependency()
    {
        $qr = new BaconQrCodeProvider(1, '#000', '#FFF', 'svg');

        $tfa = new TwoFactorAuth('Test&Issuer', 6, 30, 'sha1', $qr);

        $data = $this->DecodeDataUri($tfa->getQRCodeImageAsDataUri('Test&Label', 'VMR466AB62ZBOKHE'));
        $this->assertEquals('image/svg+xml', $data['mimetype']);
    }

    public function testBadTextColour()
    {
        $this->expectException(\RuntimeException::class);

        new BaconQrCodeProvider(1, 'not-a-colour', '#FFF');
    }

    public function testBadBackgroundColour()
    {
        $this->expectException(\RuntimeException::class);

        new BaconQrCodeProvider(1, '#000', 'not-a-colour');
    }
}
