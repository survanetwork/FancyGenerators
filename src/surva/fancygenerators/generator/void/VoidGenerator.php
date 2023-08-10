<?php

/**
 * FancyGenerators | void generator
 */

namespace surva\fancygenerators\generator\void;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;

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

        $this->spawnChunkX = $this->defaultSpawn->getX() >> 4;
        $this->spawnChunkZ = $this->defaultSpawn->getZ() >> 4;

        $this->xNbSpawnChunkX = $this->spawnChunkX;
        $this->xNbSpawnChunkZ = ($this->defaultSpawn->getZ() - 1) >> 4;

        $this->zNbSpawnChunkX = ($this->defaultSpawn->getX() - 1) >> 4;
        $this->zNbSpawnChunkZ = $this->spawnChunkZ;

        $this->bothNbSpawnChunkX = ($this->defaultSpawn->getX() - 1) >> 4;
        $this->bothNbSpawnChunkZ = ($this->defaultSpawn->getZ() - 1) >> 4;
    }

    /**
     * Generate one of the first chunks including the start blocks
     *
     * @param  \pocketmine\world\ChunkManager  $world
     * @param  int  $chunkX
     * @param  int  $chunkZ
     * @param  int  $whichChunk
     *
     * @return \pocketmine\world\format\Chunk
     */
    private function generateFirstChunk(ChunkManager $world, int $chunkX, int $chunkZ, int $whichChunk): Chunk
    {
        $chunk = $world->getChunk($chunkX, $chunkZ);

        $spawn = $this->defaultSpawn;
        $underSpawn = $spawn->subtract(0, 1, 0);

        $planks = VanillaBlocks::OAK_PLANKS()->getStateId();

        switch ($whichChunk) {
            case self::SPAWN:
                for ($x = 0; $x <= 1; $x++) {
                    for ($z = 0; $z <= 1; $z++) {
                        $chunk->setBlockStateId($x, $underSpawn->getY(), $z, $planks);
                    }
                }
                break;
            case self::NB_X:
                $chunk->setBlockStateId(0, $underSpawn->getY(), 15, $planks);
                $chunk->setBlockStateId(1, $underSpawn->getY(), 15, $planks);
                break;
            case self::NB_Z:
                $chunk->setBlockStateId(15, $underSpawn->getY(), 0, $planks);
                $chunk->setBlockStateId(15, $underSpawn->getY(), 1, $planks);
                break;
            case self::NB_BOTH:
                $chunk->setBlockStateId(15, $underSpawn->getY(), 15, $planks);
                break;
        }

        return $chunk;
    }

    /**
     * Generate a basic empty chunk
     *
     * @param  \pocketmine\world\ChunkManager  $world
     * @param  int  $chunkX
     * @param  int  $chunkZ
     *
     * @return \pocketmine\world\format\Chunk
     */
    private function generateBaseChunk(ChunkManager $world, int $chunkX, int $chunkZ): Chunk
    {
        return $world->getChunk($chunkX, $chunkZ);
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
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

        $world->setChunk($chunkX, $chunkZ, $chunk);
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        // no population needed
    }
}
