<?php

declare(strict_types=1);

namespace Tests\Providers\Rng;

use RobThree\Auth\Providers\Rng\IRNGProvider;

class TestRNGProvider implements IRNGProvider
{
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
}
