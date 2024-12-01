<?php

namespace ecstsy\advancedAbilities;

use ecstsy\advancedAbilities\conditions\VictimHealthCondition;
use ecstsy\advancedAbilities\triggers\AttackTrigger;
use ecstsy\advancedAbilities\triggers\registries\TriggerRegistery;
use ecstsy\advancedAbilities\utils\registries\ConditionRegistry;
use ecstsy\advancedAbilities\utils\registries\EffectRegistry;
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
        ];

        $conditions = [
            "VICTIM_HEALTH" => new VictimHealthCondition(),
        ];

        $effects = [

        ];

        foreach ($triggers as $trigger => $handler) {
            TriggerRegistery::register($trigger, $handler);
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
