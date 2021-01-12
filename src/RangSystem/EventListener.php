<?php


namespace RangSystem;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\utils\Config;

class EventListener implements Listener {
    public function onPlayJoin(PlayerLoginEvent $event) {
        if(!file_exists(API::getDataFolder() . "players/" . $event->getPlayer()->getName() . ".yml")) {
            $cfg = API::getPlayerConfig($event->getPlayer());
            $cfg->setNested($event->getPlayer()->getName() . ".Name", $event->getPlayer()->getName());
            $cfg->setNested($event->getPlayer()->getName() . ".Gruppe", API::getDefaultGroup());
            $cfg->setNested($event->getPlayer()->getName() . ".IP", $event->getPlayer()->getAddress());
            $cfg->setNested($event->getPlayer()->getName() . ".UUID", $event->getPlayer()->getUniqueId()->toString());
            $cfg->save();
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $info = API::getPlayerConfig($player);
        $nametag = API::getGroupConfig()->getNested($info->getNested($player->getName() . ".Gruppe") . ".nametag");
        $nametag = str_replace("%gruppe%", $info->getNested($player->getName() . ".Gruppe"), $nametag);
        $nametag = str_replace("%name%", $info->getNested($player->getName() . ".Name"), $nametag);
        $player->setNameTag($nametag);
        $player->setDisplayName($nametag);
        foreach (API::getPermissions($info->getNested($player->getName() . ".Gruppe")) as $perms) {
            $player->addAttachment(RangSystem::getInstance())->setPermission($perms, true);
        }
    }

    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
            $event->getPlayer()->addAttachment(RangSystem::getInstance())->clearPermissions();
    }

    public function getChat(PlayerChatEvent $chatEvent) {
        $msg = $chatEvent;
        $message = $chatEvent->getMessage();
        $player = $chatEvent->getPlayer();
        $info = API::getPlayerConfig($player);
        $format = API::getGroupConfig()->getNested($info->getNested($player->getName() . ".Gruppe") . ".chatformat");
        $format = str_replace("%gruppe%", $info->getNested($player->getName() . ".Gruppe"), $format);
        $format = str_replace("%name%", $info->getNested($player->getName() . ".Name"), $format);
        $format = str_replace("%message%", $message, $format);
        $chatEvent->setFormat($format);
    }
}