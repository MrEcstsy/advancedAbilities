<?php

namespace ecstsy\advancedAbilities\conditions;

use ecstsy\advancedAbilities\utils\ConditionInterface;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class IsSneakingCondition implements ConditionInterface {

    public function check(Entity $attacker, ?Entity $victim, array $conditionData, string $context, array $extraData): bool
    {
        if ($attacker instanceof Player) {
            return $attacker->isSneaking();  
        }

        return false;
    }
}
