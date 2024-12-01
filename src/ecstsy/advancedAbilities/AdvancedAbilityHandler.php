<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\conditions\IsHoldingCondition;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\conditions\IsSneakingCondition;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\effects\AddPotionEffect;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\effects\StealHealthEffect;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\triggers\AttackTrigger;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\triggers\DefenseTrigger;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\registries\ConditionRegistry;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\registries\EffectRegistry;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\registries\TriggerRegistry;
use InvalidArgumentException;
use pocketmine\plugin\Plugin;

final class AdvancedAbilityHandler {
    
    private static ?Plugin $plugin = null;
    
    public static function register(Plugin $plugin): void {
        if (self::isRegistered()) {
            throw new InvalidArgumentException("{$plugin->getName()} attempted to register " . self::class . " twice.");
        }

        $triggers = [
            "ATTACK" => new AttackTrigger(),
            "DEFENSE" => new DefenseTrigger(),
        ];

        $conditions = [
            //"VICTIM_HEALTH" => new VictimHealthCondition(),
            "IS_SNEAKING" => new IsSneakingCondition(),
            "IS_HOLDING" => new IsHoldingCondition(),
        ];

        $effects = [
            'STEAL_HEALTH' => new StealHealthEffect(),
            'ADD_POTION' => new AddPotionEffect(),
        ];

        foreach ($triggers as $trigger => $handler) {
            TriggerRegistry::register($trigger, $handler);
        }

        foreach ($conditions as $condition => $handler) {
            ConditionRegistry::register($condition, $handler);
        }

        foreach ($effects as $effect => $handler) {
            EffectRegistry::register($effect, $handler);
        }
    }

    public static function isRegistered(): bool {
        return self::$plugin instanceof Plugin;
    }
}
