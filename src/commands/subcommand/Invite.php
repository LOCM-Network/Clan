<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use phuongaz\openclan\event\player\PlayerInviteEvent;
use phuongaz\openclan\util\MessageContruct;
use phuongaz\openclan\util\trait\ClanTrait;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class Invite extends BaseSubCommand {
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
        $player = $args["player"];
        if ($player === $sender->getName()) {
            MessageContruct::new("clan-invite-self")->sendTo($sender);
            return;
        }

        if(Server::getInstance()->getPlayerExact($player) === null) {
            MessageContruct::new("player-not-found", ["player" => $player])->sendTo($sender);
            return;
        }

        $senderClan = self::getClanByPlayer($sender);
        if ($senderClan === null) {
            MessageContruct::new("not-have-clan")->sendTo($sender);
            return;
        }

        if($senderClan->isFull()) {
            MessageContruct::new("clan-is-full")->sendTo($sender);
            return;
        }

        $player = Server::getInstance()->getPlayerExact($player);
        $playerClan = self::getClanByPlayer($player);
        if ($playerClan !== null) {
            if($playerClan->getName() == $senderClan->getName()) {
                MessageContruct::new("player-is-in-your-clan", ["player" => $player->getName()])->sendTo($sender);
                return;
            }
            MessageContruct::new("player-is-in-clan", ["player" => $player->getName(), "clan" => $playerClan->getName()])->sendTo($sender);
        }

        $event = new PlayerInviteEvent($sender, $player, $senderClan);
        $event->setCallback(function(PlayerInviteEvent $event) {
            $player = $event->getPlayer();
            $target = $event->getTarget();
            if($event->isCancelled()) {
                MessageContruct::new("clan-invite-cancelled", ["player" => $target->getName()])->sendTo($player);
                return;
            }
            $canSend = self::sendInvite($player, $target, $event->getClan());
            if($canSend) {
                MessageContruct::new("send-invite-to-player", ["player" => $target->getName()])->sendTo($player);
                return;
            }
            MessageContruct::new("player-has-been-another-invite", ["player" => $target->getName()])->sendTo($player);
        });
        $event->call();
    }
}