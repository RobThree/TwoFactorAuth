<?php

declare(strict_types=1);

namespace Tests\Providers\Rng;

use PHPUnit\Framework\TestCase;
use RobThree\Auth\Providers\Rng\CSRNGProvider;

class CSRNGProviderTest extends TestCase
{
    use NeedsRngLengths;

    public function testCSRNGProvidersReturnExpectedNumberOfBytes(): void
    {
        $rng = new CSRNGProvider();
        foreach ($this->rngTestLengths as $l) {
            $this->assertSame($l, strlen($rng->getRandomBytes($l)));
        }
    }
}
