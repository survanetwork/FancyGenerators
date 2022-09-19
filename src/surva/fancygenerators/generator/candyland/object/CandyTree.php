<?php

/**
 * FancyGenerators | candy tree object
 */

namespace surva\fancygenerators\generator\candyland\object;

use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\generator\object\Tree;
use surva\fancygenerators\generator\candyland\CandyLand;

class CandyTree extends Tree
{
    public function __construct(int $treeHeight = 7)
    {
        $quartz = VanillaBlocks::QUARTZ();
        $stainedClay = VanillaBlocks::STAINED_CLAY();

        $stainedClay->setColor(CandyLand::getRandomBlockColor());

        parent::__construct($quartz, $stainedClay, $treeHeight);
    }

    protected function placeTrunk(
        int $x,
        int $y,
        int $z,
        Random $random,
        int $trunkHeight,
        BlockTransaction $transaction
    ): void {
        parent::placeTrunk($x, $y, $z, $random, $trunkHeight, $transaction);

        $transaction->addBlockAt($x, $y - 1, $z, $this->trunkBlock);
    }
}
