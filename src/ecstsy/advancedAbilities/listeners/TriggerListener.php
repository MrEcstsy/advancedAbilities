<?php

namespace ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\listeners;

use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\AdvancedAbilityHandler;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\triggers\DefenseTrigger;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\triggers\GenericTrigger;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\TriggerHelper;
use ecstsy\AdvancedEnchantments\libs\ecstsy\advancedAbilities\utils\Utils;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class TriggerListener implements Listener {

    private Plugin $plugin;

    use TriggerHelper;
    
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onPlayerAttack(EntityDamageByEntityEvent $event): void {
        if ($event->isCancelled()) {
            return;
        }
    
        $attacker = $event->getDamager();
        $victim = $event->getEntity();
    
        if (!$attacker instanceof Player || $attacker->getInventory()->getItemInHand()->isNull()) {
            return;
        }
    
        $item = $attacker->getInventory()->getItemInHand();
        $enchantments = Utils::extractEnchantmentsFromItems($this->plugin, [$item]);
    
        if (empty($enchantments)) {
            return;
        }
    
        foreach ($enchantments as &$enchantmentConfig) {
            $level = $enchantmentConfig['level'] ?? 1;
            $chance = $enchantmentConfig['config']['levels'][$level]['chance'] ?? 100;
            if ($level !== null) {
                $extraData = ['enchant-level' => $level, "chance" => $chance];
            }
        }
        
        $trigger = new GenericTrigger();
        $trigger->execute($attacker, $victim, $enchantments, 'ATTACK', $extraData);
        
    }    
    
    public function onPlayerDefend(EntityDamageByEntityEvent $event): void {
        if ($event->isCancelled()) {
            return;
        }
    
        $caster = $event->getEntity();
        $attacker = $event->getDamager();
    
        if (!$caster instanceof Living) {
            return;
        }
    
        $armorItems = $caster->getArmorInventory()->getContents();
        $enchantmentsToApply = Utils::extractEnchantmentsFromItems($this->plugin, $armorItems);
    
        foreach ($enchantmentsToApply as &$enchantmentConfig) {
            $level = $enchantmentConfig['level'] ?? 1;
            if ($level !== null) {
                $enchantmentConfig['enchant-level'] = $level;
            }
        }
    
        if (!empty($enchantmentsToApply)) {
            $trigger = new DefenseTrigger();
            $trigger->execute($attacker, $caster, $enchantmentsToApply, 'DEFENSE', ["enchant-level" => $level]);
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        $config = Utils::getConfiguration($this->plugin, "enchantments.yml");
    
        if (!$entity instanceof Living) {
            return;
        }
    
        $armorItems = $entity->getArmorInventory()->getContents();
        $effects = Utils::getEffectsFromItems($armorItems, "FALL_DAMAGE", $config);
    
        if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
            foreach ($effects as $effectGroup) {
                foreach ($effectGroup as $effect) {
                    if (isset($effect['type']) && $effect['type'] === "CANCEL_EVENT") {
                        $chance = isset($effect['chance']) ? $effect['chance'] : 100;
    
                        if (mt_rand(0, 100) <= $chance) {
                            $extraData = ['chance' => 0];  
                            $conditionsMet = $this->handleConditions($effect['conditions'] ?? [], $entity, null, "FALL_DAMAGE", $extraData);
    
                            if ($conditionsMet) {
                                $event->cancel();
                                break 2; 
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function onEntityDamageDecrease(EntityDamageByEntityEvent $event): void {
        $victim = $event->getEntity();
        $attacker = $event->getDamager();
        
        if ($event->isCancelled()) {
            return;
        }
    
        if (!$victim instanceof Living || !$attacker instanceof Player) {
            return;
        }
    
        $config = Utils::getConfiguration($this->plugin, "enchantments.yml");
        $armorItems = $victim->getArmorInventory()->getContents();
        $effects = Utils::getEffectsFromItems($armorItems, "DEFENSE", $config);
        $conditions = Utils::getConditionsFromItems($armorItems, "DEFENSE", $config);
    
        foreach ($effects as $effectGroup) {
            foreach ($effectGroup as $effect) {
                if ($effect['type'] === "DECREASE_DAMAGE") {
                    foreach ($conditions as $conditionGroup) {
                        foreach ($conditionGroup as $condition) {
                            $chance = isset($effectGroup['chance']) ? $effectGroup['chance'] : 100;
                            $extraData = ['chance' => $chance];
                            $conditionsMet = $this->handleConditions($condition, $attacker, $victim, "DEFENSE", $extraData);
    
                            if ($conditionsMet) {
                                $finalDamage = $event->getFinalDamage();
                                $percentageReduction = $effectConfig['amount'] ?? 0; 
                                $damageReduction = $finalDamage * ($percentageReduction / 100);
                        
                                $event->setBaseDamage($event->getBaseDamage() - $damageReduction);
                            }
                        }
                    }
                }
            }
        }
    }    
}