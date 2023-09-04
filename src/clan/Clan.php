<?php

declare(strict_types=1);

namespace phuongaz\openclan\clan;

use phuongaz\openclan\util\MessageContruct;
use pocketmine\player\Player;
use pocketmine\Server;

class Clan {

    private string $name;
    private string $tag;
    private string $owner;
    private string $deputy;

    private array $members;
    private string $created;
    private int $level;
    private int $point;


    public function __construct(string $name, string $tag, string $owner, string $deputy, array $members, string $created, int $level, int $point) {
        $this->name = $name;
        $this->tag = $tag;
        $this->owner = $owner;
        $this->deputy = $deputy;
        $this->members = $members;
        $this->created = $created;
        $this->level = $level;
        $this->point = $point;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getTag(): string {
        return $this->tag;
    }

    public function getOwner(): string {
        return $this->owner;
    }

    public function getDeputy(): string {
        return $this->deputy;
    }

    public function getMembers(): array {
        return $this->members;
    }

    public function getCreatedTime(): string {
        return $this->created;
    }

    public function getLevel(): int {
        return $this->level;
    }

    public function getPoint(): int {
        return $this->point;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setTag(string $tag): void {
        $this->tag = $tag;
    }

    public function setOwner(string $owner): void {
        $this->owner = $owner;
    }

    public function setDeputy(string $deputy): void {
        $this->deputy = $deputy;
    }

    public function setMembers(array $members): void {
        $this->members = $members;
    }

    public function setCreatedTime(string $createdTime): void {
        $this->created = $createdTime;
    }

    public function setLevel(int $level): void {
        $this->level = $level;
    }

    public function setPoint(int|float $point): void {
        $this->point = $point;
    }

    public function addMember(string $member): void {
        $this->members[] = $member;
    }

    public function removeMember(string $member): void {
        $key = array_search($member, $this->members);
        if($key !== false) {
            unset($this->members[$key]);
        }
    }

    public function isMember(string $member): bool {
        return in_array($member, $this->members);
    }

    public function isOwner(string $owner): bool {
        return $this->owner === $owner;
    }

    public function isDeputy(string $deputy): bool {
        return $this->deputy === $deputy;
    }

    public function getClanLevel(): Levels {
        return Levels::getClanLevelByInt($this->level);
    }


    public function isFull(): bool {
        return count($this->members) >= $this->getClanLevel()->getMaxMember();
    }

    public function isMaxLevel(): bool {
        return $this->level >= Levels::MAX_LEVEL;
    }

    public function getNextPointNeed(): int {
        $need = Levels::getClanLevelByInt($this->level + 1)?->getPoint();
        if ($need === null) {
            return -1;
        }
        return $need;
    }

    public function calculatePointNeed() :int {
        $point = $this->getNextPointNeed();
        return $this->getNextPointNeed() - $point;
    }

    public function canUpgradeClan() :bool {
        return $this->point >= $this->getNextPointNeed();
    }

    public function upgradeClan() :?MessageContruct {
        if ($this->canUpgradeClan()) {
            $this->level++;
            $this->point -= $this->getNextPointNeed();
            $next = Levels::getClanLevelByInt($this->level);
            return MessageContruct::new("clan-upgrade-success-sender", ["level" => $next->toString()]);
        }
        return MessageContruct::new("clan-upgrade-fail", ["point" => $this->getPoint(), "need" => $this->calculatePointNeed()]);
    }

    public function hasPlayer(string $player) :bool {
        return in_array($player, $this->members);
    }

    public function broadcastMessage(MessageContruct $message) :void {
        foreach ($this->members as $member) {
            $player = Server::getInstance()->getPlayerExact($member);
            if ($player instanceof Player) {
                $message->sendTo($player);
            }
        }
    }

    public function getClanInfo() :MessageContruct {
        $level = $this->getClanLevel()->toString();
        $lines = MessageContruct::new("clan-info-title", ["name" => $this->name, "tag" => $this->tag]);
        $lines->addLine("clan-info-leaders", ["owner" => $this->owner, "deputy" => $this->deputy]);
        $lines->addLine("clan-info-content",
            [
                "level" => $level,
                "point" => $this->point,
                "members" => implode(", ", $this->members)
            ]);
        return $lines;
    }

    public function toArray() :array {
        return [
            "name" => $this->name,
            "tag" => $this->tag,
            "owner" => $this->owner,
            "deputy" => $this->deputy,
            "members" => json_encode($this->members),
            "created" => $this->created,
            "level" => $this->level,
            "point" => $this->point
        ];
    }

    public static function fromArray(array $data) :Clan {
        return new self(
            $data["name"],
            $data["tag"],
            $data["owner"],
            $data["deputy"],
            json_decode($data["members"], true),
            $data["created"],
            $data["level"],
            $data["point"]
        );
    }

    public static function new(string $name, string $tag, string $owner): Clan {
        $time = date("H:i:s d/m/Y");
        return new self($name, $tag, $owner, "", [], $time, 1, 0);
    }

}