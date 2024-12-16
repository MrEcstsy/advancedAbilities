<?php

namespace ecstsy\advancedAbilities\utils;

use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;

class Utils {

    private static array $configCache = [];

    private static array $formatHandlers = [
        'yml' => Config::YAML,
        'yaml' => Config::YAML,
        'json' => Config::JSON,
    ];
    
    public static function getConfiguration(Plugin $plugin, string $fileName): ?Config {
        $filePath = $plugin->getDataFolder() . $fileName;
    
        if (isset(self::$configCache[$filePath])) {
            return self::$configCache[$filePath];
        }
    
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!isset(self::$formatHandlers[$extension])) {
            $plugin->getLogger()->warning("Unsupported configuration file format for '$filePath'.");
            return null;
        }
    
        if (!file_exists($filePath)) {
            $plugin->getLogger()->warning("Configuration file '$filePath' not found.");
            return null;
        }
    
        $config = new Config($filePath, self::$formatHandlers[$extension]);
        self::$configCache[$filePath] = $config;
        return $config;
    }

    public static function extractAbilitiesFromItems(array $items, Plugin $plugin): array {
        $abilitiesConfig = self::getConfiguration($plugin, "abilities.yml")?->getAll();
        if (!$abilitiesConfig) return [];
        
        $abilities = [];
        foreach ($items as $item) {
            if ($item->isNull()) continue;
    
            foreach ($item->getEnchantments() as $enchantmentData) {
                $identifier = strtolower($enchantmentData->getType()->getName());
                if (isset($abilitiesConfig[$identifier])) {
                    $level = $enchantmentData->getLevel();
                    $config = $abilitiesConfig[$identifier];
                    $config['level'] = $level;  
                    $abilities[] = [
                        'id' => $identifier,
                        'level' => $level,
                        'config' => $config
                    ];
                }
            }
        }
    
        return $abilities;
    }

    public static function processEffects(array $abilities, string $trigger): array {
        $effects = [];
        foreach ($abilities as $ability) {
            $config = $ability['config'];
            if (in_array($trigger, $config['type'], true)) {
                $levelData = $config['levels'][$ability['level']] ?? [];
                $effects = array_merge($effects, $levelData['effects'] ?? []);
            }
        }
        return $effects;
    }

    public static function parseRandomNumber(string $level): int {
        if (preg_match('/\{(\d+)-(\d+)\}/', $level, $matches)) {
            $min = (int) $matches[1];
            $max = (int) $matches[2];
            return mt_rand($min, $max);
        }
        return (int) $level;
    }

    public static function parseDynamicMessage(string $message): string {
        $pattern = "/<random_word>(.*?)<\/random_word>/";
        preg_match($pattern, $message, $matches);

        if (isset($matches[1])) {
            $wordCandidates = explode(",", $matches[1]);
            $randomIndex = mt_rand(0, count($wordCandidates) - 1);
            $word = $wordCandidates[$randomIndex];

            return str_replace("<random_word>" . $matches[1] . "</random_word>", $word, $message);
        }

        return $message;
    }

    public static function getEffectsFromItems(array $items, string $trigger, Config $config): array {
        return self::extractEnchantments($items, function ($itemData) use ($trigger, $config) {
            $identifier = strtolower($itemData->getType()->getName());
            $configData = $config->get($identifier);
            if ($configData && in_array($trigger, $configData['type'], true)) {
                return $configData['levels'][$itemData->getLevel()]['effects'] ?? [];
            }
            return [];
        });
    }    
    
    public static function getConditionsFromItems(array $items, string $trigger, Config $config): array {
        return self::extractEnchantments($items, function ($itemData) use ($trigger, $config) {
            $identifier = strtolower($itemData->getType()->getName());
            $configData = $config->get($identifier);
            if ($configData && in_array($trigger, $configData['type'], true)) {
                return $configData['levels'][$itemData->getLevel()]['conditions'] ?? [];
            }
        });
    }

    public static function getEnchantmentsFromConfig(Plugin $plugin, string $identifier): array {
        $config = self::getConfiguration($plugin, "enchantments.yml");
        return $config?->get($identifier, []) ?? [];
    }
    
    public static function extractEnchantments(array $items, callable $callback): array {
        $result = [];
        foreach ($items as $item) {
            if ($item->isNull()) continue;
    
            foreach ($item->getEnchantments() as $enchantmentData) {
                $result[] = $callback($enchantmentData, $item);
            }
        }
        return $result;
    }
    
    public static function getEffectsFromEnchantments(array $enchantments, array $config, string $trigger): array {
        $effects = [];
        foreach ($enchantments as $enchantment) {
            $identifier = strtolower($enchantment['id']);
            if (isset($config[$identifier])) {
                $levels = $config[$identifier]['levels'][$enchantment['level']] ?? [];
                if (in_array($trigger, $levels['triggers'], true)) {
                    $effects = array_merge($effects, $levels['effects']);
                }
            }
        }
        return $effects;
    }
    
    /**
     * Extract enchantments from items and enrich them with their configuration.
     * 
     * @param Item[] $items
     * @return array
     */
    public static function extractEnchantmentsFromItems(Plugin $plugin, array $items): array {
        $enchantmentsToApply = [];
        $enchantmentConfigs = self::getConfiguration($plugin, "enchantments.yml")->getAll(); 
    
        foreach ($items as $item) {
            if ($item->isNull()) continue;
    
            $itemEnchantments = $item->getEnchantments();
            
            foreach ($itemEnchantments as $enchantmentData) {
                $enchantmentId = strtolower($enchantmentData->getType()->getName());
    
                if (isset($enchantmentConfigs[$enchantmentId])) {
                    $level = $enchantmentData->getLevel();
                    $enchantmentConfig = $enchantmentConfigs[$enchantmentId];
                    $enchantmentConfig['level'] = $level;  
                    $enchantmentsToApply[] = [
                        'id' => $enchantmentId,
                        'level' => $level, 
                        'config' => $enchantmentConfig
                    ];
                }
            }
        }
    
        return $enchantmentsToApply;
    }
}
