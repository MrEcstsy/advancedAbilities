<?php

namespace ecstsy\advancedAbilities\utils;

use pocketmine\entity\Entity;

interface ConditionInterface {
    public function check(Entity $attacker, ?Entity $victim, array $conditionData, string $context, array $extraData): bool;
}
