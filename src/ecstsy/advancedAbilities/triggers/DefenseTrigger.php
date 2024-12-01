<?php

namespace ecstsy\advancedAbilities\triggers;

use ecstsy\advancedAbilities\utils\managers\CooldownManager;
use ecstsy\advancedAbilities\utils\managers\EffectManager;
use ecstsy\advancedAbilities\utils\registries\ConditionRegistry;
use ecstsy\advancedAbilities\utils\TriggerInterface;
use pocketmine\entity\Entity;

class DefenseTrigger implements TriggerInterface {

    public function execute(Entity $attacker, ?Entity $victim, array $enchantments, string $context, array $extraData = []): void
    {
        foreach ($enchantments as $index => $enchantment) {
            $level = $extraData['enchant-level'] ?? null;
            if ($level === null) {
                continue; 
            }
    
            $config = $enchantment;
            if (!isset($config['levels'][$level])) {
                continue;
            }
    
            $levelConfig = $config['levels'][$level];
            $chance = $levelConfig['chance'] ?? 0;
    
            if (isset($levelConfig['conditions'])) {
                foreach ($levelConfig['conditions'] as $conditionData) {
                    $conditionType = $conditionData['type'] ?? '';
                    $conditionMode = $conditionData['condition_mode'] ?? 'allow';
                    $chanceValue = $conditionData['chance'] ?? 0;
    
                    $conditionHandler = ConditionRegistry::get($conditionType);

                    if ($conditionHandler === null) {
                        error_log("Unknown condition type: $conditionType");
                        continue;
                    }
    
                    $result = $conditionHandler->check($attacker, $victim, $conditionData, $context, $extraData);
    
                    switch ($conditionMode) {
                        case 'chance':
                            if ($result) {
                                $chance += $chanceValue;
                            }
                            break;
                        case 'allow':
                            if (!$result) {
                                return;
                            }
                            break;
    
                        case 'continue':
                            if (!$result) {
                                continue 2; 
                            }
                            break;
    
                        case 'stop':
                            if ($result) {
                                return; 
                            }
                            break;
                        case 'force':
                            if ($result) {
                                $chance = 100; 
                            }
                            break;
    
                        default:
                            error_log("Unknown condition mode: $conditionMode");
                    }
                }
            }
    
            if (isset($levelConfig['effects'])) {
                foreach ($levelConfig['effects'] as $effectData) {
                    $effectType = strtolower($effectData['type'] ?? '');
                    $effectClass = EffectManager::getEffectClass($effectType);
    
                    if ($effectClass && class_exists($effectClass)) {
                        if ($chance > 0) {
                            $randomChance = mt_rand(1, 100);
                            if ($randomChance > $chance) {
                                continue; 
                            }
                        }
    
                        $cooldown = $levelConfig['cooldown'] ?? 0;
                        if ($cooldown > 0) {
                            $enchantmentId = $levelConfig['id'] ?? 'unknown';
                            if (CooldownManager::isOnCooldown($attacker, $enchantmentId, $cooldown)) {
                                continue;
                            }
                            CooldownManager::setCooldown($attacker, $enchantmentId);
                        }
    
                        /** @var EffectInterface $effect */
                        $effect = new $effectClass();
                        $effect->apply($attacker, $victim, $config, $effectData, $context, $extraData);
                    } else {
                        error_log("Effect type $effectType does not have a class mapping.");
                    }
                }
            }
        }
    }
}
