<?php

namespace Tests\Providers\Rng;

use RobThree\Auth\Providers\Rng\IRNGProvider;

class TestRNGProvider implements IRNGProvider
{
    private $isSecure;

    function __construct($isSecure = false)
    {
        $this->isSecure = $isSecure;
    }

    public function getRandomBytes($bytecount)
    {
        $result = '';

        for ($i = 0; $i < $bytecount; $i++) {
            $result .= chr($i);
        }

        return $result;
    }

    public function isCryptographicallySecure()
    {
        return $this->isSecure;
    }
}
