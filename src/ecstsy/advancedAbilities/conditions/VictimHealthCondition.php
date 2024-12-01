<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\conditions;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\ConditionInterface;
use pocketmine\entity\Entity;

class VictimHealthCondition implements ConditionInterface {
    public function check(Entity $attacker, ?Entity $victim, array $data): bool
    {
        $greaterThan = $data['greater-than'] ?? 0;
        return $victim->getHealth() > $greaterThan;
    }
}