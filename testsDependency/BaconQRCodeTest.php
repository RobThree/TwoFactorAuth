<?php

declare(strict_types=1);

namespace TestsDependency;

use PHPUnit\Framework\TestCase;
use RobThree\Auth\Algorithm;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\Providers\Qr\HandlesDataUri;
use RobThree\Auth\Providers\Qr\IQRCodeProvider;
use RobThree\Auth\TwoFactorAuth;
use RuntimeException;

class BaconQRCodeTest extends TestCase
{
    use HandlesDataUri;

    protected IQRCodeProvider $qr;

    protected function setUp(): void
    {
        $this->qr = new BaconQrCodeProvider(1, '#000', '#FFF', 'svg');
        ;
    }

    public function testDependency(): void
    {
        $tfa = new TwoFactorAuth($this->qr, 'Test&Issuer', 6, 30, Algorithm::Sha1);

        $data = $this->DecodeDataUri($tfa->getQRCodeImageAsDataUri('Test&Label', 'VMR466AB62ZBOKHE'));
        $this->assertSame('image/svg+xml', $data['mimetype']);
    }

    public function testBadTextColour(): void
    {
        $this->expectException(RuntimeException::class);

        new BaconQrCodeProvider(1, 'not-a-colour', '#FFF');
    }

    public function testBadBackgroundColour(): void
    {
        $this->expectException(RuntimeException::class);

        new BaconQrCodeProvider(1, '#000', 'not-a-colour');
    }

    public function testBadTextColourHexRef(): void
    {
        $this->expectException(RuntimeException::class);

        new BaconQrCodeProvider(1, '#AAAA', '#FFF');
    }

    public function testBadBackgroundColourHexRef(): void
    {
        $this->expectException(RuntimeException::class);

        new BaconQrCodeProvider(1, '#000', '#AAAA');
    }
}
