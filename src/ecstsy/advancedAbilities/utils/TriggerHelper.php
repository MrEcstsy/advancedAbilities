<?php

namespace ecstsy\advancedAbilities\utils;

use ecstsy\advancedAbilities\utils\managers\CooldownManager;
use ecstsy\advancedAbilities\utils\managers\EffectManager;
use ecstsy\advancedAbilities\utils\registries\ConditionRegistry;
use pocketmine\entity\Entity;

trait TriggerHelper {

    public function handleConditions(array $conditions, Entity $attacker, ?Entity $victim, string $context, array $extraData = []): bool {
        foreach ($conditions as $conditionData) {
            $conditionType = $conditionData['type'] ?? '';
            $conditionMode = $conditionData['condition_mode'] ?? 'allow';
            $chanceValue = $conditionData['chance'] ?? 0;
            
            $conditionHandler = ConditionRegistry::get($conditionType);
    
            if ($conditionHandler === null) {
                throw new \RuntimeException("Unknown condition type: $conditionType");
                continue;  
            }
    
            $result = $conditionHandler->check($attacker, $victim, $conditionData, $context, $extraData);
    
    
            switch ($conditionMode) {
                case 'chance':
                    if ($result) {
                        $extraData['chance'] += $chanceValue;
                    }
                    break;
    
                case 'force':
                    if ($result) {
                        return true;  
                    }
                    break;
    
                case 'allow':
                    if (!$result) {
                        return false;  
                    }
                    break;
    
                case 'continue':
                    if (!$result) {
                        continue 2;  
                    }
                    break;
    
                case 'stop':
                    if ($result) {
                        return false;  
                    }
                    break;
    
                default:
                    throw new \InvalidArgumentException("Unknown condition mode: $conditionMode");
            }
        }
    
        $chance = min(100, max(0, $extraData['chance']));
    
        if ($chance > 0 && $chance >= mt_rand(0, 100)) {
            return true; 
        }
    
        return false; 
    }    

    public function applyEffects(array $effects, Entity $caster, ?Entity $target, string $triggerContext, array $additionalData = [], int $effectChance = 100): void {
        foreach ($effects as $effectConfig) {
            $effectType = strtolower($effectConfig['type'] ?? '');
            $effectClass = EffectManager::getEffectClass($effectType);

            if ($effectClass && class_exists($effectClass)) {
                if ($effectChance > 0) {
                    $randomRoll = mt_rand(1, 100);
                    if ($randomRoll > $effectChance) {
                        continue; 
                    }
                }

                $effectCooldown = $effectConfig['cooldown'] ?? 0;

                if ($effectCooldown > 0) {
                    $enchantmentId = $effectConfig['id'] ?? 'unknown';

                    if (CooldownManager::isOnCooldown($caster, $enchantmentId, $effectCooldown)) {
                        continue;
                    }

                    CooldownManager::setCooldown($caster, $enchantmentId);
                }

                /** @var EffectInterface $effect */
                $effect = new $effectClass();
                $effect->apply($caster, $target, $effectConfig, $effectConfig, $triggerContext, $additionalData);
            }
        }
    }
}
