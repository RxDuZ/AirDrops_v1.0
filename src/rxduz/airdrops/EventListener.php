<?php

namespace rxduz\airdrops;

use pocketmine\block\Block;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use rxduz\airdrops\task\DropTask;

class EventListener implements Listener {

    /**
     * @param BlockPlaceEvent $ev
     * 
     * @priority HIGHEST
     */
    public function onPlace(BlockPlaceEvent $ev): void {
        if($ev->isCancelled()) return;

        $player = $ev->getPlayer();

        $item = $player->getInventory()->getItemInHand();

        $dropPositionManager = Main::getInstance()->getDropPositionManager();

        if($dropPositionManager->isValidItem($item)){
            $pluginConfig = Main::getInstance()->getConfig();

            foreach($ev->getTransaction()->getBlocks() as [$x, $y, $z, $block]){
                if($block instanceof Block){
                    if($dropPositionManager->createPosition($block->getPosition())){
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DropTask($player, $block->getPosition(), $pluginConfig->get("time-to-drop")), 1);

                        $player->getInventory()->removeItem($item->setCount(1));
                    }
                }
            }

            $ev->cancel();
        }
    }

    /**
     * @param BlockBreakEvent $ev
     * 
     * @priority HIGHEST
     */
    public function onBreak(BlockBreakEvent $ev): void {
        if($ev->isCancelled()) return;

        if(Main::getInstance()->getDropPositionManager()->exists($ev->getBlock()->getPosition())){
            $ev->cancel();
        }
    }

    /**
     * @param InventoryOpenEvent $ev
     */
    public function onOpenInventory(InventoryOpenEvent $ev): void {
        $inv = $ev->getInventory();

        if($inv instanceof ChestInventory){
            if(Main::getInstance()->getDropPositionManager()->exists($inv->getHolder())){
                $inv->setContents(Main::getInstance()->getLootGenerator()->getRandomLoot());

                Main::getInstance()->getDropPositionManager()->removePosition($inv->getHolder());
            }
        }
    }

}

?>