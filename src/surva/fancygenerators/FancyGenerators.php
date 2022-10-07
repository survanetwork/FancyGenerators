<?php

/**
 * FancyGenerators | plugin main class
 */

namespace surva\fancygenerators;

use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;
use surva\fancygenerators\generator\candyland\CandyLand;
use surva\fancygenerators\generator\void\VoidGenerator;
use surva\fancygenerators\generator\winterwonder\WinterWonder;

class FancyGenerators extends PluginBase
{
    protected function onEnable(): void
    {
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
    }
}
