<?php

declare(strict_types=1);

namespace phuongaz\openclan\util;

use phuongaz\openclan\OpenClan;

class Utils {

    /**
     * Generate initials from a string containing multiple words.
     *
     * @param string $name
     * @return string
     */
    public static function parseTagFromName(string $name) :string {
        $maxTagLength = OpenClan::getInstance()->getSettings()->getMaxClanTagLength();

        $words = explode(" ", $name);
        $tag = "";
        foreach ($words as $word) {
            $tag .= substr($word, 0, 1);
            if (strlen($tag) >= $maxTagLength) {
                break;
            }
        }
        return $tag;
    }

    public static function isAvailableClanName(string $name) :bool|MessageContruct {
        $settings = OpenClan::getInstance()->getSettings();
        $maxNameLength = $settings->getMaxClanNameLength();
        $minNameLength = $settings->getMinClanNameLength();
        if(strlen($name) < $minNameLength) {
            return MessageContruct::new("clan-name-too-short", ["min" => $minNameLength]);
        }
        if (strlen($name) > $maxNameLength) {
            return MessageContruct::new("clan-name-too-long", ["max" => $maxNameLength]);
        }
        return true;
    }

    public static function isAvailableClanTag(string $tag) :bool|MessageContruct {
        $settings = OpenClan::getInstance()->getSettings();
        $maxTagLength = $settings->getMaxClanTagLength();
        $minTagLength = 1;
        if (strlen($tag) < $minTagLength) {
            return MessageContruct::new("clan-tag-too-short", ["min" => $minTagLength]);
        }
        if (strlen($tag) > $maxTagLength) {
            return MessageContruct::new("clan-tag-too-long", ["max" => $maxTagLength]);
        }
        return true;
    }
}