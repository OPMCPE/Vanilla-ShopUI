<?php

namespace GuiShop;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\{Item, ItemBlock};
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as TF;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\event\server\DataPacketReceiveEvent;
use GuiShop\Modals\elements\{Dropdown, Input, Button, Label, Slider, StepSlider, Toggle};
use GuiShop\Modals\network\{GuiDataPickItemPacket, ModalFormRequestPacket, ModalFormResponsePacket, ServerSettingsRequestPacket, ServerSettingsResponsePacket};
use GuiShop\Modals\windows\{CustomForm, ModalWindow, SimpleForm};
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender, CommandExecutor};

use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener {
  public $shop;
  public $item;

  //documentation for setting up the items
  /*
  "Item name" => [item_id, item_damage, buy_price, sell_price]
  */
public $Blocks = [
    "ICON" => ["Blocks",2,0],
    "Oak Wood" => [17,0,300,15],
    "Birch Wood" => [17,2,300,15],
    "Spruce Wood" => [17,1,300,15],
    "Dark Oak Wood" => [162,1,300,15],
	"Cobblestone" => [4,0,100,5],
	"Obsidian" => [49,0,5000,250],
	"Bedrock" => [7,0,150000,500],
	"Sand " => [12,0,1500,7],
    "Sandstone " => [24,0,1500,7],
	"Nether Rack" => [87,0,1500,7],
    "Glass" => [20,0,2000,25],
    "Glowstone" => [89,0,1000,50],
    "Sea Lantern" => [169,0,1000,50],
	"Grass" => [2,0,300,10],
	"Dirt" => [3,0,30,5],
    "Stone" => [1,0,2000,10],
    "Planks" => [5,0,2000,10],
    "Prismarine" => [168,0,3000,20],
    "End Stone" => [121,0,1000000,20],
    "Emerald Block" => [133,0,20000,50],
    "Diamond Block" => [57,0,15000,51],
    "Glass" => [20,0,750,30],
    "Iron Block" => [42,0,5000,30],
    "Gold Block" => [41,0,5000,30],
    "Purpur Blocks" => [201,0,1200,30],
    "Quartz Block" => [155,0,1000,30]
  ];

  public $Ores = [
    "ICON" => ["Ores",266,0],
    "Coal" => [263,0,1000,50],
    "Iron Ingot" => [265,0,1100,100],
    "Gold Ingot" => [266,0,1050,150],
    "Diamond" => [264,0,1300,250],
    "Lapis" => [351,4,500,250]
  ];

  public $Tools = [
    "ICON" => ["Tools",278,0],
    "Diamond Pickaxe" => [278,0,5000,250],
    "Diamond Shovel" => [277,0,5000,250],
    "Diamond Axe" => [279,0,5000,250],
    "Diamond Hoe" => [293,0,5000,250],
    "Diamond Sword" => [276,0,7500,375],
    "Bow" => [261,0,4000,200],
    "Arrow" => [262,0,250,5]
  ];

  public $Armor = [
    "ICON" => ["Armor",311,0],
    "Diamond Helmet" => [310,0,10000,500],
    "Diamond Chestplate" => [311,0,25000,1250],
    "Diamond Leggings" => [312,0,15000,750],
    "Diamond Boots" => [313,0,10000,500]
  ];

  public $Farming = [
    "ICON" => ["Farming",293,0],
    "Pumpkin" => [86,0,1000,120],
    "Melon" => [360,13,1000,120],
    "Carrot" => [391,0,850,100],
    "Potato" => [392,0,500,85],
    "Sugarcane" => [338,0,400,65],
    "Wheat" => [296,6,350,55],
    "Pumpkin Seed" => [361,0,2000,100],
    "Melon Seed" => [362,0,2000,100],
    "Seed" => [295,0,2000,100]
  ];

  public $Food = [
    "ICON" => ["Food",364,0],
	"Cooked Chicken" => [366,0,1000,5],
    "Steak" => [364,0,1000,5],
    "Golden Apple" => [322,0,50000,100],
    "Enchanted Golden Apple" => [466,0,1000000,100]
  ];

  public $Miscellaneous = [
    "ICON" => ["Miscellaneous",368,0], 
	"Furnace" => [61,0,20,10],
    "Crafting Table" => [58,0,2000,10],
	"Ender Chest " => [130,0,100000,500],
    "Enderpearl" => [368,0,100000,500],
    "Bone" => [352,0,50,25],
    "Book & Quill" => [386,0,100,0],
    "Elytra" => [444,0,1000,500],
    "Totem of Undying" => [450,0,10000,500],
    "Echanted G Apple" => [466,0,75000,3000],
    "G Apples" => [322,0,30000,1500]
  ];

  public $Raiding = [
    "ICON" => ["Raiding",46,0],
    "Flint & Steel" => [259,0,10000,50],
    "Torch" => [50,0,500,2],
	"Packed Ice " => [174,0,500,250],
    "Water" => [9,0,5000,100],
    "Lava" => [10,0,5000,100],
    "Redstone" => [331,0,5000,205],
    "Chest" => [54,0,1000,50],
    "TNT" => [46,0,100000,500]
  ];
	
  public $Mobs = [
    "ICON" => ["Mobs",52,0],
    "Blaze" => [383,43,1500000,10000],
    "Stray" => [383,46,1400000,10000],
    "Skeleton" => [383,34,2000000,10000],
    "Zombie" => [383,32,2500000,10000],
    "Husk" => [383,47,1250000,10000],
    "Zombie_Pigman" => [383,36,2750000,10000],
    "Creeper" => [383,33,4000000,10000],
    "Mob Spawner" => [52,0,1500000,20000]
  ];

  public $Potions = [
    "ICON" => ["Potions",373,0],
    "Strength" => [373,33,10000,100],
    "Regeneration" => [373,28,10000,100],
    "Speed" => [373,16,10000,500],
    "Fire Resistance" => [373,13,10000,100],
    "Poison (SPLASH)" => [438,27,10000,100],
    "Weakness (SPLASH)" => [438,35,10000,100],
    "Slowness (SPLASH)" => [438,17,10000,100]
  ];

  public $Skulls = [
    "ICON" => ["Skulls",397,0],
    "Zombie Skull" => [397,2,50000,500],
    "Wither Skull" => [397,1,50000,500],
    "Skin Head" => [397,3,5000,100],
    "Creeper Skull" => [397,4,50000,500],
    "Dragon Skull" => [397,5,100000,600],
    "Skeleton Skull" => [397,0,50000,500]
  ];
	
  public $MobDrop = [
    "ICON" => ["MobDrop",369,0],
    "Blaze Rod" => [369,0,500,400],
    "Gold Nuggets" => [371,0,500,300],
    "Rotten Flesh" => [367,0,500,300],
    "GunPowder" => [289,0,500,350]
  ];
	
  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    PacketPool::registerPacket(new GuiDataPickItemPacket());
		PacketPool::registerPacket(new ModalFormRequestPacket());
		PacketPool::registerPacket(new ModalFormResponsePacket());
		PacketPool::registerPacket(new ServerSettingsRequestPacket());
		PacketPool::registerPacket(new ServerSettingsResponsePacket());
    $this->item = [$this->MobDrop, $this->Skulls, $this->Potions, $this->Mobs, $this->Raiding, $this->Farming, $this->Armor, $this->Tools, $this->Food, $this->Ores, $this->Blocks, $this->Miscellaneous];
  }

  public function sendMainShop(Player $player){
    $ui = new SimpleForm("§2SkyRealmPE Shop", "Purchase and sell items here");
    foreach($this->item as $category){
      if(isset($category["ICON"])){
        $rawitemdata = $category["ICON"];
        $button = new Button($rawitemdata[0]);
        $button->addImage('url', "aversionpe.buycraft.net".$rawitemdata[1]."-".$rawitemdata[2].".png");
        $ui->addButton($button);
      }
    }
    $pk = new ModalFormRequestPacket();
    $pk->formId = 110;
    $pk->formData = json_encode($ui);
    $player->dataPacket($pk);
    return true;
  }

  public function sendShop(Player $player, $id){
    $ui = new SimpleForm("§2SkyRealmPE Shop", "Shop and Sell Items Here");
    $ids = -1;
    foreach($this->item as $category){
      $ids++;
      $rawitemdata = $category["ICON"];
      if($ids == $id){
        $name = $rawitemdata[0];
        $data = $this->$name;
        foreach($data as $name => $item){
          if($name != "ICON"){
            $button = new Button($name);
            $button->addImage('url', "aversionpe.buycraft.net".$item[0]."-".$item[1].".png");
            $ui->addButton($button);
          }
        }
      }
    }
    $pk = new ModalFormRequestPacket();
    $pk->formId = 111;
    $pk->formData = json_encode($ui);
    $player->dataPacket($pk);
    return true;
  }

  public function sendConfirm(Player $player, $id){
    $ids = -1;
    $idi = -1;
    foreach($this->item as $category){
      $ids++;
      $rawitemdata = $category["ICON"];
      if($ids == $this->shop[$player->getName()]){
        $name = $rawitemdata[0];
        $data = $this->$name;
        foreach($data as $name => $item){
          if($name != "ICON"){
            if($idi == $id){
              $this->item[$player->getName()] = $id;
              $iname = $name;
              $cost = $item[2];
              $sell = $item[3];
              break;
            }
          }
          $idi++;
        }
      }
    }

    $ui = new CustomForm($iname);
    $slider = new Slider("§dAmount ",1,128,0);
    $toggle = new Toggle("§5Selling");
    if($sell == 0) $sell = "0";
    $label = new Label(TF::GREEN."Buy: $".TF::GREEN.$cost.TF::RED."\nSell: $".TF::RED.$sell);
    $ui->addElement($label);
    $ui->addElement($toggle);
    $ui->addElement($slider);
    $pk = new ModalFormRequestPacket();
    $pk->formId = 112;
    $pk->formData = json_encode($ui);
    $player->dataPacket($pk);
    return true;
  }

  public function sell(Player $player, $data, $amount){
    $ids = -1;
    $idi = -1;
    foreach($this->item as $category){
      $ids++;
      $rawitemdata = $category["ICON"];
      if($ids == $this->shop[$player->getName()]){
        $name = $rawitemdata[0];
        $data = $this->$name;
        foreach($data as $name => $item){
          if($name != "ICON"){
            if($idi == $this->item[$player->getName()]){
              $iname = $name;
              $id = $item[0];
              $damage = $item[1];
              $cost = $item[2]*$amount;
              $sell = $item[3]*$amount;
              if($sell == 0){
                $player->sendMessage(TF::BOLD . TF::DARK_GRAY . "(" . TF::RED . "!" . TF::DARK_GRAY . ") " . TF::RESET . TF::GRAY . "§cThis is not sellable!");
                return true;
              }
              if($player->getInventory()->contains(Item::get($id,$damage,$amount))){
                $player->getInventory()->removeItem(Item::get($id,$damage,$amount));
                EconomyAPI::getInstance()->addMoney($player, $sell);
                $player->sendMessage(TF::BOLD . TF::DARK_GRAY . "(" . TF::GREEN . "!" . TF::DARK_GRAY . ") " . TF::RESET . TF::GRAY . "§bYou have sold §3$amount $iname §bfor §3$$sell");
              }else{
                $player->sendMessage(TF::BOLD . TF::DARK_GRAY . "(" . TF::RED . "!" . TF::DARK_GRAY . ") " . TF::RESET . TF::GRAY . "§2You do not have §5$amount $iname!");
              }
              unset($this->item[$player->getName()]);
              unset($this->shop[$player->getName()]);
              return true;
            }
          }
          $idi++;
        }
      }
    }
    return true;
  }

  public function purchase(Player $player, $data, $amount){
    $ids = -1;
    $idi = -1;
    foreach($this->item as $category){
      $ids++;
      $rawitemdata = $category["ICON"];
      if($ids == $this->shop[$player->getName()]){
        $name = $rawitemdata[0];
        $data = $this->$name;
        foreach($data as $name => $item){
          if($name != "ICON"){
            if($idi == $this->item[$player->getName()]){
              $iname = $name;
              $id = $item[0];
              $damage = $item[1];
              $cost = $item[2]*$amount;
              $sell = $item[3]*$amount;
              if(EconomyAPI::getInstance()->myMoney($player) > $cost){
                $player->getInventory()->addItem(Item::get($id,$damage,$amount));
                EconomyAPI::getInstance()->reduceMoney($player, $cost);
                $player->sendMessage(TF::BOLD . TF::DARK_GRAY . "(" . TF::GREEN . "!" . TF::DARK_GRAY . ") " . TF::RESET . TF::GRAY . "§bYou purchased §3$amount $iname §bfor §3$$cost");
              }else{
                $player->sendMessage(TF::BOLD . TF::DARK_GRAY . "(" . TF::RED . "!" . TF::DARK_GRAY . ") " . TF::RESET . TF::GRAY . "§2You do not have enough money to buy §5$amount $iname");
              }
              unset($this->item[$player->getName()]);
              unset($this->shop[$player->getName()]);
              return true;
            }
          }
          $idi++;
        }
      }
    }
    return true;
  }

  public function DataPacketReceiveEvent(DataPacketReceiveEvent $event){
    $packet = $event->getPacket();
    $player = $event->getPlayer();
    if($packet instanceof ModalFormResponsePacket){
      $id = $packet->formId;
      $data = $packet->formData;
      $data = json_decode($data);
      if($data === Null) return true;
      if($id === 110){
        $this->shop[$player->getName()] = $data;
        $this->sendShop($player, $data);
        return true;
      }
      if($id === 111){
        //$this->shop[$player->getName()] = $data;
        $this->sendConfirm($player, $data);
        return true;
      }
      if($id === 112){
        $selling = $data[1];
        $amount = $data[2];
        if($selling){
          $this->sell($player, $data, $amount);
          return true;
        }
        $this->purchase($player, $data, $amount);
        return true;
      }
    }
    return true;
  }

  public function onCommand(CommandSender $player, Command $command, string $label, array $args) : bool{
    switch(strtolower($command)){
      case "shop":
        $this->sendMainShop($player);
        return true;
    }
  }

}
