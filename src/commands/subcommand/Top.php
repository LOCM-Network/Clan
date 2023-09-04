<?php

declare(strict_types=1);

namespace phuongaz\openclan\commands\subcommand;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use phuongaz\openclan\util\MessageContruct;
use phuongaz\openclan\util\trait\ClanTrait;
use pocketmine\command\CommandSender;

class Top extends BaseSubCommand {
    use ClanTrait;

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
       $this->registerArgument(0, new IntegerArgument("page"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $page = $args["page"] ?? 1;
        $clans = self::getTopClans($page);
        $maxPage = self::getMaxTopClanPage();

        $content = MessageContruct::new("top-clan-title");
        array_map(function ($clan) use ($clans, $content) {
            $content->addLine("top-clan-format",
                ["index" => array_search($clan, $clans) + 1, "clan" => $clan->getName(), "level" => $clan->getLevel(), "point" => $clan->getPoint()]
            );
        }, $clans, array_keys($clans));
        $content->addLine("top-clan-footer", ["page" => $page, "maxpage" => $maxPage]);
        $content->sendTo($sender);
        //TODO: send form
    }
}