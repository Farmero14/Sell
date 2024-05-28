<?php

declare(strict_types=1);

namespace Farmero\sell;

use pocketmine\plugin\PluginBase;

use Farmero\sell\Commands\SellCommand;
use Farmero\sell\Commands\SellAllCommand;

class Sell extends PluginBase {

    public function onEnable(): void {
        $this->saveResource("items.yml");

        $this->getServer()->getCommandMap()->registerAll("Sell", [
			new SellCommand($this),
			new SellAllCommand($this)
		]);
    }
}
