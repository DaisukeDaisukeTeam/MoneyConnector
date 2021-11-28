<?php

namespace PJZ9n\MoneyConnector\Connectors;

use cooldogedev\BedrockEconomy\BedrockEconomy as PBedrockEconomy;
use cooldogedev\BedrockEconomy\constant\SearchConstants;
use cooldogedev\BedrockEconomy\session\cache\SessionCache;
use cooldogedev\BedrockEconomy\session\SessionManager;
use PJZ9n\MoneyConnector\MoneyConnector;
use pocketmine\player\Player;

class BedrockEconomy implements MoneyConnector
{
    /** @var PBedrockEconomy */
    private $parentPlugin;
    /** @var SessionManager */
    private $parentAPI;
    
    public function __construct()
    {
        $this->parentPlugin = PBedrockEconomy::getInstance();
        $this->parentAPI = $this->parentPlugin->getSessionManager();
    }
    
    /**
     * @inheritDoc
     */
    public function getMonetaryUnit(): string
    {
        return $this->parentPlugin->getCurrencyManager()->getSymbol();
    }
    
    /**
     * @inheritDoc
     */
    public function getAllMoney(): array
    {
        $allSessions = $this->parentAPI->getSessions();
        $allMoney = [];
        foreach ($allSessions as $xuid => $session) {
            $cache = $session->getCache();
            $allMoney[$session->getUsername()] = $cache->getBalance();
        }
        return $allMoney;
    }
    
    /**
     * @inheritDoc
     */
    public function myMoney(Player $player): ?int
    {
        $cache = $this->getCacheByXuid($player);
        return $this->getBalance($cache);
    }
    
    /**
     * @inheritDoc
     */
    public function myMoneyByName(string $player): ?int
    {
        $cache = $this->getCacheByName($player);
        return $this->getBalance($cache);
    }
    
    protected function getBalance(?SessionCache $cache): ?int
    {
        if ($cache === null) {
            return null;
        }
        return $cache->getBalance();
    }
    
    /**
     * @inheritDoc
     */
    public function setMoney(Player $player, int $amount): int
    {
        $cache = $this->getCacheByXuid($player);
        return $this->setBalance($cache, $amount);
    }
    
    /**
     * @inheritDoc
     */
    public function setMoneyByName(string $player, int $amount): int
    {
        $cache = $this->getCacheByName($player);
        return $this->setBalance($cache, $amount);
    }
    
    protected function setBalance(?SessionCache $cache, int $amount): int
    {
        if ($cache === null) {
            return MoneyConnector::RETURN_NO_ACCOUNT;
        }
        $cache->setBalance($amount);
        return MoneyConnector::RETURN_SUCCESS;
    }
    
    /**
     * @inheritDoc
     */
    public function addMoney(Player $player, int $amount): int
    {
        $cache = $this->getCacheByXuid($player);
        return $this->addToBalance($cache, $amount);
    }
    
    /**
     * @inheritDoc
     */
    public function addMoneyByName(string $player, int $amount): int
    {
        $cache = $this->getCacheByName($player);
        return $this->addToBalance($cache, $amount);
    }
    
    public function addToBalance(?SessionCache $cache, int $amount): int
    {
        if ($cache === null) {
            return MoneyConnector::RETURN_NO_ACCOUNT;
        }
        $cache->addToBalance($amount);
        return MoneyConnector::RETURN_SUCCESS;
    }
    
    /**
     * @inheritDoc
     */
    public function reduceMoney(Player $player, int $amount): int
    {
        $cache = $this->getCacheByXuid($player);
        return $this->subtractFromBalance($cache, $amount);
    }
    
    /**
     * @inheritDoc
     */
    public function reduceMoneyByName(string $player, int $amount): int
    {
        $cache = $this->getCacheByName($player);
        return $this->subtractFromBalance($cache, $amount);
    }
    
    protected function subtractFromBalance(?SessionCache $cache, int $amount): int
    {
        if ($cache === null) {
            return MoneyConnector::RETURN_NO_ACCOUNT;
        }
        $cache->subtractFromBalance($amount);
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
    
    public function getCache(Player $player): ?SessionCache
    {
        return $this->getCacheByXuid($player->getXuid());
    }
    
    protected function getCacheByName(string $player): ?SessionCache
    {
        $session = $this->parentAPI->getSession($player, SearchConstants::SEARCH_MODE_USERNAME);
        if ($session === null) {
            return null;
        }
        return $session->getCache();
    }
    
    protected function getCacheByXuid(string $player): ?SessionCache
    {
        $session = $this->parentAPI->getSession($player);
        if ($session === null) {
            return null;
        }
        return $session->getCache();
    }
}