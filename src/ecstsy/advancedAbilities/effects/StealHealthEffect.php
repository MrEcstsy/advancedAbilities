<?php

namespace \ecstsy\advancedAbilities\effects;

use ecstsy\advancedAbilities\utils\EffectInterface;
use ecstsy\advancedAbilities\utils\Utils;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;

class StealHealthEffect implements EffectInterface {

    public function apply(Entity $attacker, ?Entity $victim, array $data, array $effectData, string $context, array $extraData): void
    {
        if (!isset($effectData['amount']) || ($amount = Utils::parseRandomNumber($effectData['amount'])) <= 0) {
            return; 
        }

        $targetEntity = match (strtolower($effectData['target'] ?? '')) {
            'attacker' => $attacker,
            'victim' => $victim,
            default => null,
        };

        if ($targetEntity === null || !$targetEntity instanceof Living) {
            return; 
        }

        if ($victim !== null && $victim instanceof Living) {
            $currentVictimHealth = $victim->getHealth();
            $healthToSteal = min($amount, $currentVictimHealth); 
            $victim->setHealth($currentVictimHealth - $healthToSteal);
        } else {
            $healthToSteal = 0; 
        }

        $currentTargetHealth = $targetEntity->getHealth();
        $maxTargetHealth = $targetEntity->getMaxHealth();
        $newTargetHealth = min($maxTargetHealth, $currentTargetHealth + $healthToSteal); 
        $targetEntity->setHealth($newTargetHealth);
    }
}
