<?php

/**
 * FancyGenerators | plugin main class
 */

namespace surva\fancygenerators;

use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;
use RuntimeException;
use surva\fancygenerators\generator\candyland\CandyLand;
use surva\fancygenerators\generator\pirateislands\PirateIslands;
use surva\fancygenerators\generator\void\VoidGenerator;
use surva\fancygenerators\generator\winterwonder\WinterWonder;

class FancyGenerators extends PluginBase
{
    /**
     * @var \surva\fancygenerators\FancyGenerators|null plugin main class instance
     */
    private static ?FancyGenerators $instance = null;

    protected function onEnable(): void
    {
        self::$instance = $this;

        $this->registerGenerators();
    }

    /**
     * Register all custom generators of this plugin
     */
    private function registerGenerators(): void
    {
        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, VoidGenerator::NAME, fn() => null);
        GeneratorManager::getInstance()->addGenerator(CandyLand::class, CandyLand::NAME, fn() => null);
        GeneratorManager::getInstance()->addGenerator(WinterWonder::class, WinterWonder::NAME, fn() => null);
        GeneratorManager::getInstance()->addGenerator(PirateIslands::class, PirateIslands::NAME, fn() => null);
    }

    /**
     * @return \surva\fancygenerators\FancyGenerators
     */
    public static function getInstance(): FancyGenerators
    {
        if (self::$instance === null) {
            throw new RuntimeException("Plugin main class instance not initialized");
        }

        return self::$instance;
    }
}
