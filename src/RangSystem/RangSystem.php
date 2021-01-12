<?php


namespace RangSystem;

use LobbySystem\FlyCommand;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class RangSystem extends PluginBase implements Listener{

    public static $instance;

    public function onEnable(): void {
        @mkdir(API::getDataFolder() . "players");
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register("rang", new RangCommand("rang.command", "rang", "Rang Command", "/rang"));
        $this->saveResource("config.yml");
        API::createDefaultGroups();
    }

    public static function getInstance():  self {
        return self::$instance;
    }

    public static function getPrefix() {
        return API::getConfig()->getNested("Prefix");
    }

    public static function getUsrMgr(): UserManager {
        return new UserManager();
    }
}