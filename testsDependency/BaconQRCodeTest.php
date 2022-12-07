<?php declare(strict_types=1);

namespace TestsDependency;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use PHPUnit\Framework\TestCase;
use RobThree\Auth\Algorithm;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\Providers\Qr\HandlesDataUri;
use RobThree\Auth\TwoFactorAuth;
use RuntimeException;

class BaconQRCodeTest extends TestCase
{
    use HandlesDataUri;

    public function testDependency(): void
    {
        // php < 7.1 will install an older Bacon QR Code
        if (!class_exists(ImagickImageBackEnd::class)) {
            $this->expectException(RuntimeException::class);

            $qr = new BaconQrCodeProvider(1, '#000', '#FFF', 'svg');
        } else {
            $qr = new BaconQrCodeProvider(1, '#000', '#FFF', 'svg');

            $tfa = new TwoFactorAuth('Test&Issuer', 6, 30, Algorithm::Sha1, $qr);

            $data = $this->DecodeDataUri($tfa->getQRCodeImageAsDataUri('Test&Label', 'VMR466AB62ZBOKHE'));
            $this->assertEquals('image/svg+xml', $data['mimetype']);
        }
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
