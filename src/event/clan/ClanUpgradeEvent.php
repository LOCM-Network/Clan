<?php

declare(strict_types=1);

namespace phuongaz\openclan\event\clan;

use phuongaz\openclan\clan\Clan;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class ClanUpgradeEvent extends ClanEvent implements Cancellable {
    use CancellableTrait;

    private int $level;

    public function __construct(Clan $clan, int $level) {
        parent::__construct($clan);
        $this->level = $level;
    }

    public function getLevel(): int {
        return $this->level;
    }
}