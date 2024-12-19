<?php

namespace ecstsy\advancedAbilities\effects;

use ecstsy\advancedAbilities\utils\EffectInterface;
use ecstsy\advancedAbilities\utils\managers\EnchantmentDisableManager;
use ecstsy\advancedAbilities\utils\Utils;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\Item;

class DisableActivationEffect implements EffectInterface {

    private static $disabledActivation = [];
    
    public function apply(Entity $attacker, ?Entity $victim, array $data, array $effectData, string $context, array $extraData): void
    {
        if (!isset($effectData['name']) || !isset($effectData['seconds'])) {
            return;
        }

        $target = $effectData['target'] === 'victim' ? $victim : $attacker;

        if (!$target instanceof Living) {
            return;
        }

        $enchantmentId = $effectData['name'];
        $duration = Utils::parseRandomNumber($effectData['seconds']);
        $playerName = $target->getName();

        if (EnchantmentDisableManager::isEnchantmentDisabled($enchantmentId, $playerName)) {
            $remainingTime = EnchantmentDisableManager::getDisabledUntilTime($enchantmentId, $playerName) - time();
            if ($remainingTime > 0) {
                return; 
            }

            EnchantmentDisableManager::removeDisableState($enchantmentId, $playerName);
        }

        EnchantmentDisableManager::disableEnchantmentForPlayer($enchantmentId, $playerName, $duration);
    }
}
