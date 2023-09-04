<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands\subcommand;

use CortexPE\Commando\BaseSubCommand;
use phuongaz\openclan\clan\Levels;
use phuongaz\openclan\event\clan\ClanUpgradeEvent;
use phuongaz\openclan\util\MessageContruct;
use phuongaz\openclan\util\trait\ClanTrait;
use phuongaz\openclan\util\trait\LoggerTrait;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class Upgrade extends BaseSubCommand {
    use ClanTrait, LoggerTrait;

    protected function prepare(): void {

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!$sender instanceof Player) {
            MessageContruct::new( "command-only-player")->sendTo($sender);
            return;
        }
        $clan = self::getClanByPlayer($sender);
        if(is_null($clan)) {
            MessageContruct::new("not-have-clan")->sendTo($sender);
            return;
        }
        if(!$clan->isOwner($sender->getName())) {
            MessageContruct::new("not-owner-clan")->sendTo($sender);
            return;
        }
        if($clan->getLevel() >= Levels::MAX_LEVEL) {
            MessageContruct::new("clan-max-level")->sendTo($sender);
            return;
        }
        if(!$clan->canUpgradeClan()) {
            $need = $clan->calculatePointNeed();
            MessageContruct::new("clan-cant-upgrade", ["point" => $clan->getPoint(), "need" => $need])->sendTo($sender);
            return;
        }
        $event = new ClanUpgradeEvent($clan, $clan->getLevel() + 1);
        $event->setCallback(function (ClanUpgradeEvent $event) use ($sender){
            if($event->isCancelled()) {
                MessageContruct::new("clan-upgrade-cancelled")->sendTo($sender);
                return;
            }
            $clan = $event->getClan();
            $statusMessage = $clan->upgradeClan();
            $statusMessage->sendTo($sender);
            $broadMessage = MessageContruct::new("clan-upgrade-success", ["name" => $clan->getName(), "level" => $clan->getLevel()]);
            Server::getInstance()->broadcastMessage($broadMessage->parse());
            self::log("Clan " . $clan->getName() . " has been upgraded to level " . $clan->getLevel() . " by " . $sender->getName());
        });
        $event->call();
    }
}