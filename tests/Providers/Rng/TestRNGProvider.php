<?php

namespace Tests\Providers\Rng;

use RobThree\Auth\Providers\Rng\IRNGProvider;

class TestRNGProvider implements IRNGProvider
{
    function __construct(private bool $isSecure = false)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getRandomBytes(int $bytecount): string
    {
        $result = '';

        for ($i = 0; $i < $bytecount; $i++) {
            $result .= chr($i);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isCryptographicallySecure(): bool
    {
        return $this->isSecure;
    }
}
