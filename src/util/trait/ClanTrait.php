<?php

declare(strict_types=1);

namespace phuongaz\openclan\util\trait;

use phuongaz\openclan\clan\Clan;
use phuongaz\openclan\clan\Levels;
use phuongaz\openclan\provider\Session;
use phuongaz\openclan\util\MessageContruct;
use pocketmine\player\Player;

trait ClanTrait {

    private static array $invites = [];

    public static function getClanByPlayer(Player $player) :?Clan {
        return Session::hasPlayerClan($player);
    }

    public static function getClanByTag(string $name) :?Clan {
        return Session::hasTagClan($name);
    }

    public static function getClanByName(string $name) :?Clan {
        return Session::hasNameClan($name);
    }

    /**
     * Get clan data sorted by level and point
     *
     * @param int $page
     * @return Clan[]
     */
    public static function getTopClans(int $page) :array {
        $itemPerPage = 10;
        $offset = ($page - 1) * $itemPerPage;
        $array = Session::getClans();
        usort($array, function (Clan $a, Clan $b) :int{
            if($a->getLevel() === $b->getLevel()){
                return $a->getPoint() <=> $b->getPoint();
            }
            return $a->getLevel() <=> $b->getLevel();
        });
        return array_slice($array, $offset, $itemPerPage);
    }


    /**
     * Get max page of top clan
     *
     * @return int
     */
    public static function getMaxTopClanPage() :int {
        $clans = Session::getClans();
        $count = count($clans);
        $itemPerPage = 10;
        $pages = intval(ceil($count / $itemPerPage));
        return ($pages > 0) ? $pages : 1;
    }

    public static function sendInvite(Player $sender, Player $target, Clan $clan, int $timeout = 30) :bool {
        if(self::hasInvite($target)){
            $currentInvite = self::getInvite($target);
            $time = $currentInvite["time"];
            $timeout = $currentInvite["timeout"];
            $now = time();
            if($now - $time > $timeout) {
                unset(self::$invites[$target->getName()]);
            } else {
                return false;
            }
        }
        $now = time();
        $senderName = $sender->getName();
        $targetName = $target->getName();
        self::$invites[$targetName] = [
            "clan" => $clan,
            "sender" => $senderName,
            "time" => $now,
            "timeout" => $timeout
        ];
        return true;
    }

    public static function acceptInvite(Player $player) :MessageContruct {
        $playerName = $player->getName();
        if(self::hasInvite($player)){
            return MessageContruct::new("no-invite");
        }
        $invite = self::$invites[$playerName];
        $inviteTime = $invite["time"];
        $inviteTimeout = $invite["timeout"];
        $now = time();
        if($now - $inviteTime > $inviteTimeout){
            unset(self::$invites[$playerName]);
            return MessageContruct::new("invite-timeout");
        }
        $clanName = $invite["clan"];
        $clan = self::getClanByName($clanName);
        $clan->addMember($player->getName());
        unset(self::$invites[$playerName]);
        return MessageContruct::new("invite-accept", ["clan" => $clanName]);
    }

    public static function getInvite(Player $player) :?array {
        return self::$invites[$player->getName()] ?? null;
    }

    public static function hasInvite(Player $player) :bool {
        return isset(self::$invites[$player->getName()]);
    }

}