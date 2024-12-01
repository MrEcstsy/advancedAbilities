<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\conditions;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\ConditionInterface;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class IsSneakingCondition implements ConditionInterface {

    public function check($attacker, $victim, array $data, string $context, array $extraData): bool {
        if ($attacker instanceof Player) {
            return $attacker->isSneaking();
        }
        return false;
    }
}