<?php

declare(strict_types=1);

namespace phuongaz\openclan\event\player;

use ColinHDev\libAsyncEvent\AsyncEvent;
use ColinHDev\libAsyncEvent\ConsecutiveEventHandlerExecutionTrait;
use phuongaz\openclan\clan\Clan;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerClanEvent extends PlayerEvent implements AsyncEvent {
    use ConsecutiveEventHandlerExecutionTrait;

    private Clan $clan;

    public function __construct(Player $player, Clan $clan) {
        $this->player = $player;
        $this->clan = $clan;
    }

    public function getClan() : Clan {
        return $this->clan;
    }
}