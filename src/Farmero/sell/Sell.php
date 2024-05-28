<?php

declare(strict_types=1);

namespace Farmero\sell;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;

use Farmero\sell\Commands\SellCommand;
use Farmero\sell\Commands\SellAllCommand;

class Sell extends PluginBase {

    public function onEnable(): void {
        $this->saveResource("items.yml");

        $this->getServer()->getCommandMap()->registerAll("Sell", [
	    new SellCommand($this),
	    new SellAllCommand($this)
	    ]);
	    
	if (Server::getInstance()->getPluginManager()->getPlugin("MoneySystem") === null) {
            $this->getLogger()->info("Disabling Sell, MoneySystem not found... Please make sure to have it installed before trying again!");
            Server::getInstance()->getPluginManager()->disablePlugin($this);
            return;
        }
}
