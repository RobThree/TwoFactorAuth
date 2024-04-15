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
}
