<?php

declare(strict_types=1);

namespace Tests\Providers\Time;

use RobThree\Auth\Providers\Time\ITimeProvider;

class TestTimeProvider implements ITimeProvider
{
    public function __construct(
        private int $time
    ) {
        $this->time = $time;
    }

    public function getTime(): int
    {
        return $this->time;
    }
}
