<?php

namespace rxduz\airdrops;

use pocketmine\block\VanillaBlocks;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use rxduz\airdrops\command\AirDropCommand;
use rxduz\airdrops\position\DropPositionManager;
use rxduz\airdrops\task\MeteoriteTask;
use rxduz\airdrops\utils\LootGenerator;

class Main extends PluginBase {

    public const PREFIX = TextFormat::BOLD . TextFormat::DARK_GRAY . "(" . TextFormat::RED . "AirDrops" . TextFormat::DARK_GRAY . ")" . TextFormat::RESET . " ";

    use SingletonTrait;

    private DropPositionManager $dropPositionManager;

    private LootGenerator $lootGenerator;

    protected function onEnable(): void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();
        $this->saveResource("/items.yml");

        $this->dropPositionManager = new DropPositionManager();

        $this->lootGenerator = new LootGenerator();

        $this->getServer()->getCommandMap()->register("AirDropCommand", new AirDropCommand());

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        if($this->getConfig()->get("meteorite")){
            $this->getScheduler()->scheduleRepeatingTask(new MeteoriteTask(), (20 * $this->getConfig()->get("meteorite-time")));
        }
    }

    protected function onDisable(): void
    {
        foreach($this->dropPositionManager?->getPositions() as $position){
            if($position->getWorld()->getBlock($position->asVector3())->getTypeId() === VanillaBlocks::CHEST()->getTypeId()){
                $position->getWorld()->setBlock($position->asVector3(), VanillaBlocks::AIR());
            }
        }
    }

    public function getDropPositionManager(): DropPositionManager {
        return $this->dropPositionManager;
    }

    public function getLootGenerator(): LootGenerator {
        return $this->lootGenerator;
    }

}

?>