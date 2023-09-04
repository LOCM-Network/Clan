<?php

declare(strict_types=1);

namespace phuongaz\openclan;

use DaPigGuy\libPiggyEconomy\exceptions\MissingProviderDependencyException;
use DaPigGuy\libPiggyEconomy\exceptions\UnknownProviderException;
use phuongaz\openclan\commands\ClanCommand;
use phuongaz\openclan\provider\Session;
use phuongaz\openclan\provider\SQLProvider;
use phuongaz\openclan\task\SaveTask;
use phuongaz\openclan\util\Language;
use phuongaz\openclan\util\Settings;
use phuongaz\openclan\util\trait\EconomyTrait;
use phuongaz\openclan\util\trait\LoggerTrait;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\libasynql;

class OpenClan extends PluginBase {
    use SingletonTrait;
    use LoggerTrait, EconomyTrait;

    private Language $language;
    private SQLProvider $provider;
    private Settings $settings;


    public function onLoad(): void {
        self::setInstance($this);
    }

    /**
     * @throws UnknownProviderException
     * @throws MissingProviderDependencyException
     */
    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->saveResource("language". DIRECTORY_SEPARATOR . "eng.ini");
        $connector = libasynql::create($this, $this->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
        ]);
        $this->provider = new SQLProvider($connector);
        $this->language = new Language($this->getConfig()->get("language"), $this->getDataFolder() . "language". DIRECTORY_SEPARATOR);
        $this->settings = new Settings($this->getConfig());
        $this->getServer()->getCommandMap()->register("openclan", new ClanCommand($this, "openclan", "OpenClan command", ["oc"]));
        $this->getScheduler()->scheduleDelayedRepeatingTask(new SaveTask(), 60 * 20, $this->getSettings()->getSaveInterval() * 20 * 60);
        self::initEconomy();
        self::initLogger();
        Session::init();
    }

    public function getProvider() :SQLProvider{
        return $this->provider;
    }

    public function getLanguage() : Language{
        return $this->language;
    }

    public function getSettings() : Settings{
        return $this->settings;
    }

    public function onDisable(): void {
        Session::saveAll();
        $this->provider->getConnector()->waitAll();
    }

}