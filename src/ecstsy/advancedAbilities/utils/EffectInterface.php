<?php

namespace ecstsy\advancedAbilities\utils;

use pocketmine\entity\Entity;

interface EffectInterface {
    public function apply(Entity $attacker, ?Entity $victim, array $data, array $effectData, string $context, array $extraData): void;
}
