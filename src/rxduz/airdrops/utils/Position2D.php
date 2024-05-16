<?php

namespace rxduz\airdrops\utils;

use pocketmine\world\World;

class Position2D {

    public function __construct(
		public float $x,
		public float $z,
		public World $world
	){}
    
}

?>