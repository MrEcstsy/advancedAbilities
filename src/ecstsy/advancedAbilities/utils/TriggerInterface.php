<?php

namespace ecstsy\advancedAbilities\utils;

use pocketmine\entity\Entity;

interface TriggerInterface {
    public function execute(Entity $attacker, ?Entity $victim, array $enchantments, string $context, array $exteraData = []): void ;
}
