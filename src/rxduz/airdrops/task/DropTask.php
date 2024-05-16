<?php

namespace rxduz\airdrops\task;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\ExplodeParticle;
use pocketmine\world\particle\LavaParticle;
use pocketmine\world\Position;
use pocketmine\world\World;
use rxduz\airdrops\Main;
use rxduz\airdrops\utils\Utils;

class DropTask extends Task {

    private int $currentTicks = 0;

    private int $height;

    public function __construct(private Player $player, private Position $position, private int $countdownSeconds)
    {
        $this->height = ($position->getY() + 19);
    }

    public function onRun(): void
    {
        $this->currentTicks++;

        $checkSeconds = $this->currentTicks % 20 === 0;

        $position = $this->position;

        $world = $position->getWorld();

        if(!($this->player instanceof Player and $this->player->isOnline())){
            $this->getHandler()->cancel();

            return;
        }

        if(!$world instanceof World){
            $this->getHandler()->cancel();

            return;
        }

        $pluginConfig = Main::getInstance()->getConfig();

        if($this->countdownSeconds === 0){
            $world->setBlock($position->asVector3(), VanillaBlocks::CHEST());

            Utils::playSound($this->player, $pluginConfig->get("drop-sound"));

            if($pluginConfig->get("lightning-to-drop")){
                Utils::addStrike(Location::fromObject($position->asVector3(), $world));
            }

            if($pluginConfig->get("particle-to-drop")){
                $world->addParticle($position->asVector3(), new BlockBreakParticle(VanillaBlocks::GLASS()), [$this->player]);
            }

            $this->getHandler()->cancel();
        } else if($this->countdownSeconds === 1){
            $vector = new Vector3($position->getX(), $this->height, $position->getZ());

            if($pluginConfig->get("particle-to-countdown")){
                for($i = 0; $i <= 6; $i++){
                    $world->addParticle($vector, new ExplodeParticle(), [$this->player]);

                    $world->addParticle($vector, new LavaParticle(), [$this->player]);
                }
            }

            $this->height--;
        }

        if($checkSeconds){
            $this->countdownSeconds--;

            Utils::playSound($this->player, $pluginConfig->get("countdown-sound"));
        }
    }

}

?>