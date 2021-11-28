<?php

namespace PJZ9n\MoneyConnector\Connectors;

use hayao\main as PLevelMoneySystem;
use PJZ9n\MoneyConnector\MoneyConnector;
use pocketmine\player\Player;
use pocketmine\Server;
use rark\simple_economy\Account;
use rark\simple_economy\api\SimpleEconomyAPI;

class SimpleEconomy implements MoneyConnector
{
    protected const CURRENCY = "yen";

    /** @var SimpleEconomyAPI */
    private $parentAPI;

    public function __construct()
    {
        $this->parentAPI = new SimpleEconomyAPI(self::CURRENCY);
    }

    /**
     * @inheritDoc
     */
    public function getMonetaryUnit(): string
    {
        return self::CURRENCY;//HACK
    }

    /**
     * @inheritDoc
     */
    public function getAllMoney(): array
    {
        //HACK
        $function = function(){
            return self::$instances;
        };
        $function = $function->bindTo(null, Account::class);
        /** @var Account[] $instances */
        $instances = $function();

        $allMoney = [];
        foreach ($instances as $account) {
            $username = $account->getName();
            $allMoney[$username] = $this->parentAPI->myMoney($username);
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
        return $this->parentAPI->myMoney($player);
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
        $this->parentAPI->addMoney($player, $amount);
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
        $this->parentAPI->reduceMoney($player, $amount);
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
        return "SimpleEconomy";
    }
}