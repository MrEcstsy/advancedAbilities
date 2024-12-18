<?php

namespace ecstsy\advancedAbilities\triggers;

use ecstsy\advancedAbilities\utils\managers\CooldownManager;
use ecstsy\advancedAbilities\utils\registries\ConditionRegistry;
use ecstsy\advancedAbilities\utils\TriggerHelper;
use ecstsy\advancedAbilities\utils\TriggerInterface;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

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
            $baseChance = $levelConfig['chance'] ?? 100;
    
            $conditionsMet = true;
            $adjustedChance = $baseChance;
            $forceTriggered = false;
    
            if (!empty($levelConfig['conditions'])) {
                foreach ($levelConfig['conditions'] as $condition) {
                    $target = $condition['target'] === 'victim' ? $victim : $attacker;
    
                    if (!$target instanceof Player) {
                        break;
                    }
    
                    $conditionData = array_merge($extraData, ['chance' => $adjustedChance]);
                    $conditionHandler = ConditionRegistry::get($condition['type']);
    
                    if ($conditionHandler === null) {
                        throw new \RuntimeException("Unknown condition type: {$condition['type']}");
                    }
    
                    $result = $conditionHandler->check($attacker, $victim, $condition, $context, $extraData);
                    $adjustedChance = $conditionData['chance'] ?? $adjustedChance;
                    $conditionMode = strtolower($condition['condition_mode'] ?? 'allow');
    
                    if ($conditionMode === 'force') {
                        if ($result) {
                            $forceTriggered = true;
                            break;
                        } else {
                            $forceTriggered = false;
                        }
                    } elseif (!$result) {
                        $conditionsMet = false;
                        break; 
                    }
                }
            }
    
            if (!$conditionsMet && !$forceTriggered) {
                continue;
            }
    
            $effectCooldown = $levelConfig['cooldown'] ?? 0;
            $enchantmentId = $enchantmentData['id'] ?? 'unknown';
    
            if (!$forceTriggered && $effectCooldown > 0 && CooldownManager::isOnCooldown($attacker, $enchantmentId)) {
                continue;
            }
    
            if ($forceTriggered) {
                $this->applyEffects($levelConfig, $attacker, $victim, $context, $extraData, $adjustedChance, $enchantmentData['id'], true);
            } else {
                $this->applyEffects($levelConfig, $attacker, $victim, $context, $extraData, $adjustedChance, $enchantmentData['id'], false);
            }            
        }
    }    
}
