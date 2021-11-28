<?php

/**
 * Copyright (c) 2020 PJZ9n.
 *
 * This file is part of MoneyConnector.
 *
 * MoneyConnector is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MoneyConnector is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MoneyConnector. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace PJZ9n\MoneyConnector\Connectors;

use PJZ9n\MoneyConnector\MoneyConnector;
use pocketmine\player\Player;
use onebone\economyapi\EconomyAPI as PEconomyAPI;

/**
 * Class EconomyAPI
 * Connector for onebone/EconomyAPI
 *
 * @package PJZ9n\MoneyConnector\Connectors
 */
class EconomyAPI implements MoneyConnector
{
    private static function convertResult(int $return): int
    {
        switch ($return) {
            case PEconomyAPI::RET_NO_ACCOUNT:
                return MoneyConnector::RETURN_NO_ACCOUNT;
            case PEconomyAPI::RET_CANCELLED:
                return MoneyConnector::RETURN_CANCELLED;
            case PEconomyAPI::RET_NOT_FOUND:
                return MoneyConnector::RETURN_NOT_FOUND;
            case PEconomyAPI::RET_INVALID:
                return MoneyConnector::RETURN_INVALID;
            case PEconomyAPI::RET_SUCCESS:
                return MoneyConnector::RETURN_SUCCESS;
        }
        return MoneyConnector::RETURN_FAILED;
    }
    
    /** @var PEconomyAPI */
    private $parentAPI;
    
    public function __construct()
    {
        $this->parentAPI = PEconomyAPI::getInstance();
    }
    
    /**
     * @inheritDoc
     */
    public function getMonetaryUnit(): string
    {
        return $this->parentAPI->getMonetaryUnit();
    }
    
    /**
     * @inheritDoc
     */
    public function getAllMoney(): array
    {
        return $this->parentAPI->getAllMoney();
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
        return (int)floor($this->parentAPI->myMoney($player));
    }
    
    /**
     * @inheritDoc
     */
    public function setMoney(Player $player, int $amount): int
    {
        return $this->setMoneyByName($player->getName(), $amount);
    }
    
    /**
     * @inheritDoc
     */
    public function setMoneyByName(string $player, int $amount): int
    {
        return self::convertResult($this->parentAPI->setMoney($player, $amount));
    }
    
    /**
     * @inheritDoc
     */
    public function addMoney(Player $player, int $amount): int
    {
        return $this->addMoneyByName($player->getName(), $amount);
    }
    
    /**
     * @inheritDoc
     */
    public function addMoneyByName(string $player, int $amount): int
    {
        return self::convertResult($this->parentAPI->addMoney($player, $amount));
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
        return self::convertResult($this->parentAPI->reduceMoney($player, $amount));
    }
    
    /**
     * @inheritDoc
     *
     * @return PEconomyAPI
     */
    public function getParentAPIInstance(): Object
    {
        return $this->parentAPI;
    }
    
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return "EconomyAPI";
    }
}