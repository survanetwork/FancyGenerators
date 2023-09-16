<?php

/**
 * FancyGenerators | WinterWonder generator
 *
 * most of the generation code adapted from PocketMine's Normal and Nether generators
 * https://github.com/pmmp/PocketMine-MP/blob/stable/src/world/generator/normal/Normal.php
 * https://github.com/pmmp/PocketMine-MP/blob/stable/src/world/generator/hell/Nether.php
 */

namespace surva\fancygenerators\generator\winterwonder;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use surva\fancygenerators\FancyGenerators;
use surva\fancygenerators\generator\winterwonder\populator\ChristmasTreePopulator;
use surva\fancygenerators\generator\winterwonder\populator\GiftPopulator;

class WinterWonder extends Generator
{
    public const NAME = "winterwonder";

    private const EMPTY_HEIGHT = 16;
    private const MIN_HEIGHT = 32;

    /**
     * @var \pocketmine\world\generator\populator\Populator[] populators
     */
    private array $populators;

    private Simplex $noiseBase;

    private int $fullSnow;
    private int $fullWood;
    private int $fullRedWool;
    private int $fullGreenWool;

    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset);

        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 32);

        $this->fullSnow = VanillaBlocks::SNOW()->getStateId();
        $this->fullWood = VanillaBlocks::SPRUCE_WOOD()->getStateId();
        $this->fullRedWool = VanillaBlocks::WOOL()->setColor(DyeColor::RED())->getStateId();
        $this->fullGreenWool = VanillaBlocks::WOOL()->setColor(DyeColor::GREEN())->getStateId();

        $treePop = new ChristmasTreePopulator();
        $giftPop = new GiftPopulator();

        $this->populators = [$treePop, $giftPop];
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        $noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

        $chunk = $world->getChunk($chunkX, $chunkZ);

        if ($chunk === null) {
            FancyGenerators::getInstance()->getLogger()->error(
                "WinterWonder generator cannot generate chunk, chunk was null!"
            );

            return;
        }

        $bedrock = VanillaBlocks::BEDROCK()->getStateId();

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                for ($y = 0; $y < 128; ++$y) {
                    if ($y === 0) {
                        $chunk->setBlockStateId($x, $y, $z, $bedrock);

                        continue;
                    }

                    $noiseValue = $noise[$x][$z][$y] - 1 / self::EMPTY_HEIGHT * ($y - self::EMPTY_HEIGHT
                                                                                 - self::MIN_HEIGHT);

                    if ($noiseValue > 0) {
                        $chunk->setBlockStateId($x, $y, $z, $this->getRandomWinterBlock());
                    }
                }
            }
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        foreach ($this->populators as $populator) {
            $populator->populate($world, $chunkX, $chunkZ, $this->random);
        }
    }

    /**
     * Get random block for winter ground
     *
     * @return int
     */
    public function getRandomWinterBlock(): int
    {
        $randId = rand(0, 10);

        if ($randId < 7) {
            return $this->fullSnow;
        } elseif ($randId < 8) {
            return $this->fullWood;
        } elseif ($randId < 9) {
            return $this->fullRedWool;
        } else {
            return $this->fullGreenWool;
        }
    }
}
