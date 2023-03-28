<?php
namespace SansShop\Xcnox21;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\ItemFactory;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\world\sound\PopSound;
use onebone\economyapi\EconomyAPI;
use pocketmine\world\sound\DoorCrashSound;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;

class Main extends PluginBase implements Listener{
	
	public function onEnable(): void{
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$this->chest = InvMenu::create(InvMenu::TYPE_CHEST);
		$this->block = InvMenu::create(InvMenu::TYPE_CHEST);
		$this->mine = InvMenu::create(InvMenu::TYPE_CHEST);
		$this->tool = InvMenu::create(InvMenu::TYPE_CHEST);
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        switch($cmd->getName()){                    
            case "sansshop":
                if($sender instanceof Player){
                    if(!isset($args[0])){
                    	$sender->sendMessage("§eKetik /sansshop dan pilih yg ingin di beli §7[Potion\Mine\Enchant\Tool\Eat\Block\Sell]");
                        return true;
                    }
                    $arg = array_shift($args);
                    switch($arg){
                    	case "potion":
                            $this->openPotionShop($sender);
                        break;
                        case "mine":
                            $this->openMineShop($sender);
                        break;
                        case "enchant":
                            $this->openEnchantShop($sender);
                        break;
                        case "tool":
                            $this->openToolShop($sender);
                        break;
                        case "eat":
                            $this->openEatShop($sender);
                        break;
                        case "block":
                            $this->openBlockShop($sender);
                        break;
                        case "sell":
                            $this->openSellShop($sender);
                        break;
                    }
                }
            break;
        }
        return true;
    }
    
    public function openBlockShop($sender){
    	$this->block->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
    		$sender = $transaction->getPlayer();
  		  $item = $transaction->getItemClicked();
  		  $this->openBlockShop2($sender, $item);
			return $transaction->discard();
		});
        $this->block->setName("BlockShop");
	    $inventory = $this->block->getInventory();
	    $inventory->setItem(0, ItemFactory::getInstance()->get(5, 0, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(1, ItemFactory::getInstance()->get(5, 1, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(2, ItemFactory::getInstance()->get(5, 2, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(3, ItemFactory::getInstance()->get(5, 3, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(4, ItemFactory::getInstance()->get(5, 4, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(5, ItemFactory::getInstance()->get(5, 5, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(18, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(19, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(20, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(21, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(22, ItemFactory::getInstance()->get(381, 0, 1)->setCustomName("§l§cEXIT"));
        $inventory->setItem(23, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(24, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(25, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(26, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
	    $this->block->send($sender);
	}
	
	public function openBlockShop2(Player $sender, Item $item){
		$hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->block->getInventory();
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $sender->removeCurrentWindow();
        }
        if($item->getId() == 5 && $item->getMeta() == 0){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(5, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 5 && $item->getMeta() == 1){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(5, 1, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 5 && $item->getMeta() == 2){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(5, 2, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 5 && $item->getMeta() == 3){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(5, 3, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 5 && $item->getMeta() == 4){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(5, 4, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 5 && $item->getMeta() == 5){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(5, 5, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
    }
    
    public function openMineShop($sender){
    	$this->mine->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
    		$sender = $transaction->getPlayer();
  		  $item = $transaction->getItemClicked();
  		  $this->openMineShop2($sender, $item);
			return $transaction->discard();
		});
        $this->mine->setName("MineShop");
	    $inventory = $this->mine->getInventory();
	    $inventory->setItem(0, ItemFactory::getInstance()->get(266, 0, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(1, ItemFactory::getInstance()->get(265, 0, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(2, ItemFactory::getInstance()->get(388, 0, 1)->setLore(["\n§l§bSELL 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(3, ItemFactory::getInstance()->get(264, 0, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(4, ItemFactory::getInstance()->get(351, 4, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(5, ItemFactory::getInstance()->get(406, 0, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(6, ItemFactory::getInstance()->get(331, 0, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
	    $inventory->setItem(18, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(19, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(20, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(21, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(22, ItemFactory::getInstance()->get(381, 0, 1)->setCustomName("§l§cEXIT"));
        $inventory->setItem(23, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(24, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(25, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(26, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
	    $this->mine->send($sender);
	}
	
	public function openMineShop2(Player $sender, Item $item){
		$hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->mine->getInventory();
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $sender->removeCurrentWindow();
        }
        if($item->getId() == 266){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(266, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 265){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(265, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 388){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(388, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 264){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(264, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 351){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(351, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 406){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(406, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 331){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(331, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
    }
	
    public function openToolShop($sender){    	
	    $this->tool->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
    		$sender = $transaction->getPlayer();
  		  $item = $transaction->getItemClicked();
  		  $this->openToolShop2($sender, $item);
			return $transaction->discard();
		});
        $this->tool->setName("ToolShop");
	    $inventory = $this->tool->getInventory();
        $inventory->setItem(2, ItemFactory::getInstance()->get(276, 0, 1)->setCustomName("Diamond Sword")->setLore(["Harga : 124000/1"]));
        $inventory->setItem(3, ItemFactory::getInstance()->get(278, 0, 1)->setCustomName("Diamond Pickaxe")->setLore(["Harga : 128000/1"]));
        $inventory->setItem(4, ItemFactory::getInstance()->get(293, 0, 1)->setCustomName("Diamond Hoe")->setLore(["Harga : 100000/1"]));
        $inventory->setItem(5, ItemFactory::getInstance()->get(279, 0, 1)->setCustomName("Diamond Axe")->setLore(["Harga : 120000/1"]));
        $inventory->setItem(6, ItemFactory::getInstance()->get(277, 0, 1)->setCustomName("Diamond Shovel")->setLore(["Harga : 100000/1"]));
        $inventory->setItem(18, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(19, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(20, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(21, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(22, ItemFactory::getInstance()->get(381, 0, 1)->setCustomName("§l§cEXIT"));
        $inventory->setItem(23, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(24, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(25, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(26, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $this->tool->send($sender);  
    }
    
    public function openToolShop2(Player $sender, Item $item){
    	$hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->tool->getInventory();
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $sender->removeCurrentWindow();
        }
    	if($item->getId() == 276){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 124000){
		        $this->eco->reduceMoney($sender, "124000"); 
		        $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(276, 0, 1));
		    }else{
			    $sender->sendMessage("§cCheck your money!!");
			}
        }
        if($item->getId() == 278){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 128000){
			    $this->eco->reduceMoney($sender, "128000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(278, 0, 1));
		    }else{
			    $sender->sendMessage("§cCheck your money!!");
			}
        }
        if($item->getId() == 293){
            $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 100000){
			    $this->eco->reduceMoney($sender, "100000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(293, 0, 1));
		    }else{
			    $sender->sendMessage("§cCheck your money!!");
			}
        }
        if($item->getId() == 279){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 120000){
			    $this->eco->reduceMoney($sender, "120000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(279, 0, 1));
		    }else{
			    $sender->sendMessage("§cCheck your money!!");
			}
        }
        if($item->getId() == 277){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 100000){
			    $this->eco->reduceMoney($sender, "100000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(277, 0, 1));
		    }else{
			    $sender->sendMessage("§cCheck your money!!");
			}
        }
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
        }
    }
    
    public function openSellShop($sender){
	    $this->chest->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
    		$sender = $transaction->getPlayer();
  		  $item = $transaction->getItemClicked();
  		  $this->openSellShop2($sender, $item);
			return $transaction->discard();
		});
        $this->chest->setName("SellShop");
	    $inventory = $this->chest->getInventory();
        $inventory->setItem(0, ItemFactory::getInstance()->get(296, 0, 1)->setCustomName("Sell Wheat")->setLore(["Sell 64 = 48000/64"]));
        $inventory->setItem(1, ItemFactory::getInstance()->get(392, 0, 1)->setCustomName("Sell Potato")->setLore(["Sell 128 = 112000/128"]));
        $inventory->setItem(2, ItemFactory::getInstance()->get(360, 0, 1)->setCustomName("Sell Melon")->setLore(["Sell 32 = 38000/32"]));
        $inventory->setItem(3, ItemFactory::getInstance()->get(391, 0, 1)->setCustomName("Sell Carrot")->setLore(["Sell 64 = 43500/64"]));
        $inventory->setItem(4, ItemFactory::getInstance()->get(86, 0, 1)->setCustomName("Sell Pumpkin")->setLore(["Sell 64 = 53000/64"]));
        $inventory->setItem(10, ItemFactory::getInstance()->get(264, 0, 1)->setCustomName("Sell Diamond")->setLore(["Sell 64 = 250000/64"]));
        $inventory->setItem(11, ItemFactory::getInstance()->get(388, 0, 1)->setCustomName("Sell Emerald")->setLore(["Sell 64 = 305000/64"]));
        $inventory->setItem(12, ItemFactory::getInstance()->get(351, 4, 1)->setCustomName("Sell Lazuli")->setLore(["Sell 64 = 118000/64"]));
        $inventory->setItem(13, ItemFactory::getInstance()->get(263, 0, 1)->setCustomName("Sell Coal")->setLore(["Sell 64 = 11000/64"]));
        $inventory->setItem(14, ItemFactory::getInstance()->get(265, 0, 1)->setCustomName("Sell Iron Ingot")->setLore(["Sell 64 = 83500/64"]));
        $inventory->setItem(15, ItemFactory::getInstance()->get(266, 0, 1)->setCustomName("Sell Gold Ingot")->setLore(["Sell 64 = 115000/64"]));
        $inventory->setItem(18, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(19, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(20, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(21, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(22, ItemFactory::getInstance()->get(381, 0, 1)->setCustomName("§l§cEXIT"));
        $inventory->setItem(23, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(24, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(25, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(26, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $this->chest->send($sender);  
    }
    
    public function openSellShop2(Player $sender, Item $item){
    	$hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->chest->getInventory();
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $sender->removeCurrentWindow();
        }
    	if($item->getId() == 296){
	        $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $n = ItemFactory::getInstance()->get(296, 0, 64);
            $inv = $sender->getInventory()->getItemInHand($n);
			if($inv >= $n){
				$sender->getInventory()->remove($n);
				$this->eco->addMoney($sender, "48000");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §eWheat 64");
			}
        }
        if($item->getId() == 392){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $o = ItemFactory::getInstance()->get(392, 0, 128);
            $inv = $sender->getInventory()->getItemInHand($o);
			if($inv >= $o){
				$sender->getInventory()->remove($o);
				$this->eco->addMoney($sender, "112000");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §ePotato 128");
			}
        }
        if($item->getId() == 360){
            $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $k = ItemFactory::getInstance()->get(360, 0, 32);
            $inv = $sender->getInventory()->getItemInHand($k);
			if($inv >= $k){
				$sender->getInventory()->remove($k);
				$this->eco->addMoney($sender, "38000");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §eMelon 32");
			}
        }
        if($item->getId() == 391){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));   
		    $i = ItemFactory::getInstance()->get(391, 0, 64);
            $inv = $sender->getInventory()->getItemInHand($i);
			if($inv >= $i){
				$sender->getInventory()->remove($i);
				$this->eco->addMoney($sender, "43500");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §eCarrot 64");
			}
        }
        if($item->getId() == 86){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $y = ItemFactory::getInstance()->get(86, 0, 64);
            $inv = $sender->getInventory()->getItemInHand($y);
			if($inv >= $y){
				$sender->getInventory()->remove($y);
				$this->eco->addMoney($sender, "53000");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §ePumpkin 64");
			}
        }
        if($item->getId() == 264){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));   
		    $p = ItemFactory::getInstance()->get(264, 0, 64);
            $inv = $sender->getInventory()->getItemInHand($p);
			if($inv >= $p){
				$sender->getInventory()->remove($p);
				$this->eco->addMoney($sender, "250000");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §eDiamond 64");
			}
        }
        if($item->getId() == 388){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));   
		    $e = ItemFactory::getInstance()->get(388, 0, 64);
            $inv = $sender->getInventory()->getItemInHand($e);
			if($inv >= $e){
				$sender->getInventory()->remove($e);
				$this->eco->addMoney($sender, "305000");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §eEmerald 64");
			}
        }
        if($item->getId() == 351){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));   
		    $f = ItemFactory::getInstance()->get(351, 4, 64);
            $inv = $sender->getInventory()->getItemInHand($f);
			if($inv >= $f){
				$sender->getInventory()->remove($f);
				$this->eco->addMoney($sender, "118000");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §eLazuli 64");
			}
        }
        if($item->getId() == 263){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));   
		    $c = ItemFactory::getInstance()->get(263, 0, 64);
            $inv = $sender->getInventory()->getItemInHand($c);
			if($inv >= $c){
				$sender->getInventory()->remove($c);
				$this->eco->addMoney($sender, "11000");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §eCoal 64");
			}
        }
        if($item->getId() == 265){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));   
		    $r = ItemFactory::getInstance()->get(265, 0, 64);
            $inv = $sender->getInventory()->getItemInHand($r);
			if($inv >= $r){
				$sender->getInventory()->remove($r);
				$this->eco->addMoney($sender, "83500");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §eIron_Ingot 64");
			}
        }
        if($item->getId() == 266){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $t = ItemFactory::getInstance()->get(266, 0, 64);
            $inv = $sender->getInventory()->getItemInHand($t);
			if($inv >= $t){
				$sender->getInventory()->remove($t);
				$this->eco->addMoney($sender, "115000");
			}else{
			    $sender->sendMessage("§l§e»§r §cYou don't have Item §f: §eGold_Ingot 64");
			}
        }
    }
    
    public function openPotionShop($sender){
	    $this->menu->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
    		$sender = $transaction->getPlayer();
  		  $item = $transaction->getItemClicked();
  		  $this->openPotionShop2($sender, $item);
			return $transaction->discard();
		});
        $this->menu->setName("PotionShop");
	    $inventory = $this->menu->getInventory();
        $inventory->setItem(0, ItemFactory::getInstance()->get(373, 0, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(1, ItemFactory::getInstance()->get(373, 1, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(2, ItemFactory::getInstance()->get(373, 2, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(3, ItemFactory::getInstance()->get(373, 3, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(4, ItemFactory::getInstance()->get(373, 4, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(5, ItemFactory::getInstance()->get(373, 5, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(6, ItemFactory::getInstance()->get(373, 6, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(7, ItemFactory::getInstance()->get(373, 7, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(8, ItemFactory::getInstance()->get(373, 8, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(9, ItemFactory::getInstance()->get(373, 9, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(10, ItemFactory::getInstance()->get(373, 10, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(11, ItemFactory::getInstance()->get(373, 11, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(12, ItemFactory::getInstance()->get(373, 12, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(13, ItemFactory::getInstance()->get(373, 13, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(14, ItemFactory::getInstance()->get(373, 14, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(15, ItemFactory::getInstance()->get(373, 15, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(16, ItemFactory::getInstance()->get(373, 16, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(17, ItemFactory::getInstance()->get(373, 17, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(18, ItemFactory::getInstance()->get(373, 18, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(19, ItemFactory::getInstance()->get(373, 19, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(20, ItemFactory::getInstance()->get(373, 20, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(21, ItemFactory::getInstance()->get(373, 21, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(22, ItemFactory::getInstance()->get(373, 22, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(23, ItemFactory::getInstance()->get(373, 23, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(24, ItemFactory::getInstance()->get(373, 24, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(25, ItemFactory::getInstance()->get(373, 25, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(26, ItemFactory::getInstance()->get(373, 26, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(27, ItemFactory::getInstance()->get(373, 27, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(28, ItemFactory::getInstance()->get(373, 28, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(29, ItemFactory::getInstance()->get(373, 29, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(30, ItemFactory::getInstance()->get(373, 30, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(31, ItemFactory::getInstance()->get(373, 31, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(32, ItemFactory::getInstance()->get(373, 32, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(33, ItemFactory::getInstance()->get(373, 33, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(34, ItemFactory::getInstance()->get(373, 34, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(35, ItemFactory::getInstance()->get(373, 35, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(36, ItemFactory::getInstance()->get(373, 36, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(37, ItemFactory::getInstance()->get(373, 37, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(38, ItemFactory::getInstance()->get(373, 38, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(39, ItemFactory::getInstance()->get(373, 39, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(40, ItemFactory::getInstance()->get(373, 40, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(41, ItemFactory::getInstance()->get(373, 41, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(45, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(46, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(47, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(48, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(49, ItemFactory::getInstance()->get(381, 0, 1)->setCustomName("§l§cEXIT"));
        $inventory->setItem(50, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(51, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(52, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(53, ItemFactory::getInstance()->get(339, 0, 1)->setCustomName("Next"));
        $this->menu->send($sender);
    }
        
    public function openPotionShop2(Player $sender, Item $item){
    	$hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->menu->getInventory();
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $sender->removeCurrentWindow();
        }
        if($item->getId() == 339){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
            $menu->setName("PotionShop");
            $menu->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
    			$sender = $transaction->getPlayer();
  			  $item = $transaction->getItemClicked();
  		 	 $this->openPotionShop3($sender, $item);
				return $transaction->discard();
			});
	        $inventory = $menu->getInventory();
            $inventory->setItem(0, ItemFactory::getInstance()->get(438, 0, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(1, ItemFactory::getInstance()->get(438, 1, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(2, ItemFactory::getInstance()->get(438, 2, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(3, ItemFactory::getInstance()->get(438, 3, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(4, ItemFactory::getInstance()->get(438, 4, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(5, ItemFactory::getInstance()->get(438, 5, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(6, ItemFactory::getInstance()->get(438, 6, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(7, ItemFactory::getInstance()->get(438, 7, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(8, ItemFactory::getInstance()->get(438, 8, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(9, ItemFactory::getInstance()->get(438, 9, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(10, ItemFactory::getInstance()->get(438, 10, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(11, ItemFactory::getInstance()->get(438, 11, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(12, ItemFactory::getInstance()->get(438, 12, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(13, ItemFactory::getInstance()->get(438, 13, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(14, ItemFactory::getInstance()->get(438, 14, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(15, ItemFactory::getInstance()->get(438, 15, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(16, ItemFactory::getInstance()->get(438, 16, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(17, ItemFactory::getInstance()->get(438, 17, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(18, ItemFactory::getInstance()->get(438, 18, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(19, ItemFactory::getInstance()->get(438, 19, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(20, ItemFactory::getInstance()->get(438, 20, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(21, ItemFactory::getInstance()->get(438, 21, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(22, ItemFactory::getInstance()->get(438, 22, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(23, ItemFactory::getInstance()->get(438, 23, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(24, ItemFactory::getInstance()->get(438, 24, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(25, ItemFactory::getInstance()->get(438, 25, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(26, ItemFactory::getInstance()->get(438, 26, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(27, ItemFactory::getInstance()->get(438, 27, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(28, ItemFactory::getInstance()->get(438, 28, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(29, ItemFactory::getInstance()->get(438, 29, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(30, ItemFactory::getInstance()->get(438, 30, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(31, ItemFactory::getInstance()->get(438, 31, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(32, ItemFactory::getInstance()->get(438, 32, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(33, ItemFactory::getInstance()->get(438, 33, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(34, ItemFactory::getInstance()->get(438, 34, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(35, ItemFactory::getInstance()->get(438, 35, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(36, ItemFactory::getInstance()->get(438, 36, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(37, ItemFactory::getInstance()->get(438, 37, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(38, ItemFactory::getInstance()->get(438, 38, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(39, ItemFactory::getInstance()->get(438, 39, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(40, ItemFactory::getInstance()->get(438, 40, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(41, ItemFactory::getInstance()->get(438, 41, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(45, ItemFactory::getInstance()->get(339, 0, 1)->setCustomName("Previous"));
            $inventory->setItem(46, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(47, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(48, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(49, ItemFactory::getInstance()->get(381, 0, 1)->setCustomName("§l§cEXIT"));
            $inventory->setItem(50, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(51, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(52, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(53, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $menu->send($sender);
        }
        if($item->getId() == 373 && $item->getMeta() == 0){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 1){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 1, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 2){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 2, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 3){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 3, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 4){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 4, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 5){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 5, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 6){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 6, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 7){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 7, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 8){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 8, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 9){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 9, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 10){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 10, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 11){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 11, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 12){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 12, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 13){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 13, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 14){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 14, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 15){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 15, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 16){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 16, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 17){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 17, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 18){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 18, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 19){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 19, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 20){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 20, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 21){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 21, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 22){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 22, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 23){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 23, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 24){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 24, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 25){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 25, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 26){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 26, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 27){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 27, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 28){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 28, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 29){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 29, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 30){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 30, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 31){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 31, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 32){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 32, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 33){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 33, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 34){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 34, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 35){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 35, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 36){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 36, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 37){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 37, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 38){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 38, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 39){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 39, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 40){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 40, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 373 && $item->getMeta() == 41){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(373, 41, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
    }
    
    public function openPotionShop3(Player $sender, Item $item){
    	$hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->menu->getInventory();
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $sender->removeCurrentWindow();
        }
        if($item->getId() == 339){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $this->openPotionShop($sender);
        }
        if($item->getId() == 438 && $item->getMeta() == 0){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 1){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 1, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 2){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 2, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 3){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 3, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 4){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 4, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 5){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 5, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 6){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 6, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 7){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 7, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 8){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 8, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 9){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 9, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 10){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 10, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 11){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 11, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 12){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 12, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 13){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 13, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 14){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 14, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 15){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 15, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 16){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 16, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 17){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 17, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 18){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 18, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 19){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 19, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 20){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 20, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 21){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 21, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 22){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 22, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 23){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 23, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 24){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 24, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 25){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 25, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 26){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 26, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 27){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 27, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 28){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 28, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 29){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 29, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 30){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 30, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 31){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 31, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 32){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 32, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 33){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 33, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 34){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 34, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 35){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 35, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 36){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 36, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 37){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 37, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 38){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 38, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 39){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 39, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 40){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 40, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 438 && $item->getMeta() == 41){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(438, 41, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
    }
    
    public function openEnchantShop($sender){
    	$this->menu->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
    		$sender = $transaction->getPlayer();
  		  $item = $transaction->getItemClicked();
  		  $this->openEnchantShop2($sender, $item);
			return $transaction->discard();
		});
        $this->menu->setName("EnchantShop"); 	
	    $inventory = $this->menu->getInventory();
        $inventory->setItem(0, ItemFactory::getInstance()->get(403, 0, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(1, ItemFactory::getInstance()->get(403, 1, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(2, ItemFactory::getInstance()->get(403, 2, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(3, ItemFactory::getInstance()->get(403, 3, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(4, ItemFactory::getInstance()->get(403, 4, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(5, ItemFactory::getInstance()->get(403, 5, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(6, ItemFactory::getInstance()->get(403, 6, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(7, ItemFactory::getInstance()->get(403, 7, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(8, ItemFactory::getInstance()->get(403, 8, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(9, ItemFactory::getInstance()->get(403, 9, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(10, ItemFactory::getInstance()->get(403, 10, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(11, ItemFactory::getInstance()->get(403, 11, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(12, ItemFactory::getInstance()->get(403, 12, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(13, ItemFactory::getInstance()->get(403, 13, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(14, ItemFactory::getInstance()->get(403, 14, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(15, ItemFactory::getInstance()->get(403, 15, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(16, ItemFactory::getInstance()->get(403, 16, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(17, ItemFactory::getInstance()->get(403, 17, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(18, ItemFactory::getInstance()->get(403, 18, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(19, ItemFactory::getInstance()->get(403, 19, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(20, ItemFactory::getInstance()->get(403, 20, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(21, ItemFactory::getInstance()->get(403, 21, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(22, ItemFactory::getInstance()->get(403, 22, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(23, ItemFactory::getInstance()->get(403, 23, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(24, ItemFactory::getInstance()->get(403, 24, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(25, ItemFactory::getInstance()->get(403, 25, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(26, ItemFactory::getInstance()->get(403, 26, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(27, ItemFactory::getInstance()->get(403, 27, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(28, ItemFactory::getInstance()->get(403, 28, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(29, ItemFactory::getInstance()->get(403, 29, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(30, ItemFactory::getInstance()->get(403, 30, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(31, ItemFactory::getInstance()->get(403, 31, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(32, ItemFactory::getInstance()->get(403, 32, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(33, ItemFactory::getInstance()->get(403, 33, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(34, ItemFactory::getInstance()->get(403, 34, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(35, ItemFactory::getInstance()->get(403, 35, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(36, ItemFactory::getInstance()->get(403, 36, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(37, ItemFactory::getInstance()->get(403, 37, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(38, ItemFactory::getInstance()->get(403, 38, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(39, ItemFactory::getInstance()->get(403, 39, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(40, ItemFactory::getInstance()->get(403, 40, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(41, ItemFactory::getInstance()->get(403, 41, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(42, ItemFactory::getInstance()->get(403, 42, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(43, ItemFactory::getInstance()->get(403, 43, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(44, ItemFactory::getInstance()->get(403, 44, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
        $inventory->setItem(45, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(46, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(47, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(48, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(49, ItemFactory::getInstance()->get(381, 0, 1)->setCustomName("§l§cEXIT"));
        $inventory->setItem(50, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(51, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(52, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(53, ItemFactory::getInstance()->get(339, 0, 1)->setCustomName("Next"));
        $this->menu->send($sender);
    }
    
    public function openEnchantShop2(Player $sender, Item $item){
    	$hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->menu->getInventory();
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $sender->removeCurrentWindow();
        }
        if($item->getId() == 339){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
            $this->menu->setName("EnchantShop");
            $this->menu->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
    			$sender = $transaction->getPlayer();
  			  $item = $transaction->getItemClicked();
  			  $this->openEnchantShop3($sender, $item);
				return $transaction->discard();
			});
	        $inventory = $this->menu->getInventory();
            $inventory->setItem(0, ItemFactory::getInstance()->get(403, 45, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(1, ItemFactory::getInstance()->get(403, 46, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(2, ItemFactory::getInstance()->get(403, 47, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(3, ItemFactory::getInstance()->get(403, 48, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(4, ItemFactory::getInstance()->get(403, 49, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(5, ItemFactory::getInstance()->get(403, 50, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(6, ItemFactory::getInstance()->get(403, 51, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(7, ItemFactory::getInstance()->get(403, 52, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(8, ItemFactory::getInstance()->get(403, 53, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(9, ItemFactory::getInstance()->get(403, 50, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(10, ItemFactory::getInstance()->get(403, 51, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(11, ItemFactory::getInstance()->get(403, 52, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(12, ItemFactory::getInstance()->get(403, 53, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(13, ItemFactory::getInstance()->get(403, 54, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(14, ItemFactory::getInstance()->get(403, 55, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(15, ItemFactory::getInstance()->get(403, 56, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(16, ItemFactory::getInstance()->get(403, 57, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(17, ItemFactory::getInstance()->get(403, 58, 1)->setLore(["\n§l§bBUY 1: §a$5000.0 §r§o(Left-Click)\n§l§bBUY 64: §a320000.0 §r§o(Left-Click)"]));
            $inventory->setItem(45, ItemFactory::getInstance()->get(339, 0, 1)->setCustomName("Previous"));
            $inventory->setItem(46, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(47, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(48, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(49, ItemFactory::getInstance()->get(381, 0, 1)->setCustomName("§l§cEXIT"));
            $inventory->setItem(50, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(51, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(52, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $inventory->setItem(53, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
            $this->menu->send($sender);
        }
        if($item->getId() == 403){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(403, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
    }
    
    public function openEnchantShop3(Player $sender, Item $item){
    	$hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->menu->getInventory();
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $sender->removeCurrentWindow();
        }
        if($item->getId() == 339){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $this->openEnchantShop($sender);
        }
        if($item->getId() == 403){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 5000){
			    $this->eco->reduceMoney($sender, "5000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(403, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
    }
    
    public function openEatShop($sender){
    	$this->chest->setListener(function(InvMenuTransaction $transaction): InvMenuTransactionResult{
    		$sender = $transaction->getPlayer();
  		  $item = $transaction->getItemClicked();
  		  $this->openEatShop2($sender, $item);
			return $transaction->discard();
		});
        $this->chest->setName("Eatshop");
	    $inventory = $this->chest->getInventory();
        $inventory->setItem(0, ItemFactory::getInstance()->get(297, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(1, ItemFactory::getInstance()->get(400, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(2, ItemFactory::getInstance()->get(354, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(3, ItemFactory::getInstance()->get(463, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(4, ItemFactory::getInstance()->get(350, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(5, ItemFactory::getInstance()->get(412, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(6, ItemFactory::getInstance()->get(424, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(7, ItemFactory::getInstance()->get(320, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(8, ItemFactory::getInstance()->get(366, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(9, ItemFactory::getInstance()->get(357, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(10, ItemFactory::getInstance()->get(393, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(11, ItemFactory::getInstance()->get(413, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(12, ItemFactory::getInstance()->get(459, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(13, ItemFactory::getInstance()->get(282, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(14, ItemFactory::getInstance()->get(464, 0, 1)->setLore(["\n§l§bBUY 1: §a$1000.0 §r§o(Left-Click)\n§l§bBUY 64: §a64000.0 §r§o(Left-Click)"]));
        $inventory->setItem(18, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(19, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(20, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(21, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(22, ItemFactory::getInstance()->get(381, 0, 1)->setCustomName("§l§cEXIT"));
        $inventory->setItem(23, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(24, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(25, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $inventory->setItem(26, ItemFactory::getInstance()->get(160, 1, 1)->setCustomName("---"));
        $this->chest->send($sender);  
    }
    
    public function openEatShop2(Player $sender, Item $item){
    	$hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->chest->getInventory();
        if($item->getId() == 381){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
            $sender->removeCurrentWindow();
        }
        if($item->getId() == 297){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(297, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 400){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(400, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 354){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(354, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 463){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(463, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 350){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(350, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 412){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(412, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 424){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(424, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 320){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(320, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 366){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(366, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 357){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(357, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 393){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(393, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 413){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(413, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 459){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(459, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 282){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(282, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
        if($item->getId() == 464){
        	$sender->getWorld()->addSound($sender->getPosition()->asVector3(), new PopSound(1.0));
	        $money = $this->eco->myMoney($sender);
	        if($money >= 1000){
			    $this->eco->reduceMoney($sender, "1000"); 
			    $inv = $sender->getInventory();
		        $inv->addItem(ItemFactory::getInstance()->get(464, 0, 1));
		        $sender->sendMessage("§aYou bought 1 item(s).");
		    }else{
			    $sender->sendMessage("§c§oYou don't have money to buy this item!");
			}
        }
    }
}