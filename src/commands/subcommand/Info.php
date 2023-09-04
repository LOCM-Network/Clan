<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use phuongaz\openclan\util\MessageContruct;
use phuongaz\openclan\util\trait\ClanTrait;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Info extends BaseSubCommand {
    use ClanTrait;

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->registerArgument(0, new RawStringArgument("clan"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!isset($args["clan"])) {
            if(!$sender instanceof Player) {
                MessageContruct::new("command-only-player")->sendTo($sender);
                return;
            }
            $clan = self::getClanByPlayer($sender);
            if($clan === null) {
                MessageContruct::new("not-have-clan")->sendTo($sender);
                return;
            }
            $info = $clan->getClanInfo();
            $info->sendTo($sender);
            return;
        }
        $clan = self::getClanByName($args["clan"]);
        if($clan === null) {
            MessageContruct::new("clan-not-found", ["clan" => $args["clan"]])->sendTo($sender);
            return;
        }

        $info = $clan->getClanInfo();
        $info->sendTo($sender);
    }
}