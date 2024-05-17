<?php

namespace rxduz\airdrops\utils;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class Utils {

    public static function getStringToPosition(Position $position): string {
        return ($position->getWorld()->getFolderName() . ":" . $position->getFloorX() . ":" . $position->getFloorY() . ":" . $position->getFloorZ());
    }

    public static function getPositionToString(string $string): Position {
        $position = explode(":", $string);

        return new Position($position[1], $position[2], $position[3], Server::getInstance()->getWorldManager()->getWorldByName($position[0]));
    }

    /**
    * Sends a certain sound to a player (from game resources).
    * @param Player $player
    * @param string $soundName		You can find all sounds here: https://minecraft.fandom.com/wiki/Sounds.json/Bedrock_Edition_values
    * @param float $volume			Default: 1.0
    * @param float $pitch 			Default: 1.0
    */
    public static function playSound(Player $player, string $soundName, float $volume = 1.0, float $pitch = 1.0): void {
        $pk = new PlaySoundPacket();
	$pk->soundName = $soundName;
	$pk->x = (int)$player->getLocation()->asVector3()->getX();
	$pk->y = (int)$player->getLocation()->asVector3()->getY();
	$pk->z = (int)$player->getLocation()->asVector3()->getZ();
	$pk->volume = $volume;
	$pk->pitch = $pitch;
	$player->getNetworkSession()->sendDataPacket($pk);
    }

    public static function addStrike(Location $location): void {
        $light = new AddActorPacket();
        $light->type = "minecraft:lightning_bolt";
        $light->actorUniqueId = Entity::nextRuntimeId();
        $light->actorRuntimeId = 1;
        $light->metadata = [];
        $light->motion = null;
        $light->yaw = $location->getYaw();
        $light->pitch = $location->getPitch();
        $light->position = $location->asVector3();
        $light->syncedProperties = new PropertySyncData([], []);

        NetworkBroadcastUtils::broadcastPackets($location->getWorld()->getPlayers(), [$light]);
    }

}

?>
