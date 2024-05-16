<?php

namespace rxduz\airdrops\utils;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use rxduz\airdrops\Main;

class LootGenerator {

    private Config $data;

    private array $items =[];

    public function __construct()
    {
        $this->data = new Config(Main::getInstance()->getDataFolder() . "/items.yml", Config::YAML);

        $stringToItem = StringToItemParser::getInstance();

        foreach($this->data->get("items") as $k => $v){
            for($i = 0; $i < $v["chance"]; $i++){
                $item = $stringToItem->parse($v["id"]) ?? VanillaItems::AIR();

                $item->setCount($v["count"] ?? 1);

                if(isset($v["customname"])){
                    $item->setCustomName(TextFormat::colorize($v["customname"]));
                }

                if(isset($v["enchantments"])){
                    $enchantments = $v["enchantments"];
    
                    foreach($enchantments as $key => $value) {
                        if(is_string($value["id"])){
                            $enchantment = StringToEnchantmentParser::getInstance()->parse($value["id"]);
                        } else {
                            $enchantment = EnchantmentIdMap::getInstance()->fromId($value["id"]);
                        }
                                                    
                        if(isset($value["level"])) $enchantment = new EnchantmentInstance($enchantment, $value["level"]);
    
                        $item->addEnchantment($enchantment);
                    }
                }

                $this->items[] = $item;
            }
        }
    }

    public function getRandomLoot(): array {
        $items = [];

        $count = Main::getInstance()->getConfig()->get("items-to-drop", 5);

        for($i = 0; $i < $count; $i++){
            $items[$i] = $this->items[array_rand($this->items)];
        }

        return $items;
    }

}

?>