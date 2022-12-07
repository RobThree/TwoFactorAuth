<?php

namespace RobThree\Auth\Providers\Rng;

interface IRNGProvider
{
    public function getRandomBytes(int $bytecount): string;

    public function isCryptographicallySecure(): bool;
}
