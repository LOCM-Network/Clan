<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands;

use CortexPE\Commando\BaseCommand;
use phuongaz\openclan\commands\subcommand\Accept;
use phuongaz\openclan\commands\subcommand\Create;
use phuongaz\openclan\commands\subcommand\Delete;
use phuongaz\openclan\commands\subcommand\Help;
use phuongaz\openclan\commands\subcommand\Info;
use phuongaz\openclan\commands\subcommand\Invite;
use phuongaz\openclan\commands\subcommand\Kick;
use phuongaz\openclan\commands\subcommand\Top;
use phuongaz\openclan\commands\subcommand\Upgrade;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;

class ClanCommand extends BaseCommand {

    private string $permission = "clan.command";

    public function __construct(Plugin $plugin, string $name, Translatable|string $description = "", array $aliases = []) {
        parent::__construct($plugin, $name, $description, $aliases);
        $this->setPermission($this->permission);
    }

    protected function prepare(): void {
        $this->registerSubCommand(new Create("create", "Create a clan"));
        $this->registerSubCommand(new Delete("delete", "Delete a clan"));
        $this->registerSubCommand(new Top("top", "Top clans"));
        $this->registerSubCommand(new Help("help", "Help"));
        $this->registerSubCommand(new Upgrade("upgrade", "Upgrade clan"));
        $this->registerSubCommand(new Invite("invite", "Invite a player to clan"));
        $this->registerSubCommand(new Accept("accept", "Accept a clan invite"));
        $this->registerSubCommand(new Kick("kick", "Kick a player from clan"));
        $this->registerSubCommand(new Info("info", "Clan info"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        // TODO: Implement onRun() method.
    }

    public function getPermission() : string{
        return $this->permission;
    }
}