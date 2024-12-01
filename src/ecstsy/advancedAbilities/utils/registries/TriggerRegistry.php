<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\triggers\registries;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\TriggerInterface;
use InvalidArgumentException;

class TriggerRegistery {

    private static array $triggers = [];

    public static function register(string $name, TriggerInterface $trigger): void {
        self::$triggers[$name] = $trigger;
    }

    public static function get(string $name): ?TriggerInterface {
        return self::$triggers[$name] ?? null;
    }
}