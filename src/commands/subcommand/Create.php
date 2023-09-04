<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands\subcommand;

use CortexPE\Commando\args\BooleanArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use phuongaz\openclan\clan\Clan;
use phuongaz\openclan\event\clan\ClanCreationEvent;
use phuongaz\openclan\OpenClan;
use phuongaz\openclan\provider\Session;
use phuongaz\openclan\util\MessageContruct;
use phuongaz\openclan\util\trait\EconomyTrait;
use phuongaz\openclan\util\trait\LoggerTrait;
use phuongaz\openclan\util\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class Create extends BaseSubCommand {
    use EconomyTrait, LoggerTrait;

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->registerArgument(1, new RawStringArgument("tag"));
        $this->registerArgument(2, new BooleanArgument("broadcast", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!$sender instanceof Player) {
            MessageContruct::new( "command-only-player")->sendTo($sender);
            return;
        }
        if(!isset($args["name"])) {
            //send form
            return;
        }
        $name = $args["name"];
        $tag = $args["tag"] ?? Utils::parseTagFromName($name);
        $broadcast = $args["broadcast"] ?? true;

        $availableName = Utils::isAvailableClanName($name);
        if($availableName instanceof MessageContruct) {
            $availableName->sendTo($sender);
            return;
        }
        $availableTag = Utils::isAvailableClanTag($tag);
        if($availableTag instanceof MessageContruct) {
            $availableTag->sendTo($sender);
            return;
        }

        $availableClan = Session::get($name, function($clan) use ($sender, $name, $tag, $broadcast) {
            if($clan !== null) {
                MessageContruct::new("clan-exists", ["name" => $name])->sendTo($sender);
                return;
            }
            $cost = OpenClan::getInstance()->getSettings()->getCreateClanPrice();
            self::getEconomy()->getMoney($sender, function(int $balance) use ($broadcast, $tag, $name, $sender, $cost) {
                if($balance < $cost) {
                    MessageContruct::new("clan-create-not-enough-money", ["money" => $cost])->sendTo($sender);
                    return;
                }
                $clan = Clan::new($name, $tag, $sender->getName());
                $event = new ClanCreationEvent($clan, $sender);
                $event->setCallback(function(ClanCreationEvent $event) use ($broadcast, $tag, $name, $sender, $clan) {
                    if($event->isCancelled()) {
                        MessageContruct::new("clan-create-cancelled")->sendTo($event->getPlayer());
                    }else{
                        self::getEconomy()->takeMoney($sender, OpenClan::getInstance()->getSettings()->getCreateClanPrice());
                        Session::create($clan, function() use ($clan, $sender, $name, $tag, $broadcast) {
                            if($broadcast) {
                                Server::getInstance()->broadcastMessage(
                                    MessageContruct::new("clan-create-broadcast",
                                        ["name" => $name, "tag" => $tag, "player" => $sender->getName()])->parse()
                                );
                            }
                            MessageContruct::new("clan-create-success", ["name" => $clan->getName(), "tag" => $clan->getTag()])->sendTo($sender);
                        });
                        self::log("Clan " . $clan->getName() . " has been created by " . $sender->getName());
                    }
                });
                $event->call();
            });
        });
        if ($availableClan instanceof Clan) {
            MessageContruct::new("clan-exists", ["name" => $name])->sendTo($sender);
        }
    }
}