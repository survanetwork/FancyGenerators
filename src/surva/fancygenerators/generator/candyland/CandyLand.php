<?php

/**
 * FancyGenerators | CandyLand generator, creates a world made of colored clay
 * and populated with candy trees
 *
 * most of the generation code adapted from PocketMine's Normal and Nether generators
 * https://github.com/pmmp/PocketMine-MP/blob/stable/src/world/generator/normal/Normal.php
 * https://github.com/pmmp/PocketMine-MP/blob/stable/src/world/generator/hell/Nether.php
 */

namespace surva\fancygenerators\generator\candyland;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\world\generator\populator\Populator;
use surva\fancygenerators\FancyGenerators;
use surva\fancygenerators\generator\candyland\populator\CandyTreePopulator;

class CandyLand extends Generator
{
    public const NAME = "candyland";

    private const EMPTY_HEIGHT = 64;
    private const MIN_HEIGHT = 16;
    private const COLOR_MIN = 0;
    private const COLOR_MAX = 15;

    /**
     * @var Populator[] populators
     */
    private array $populators = [];

    private Simplex $noiseBase;

    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset);

        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);

        $treePop = new CandyTreePopulator();
        $this->populators[] = $treePop;
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        $noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

        $chunk = $world->getChunk($chunkX, $chunkZ);

        if ($chunk === null) {
            FancyGenerators::getInstance()->getLogger()->error("Cannot generate chunk: chunk to generate is null");

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
                        $stainedClay = VanillaBlocks::STAINED_CLAY();
                        $stainedClay->setColor(self::getRandomBlockColor());

                        $chunk->setBlockStateId($x, $y, $z, $stainedClay->getStateId());
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
     * Get a random color for a block
     *
     * @return DyeColor
     */
    public static function getRandomBlockColor(): DyeColor
    {
        $colId = rand(self::COLOR_MIN, self::COLOR_MAX);

        return DyeColor::cases()[$colId];
    }
}
