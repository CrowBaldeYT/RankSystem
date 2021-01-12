<?php


namespace RangSystem;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;

class RangCommand extends Command {

    public function __construct(string $perm, string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission($perm);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender->hasPermission($this->getPermission())) {
            if (isset($args[0])) {
                switch ($args[0]) {
                    case "setgruppe":
                        if (isset($args[1])) {
                            $player = RangSystem::getInstance()->getServer()->getPlayer($args[1]);
                            if (!file_exists(API::getDataFolder() . "players/" . $args[1] . ".yml") and !$player) {
                                $sender->sendMessage(RangSystem::getPrefix() . "§cDieser Spieler existiert nicht oder ist Offline!");
                                return true;
                            } else {
                                if (isset($args[2])) {
                                    $cfg = new Config(API::getDataFolder() . "groups.yml", 2);
                                    if (!$cfg->get($args[2])) {
                                        $sender->sendMessage(RangSystem::getPrefix() . "§cDiese Gruppe existiert nicht!");
                                        return true;
                                    } else {
                                        if (API::getUserManager()->getGroup($player) == $args[2]) {
                                            $sender->sendMessage(RangSystem::getPrefix() . "§cDer Spieler hat die Gruppe bereits!");
                                            return true;
                                        } else {
                                            API::getUserManager()->setGroup($player, $args[2]);
                                            $sender->sendMessage(RangSystem::getPrefix() . "§aDu hast dem Spieler §e" . $player->getName() . " §adie Gruppe §r" . $args[2] . " §azugewiesen.");
                                            $player->kick("§aDu wurdest der Gruppe §r" . $args[2] . " §avon §e" . $sender->getName() . " §azugewiesen." . "\n§aRejoine um dein Nametag + Rechte zu erhalten!", false);
                                        }
                                    }
                                } else {
                                    $sender->sendMessage(RangSystem::getPrefix() . "§c/rang setgruppe <player> <gruppe>");
                                }
                            }
                        } else {
                            $sender->sendMessage(RangSystem::getPrefix() . "§c/rang setgruppe <player> <gruppe>");
                        }
                        break;
                    case "groups":

                        $ranks = [];
                        foreach (API::getAllGroups() as $group) {
                            $ranks[] = $group;
                        }

                        $sender->sendMessage(RangSystem::getPrefix() . "§aRänge: §r" . implode("§a,§r ", $ranks));
                        return true;
                        break;

                    case "addgruppe":
                        if(isset($args[1])) {
                            if (API::getGroup($args[1])) {
                                $sender->sendMessage(RangSystem::getPrefix() . "§cDiese Gruppe existiert bereits!");
                                return true;
                            } else {
                                API::addGroup($args[1]);
                                $sender->sendMessage(RangSystem::getPrefix() . "§aDie Gruppe §r" . $args[1] . " §awurde erfolgreich erstellt.");
                            }
                        } else {
                            $sender->sendMessage(RangSystem::getPrefix() . "§c/rang addgroup <name>");
                        }
                        break;
                    case "removegruppe":
                        if(isset($args[1])) {
                            if (!API::getGroup($args[1])) {
                                $sender->sendMessage(RangSystem::getPrefix() . "§cDiese Gruppe existiert nicht!");
                                return true;
                            } else {
                                API::removeGroup($args[1]);
                                $sender->sendMessage(RangSystem::getPrefix() . "§aDie Gruppe §r" . $args[1] . " §awurde erfolgreich gelöscht.");
                            }
                        } else {
                            $sender->sendMessage(RangSystem::getPrefix() . "§c/rang addgroup <name>");
                        }
                }
            } else{
                $sender->sendMessage(RangSystem::getPrefix() . "§c/rang setgruppe <player> <gruppe>");
                $sender->sendMessage(RangSystem::getPrefix() . "§c/rang groups");
                $sender->sendMessage(RangSystem::getPrefix() . "§c/rang addgruppe <name>");
                $sender->sendMessage(RangSystem::getPrefix() . "§c/rang removegruppe <gruppe>");
                $sender->sendMessage(RangSystem::getPrefix() . "§c/rang reload");

            }
        } else {
            $sender->sendMessage(RangSystem::getPrefix() . "§cDafür hast du keine Rechte!");
        }
        return true;
    }
}
