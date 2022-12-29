<?php

declare(strict_types=1);

namespace Tests\Providers\Rng;

use PHPUnit\Framework\TestCase;
use RobThree\Auth\Providers\Rng\CSRNGProvider;
use Tests\MightNotMakeAssertions;

class CSRNGProviderTest extends TestCase
{
    use NeedsRngLengths;
    use MightNotMakeAssertions;

    /**
     * @requires function random_bytes
     *
     * @return void
     */
    public function testCSRNGProvidersReturnExpectedNumberOfBytes()
    {
        if (function_exists('random_bytes')) {
            $rng = new CSRNGProvider();
            foreach ($this->rngTestLengths as $l) {
                $this->assertEquals($l, strlen($rng->getRandomBytes($l)));
            }
            $this->assertTrue($rng->isCryptographicallySecure());
        } else {
            $this->noAssertionsMade();
        }
    }
}
