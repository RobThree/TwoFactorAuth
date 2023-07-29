<?php

declare(strict_types=1);

namespace RobThree\Auth\Providers\Rng;

class OpenSSLRNGProvider implements IRNGProvider
{
    public function __construct(
        private readonly bool $requirestrong = true
    ) {
    }

    public function getRandomBytes(int $bytecount): string
    {
        // will throw an Exception on failure
        return openssl_random_pseudo_bytes($bytecount, $crypto_strong);
    }

    public function isCryptographicallySecure(): bool
    {
        return $this->requirestrong;
    }
}
