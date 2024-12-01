<?php

namespace ecstsy\advancedAbilities\utils\registries;

use ecstsy\advancedAbilities\utils\TriggerInterface;
use InvalidArgumentException;

class TriggerRegistry {

    private static array $triggers = [];

    public static function register(string $name, TriggerInterface $trigger): void {
        self::$triggers[$name] = $trigger;
    }

    public static function get(string $name): ?TriggerInterface {
        return self::$triggers[$name] ?? null;
    }
}
