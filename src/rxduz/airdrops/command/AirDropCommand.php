<?php

namespace rxduz\airdrops\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use rxduz\airdrops\Main;

class AirDropCommand extends Command {

    public function __construct()
    {
        parent::__construct("airdrop", "AirDrop Command by iRxDuZ", null, []);

        $this->setPermission("airdrop.command.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$this->testPermission($sender)){
            return;
        }

        if(!isset($args[0])){
            $sender->sendMessage(TextFormat::RED . "Use /" . $commandLabel . " help");
            return;
        }

        switch($args[0]){
            case "give":
                $player = $sender;

                if(isset($args[1])){
                    $player = Server::getInstance()->getPlayerByPrefix($args[1]);
                }

                if(!$player instanceof Player){
                    $sender->sendMessage(TextFormat::RED . "Player is not online");

                    return;
                }

                $count = 1;

                if(isset($args[2]) and is_numeric($args[2]) and $args[2] > 0){
                    $count = intval($args[2]);
                }

                $item = Main::getInstance()->getDropPositionManager()->getAirdropBlock()?->setCount($count);

                $player->getInventory()->addItem($item);

                $sender->sendMessage(Main::PREFIX . TextFormat::GREEN . $player->getName() . " received x" . strval($count) . " AirDrop" . ($count > 1 ? "s" : ""));
                break;
            case "giveall":
                $count = 1;

                if(isset($args[1]) and is_numeric($args[1]) and $args[1] > 0){
                    $count = intval($args[1]);
                }

                $item = Main::getInstance()->getDropPositionManager()->getAirdropBlock()?->setCount($count);

                foreach(Server::getInstance()->getOnlinePlayers() as $player){
                    $player->getInventory()->addItem($item);

                    $player->sendMessage(Main::PREFIX . "You received x" . strval($count) . " AirDrop" . ($count > 1 ? "s" : ""));
                }

                $sender->sendMessage(Main::PREFIX . TextFormat::GREEN . "Online players received x" . strval($count) . " AirDrop" . ($count > 1 ? "s" : ""));
                break;
            case "map":
                $positions = Main::getInstance()->getDropPositionManager()->getPositions();

                if(empty($positions)){
                    $sender->sendMessage(TextFormat::RED . "There are no drops on the map");

                    return;
                }

                foreach(array_values($positions) as $position){
                    $sender->sendMessage(TextFormat::YELLOW . "WORLD: " . TextFormat::WHITE . $position->getWorld()->getFolderName() . TextFormat::YELLOW . " X: " . TextFormat::WHITE . $position->getX() . TextFormat::YELLOW . " Y: " . TextFormat::WHITE . $position->getY() . TextFormat::YELLOW . " Z: " . TextFormat::WHITE . $position->getZ());
                }
                break;
            case "help":
            default:
                $sender->sendMessage(Main::PREFIX . TextFormat::GOLD . "Commands:");
                $sender->sendMessage(TextFormat::GOLD . "Use /" . $commandLabel . " give <player> <amount> " . TextFormat::WHITE . "To give Airdrop a player.");
                $sender->sendMessage(TextFormat::GOLD . "Use /" . $commandLabel . " giveall <amount> " . TextFormat::WHITE . "To give Airdrop a online players.");
                $sender->sendMessage(TextFormat::GOLD . "Use /" . $commandLabel . " map " . TextFormat::WHITE . "To see the drops on the map.");
                break;
        }
    }

}

?>