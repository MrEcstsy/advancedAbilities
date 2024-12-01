<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\managers;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\effects\AddPotionEffect;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\effects\StealHealthEffect;

class EffectManager {

    private static $effectMap = [
        "add_potion" => AddPotionEffect::class,
        "steal_health" => StealHealthEffect::class,
    ];

    public static function getEffectClass(string $effectType): ?string {
        return self::$effectMap[$effectType] ?? null;
    }
}
