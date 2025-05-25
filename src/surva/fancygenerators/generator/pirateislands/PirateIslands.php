<?php

/**
 * FancyGenerators | PirateIslands generator
 */

namespace surva\fancygenerators\generator\pirateislands;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\PalettedBlockArray;
use pocketmine\world\format\SubChunk;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use surva\fancygenerators\FancyGenerators;
use surva\fancygenerators\generator\pirateislands\populator\JungleTreePopulator;

class PirateIslands extends Generator
{
    public const NAME = "pirateislands";

    // base chunk y values
    private const GROUND_CHUNK = 2;
    private const SANDSTONE_UNTIL = 1;
    private const SAND_UNTIL = 5;
    private const WATER_UNTIL = 8;

    // island spacing values
    private const ISLAND_CELL_SIZE = 96;
    private const ISLAND_RADIUS = 64;

    private Simplex $noiseBase;
    /**
     * @var \pocketmine\world\generator\populator\Populator[] populators
     */
    private array $populators = [];

    private SubChunk $baseGroundSubChunk;

    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset);

        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);
        $this->populators[] = new JungleTreePopulator();

        $this->generateBaseGroundSubChunk();
    }

    /**
     * Generate a basic "beach-like" ground sub chunk
     */
    private function generateBaseGroundSubChunk(): void
    {
        // @phpstan-ignore class.notFound
        $subChunk = new SubChunk(Block::EMPTY_STATE_ID, [], new PalettedBlockArray(BiomeIds::OCEAN));

        $sandStone = VanillaBlocks::SANDSTONE()->getStateId();
        $sand = VanillaBlocks::SAND()->getStateId();
        $water = VanillaBlocks::WATER()->getStateId();

        for ($y = 0; $y < self::WATER_UNTIL; $y++) {
            for ($z = 0; $z < 16; $z++) {
                for ($x = 0; $x < 16; $x++) {
                    if ($y < self::SANDSTONE_UNTIL) {
                        $subChunk->setBlockStateId($x, $y, $z, $sandStone);
                    } elseif ($y < self::SAND_UNTIL) {
                        $subChunk->setBlockStateId($x, $y, $z, $sand);
                    } else {
                        $subChunk->setBlockStateId($x, $y, $z, $water);
                    }
                }
            }
        }

        $this->baseGroundSubChunk = $subChunk;
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        $chunk = $world->getChunk($chunkX, $chunkZ);

        if ($chunk === null) {
            FancyGenerators::getInstance()->getLogger()->error("Cannot generate chunk: chunk to generate is null");

            return;
        }

        $subChunk = clone $this->baseGroundSubChunk;
        $chunk->setSubChunk(self::GROUND_CHUNK, $subChunk);

        $yOffs = self::GROUND_CHUNK * 16 + self::SAND_UNTIL;
        $noise = $this->noiseBase->getFastNoise3D(
            16,
            128,
            16,
            4,
            64,
            4,
            $chunkX * 16,
            $yOffs,
            $chunkZ * 16
        );
        $sandStone = VanillaBlocks::SANDSTONE()->getStateId();

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $worldX = $chunkX * 16 + $x;
                $worldZ = $chunkZ * 16 + $z;

                // check if we're inside an island cell
                $cellX = intdiv($worldX, self::ISLAND_CELL_SIZE);
                $cellZ = intdiv($worldZ, self::ISLAND_CELL_SIZE);

                $cellRand = new Random(crc32("isl$cellX$cellZ"));
                if ($cellRand->nextFloat() < 0.2) {
                    continue; // no island in this cell
                }

                // distance to island center
                $centerX = $cellX * self::ISLAND_CELL_SIZE + self::ISLAND_CELL_SIZE / 2;
                $centerZ = $cellZ * self::ISLAND_CELL_SIZE + self::ISLAND_CELL_SIZE / 2;

                // randomize position of the island inside its cell
                $offsetX = $cellRand->nextBoundedInt(21) - 10; // -10 to +10
                $offsetZ = $cellRand->nextBoundedInt(21) - 10;

                $centerX += $offsetX;
                $centerZ += $offsetZ;

                // check our distance to the island center
                $dx = $worldX - $centerX;
                $dz = $worldZ - $centerZ;
                $distance = sqrt($dx * $dx + $dz * $dz);

                if ($distance > self::ISLAND_RADIUS) {
                    continue; // outside island radius
                }

                // prepare island terrain and falloff
                $edgeDrop = 4;
                $radialFalloff = pow(1 - ($distance / self::ISLAND_RADIUS), 2);

                $heightShift = (1 - $radialFalloff) * $edgeDrop;

                $verticalBase = $yOffs + 5;
                $baseHeight = $verticalBase + $cellRand->nextBoundedInt(6) - 3;

                for ($y = 0; $y < 128; ++$y) {
                    // generate terrain
                    $terrainValue = $noise[$x][$z][$y];
                    $verticalFalloff = ($y - ($baseHeight - $heightShift)) * 0.08;

                    $value = ($terrainValue * $radialFalloff) - $verticalFalloff;

                    if ($value > 0.2) {
                        $chunk->setBlockStateId($x, $y, $z, $sandStone);
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
}
