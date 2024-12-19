<?php

namespace ecstsy\advancedAbilities\effects;

use ecstsy\advancedAbilities\utils\EffectInterface;
use ecstsy\advancedAbilities\utils\Utils;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class AddFoodEffect implements EffectInterface {

    public function apply(Entity $attacker, ?Entity $victim, array $data, array $effectData, string $context, array $extraData): void
    {
        if (isset($effectData['amount'])) {
            $target = $effectData['target'] === 'victim' ? $victim : $attacker;

            if (!$target instanceof Player) {
                return;
            }

            $amount = Utils::parseRandomNumber($effectData['amount']);
            $newFoodLevel = $target->getHungerManager()->getFood() + $amount;
            $newFoodLevel = max(0, min(20, $newFoodLevel));
            
            $target->getHungerManager()->setFood($newFoodLevel);        
        }
    }
}
