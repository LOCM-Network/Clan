<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands\subcommand;

use CortexPE\Commando\BaseSubCommand;
use phuongaz\openclan\util\MessageContruct;
use pocketmine\command\CommandSender;

class Help extends BaseSubCommand {

    const HELP_LIST = [
        "create" => "Create a clan",
        "delete" => "Delete a clan",
        "top" => "Top clans",
        "upgrade" => "Upgrade clan",
        "help" => "list clan command",
        "invite" => "Invite a player to clan",
        "accept" => "Accept a clan invite",
        "kick" => "Kick a player from clan",
    ];

    protected function prepare(): void {
        // TODO: Implement prepare() method.
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $content = MessageContruct::new("help-list-title");
        foreach (self::HELP_LIST as $command => $description) {
            $content->addLine("help-list-content", ["command" => $command, "description" => $description]);
        }
        $content->sendTo($sender);
    }
}