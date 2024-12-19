<?php

namespace ecstsy\advancedAbilities\utils\managers;

use ecstsy\advancedAbilities\effects\ActionBarEffect;
use ecstsy\advancedAbilities\effects\AddAirEffect;
use ecstsy\advancedAbilities\effects\AddFoodEffect;
use ecstsy\advancedAbilities\effects\AddHealthEffect;
use ecstsy\advancedAbilities\effects\AddPotionEffect;
use ecstsy\advancedAbilities\effects\BloodEffect;
use ecstsy\advancedAbilities\effects\BurnEffect;
use ecstsy\advancedAbilities\effects\DisableActivationEffect;
use ecstsy\advancedAbilities\effects\StealHealthEffect;

class EffectManager {

    private static $effectMap = [
        "action_bar" => ActionBarEffect::class,
        "add_air" => AddAirEffect::class,
        "add_food" => AddFoodEffect::class,
        "add_health" => AddHealthEffect::class,
        "blood" => BloodEffect::class,
        "burn" => BurnEffect::class,
        "disable_activation" => DisableActivationEffect::class,
        "add_potion" => AddPotionEffect::class,
        "steal_health" => StealHealthEffect::class,
        
    ];

    public static function getEffectClass(string $effectType): ?string {
        return self::$effectMap[$effectType] ?? null;
    }
}
