<?php

declare(strict_types=1);

namespace Tests\Providers\Rng;

use PHPUnit\Framework\TestCase;
use RobThree\Auth\Algorithm;
use RobThree\Auth\TwoFactorAuth;

class IRNGProviderTest extends TestCase
{
    public function testCreateSecret(): void
    {
        $tfa = new TwoFactorAuth('Test', 6, 30, Algorithm::Sha1, null, null);
        $this->assertIsString($tfa->createSecret());
    }

    public function testCreateSecretGeneratesDesiredAmountOfEntropy(): void
    {
        $rng = new TestRNGProvider();

        $tfa = new TwoFactorAuth('Test', 6, 30, Algorithm::Sha1, null, $rng);
        $this->assertSame('A', $tfa->createSecret(5));
        $this->assertSame('AB', $tfa->createSecret(6));
        $this->assertSame('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $tfa->createSecret(128));
        $this->assertSame('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', $tfa->createSecret(160));
        $this->assertSame('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', $tfa->createSecret(320));
        $this->assertSame('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567ABCDEFGHIJKLMNOPQRSTUVWXYZ234567A', $tfa->createSecret(321));
    }
}
