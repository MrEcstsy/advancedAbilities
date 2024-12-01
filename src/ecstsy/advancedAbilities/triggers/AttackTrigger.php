<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\triggers;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\managers\CooldownManager;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\managers\EffectManager;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\TriggerInterface;
use pocketmine\entity\Entity;

class AttackTrigger implements TriggerInterface {
    
    public function execute(Entity $attacker, ?Entity $victim, array $enchantments, string $context, array $extraData = []): void
    {
        foreach ($enchantments as $index => $enchantment) {
            $level = $extraData[$index] ?? null;
    
            if ($level === null) {
                continue; 
            }
    
            $config = $enchantment; 
    
            if (isset($config['levels'][$level])) {
                $levelConfig = $config['levels'][$level];
                if (isset($levelConfig['effects'])) {
                    foreach ($levelConfig['effects'] as $effectData) {
                        $effectType = strtolower($effectData['type'] ?? '');
                        
                        $effectClass = EffectManager::getEffectClass($effectType);
    
                        if ($effectClass && class_exists($effectClass)) {
                            $chance = $levelConfig['chance'] ?? 0;
                            if ($chance > 0) {
                                $randomChance = mt_rand(1, 100);
                                if ($randomChance > $chance) {
                                    continue; 
                                }
                            }
    
                            $cooldown = $levelConfig['cooldown'] ?? 0;
                            if ($cooldown > 0) {
                                $enchantmentId = $effectData['id'] ?? 'unknown';
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
}