<?php

namespace Tests\Providers\Time;

use RobThree\Auth\Providers\Time\ITimeProvider;

class TestTimeProvider implements ITimeProvider
{
    private $time;

    function __construct($time)
    {
        $this->time = $time;
    }

    public function getTime()
    {
        return $this->time;
    }
}
