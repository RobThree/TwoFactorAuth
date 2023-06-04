<?php

declare(strict_types=1);

namespace Tests\Providers\Rng;

use PHPUnit\Framework\TestCase;
use RobThree\Auth\Providers\Rng\OpenSSLRNGProvider;

class OpenSSLRNGProviderTest extends TestCase
{
    use NeedsRngLengths;

    public function testStrongOpenSSLRNGProvidersReturnExpectedNumberOfBytes(): void
    {
        $rng = new OpenSSLRNGProvider(true);
        foreach ($this->rngTestLengths as $l) {
            $this->assertSame($l, strlen($rng->getRandomBytes($l)));
        }

        $this->assertTrue($rng->isCryptographicallySecure());
    }

    public function testNonStrongOpenSSLRNGProvidersReturnExpectedNumberOfBytes(): void
    {
        $rng = new OpenSSLRNGProvider(false);
        foreach ($this->rngTestLengths as $l) {
            $this->assertSame($l, strlen($rng->getRandomBytes($l)));
        }

        $this->assertFalse($rng->isCryptographicallySecure());
    }
}
