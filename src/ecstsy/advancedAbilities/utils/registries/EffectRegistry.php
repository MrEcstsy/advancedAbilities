<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\registries;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\EffectInterface;

class EffectRegistry {
    private static array $effects = [];

    public static function register(string $name, EffectInterface $effect): void {
        self::$effects[$name] = $effect;
    }

    public static function get(string $name): ?EffectInterface {
        return self::$effects[$name] ?? null;
    }
}