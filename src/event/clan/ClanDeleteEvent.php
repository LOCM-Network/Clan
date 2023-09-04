<?php

declare(strict_types=1);

namespace phuongaz\openclan\event\clan;

use phuongaz\openclan\clan\Clan;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class ClanDeleteEvent extends ClanEvent implements Cancellable {
    use CancellableTrait;

    private Player $player;

    public function __construct(Clan $clan, Player $player) {
        parent::__construct($clan);
        $this->player = $player;
    }

    public function getPlayer() : Player {
        return $this->player;
    }
}