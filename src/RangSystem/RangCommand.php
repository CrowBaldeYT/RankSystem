<?php


namespace RangSystem;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\Server;
use jojoe77777\FormAPI\SimpleForm;

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
					case "setgroup":
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
						if(!$sender instanceof Player){
							$ranks = [];
							foreach (API::getAllGroups() as $group) {
								$ranks[] = $group;
							}

							$sender->sendMessage(RangSystem::getPrefix() . "§aRänge: §r" . implode("§a,§r ", $ranks));
						} else {
							$this->groupsUI($sender);
						}
						break;

					case "reload":
						if(!$sender instanceof Player){
							API::getGroupConfig()->reload();
							foreach (RangSystem::getInstance()->getServer()->getOnlinePlayers() as $op) {
								API::getPlayerConfig($op)->reload();
								API::getUserManager()->setGroup($op, API::getUserManager()->getGroup($op));
							}

							$sender->sendMessage(RangSystem::getPrefix() . "§aReload erfolgreich ausgeführt.");
						} else {
							$this->reloadUI($sender);
						}
						break;

					case "addgroup":
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

					case "removegroup":
						if(isset($args[1])) {
							if (!API::getGroup($args[1])) {
								$sender->sendMessage(RangSystem::getPrefix() . "§cDiese Gruppe existiert nicht!");
								return true;
							} else {
								API::removeGroup($args[1]);
								$sender->sendMessage(RangSystem::getPrefix() . "§aDie Gruppe §r" . $args[1] . " §awurde erfolgreich gelöscht.");
							}
						} else {
							$sender->sendMessage(RangSystem::getPrefix() . "§c/rang removegroup <name>");
						}
				}
			} else {
				$sender->sendMessage(RangSystem::getPrefix() . "§c/rang setgroup <player> <gruppe>");
				$sender->sendMessage(RangSystem::getPrefix() . "§c/rang groups");
				$sender->sendMessage(RangSystem::getPrefix() . "§c/rang addgroup <name>");
				$sender->sendMessage(RangSystem::getPrefix() . "§c/rang removegroup <gruppe>");
				$sender->sendMessage(RangSystem::getPrefix() . "§c/rang reload");

			}
		} else {
			$sender->sendMessage(RangSystem::getPrefix() . "§cDafür hast du keine Rechte!");
		}
		return true;
	}

	public function groupsUI(Player $player){
		$form = new SimpleForm(function (Player $player, int $data = null) {
			$result = $data;
			if($result === null){
				return true;
			}
			switch($result) {
				case 0:
					$ranks = [];
					foreach (API::getAllGroups() as $group) {
						$ranks[] = $group;
					}

					$player->sendMessage(RangSystem::getPrefix() . "§aRänge: §r" . implode("§a,§r ", $ranks));
					break;
			}
		});
		$form->setTitle(RangSystem::getPrefix());
		$form->setContent("§aZeige dir alle Ränge an.");
		$form->addButton("§7Senden");
		$form->sendToPlayer($player);
		return $form;
	}

	public function reloadUI(Player $player){
		$form = new SimpleForm(function (Player $player, int $data = null) {
			$result = $data;
			if($result === null){
				return true;
			}
			switch($result) {
				case 0:
					API::getGroupConfig()->reload();
					foreach (RangSystem::getInstance()->getServer()->getOnlinePlayers() as $op) {
						API::getPlayerConfig($op)->reload();
						API::getUserManager()->setGroup($op, API::getUserManager()->getGroup($op));
					}

					$player->sendMessage(RangSystem::getPrefix() . "§aReload erfolgreich ausgeführt.");
					break;
			}
		});
		$form->setTitle(RangSystem::getPrefix());
		$form->setContent("§aReloade das Plugin.");
		$form->addButton("§7Senden");
		$form->sendToPlayer($player);
		return $form;
	}
}
