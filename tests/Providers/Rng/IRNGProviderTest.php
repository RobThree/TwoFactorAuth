<?php

namespace Tests\Providers\Rng;

use PHPUnit\Framework\TestCase;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;

class IRNGProviderTest extends TestCase
{
    public function testCreateSecretThrowsOnInsecureRNGProvider()
    {
        $rng = new TestRNGProvider();

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, $rng);

        $this->expectException(TwoFactorAuthException::class);
        $tfa->createSecret();
    }

    public function testCreateSecretOverrideSecureDoesNotThrowOnInsecureRNG()
    {
        $rng = new TestRNGProvider();

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, $rng);
        $this->assertEquals('ABCDEFGHIJKLMNOP', $tfa->createSecret(80, false));
    }

    public function testCreateSecretDoesNotThrowOnSecureRNGProvider()
    {
        $rng = new TestRNGProvider(true);

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, $rng);
        $this->assertEquals('ABCDEFGHIJKLMNOP', $tfa->createSecret());
    }

    public function testCreateSecretGeneratesDesiredAmountOfEntropy()
    {
        $rng = new TestRNGProvider(true);

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, $rng);
        $this->assertEquals('A', $tfa->createSecret(5));
        $this->assertEquals('AB', $tfa->createSecret(6));
        $this->assertEquals('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $tfa->createSecret(128));
        $this->assertEquals('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', $tfa->createSecret(160));
        $this->assertEquals('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', $tfa->createSecret(320));
        $this->assertEquals('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567ABCDEFGHIJKLMNOPQRSTUVWXYZ234567A', $tfa->createSecret(321));
    }
}
