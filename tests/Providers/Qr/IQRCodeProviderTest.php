<?php

declare(strict_types=1);

namespace Tests\Providers\Qr;

use PHPUnit\Framework\TestCase;
use RobThree\Auth\Algorithm;
use RobThree\Auth\Providers\Qr\HandlesDataUri;
use RobThree\Auth\Providers\Qr\IQRCodeProvider;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;

class IQRCodeProviderTest extends TestCase
{
    use HandlesDataUri;

    protected IQRCodeProvider $qr;

    protected function setUp(): void
    {
        $this->qr = new TestQrProvider();
    }

    public function testTotpUriIsCorrect(): void
    {
        $tfa = new TwoFactorAuth($this->qr, 'Test&Issuer', 6, 30, Algorithm::Sha1);
        $data = $this->DecodeDataUri($tfa->getQRCodeImageAsDataUri('Test&Label', 'VMR466AB62ZBOKHE'));
        $this->assertSame('test/test', $data['mimetype']);
        $this->assertSame('base64', $data['encoding']);
        $this->assertSame('otpauth://totp/Test%26Label?secret=VMR466AB62ZBOKHE&issuer=Test%26Issuer&period=30&algorithm=SHA1&digits=6@200', $data['data']);
    }

    public function testTotpUriIsCorrectNoIssuer(): void
    {
        /**
         * The library specifies the issuer is null by default however in PHP 8.1
         * there is a deprecation warning for passing null as a string argument to rawurlencode
         */

        $tfa = new TwoFactorAuth($this->qr, null, 6, 30, Algorithm::Sha1);
        $data = $this->DecodeDataUri($tfa->getQRCodeImageAsDataUri('Test&Label', 'VMR466AB62ZBOKHE'));
        $this->assertSame('test/test', $data['mimetype']);
        $this->assertSame('base64', $data['encoding']);
        $this->assertSame('otpauth://totp/Test%26Label?secret=VMR466AB62ZBOKHE&issuer=&period=30&algorithm=SHA1&digits=6@200', $data['data']);
    }

    public function testGetQRCodeImageAsDataUriThrowsOnInvalidSize(): void
    {
        $tfa = new TwoFactorAuth($this->qr, 'Test', 6, 30, Algorithm::Sha1);

        $this->expectException(TwoFactorAuthException::class);

        $tfa->getQRCodeImageAsDataUri('Test', 'VMR466AB62ZBOKHE', 0);
    }
}
