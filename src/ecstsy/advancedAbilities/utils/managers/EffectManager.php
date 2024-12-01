<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\managers;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\effects\AddPotionEffect;

class EffectManager {

    private static $effectMap = [
        "add_potion" => AddPotionEffect::class,

    ];

    public static function getEffectClass(string $effectType): ?string {
        return self::$effectMap[strtolower($effectType)] ?? null;
    }
}