<?php

declare(strict_types=1);

namespace phuongaz\openclan\util\trait;

use DaPigGuy\libPiggyEconomy\exceptions\MissingProviderDependencyException;
use DaPigGuy\libPiggyEconomy\exceptions\UnknownProviderException;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use phuongaz\openclan\OpenClan;

trait EconomyTrait {

    private static EconomyProvider $economyProvider;

    /**
     * @throws UnknownProviderException
     * @throws MissingProviderDependencyException
     */
    public static function initEconomy(): void{
        libPiggyEconomy::init();
        self::$economyProvider = libPiggyEconomy::getProvider(OpenClan::getInstance()->getConfig()->get("economy"));
    }

    /**
     * @throws UnknownProviderException
     * @throws MissingProviderDependencyException
     */
    public static function getEconomy(): EconomyProvider{
        if (!isset(self::$economyProvider)) {
            self::initEconomy();
        }
        return self::$economyProvider;
    }
}