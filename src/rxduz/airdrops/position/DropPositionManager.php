<?php

namespace rxduz\airdrops\position;

use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;
use rxduz\airdrops\Main;
use rxduz\airdrops\utils\Position2D;
use rxduz\airdrops\utils\Utils;

class DropPositionManager {

    private array $positions;

    public function __construct()
    {
        $this->positions = [];
    }

    public function getAirdropBlock(): Item {
        $stringToItem = StringToItemParser::getInstance();

        $pluginConfig = Main::getInstance()->getConfig();

        $item = $stringToItem->parse($pluginConfig->get("airdrop-block")) ?? VanillaItems::AIR();

        if(!$item->isNull()){
            $customName = TextFormat::colorize($pluginConfig->get("airdrop-block-customname"));

            $item->setCustomName($customName);

            $lore = TextFormat::colorize($pluginConfig->get("airdrop-block-lore"));

            $item->setLore([$lore]);

            $item->getNamedTag()->setString("AirDrop", $customName);
        }

        return $item;
    }

    public function isValidItem(Item $item): bool {
        return $item->getNamedTag()->getTag("AirDrop") !== null;
    }

    /**
     * @return Position[]
     */
    public function getPositions(): array {
        return $this->positions;
    }

    public function exists(Position $position): bool {
        return isset($this->positions[Utils::getStringToPosition($position)]);
    }

    public function createPosition(Position $position): bool {
        if(isset($this->positions[Utils::getStringToPosition($position)])){
            return false;
        }

        $this->positions[Utils::getStringToPosition($position)] = $position;

        return true;
    }

    public function removePosition(Position $position): void {
        if(isset($this->positions[Utils::getStringToPosition($position)])) unset($this->positions[Utils::getStringToPosition($position)]);
    }

    public function generatePosition(int $minx, int $maxx, int $minz, int $maxz, World $world): ? Position2D {
        $x = $this->getRandom2DCoordinates($minx, $maxx);

	$z = $this->getRandom2DCoordinates($minz, $maxz);

	return new Position2D($x + 0.5, $z + 0.5, $world);
    }

    public function getRandom2DCoordinates(int $min, int $max): int {
        return mt_rand($min, $max);
    }

}

?>
