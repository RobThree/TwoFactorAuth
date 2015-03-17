<?php

namespace RobThree\TwoFactorAuth\Providers\Rng;

interface IRNGProvider
{
    public function getRandomBytes($bytecount);
}