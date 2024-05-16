<?php

namespace rxduz\airdrops\task;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;
use rxduz\airdrops\Main;

class MeteoriteTask extends Task {

    public function onRun(): void
    {
        $this->drop();
    }

    public function drop(): void {
        $pluginConfig = Main::getInstance()->getConfig()->get("random-position");

        $configWorld = Server::getInstance()->getWorldManager()->getWorldByName($pluginConfig["world"]);

        if(!$configWorld instanceof World){
            return;
        }

        $position = Main::getInstance()->getDropPositionManager()->generatePosition($pluginConfig["minx"], $pluginConfig["maxx"], $pluginConfig["minz"], $pluginConfig["maxz"], $configWorld);

        $x = (int) floor($position->x);

		$z = (int) floor($position->z);

        $world = $position->world;

        $world->loadChunk($x >> Chunk::COORD_BIT_SIZE, $z >> Chunk::COORD_BIT_SIZE);

        $position->world->orderChunkPopulation($x >> 4, $z >> 4, null)->onCompletion(function() use($world, $position, $x, $z) : void {
            if($world !== null){
                $pos = new Vector3($position->x, $world->getHighestBlockAt($x, $z) + 1.0, $position->z);

                $spawn = $world->getSafeSpawn($pos);

                if(Main::getInstance()->getDropPositionManager()->createPosition($spawn)){
                    $world->setBlock($spawn->asVector3(), VanillaBlocks::CHEST());

                    $coordinatesStr = ($world->getFolderName() . " X: " . (string)round($pos->getX()) . " Y: " . (string)round($pos->getY()) . " Z: " . (string)round($pos->getZ()));

                    Server::getInstance()->broadcastMessage(Main::PREFIX . TextFormat::colorize(str_replace("{COORDINATES}", $coordinatesStr, Main::getInstance()->getConfig()->get("meteorite-broadcast-message"))));
                }
            }
        }, function() : void{
            // FAILED
        });
    }

}

?>