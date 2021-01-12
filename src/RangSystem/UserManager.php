<?php


namespace RangSystem;


use pocketmine\Player;

class UserManager {

    public static function setGroup(Player $player, $group) {
        $cfg = API::getPlayerConfig($player);
        $cfg->setNested($player->getName() . ".Gruppe", $group);
        $cfg->save();
        $cfg->reload();
        $info = API::getPlayerConfig($player);
        $player->addAttachment(RangSystem::getInstance())->clearPermissions();
        foreach (API::getPermissions($info->getNested($player->getName() . ".Gruppe")) as $perms) {
            $player->addAttachment(RangSystem::getInstance())->setPermission($perms, true);
        }
    }

    public static function getGroup(Player $player) {
        return API::getPlayerConfig($player)->getNested($player->getName() . ".Gruppe");
    }
}