<?php

namespace ecstsy\advancedAbilities;

use ecstsy\advancedAbilities\conditions\IsHoldingCondition;
use ecstsy\advancedAbilities\conditions\IsSneakingCondition;
use ecstsy\advancedAbilities\effects\ActionBarEffect;
use ecstsy\advancedAbilities\effects\AddAirEffect;
use ecstsy\advancedAbilities\effects\AddFoodEffect;
use ecstsy\advancedAbilities\effects\AddHealthEffect;
use ecstsy\advancedAbilities\effects\AddPotionEffect;
use ecstsy\advancedAbilities\effects\BloodEffect;
use ecstsy\advancedAbilities\effects\BurnEffect;
use ecstsy\advancedAbilities\effects\DisableActivationEffect;
use ecstsy\advancedAbilities\effects\StealHealthEffect;
use ecstsy\advancedAbilities\listeners\TriggerListener;
use ecstsy\advancedAbilities\triggers\AttackTrigger;
use ecstsy\advancedAbilities\triggers\DefenseTrigger;
use ecstsy\advancedAbilities\utils\registries\ConditionRegistry;
use ecstsy\advancedAbilities\utils\registries\EffectRegistry;
use ecstsy\advancedAbilities\utils\registries\TriggerRegistry;
use InvalidArgumentException;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

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
            'ADD_POTION' => new AddPotionEffect(),
            'ACTION_BAR' => new ActionBarEffect(),
            'ADD_AIR' => new AddAirEffect(),
            'ADD_FOOD' => new AddFoodEffect(),
            'ADD_HEALTH' => new AddHealthEffect(),
            'BLOOD' => new BloodEffect(),
            'BURN' => new BurnEffect(),
            'DISABLE_ACTIVATION' => new DisableActivationEffect(),
            'STEAL_HEALTH' => new StealHealthEffect(),
        ];

        $listeners = [
            new TriggerListener($plugin)
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

        foreach ($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents($listener, $plugin);
        }
    }

    public static function isRegistered(): bool {
        return self::$plugin instanceof Plugin;
    }
}
