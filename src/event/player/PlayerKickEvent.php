<?php

declare(strict_types=1);

namespace phuongaz\openclan\event\player;

use phuongaz\openclan\clan\Clan;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerKickEvent extends PlayerClanEvent implements Cancellable {
    use CancellableTrait;

    private string $target;

    public function __construct(string $target, Player $player, Clan $clan) {
        $this->target = $target;
        parent::__construct($player, $clan);
    }

    public function getTarget() : string {
        return $this->target;
    }
}