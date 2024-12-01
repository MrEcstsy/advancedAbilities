<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\effects;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\EffectInterface;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;

class AddPotionEffect implements EffectInterface {

    public function apply(Entity $attacker, ?Entity $victim, array $data, array $effectData, string $context, array $extraData): void
    {
        $target = $effectData['target'] === "attacker" ? $attacker : $victim;

        if ($target === null) return;

        $potion = StringToEffectParser::getInstance()->parse($effectData['potion'] ?? '');
        
        if ($potion === null) return;

        $amplifier = (int) ($effectData['amplifier'] ?? 0);
        $duration = (int) ($effectData['duration'] ?? 2147483647);

        if ($target instanceof Living) {
            $target->getEffects()->add(new EffectInstance($potion, $duration, $amplifier));
        }
    }
}