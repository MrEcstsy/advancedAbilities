<?php

namespace ecstsy\advancedAbilities\conditions;

use ecstsy\advancedAbilities\utils\ConditionInterface;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class IsSneakingCondition implements ConditionInterface {

    public function check($attacker, $victim, array $data, string $context, array $extraData): bool {
        $target = $data['target'] ?? null;
        $entity = ($target === 'attacker') ? $attacker : ($target === 'victim' ? $victim : null);

        if (!$entity instanceof Player) {
            return false;
        }

        return false;
    }
}
