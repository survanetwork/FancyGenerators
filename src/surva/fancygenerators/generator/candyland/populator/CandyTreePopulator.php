<?php

/**
 * FancyGenerators | candy tree populator
 *
 * adapted from PocketMine's Tree populator class
 * https://github.com/pmmp/PocketMine-MP/blob/stable/src/world/generator/populator/Tree.php
 */

namespace surva\fancygenerators\generator\candyland\populator;

use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\populator\Populator;
use surva\fancygenerators\generator\candyland\object\CandyTree;

class CandyTreePopulator implements Populator
{
    private int $randomAmount = 1;
    private int $baseAmount = 0;

    public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void
    {
        $amount = $random->nextRange(0, $this->randomAmount) + $this->baseAmount;

        for ($i = 0; $i < $amount; ++$i) {
            $chunkXCoord = $chunkX << 4;
            $chunkZCoord = $chunkZ << 4;

            $treeX = $random->nextRange($chunkXCoord, $chunkXCoord + 15);
            $treeZ = $random->nextRange($chunkZCoord, $chunkZCoord + 15);
            $treeY = $this->getHighestTreeBlock($world, $treeX, $treeZ);

            if ($treeY === null) {
                continue;
            }

            $tree = new CandyTree();
            $transaction = $tree->getBlockTransaction($world, $treeX, $treeY, $treeZ, $random);
            $transaction?->apply();
        }
    }

    /**
     * Get the y + 1 coordinate of the highest block at a specific position
     *
     * @param  \pocketmine\world\ChunkManager  $world
     * @param  int  $x
     * @param  int  $z
     *
     * @return int|null
     */
    private function getHighestTreeBlock(ChunkManager $world, int $x, int $z): ?int
    {
        $stainedClay = VanillaBlocks::STAINED_CLAY()->getId();
        $air = VanillaBlocks::AIR()->getId();

        for ($y = 127; $y >= 0; --$y) {
            $block = $world->getBlockAt($x, $y, $z);
            $blockId = $block->getId();

            if ($blockId === $stainedClay) {
                return $y + 1;
            }

            if ($blockId !== $air) {
                return null;
            }
        }

        return null;
    }
}
