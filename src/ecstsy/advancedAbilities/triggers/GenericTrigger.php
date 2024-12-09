<?php

namespace ecstsy\advancedAbilities\triggers;

use ecstsy\advancedAbilities\utils\TriggerHelper;
use ecstsy\advancedAbilities\utils\TriggerInterface;
use ecstsy\advancedAbilities\utils\Utils;
use pocketmine\entity\Entity;

class GenericTrigger implements TriggerInterface {
    use TriggerHelper;

    public function execute(Entity $attacker, ?Entity $victim, array $enchantments, string $context, array $extraData = []): void
    {
        foreach ($enchantments as $enchantmentData) {
            $level = $extraData['enchant-level'] ?? null;
    
            if ($level === null) {
                continue;
            }
    
            $config = $enchantmentData['config']; 
    
            if (!isset($config['levels'][$level])) {
                continue;
            }
    
            $levelConfig = $config['levels'][$level];
    
            $chance = $levelConfig['chance'] ?? 100;
        
            if (isset($levelConfig['conditions']) && !$this->handleConditions($levelConfig['conditions'], $attacker, $victim, $context, $extraData)) {
                continue;
            }
    
            if (mt_rand(0, 100) <= $chance) {
                $this->applyEffects($levelConfig['effects'], $attacker, $victim, $context, $extraData);
            }       
        }
    }   
}
