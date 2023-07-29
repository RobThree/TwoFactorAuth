<?php

declare(strict_types=1);

namespace RobThree\Auth\Providers\Rng;

class CSRNGProvider implements IRNGProvider
{
    public function getRandomBytes(int $bytecount): string
    {
        return random_bytes($bytecount);    // PHP7+
    }

    public function isCryptographicallySecure(): bool
    {
        return true;
    }
}
