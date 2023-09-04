<?php

declare(strict_types=1);

namespace phuongaz\openclan\task;

use phuongaz\openclan\provider\Session;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class SaveTask extends Task {
    public function onRun(): void {
        Session::saveAll();
    }
}