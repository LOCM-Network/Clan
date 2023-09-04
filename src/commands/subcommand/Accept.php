<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands\subcommand;

use CortexPE\Commando\BaseSubCommand;
use phuongaz\openclan\event\player\PlayerAcceptInviteEvent;
use phuongaz\openclan\util\MessageContruct;
use phuongaz\openclan\util\trait\ClanTrait;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Accept extends BaseSubCommand {
    use ClanTrait;

    protected function prepare(): void {

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            MessageContruct::new( "command-only-player")->sendTo($sender);
            return;
        }
        if(!self::hasInvite($sender)) {
            MessageContruct::new("no-invite")->sendTo($sender);
            return;
        }
        $invite = self::getInvite($sender);
        $clan = $invite["clan"];
        $event = new PlayerAcceptInviteEvent($sender, $clan);
        $event->setCallback(function(PlayerAcceptInviteEvent $event) use ($clan){
           if($event->isCancelled()) {
               MessageContruct::new("accept-invite-cancelled")->sendTo($event->getPlayer());
               return;
           }
           $clan = $event->getClan();
           $statusMessage = self::acceptInvite($event->getPlayer());
           $statusMessage->sendTo($event->getPlayer());
           $clan->broadcastMessage(MessageContruct::new("player-join-clan", ["player" => $event->getPlayer()->getName()]));
        });
        $event->call();
    }
}