<?php

namespace ecstsy\advancedAbilities\utils\managers;

use pocketmine\entity\Entity;

class CooldownManager {

    private static $cooldowns = [];

    /**
     * Check if an enchantment is on cooldown for the given entity.
     *
     * @param Entity $entity The entity whose cooldown is being checked
     * @param string $enchantmentId The ID of the enchantment
     * @param int $cooldown The cooldown time in seconds
     * @return bool True if the enchantment is on cooldown, false otherwise
     */
    public static function isOnCooldown(Entity $entity, string $enchantmentId, int $cooldown): bool
    {
        $currentTime = time();

        if (isset(self::$cooldowns[$entity->getId()][$enchantmentId])) {
            $lastAppliedTime = self::$cooldowns[$entity->getId()][$enchantmentId];

            if (($currentTime - $lastAppliedTime) < $cooldown) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the last applied time for an enchantment for a given entity.
     *
     * @param Entity $entity The entity whose cooldown is being updated
     * @param string $enchantmentId The ID of the enchantment
     */
    public static function setCooldown(Entity $entity, string $enchantmentId): void
    {
        self::$cooldowns[$entity->getId()][$enchantmentId] = time();
    }
}
