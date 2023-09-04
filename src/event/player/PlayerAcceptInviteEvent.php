<?php

declare(strict_types=1);

namespace phuongaz\openclan\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class PlayerAcceptInviteEvent extends PlayerClanEvent implements Cancellable {
    use CancellableTrait;
}