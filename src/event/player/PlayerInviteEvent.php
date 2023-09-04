<?php

declare(strict_types=1);

namespace phuongaz\openclan\event\player;

use phuongaz\openclan\clan\Clan;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerInviteEvent extends PlayerClanEvent implements Cancellable {
    use CancellableTrait;

    private Player $target;

    public function __construct(Player $player, Player $target, Clan $clan) {
        parent::__construct($player, $clan);
        $this->target = $target;
    }

    public function getTarget(): Player {
        return $this->target;
    }
}