<?php

/**
 * FancyGenerators | CandyLand generator
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
use surva\fancygenerators\generator\candyland\populator\CandyTreePopulator;

class CandyLand extends Generator
{
    public const NAME = "candyland";

    private const EMPTY_HEIGHT = 64;
    private const MIN_HEIGHT = 16;
    private const COLOR_MIN = 1;
    private const COLOR_MAX = 16;

    /**
     * @var \pocketmine\world\generator\populator\Populator[] populators
     */
    private array $populators = [];

    private Simplex $noiseBase;

    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset);

        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);

        $treePop            = new CandyTreePopulator();
        $this->populators[] = $treePop;
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        $noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

        $chunk = $world->getChunk($chunkX, $chunkZ);

        $bedrock = VanillaBlocks::BEDROCK()->getFullId();

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                for ($y = 0; $y < 128; ++$y) {
                    if ($y === 0) {
                        $chunk->setFullBlock($x, $y, $z, $bedrock);

                        continue;
                    }

                    $noiseValue = $noise[$x][$z][$y] - 1 / self::EMPTY_HEIGHT * ($y - self::EMPTY_HEIGHT
                                                                                 - self::MIN_HEIGHT);

                    if ($noiseValue > 0) {
                        $stainedClay = VanillaBlocks::STAINED_CLAY();
                        $stainedClay->setColor(self::getRandomBlockColor());

                        $chunk->setFullBlock($x, $y, $z, $stainedClay->getFullId());
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
     * @return \pocketmine\block\utils\DyeColor
     */
    public static function getRandomBlockColor(): DyeColor
    {
        $colId = rand(self::COLOR_MIN, self::COLOR_MAX);

        return match ($colId) {
            1 => DyeColor::BLACK(),
            2 => DyeColor::BLUE(),
            3 => DyeColor::BROWN(),
            4 => DyeColor::CYAN(),
            5 => DyeColor::GRAY(),
            6 => DyeColor::GREEN(),
            7 => DyeColor::LIGHT_BLUE(),
            8 => DyeColor::LIGHT_GRAY(),
            9 => DyeColor::LIME(),
            10 => DyeColor::MAGENTA(),
            11 => DyeColor::ORANGE(),
            12 => DyeColor::PINK(),
            13 => DyeColor::PURPLE(),
            14 => DyeColor::RED(),
            15 => DyeColor::WHITE(),
            16 => DyeColor::YELLOW()
        };
    }
}
