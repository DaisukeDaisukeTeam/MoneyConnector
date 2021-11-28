<?php

namespace PJZ9n\MoneyConnector\Connectors;

use PocketMoney\PocketMoney as PluginPocketMoney;
use PJZ9n\MoneyConnector\MoneyConnector;
use pocketmine\player\Player;
use pocketmine\Server;

class PocketMoney implements MoneyConnector
{

    protected const KEYS_MONEY = "money";

    /** @var PluginPocketMoney */
    private $parentAPI;

    public function __construct()
    {
        $this->parentAPI = Server::getInstance()->getPluginManager()->getPlugin("PocketMoney");
    }

    /**
     * @inheritDoc
     */
    public function getMonetaryUnit(): string
    {
        return "M";//HACK
    }

    /**
     * @inheritDoc
     */
    public function getAllMoney(): array
    {

        $property =  new \ReflectionProperty($this->parentAPI, "users");
        $property->setAccessible(true);
        $users = $property->getValue($this->parentAPI);
        $allUser = $users->getAll();
        $allMoney = [];
        foreach($allUser as $name => $data){
            $allMoney[$name] = (int)$data[self::KEYS_MONEY];
        }
        return $allMoney;
    }

    /**
     * @inheritDoc
     */
    public function myMoney(Player $player): ?int
    {
        return $this->myMoneyByName($player->getName());
    }

    /**
     * @inheritDoc
     */
    public function myMoneyByName(string $player): ?int
    {
        return $this->parentAPI->getMoney($player);
    }

    /**
     * @inheritDoc
     */
    public function setMoney(Player $player, int $amount): int
    {
        return $this->addMoneyByName($player->getName(), $amount);
    }

    /**
     * @inheritDoc
     */
    public function setMoneyByName(string $player, int $amount): int
    {
        $this->parentAPI->setMoney($player, $amount);
        return MoneyConnector::RETURN_SUCCESS;
    }

    /**
     * @inheritDoc
     */
    public function addMoney(Player $player, int $amount): int
    {
        $this->addMoneyByName($player->getName(), $amount);
        return MoneyConnector::RETURN_SUCCESS;
    }

    /**
     * @inheritDoc
     */
    public function addMoneyByName(string $player, int $amount): int
    {
        $this->parentAPI->grantMoney($player, $amount);
        return MoneyConnector::RETURN_SUCCESS;
    }

    /**
     * @inheritDoc
     */
    public function reduceMoney(Player $player, int $amount): int
    {
        return $this->reduceMoneyByName($player->getName(), $amount);
    }

    /**
     * @inheritDoc
     */
    public function reduceMoneyByName(string $player, int $amount): int
    {
        $amount = $this->myMoneyByName($player) - $amount;
        if($amount < 0){
            $amount = 0;
        }
        $this->setMoneyByName($player, $amount);
        return MoneyConnector::RETURN_SUCCESS;
    }

    /**
     * @inheritDoc
     */
    public function getParentAPIInstance(): object
    {
        return $this->parentAPI;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return "PocketMoney";
    }
}