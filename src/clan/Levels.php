<?php


declare(strict_types=1);

namespace phuongaz\openclan\clan;

use phuongaz\openclan\OpenClan;

enum Levels {
    case GRASS;
    case STONE;
    case IRON;
    case GOLD;
    case DIAMOND;
    case EMERALD;
    case OBSIDIAN;
    case BEDROCK;
    case LEGENDARY;

    const MAX_LEVEL = 9;

    public function getPoint(): int {
        return match($this) {
            self::GRASS => 0,
            self::STONE => 1000,
            self::IRON => 5000,
            self::GOLD => 10000,
            self::DIAMOND => 50000,
            self::EMERALD => 100000,
            self::OBSIDIAN => 500000,
            self::BEDROCK => 1000000,
            self::LEGENDARY => 5000000
        };
    }

    public function toInt(): int {
        return match($this) {
            self::GRASS => 1,
            self::STONE => 2,
            self::IRON => 3,
            self::GOLD => 4,
            self::DIAMOND => 5,
            self::EMERALD => 6,
            self::OBSIDIAN => 7,
            self::BEDROCK => 8,
            self::LEGENDARY => 9
        };
    }

    public function getMaxMember(): int {
        return match($this) {
            self::GRASS => 10,
            self::STONE => 15,
            self::IRON => 20,
            self::GOLD => 25,
            self::DIAMOND => 30,
            self::EMERALD => 35,
            self::OBSIDIAN => 40,
            self::BEDROCK => 45,
            self::LEGENDARY => 50
        };
    }

    public function toString() :string{
        $language = OpenClan::getInstance()->getLanguage();
        return match($this) {
            self::GRASS => $language->get("grass"),
            self::STONE => $language->get("stone"),
            self::IRON => $language->get("iron"),
            self::GOLD => $language->get("gold"),
            self::DIAMOND => $language->get("diamond"),
            self::EMERALD => $language->get("emerald"),
            self::OBSIDIAN => $language->get("obsidian"),
            self::BEDROCK => $language->get("bedrock"),
            self::LEGENDARY => $language->get("legendary")
        };
    }

    public static function getClanLevelByInt(int $level): Levels {
        return match($level) {
            1 => self::GRASS,
            2 => self::STONE,
            3 => self::IRON,
            4 => self::GOLD,
            5 => self::DIAMOND,
            6 => self::EMERALD,
            7 => self::OBSIDIAN,
            8 => self::BEDROCK,
            9 => self::LEGENDARY
        };
    }
}
