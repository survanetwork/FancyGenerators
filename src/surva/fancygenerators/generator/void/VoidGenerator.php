<?php

/**
 * FancyGenerators | void generator, generates an empty world with a small
 * platform at the spawn
 */

namespace surva\fancygenerators\generator\void;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;
use surva\fancygenerators\FancyGenerators;
use surva\fancygenerators\generator\exception\ChunkPopulateException;

class VoidGenerator extends Generator
{
    public const NAME = "void";

    private const SPAWN = 0;
    private const NB_X = 1;
    private const NB_Z = 2;
    private const NB_BOTH = 3;

    private Vector3 $defaultSpawn;

    private int $spawnChunkX;
    private int $spawnChunkZ;

    private int $xNbSpawnChunkX;
    private int $xNbSpawnChunkZ;

    private int $zNbSpawnChunkX;
    private int $zNbSpawnChunkZ;

    private int $bothNbSpawnChunkX;
    private int $bothNbSpawnChunkZ;

    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset);

        $this->defaultSpawn = new Vector3(256, 65, 256);

        $this->spawnChunkX = (int) $this->defaultSpawn->getX() >> 4;
        $this->spawnChunkZ = (int) $this->defaultSpawn->getZ() >> 4;

        $this->xNbSpawnChunkX = $this->spawnChunkX;
        $this->xNbSpawnChunkZ = ((int) $this->defaultSpawn->getZ() - 1) >> 4;

        $this->zNbSpawnChunkX = ((int) $this->defaultSpawn->getX() - 1) >> 4;
        $this->zNbSpawnChunkZ = $this->spawnChunkZ;

        $this->bothNbSpawnChunkX = ((int) $this->defaultSpawn->getX() - 1) >> 4;
        $this->bothNbSpawnChunkZ = ((int) $this->defaultSpawn->getZ() - 1) >> 4;
    }

    /**
     * Generate one of the first chunks including the start blocks
     *
     * @param  ChunkManager  $world
     * @param  int  $chunkX
     * @param  int  $chunkZ
     * @param  int  $whichChunk
     *
     * @return Chunk
     * @throws ChunkPopulateException
     */
    private function generateFirstChunk(ChunkManager $world, int $chunkX, int $chunkZ, int $whichChunk): Chunk
    {
        $chunk = $world->getChunk($chunkX, $chunkZ);

        if ($chunk === null) {
            throw new ChunkPopulateException("first chunk is null");
        }

        $spawn = $this->defaultSpawn;
        $underSpawn = $spawn->subtract(0, 1, 0);
        $yUnderSpawn = (int) $underSpawn->getY();

        $planks = VanillaBlocks::OAK_PLANKS()->getStateId();

        switch ($whichChunk) {
            case self::SPAWN:
                for ($x = 0; $x <= 1; $x++) {
                    for ($z = 0; $z <= 1; $z++) {
                        $chunk->setBlockStateId($x, $yUnderSpawn, $z, $planks);
                    }
                }
                break;
            case self::NB_X:
                $chunk->setBlockStateId(0, $yUnderSpawn, 15, $planks);
                $chunk->setBlockStateId(1, $yUnderSpawn, 15, $planks);
                break;
            case self::NB_Z:
                $chunk->setBlockStateId(15, $yUnderSpawn, 0, $planks);
                $chunk->setBlockStateId(15, $yUnderSpawn, 1, $planks);
                break;
            case self::NB_BOTH:
                $chunk->setBlockStateId(15, $yUnderSpawn, 15, $planks);
                break;
        }

        return $chunk;
    }

    /**
     * Generate a basic empty chunk
     *
     * @param  ChunkManager  $world
     * @param  int  $chunkX
     * @param  int  $chunkZ
     *
     * @return Chunk
     * @throws ChunkPopulateException
     */
    private function generateBaseChunk(ChunkManager $world, int $chunkX, int $chunkZ): Chunk
    {
        $chunk = $world->getChunk($chunkX, $chunkZ);

        if ($chunk === null) {
            throw new ChunkPopulateException("chunk to generate is null");
        }

        return $chunk;
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        try {
            if ($chunkX === $this->spawnChunkX and $chunkZ === $this->spawnChunkZ) {
                $chunk = $this->generateFirstChunk($world, $chunkX, $chunkZ, self::SPAWN);
            } elseif ($chunkX === $this->xNbSpawnChunkX and $chunkZ === $this->xNbSpawnChunkZ) {
                $chunk = $this->generateFirstChunk($world, $chunkX, $chunkZ, self::NB_X);
            } elseif ($chunkX === $this->zNbSpawnChunkX and $chunkZ === $this->zNbSpawnChunkZ) {
                $chunk = $this->generateFirstChunk($world, $chunkX, $chunkZ, self::NB_Z);
            } elseif ($chunkX === $this->bothNbSpawnChunkX and $chunkZ === $this->bothNbSpawnChunkZ) {
                $chunk = $this->generateFirstChunk($world, $chunkX, $chunkZ, self::NB_BOTH);
            } else {
                $chunk = $this->generateBaseChunk($world, $chunkX, $chunkZ);
            }
        } catch (ChunkPopulateException $ex) {
            FancyGenerators::getInstance()->getLogger()->error("Cannot generate void chunk: " . $ex->getMessage());

            return;
        }

        $world->setChunk($chunkX, $chunkZ, $chunk);
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        // no population needed
    }
}
