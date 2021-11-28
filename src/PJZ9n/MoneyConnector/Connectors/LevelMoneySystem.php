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

use hayao\main as PLevelMoneySystem;
use PJZ9n\MoneyConnector\MoneyConnector;
use pocketmine\player\Player;
use pocketmine\Server;

class LevelMoneySystem implements MoneyConnector
{

    /** @var PLevelMoneySystem */
    private $parentAPI;

    public function __construct()
    {
        $this->parentAPI = Server::getInstance()->getPluginManager()->getPlugin("LevelMoneySystem");
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

        $allConfig = $this->parentAPI->config;
        $allMoney = [];
        foreach($allConfig as $name => $money){
            $allMoney[$name] = (int)$money;
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
        $this->parentAPI->removeMoney($player, $amount);
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
        return "LevelMoneySystem";
    }
}