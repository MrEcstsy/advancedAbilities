<?php

namespace ecstsy\advancedAbilities\effects;

use ecstsy\advancedAbilities\utils\EffectInterface;
use ecstsy\advancedAbilities\utils\Utils;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;

class AddHealthEffect implements EffectInterface {

    public function apply(Entity $attacker, ?Entity $victim, array $data, array $effectData, string $context, array $extraData): void
    {
        if (isset($effectData['amount'])) {
            $target = $effectData['target'] === 'victim' ? $victim : $attacker;

            if (!$target instanceof Living) {
                return;
            }

            $target->setHealth($target->getHealth() + Utils::parseRandomNumber($effectData['amount']));
        }
    }
}
