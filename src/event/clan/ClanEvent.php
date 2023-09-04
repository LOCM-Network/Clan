<?php

declare(strict_types=1);

namespace phuongaz\openclan\event\clan;

use ColinHDev\libAsyncEvent\AsyncEvent;
use ColinHDev\libAsyncEvent\ConsecutiveEventHandlerExecutionTrait;
use phuongaz\openclan\clan\Clan;
use pocketmine\event\Event;

class ClanEvent extends Event implements AsyncEvent {
    use ConsecutiveEventHandlerExecutionTrait;

    private Clan $clan;

    public function __construct(Clan $clan) {
        $this->clan = $clan;
    }

    public function getClan() : Clan {
        return $this->clan;
    }
}