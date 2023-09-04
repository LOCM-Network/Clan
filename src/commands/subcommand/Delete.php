<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands\subcommand;

use CortexPE\Commando\args\BooleanArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use phuongaz\openclan\event\clan\ClanDeleteEvent;
use phuongaz\openclan\util\trait\LoggerTrait;
use phuongaz\openclan\provider\Session;
use phuongaz\openclan\util\MessageContruct;
use phuongaz\openclan\util\trait\ClanTrait;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Delete extends BaseSubCommand {
    use ClanTrait, LoggerTrait;

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->registerArgument(0, new BooleanArgument("confirm"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!$sender instanceof Player) {
            MessageContruct::new( "command-only-player")->sendTo($sender);
            return;
        }
        if(!isset($args["confirm"])) {
            //send form
            return;
        }

        $confirm = $args["confirm"];
        if($confirm) {
            $clan = self::getClanByPlayer($sender);
            $event = new ClanDeleteEvent($clan, $sender);
            $event->setCallback(function (ClanDeleteEvent $event) use ($sender, $clan) {
                if($event->isCancelled()) {
                    MessageContruct::new("clan-delete-cancelled")->sendTo($sender);
                }else{
                    Session::remove($clan, function() use ($sender, $clan) {
                        MessageContruct::new("clan-delete-success")->sendTo($sender);
                        self::log("Clan " . $clan->getName() . " has been deleted by " . $sender->getName());
                    });
                }
            });
            $event->call();
        }
    }
}