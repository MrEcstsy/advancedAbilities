<?php

namespace ecstsy\advancedAbilities\utils;

use ecstsy\advancedAbilities\utils\managers\EffectManager;
use ecstsy\advancedAbilities\utils\registries\ConditionRegistry;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

trait TriggerHelper {

    public function handleConditions(array $condition, Entity $attacker, ?Entity $victim, string $context, array $extraData = []): bool {
        $target = $condition['target'] === 'victim' ? $victim : $attacker;
    
        if (!$target instanceof Player) {
            return false; 
        }

        $conditionType = $condition['type'] ?? '';
        $conditionMode = strtolower($condition['condition_mode'] ?? 'allow');

        var_dump("Evaluating condition: {$conditionType} for target " . $target->getName() ?: "non-player");

        $conditionHandler = ConditionRegistry::get($conditionType);

        if ($conditionHandler === null) {
            throw new \RuntimeException("Unknown condition type: $conditionType");
        }

        $result = $conditionHandler->check($attacker, $victim, $condition, $context, $extraData);

        var_dump("Condition '{$conditionType}' result: " . ($result ? "true" : "false"));

        switch ($conditionMode) {
            case 'force':
                if ($result) {
                    var_dump("Condition '{$conditionType}' triggered FORCE.");
                    return true; 
                }
                break;

            case 'allow':
                if (!$result) {
                    return false; 
                }
                break;

            case 'continue':
                return true; 
                break;

            case 'stop':
                if ($result) {
                    return false; 
                }
                break;

            case 'chance':
                if ($result) {
                    $chanceAdjustment = $condition['chance'] ?? 0;
                    $extraData['chance'] = ($extraData['chance'] ?? 100) + $chanceAdjustment;
                }
                break;

            default:
                throw new \InvalidArgumentException("Unknown condition mode: $conditionMode");
        }

        return true;
    }   

    public function applyEffects(array $effects, Entity $caster, ?Entity $target, string $triggerContext, array $additionalData = [], int $effectChance = 100): void {
        foreach ($effects as $effectConfig) {
            $effectType = strtolower($effectConfig['type'] ?? '');
            $effectClass = EffectManager::getEffectClass($effectType);

            if ($effectClass && class_exists($effectClass)) {
                if ($effectChance > 0) {
                    $randomRoll = mt_rand(1, 100);
                    if ($randomRoll > $effectChance) {
                        var_dump("Effect '{$effectType}' skipped due to chance ({$effectChance}%).");
                        continue; 
                    }
                }

                /** @var EffectInterface $effect */
                $effect = new $effectClass();
                $effect->apply($caster, $target, $effectConfig, $effectConfig, $triggerContext, $additionalData);
            }
        }
    }
}
