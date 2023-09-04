<?php

declare(strict_types=1);

namespace phuongaz\openclan\util;

use pocketmine\utils\Config;

class Settings {

    private int $maxClanNameLength;
    private int $minClanNameLength;
    private int $maxClanTagLength;
    private int $createClanPrice;

    private int $saveInterval;


    private string $language;

    private string $messageFormat;

    public function __construct(Config $config) {
        $this->maxClanNameLength = $config->get("max-clan-name-length", 15);
        $this->minClanNameLength = $config->get("min-clan-name-length", 3);
        $this->maxClanTagLength = $config->get("max-clan-tag-length", 3);
        $this->createClanPrice = $config->get("create-clan-price", 10000);
        $this->saveInterval = $config->get("save-interval", 60);
        $this->language = $config->get("language", "eng");
        $this->messageFormat = $config->get("message-format", "OPENCLAN -> {message}");

    }

    public function getMaxClanNameLength(): int {
        return $this->maxClanNameLength;
    }

    public function getMinClanNameLength(): int {
        return $this->minClanNameLength;
    }

    public function getMaxClanTagLength(): int {
        return $this->maxClanTagLength;
    }

    public function getCreateClanPrice(): int {
        return $this->createClanPrice;
    }

    public function getLanguage(): string {
        return $this->language;
    }

    public function getMessageFormat(): string {
        return $this->messageFormat;
    }

    public function getSaveInterval(): int {
        return $this->saveInterval;
    }
}