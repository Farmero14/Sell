<?php

declare(strict_types=1);

namespace Farmero\sell\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;
use pocketmine\player\Player;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;

use Farmero\sell\Sell;

use Farmero\moneysystem\MoneySystem;

class SellAllCommand extends Command implements PluginOwned {

    private $plugin;
    private $itemsConfig;
    private $money;

    public function __construct(Sell $plugin) {
        parent::__construct("sellall", "Sell all items in your inventory");
        $this->plugin = $plugin;
        $this->setPermission("sell.cmd.sellall");
        $this->itemsConfig = new Config($this->plugin->getDataFolder() . "items.yml", Config::YAML);
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used by players!");
            return true;
        }

        $inventory = $sender->getInventory();
        $sellableItems = $this->itemsConfig->get("items", []);
        $totalEarnings = 0;

        foreach ($inventory->getContents() as $slot => $itemInHand) {
            foreach ($sellableItems as $itemData) {
                if (is_array($itemData) && isset($itemData["id"]) && is_string($itemData["id"])) {
                    $itemName = $itemData["id"];
                    $parsedItem = StringToItemParser::getInstance()->parse($itemName);

                    if ($itemInHand->equals($parsedItem)) {
                        $itemPrice = $this->getItemPrice($itemName);

                        if ($itemPrice > 0) {
                            $totalPrice = $itemPrice * $itemInHand->getCount();
                            $totalEarnings += $totalPrice;
                            $inventory->setItem($slot, VanillaItems::AIR());
                        }
                    }
                }
            }
        }

        if ($totalEarnings > 0) {
            $this->money = MoneySystem::getInstance()->getMoneyManager();
            $this->money->addMoney($sender, $totalEarnings);
            $sender->sendMessage("§l§a(§f!§a) §r§fYou have sold all the sellable items in your inventory for §e$" . $totalEarnings . "§f!");
        } else {
            $sender->sendMessage("§l§c(§f!§c) §r§fNo sellable items to sell in your inventory!");
        }
        return true;
    }

    private function getItemPrice(string $itemName): int {
        $sellableItems = $this->itemsConfig->get("items", []);

        foreach ($sellableItems as $itemData) {
            if (is_array($itemData) && isset($itemData["id"]) && is_string($itemData["id"]) && $itemData["id"] === $itemName) {
                if (isset($itemData["price"]) && is_numeric($itemData["price"])) {
                    return (int)$itemData["price"];
                }
            }
        }
        return 0;
    }
}
