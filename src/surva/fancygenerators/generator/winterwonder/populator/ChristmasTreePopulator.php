<?php

/**
 * FancyGenerators | Christmas tree populator
 *
 * adapted from PocketMine's Tree populator class
 * https://github.com/pmmp/PocketMine-MP/blob/stable/src/world/generator/populator/Tree.php
 */

namespace surva\fancygenerators\generator\winterwonder\populator;

use pocketmine\block\BlockTypeIds;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\populator\Populator;
use surva\fancygenerators\generator\winterwonder\object\ChristmasTree;

class ChristmasTreePopulator implements Populator
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

            $tree = new ChristmasTree();
            $transaction = $tree->getBlockTransaction($world, $treeX, $treeY, $treeZ);
            $transaction?->apply();
        }
    }

    /**
     * Get the y + 1 coordinate of the highest block at a specific position
     *
     * @param ChunkManager $world
     * @param int $x
     * @param int $z
     *
     * @return int|null
     */
    private function getHighestTreeBlock(ChunkManager $world, int $x, int $z): ?int
    {
        for ($y = 127; $y >= 0; --$y) {
            $block = $world->getBlockAt($x, $y, $z);
            $blockId = $block->getTypeId();

            if (
                $blockId === BlockTypeIds::SNOW
                || $blockId === BlockTypeIds::SPRUCE_WOOD
                || $blockId === BlockTypeIds::WOOL
            ) {
                return $y + 1;
            }

            if ($blockId !== BlockTypeIds::AIR) {
                return null;
            }
        }

        return null;
    }
}
