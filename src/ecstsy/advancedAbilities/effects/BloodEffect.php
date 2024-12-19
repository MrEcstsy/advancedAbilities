<?php

namespace ecstsy\advancedAbilities\effects;

use ecstsy\advancedAbilities\utils\EffectInterface;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\world\particle\BlockBreakParticle;

class BloodEffect implements EffectInterface {

    public function apply(Entity $attacker, ?Entity $victim, array $data, array $effectData, string $context, array $extraData): void
    {
        $target = $effectData['target'] === 'victim' ? $victim : $attacker;

        if (!$target instanceof Living) {
            return;
        }

        $target->getWorld()->addParticle($target->getPosition()->asVector3(), new BlockBreakParticle(VanillaBlocks::REDSTONE()));
    }
}
