<?php

namespace ecstsy\advancedAbilities\utils\managers;

use ecstsy\advancedAbilities\effects\AddPotionEffect;
use ecstsy\advancedAbilities\effects\StealHealthEffect;

class EffectManager {

    private static $effectMap = [
        "add_potion" => AddPotionEffect::class,
        "steal_health" => StealHealthEffect::class,
    ];

    public static function getEffectClass(string $effectType): ?string {
        return self::$effectMap[$effectType] ?? null;
    }
}
