<?php

namespace Rom;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\event\Listener;

use Rom\API\api;
use Rom\Command\Commands;
use Rom\RPG\Weapon;
use Rom\RPG\Armour;
use Rom\RPG\Level;
use Rom\Event\onJoin;
use Rom\Event\onDeath;
use Rom\Event\onChat;
use Rom\Event\onDamage;
use Rom\Event\onChange;
use Rom\Event\onLevelChange;
use Rom\Event\onTouch;
use Rom\Event\onBreak;
use Rom\Shop\Shop;
use Rom\Task\RunTime;
use Rom\Task\Task;

class rpg extends PluginBase{

    public $info;
    
    public $weapon;
    
    public $armor;
    
    public $players;
    
    public $shops;
    
    public $messages;
    
    public $exp;
    
    public $level;
    
    public $shop;

    public function onEnable(){
        @mkdir($this->getDataFolder()."/Weapon");
        $this->weapon = $this->getDataFolder()."/Weapon/";
        @mkdir($this->getDataFolder()."/Players");
        $this->players = $this->getDataFolder()."/Players/";
        @mkdir($this->getDataFolder()."/Armour");
        $this->armor = $this->getDataFolder()."/Armour/";
        @mkdir($this->getDataFolder()."/Shop");
        $this->shops = $this->getDataFolder()."/Shop/";
        new api($this);
        new Weapon($this);
        new Armour($this);
        new Level($this);
        new Shop($this);
        $this->getServer()->getCommandMap()->register("", new Commands($this));
        @mkdir($this->getDataFolder());
        $this->info = new Config($this->getDataFolder()."Info.yml",Config::YAML,[
            "顶部显示"=>"§eLV.{%level}§6 {%player}",
            "聊天显示"=>"§4[{%prefixName}§4][§eLV.{%level}§4]§6{%player}§d>>>§f{%message}",
            "底部显示"=>"§a玩家:§e {%player} §f||§a 等级:§e {%level}§f ||§a 经验:§e {%nowexp}§c/§e{%nextexp}§f ||§a 公会:§e {%guildName} {%n} §a血量:§e {%health}§c/§e{%maxhealth}§f ||§a 称号:§e {%prefixName}§f ||§a金币:§e {%money}"
            ]);
        $this->exp = new Config($this->getDataFolder()."Exp.yml",Config::YAML,[
            "经验增量"=>"100",
        ]);
        $this->messages = new Config($this->getDataFolder()."Messages.yml",Config::YAML,[
            "经验"=>[
                "经验增加通知"=>"§e恭喜你增加了{%exp}点经验",
                "升级通知"=>"§e恭喜你升到了{%newLevel}级"
            ],
            "武器消息"=>[
                "吸血"=>"吸血",
                "暴击"=>"暴击"
            ]
        ]);
        $this->level = new Config($this->getDataFolder()."LevelChange.yml",Config::YAML,[]);
        $this->shop = new Config($this->getDataFolder()."Shop.yml",Config::YAML,[]);
        $this->onRegister();
        $this->getScheduler()->scheduleRepeatingTask(new RunTime([$this,"Tip"]),20);
        $this->getLogger()->info("Rom加载完成，作者：若尘，QQ：3149858622");
    }
    public function onRegister(){
        $this->getServer()->getPluginManager()->registerEvents(new onJoin($this),$this);
        $this->getServer()->getPluginManager()->registerEvents(new onDeath($this),$this);
        $this->getServer()->getPluginManager()->registerEvents(new onChat($this),$this);
        $this->getServer()->getPluginManager()->registerEvents(new onDamage($this),$this);
        $this->getServer()->getPluginManager()->registerEvents(new onChange($this),$this);
        $this->getServer()->getPluginManager()->registerEvents(new onLevelChange($this),$this);
        $this->getServer()->getPluginManager()->registerEvents(new onTouch($this),$this);
        $this->getServer()->getPluginManager()->registerEvents(new onBreak($this),$this);
    }
    
    public function Tip(){
        new Task($this);
    }
}

