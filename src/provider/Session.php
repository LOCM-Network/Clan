<?php

declare(strict_types=1);

namespace phuongaz\openclan\provider;

use phuongaz\openclan\clan\Clan;
use phuongaz\openclan\OpenClan;
use pocketmine\player\Player;
use SplObjectStorage;

final class Session {

    /**
     * @var SplObjectStorage
     * @phpstan-var SplObjectStorage<Clan>
     */
    private static SplObjectStorage $sessions;

    public static function init() :void{
        self::$sessions = new SplObjectStorage();
    }

    public static function get(string $name, ?callable $callable = null) :?Clan{
        $clan = self::find($name);
        if ($callable !== null) {
            $callable($clan);
        }
        return $clan;
    }

    private static function find(string $name) :?Clan{
        foreach (self::$sessions as $clan) {
            if ($clan->getName() === $name) {
                return $clan;
            }
        }
        return null;
    }

    public static function add(Clan $clan, ?callable $callable = null) :void{
        self::$sessions->attach($clan);
        if($callable !== null){
            $callable($clan);
        }
    }

    public static function remove(Clan $clan, ?callable $callable = null) :void{
        self::$sessions->detach($clan);
        if($callable !== null){
            $callable($clan);
        }
        OpenClan::getInstance()->getProvider()->deleteClan($clan->getName());
    }

    public static function update(Clan $clan, ?callable $callable = null) :void{
        self::$sessions->detach(self::get($clan->getName()));
        self::$sessions->attach($clan);
        if($callable !== null){
            $callable($clan);
        }
        OpenClan::getInstance()->getProvider()->updateClan($clan);
    }

    public static function has(string $name, ?callable $callable = null) :bool{
        $has = self::$sessions->contains(self::get($name));
        if($callable !== null){
            $callable($has);
        }
        return $has;
    }

    public static function create(Clan $clan, ?callable $callable = null) :void{
        self::$sessions->attach($clan);
        if($callable !== null){
            $callable($clan);
        }
        OpenClan::getInstance()->getProvider()->insertClan($clan);
    }

    public static function hasPlayerClan(Player $player) :?Clan {
        /** @phpstan-var  $clan Clan */
        foreach(self::$sessions as $clan){
            if($clan->hasPlayer($player->getName())){
                return $clan;
            }
        }
        return null;
    }

    public static function hasTagClan(string $tag) :?Clan {
        /** @phpstan-var  $clan Clan */
        foreach(self::$sessions as $clan){
            if($clan->getTag() === $tag){
                return $clan;
            }
        }
        return null;
    }

    public static function hasNameClan(string $name) :?Clan {
        /** @phpstan-var  $clan Clan */
        foreach(self::$sessions as $clan){
            if($clan->getName() === $name){
                return $clan;
            }
        }
        return null;
    }

    /**
     * @return Clan[]
     */
    public static function getClans() :array {
        return array_map(function($object) {
            return $object;
        }, iterator_to_array(self::$sessions));
    }

    public static function saveAll() :void{
        /** @phpstan-var $clan Clan */
        foreach(self::$sessions as $clan){
            OpenClan::getInstance()->getProvider()->updateClan($clan);
        }
    }

}