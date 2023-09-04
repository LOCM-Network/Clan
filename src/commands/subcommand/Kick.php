<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use phuongaz\openclan\event\player\PlayerKickEvent;
use phuongaz\openclan\util\MessageContruct;
use phuongaz\openclan\util\trait\ClanTrait;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class Kick extends BaseSubCommand{
    use ClanTrait;

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->registerArgument(0, new RawStringArgument("player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            MessageContruct::new( "command-only-player")->sendTo($sender);
            return;
        }
        if(!isset($args["player"])) {
            //send form
            return;
        }
        $player = $args["player"];
        $clan = self::getClanByPlayer($sender);
        if($clan === null) {
            MessageContruct::new( "not-have-clan")->sendTo($sender);
            return;
        }
        if(!$clan->isOwner($sender->getName()) || !$clan->isDeputy($sender->getName())) {
            MessageContruct::new( "not-owner-or-deputy")->sendTo($sender);
            return;
        }
        if(!$clan->isMember($player)) {
            MessageContruct::new( "not-member", ["player" => $player])->sendTo($sender);
            return;
        }
        $event = new PlayerKickEvent($player, $sender, $clan);
        $event->setCallback(function(PlayerKickEvent $event){
            $player = $event->getPlayer();
            $target = $event->getTarget();
            $clan = $event->getClan();
            if($event->isCancelled()) {
                MessageContruct::new( "kick-cancelled")->sendTo($player);
                return;
            }
            $clan->removeMember($target);
            if(($target = Server::getInstance()->getPlayerExact($target)) !== null) {
                MessageContruct::new( "has-kicked", ["clan" => $clan->getName()])->sendTo($target);
            }
            MessageContruct::new( "kicked", ["player" => $target])->sendTo($player);
        });
        $event->call();
    }
}