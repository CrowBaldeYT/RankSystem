<?php

namespace RangSystem;

use http\Message;
use pocketmine\permission\PermissionManager;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class API {

    static $plugin;

    public function __construct(RangSystem $rangSystem) {
        self::$plugin = $rangSystem;
    }
	
	public static function getPlugin() {
		return self::$plugin;
	}
    
    public static function getDataFolder() {
        return self::$plugin->getInstance()->getDataFolder();
    }

    public static function getGroupConfig() {
        return new Config(self::getDataFolder() . "groups.yml", 2);
    }

    public static function getConfig() {
        return new Config(self::getDataFolder() . "config.yml", 2);
    }

    public static function getPlayerConfig(Player $player) {
        return new Config(self::getDataFolder() . "players/" . $player->getName() . ".yml", 2);
    }

    public static function getDefaultGroup() {
        return self::getConfig()->get("DefaultGruppe");
    }

    public static function getPrefix($group) {
        return self::getGroupConfig()->getNested($group . ".chatformat");
    }

    public static function getNameTag($group) {
        return self::getGroupConfig()->getNested($group . ".nametag");
    }

    public static function getAllGroups() {
        return self::getGroupConfig()->getAll(true);
    }

    public static function createDefaultGroups() {
        if (file_exists(self::getDataFolder() . "groups.yml")) {
            return;
        } else {
            $cfg = new Config(self::getDataFolder() . "groups.yml", 2);
            // Spieler
            $cfg->setNested("Spieler.chatformat", "§7§l%gruppe% §r§8| §7%name% §8| §r§f%message%");
            $cfg->setNested("Spieler.nametag", "§7§l%gruppe% §r§8| §7%name%");
            $cfg->setNested("Spieler.permissions", ["pocketmine.command.transferserver"]);

            //Leitung
            $cfg->setNested("Leitung.chatformat", "§c§l%gruppe% §r§8| §c%name% §8| §r§f%message%");
            $cfg->setNested("Leitung.nametag", "§c§l%gruppe% §r§8| §c%name%");
            $cfg->setNested("Leitung.permissions", ["pocketmine.command.op.give", "*"]);

            //Admin
            $cfg->setNested("Admin.chatformat", "§4§l%gruppe% §r§8| §4%name% §8| §r§f%message%");
            $cfg->setNested("Admin.nametag", "§4§l%gruppe% §r§8| §4%name%");
            $cfg->setNested("Admin.permissions", ["pocketmine.command.op.give", "*"]);
            $cfg->save();
        }
    }

    public static function removePermissions(Player $player) {
        $player->addAttachment(RangSystem::getInstance())->clearPermissions();
    }

    public static function getPermissions($group) {
        return self::getGroupConfig()->getNested($group . ".permissions");
    }

    public static function setPermissions(Player $player) {
        $info = self::getPlayerConfig($player);
        self::removePermissions($player);
        $array = [
            "perms" => self::getPermissions($info->getNested($player->getName() . ".Gruppe"))
        ];
        foreach ($array["perms"] as $perms) {
            $player->addAttachment(RangSystem::getInstance())->setPermissions($perms);
        }
    }

    public static function getGroup($group) {
        return API::getGroupConfig()->get($group);
    }

    public static function getUserManager(): UserManager {
        return new UserManager();
    }

    public static function addGroup($name) {
        $info = API::getGroupConfig();
        $info->setNested($name . ".chatformat", "§7§l%gruppe% §r§8| §7%name% §8| §r§f%message%");
        $cfg = $info;
        $cfg->setNested($name . ".nametag", "§7§l%gruppe% §r§8| §7%name%");
        $cfg->setNested($name . ".permissions", []);
        $cfg->save();
        $cfg->reload();
    }

    public static function removeGroup($name) {
        $info = API::getGroupConfig();
        $info->removeNested($name . ".chatformat");
        $cfg = $info;
        $cfg->removeNested($name . ".nametag");
        $cfg->removeNested($name . ".permissions");
        $cfg->remove($name);
        $cfg->save();
        $cfg->reload();
    }
}
