<?php

/**
 * FancyGenerators | Christmas tree object
 *
 * adapted from PocketMine's Tree class
 * https://github.com/pmmp/PocketMine-MP/blob/stable/src/world/generator/object/Tree.php
 */

namespace surva\fancygenerators\generator\winterwonder\object;

use pocketmine\block\Block;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;

class ChristmasTree
{
    private Block $trunkBlock;
    private Block $leafBlock;

    private int $trunkHeight;
    private int $canopyHeight;

    public function __construct(int $trunkHeight = 2, int $canopyHeight = 11)
    {
        $wood = VanillaBlocks::SPRUCE_WOOD();
        $greenWool = VanillaBlocks::WOOL();
        $greenWool->setColor(DyeColor::GREEN());

        $this->trunkBlock = $wood;
        $this->leafBlock = $greenWool;

        $this->trunkHeight = $trunkHeight;
        $this->canopyHeight = $canopyHeight;
    }

    /**
     * Get block transaction for Christmas tree
     *
     * @param  \pocketmine\world\ChunkManager  $world
     * @param  int  $x
     * @param  int  $y
     * @param  int  $z
     *
     * @return \pocketmine\world\BlockTransaction|null
     */
    public function getBlockTransaction(ChunkManager $world, int $x, int $y, int $z): ?BlockTransaction
    {
        if (!$this->canPlaceObject($world, $x, $y, $z)) {
            return null;
        }

        $transaction = new BlockTransaction($world);

        $this->placeTrunk($x, $y, $z, $transaction);
        $this->placeCanopy($x, $y, $z, $transaction);
        $this->placeTorches($x, $y, $z, $transaction);

        return $transaction;
    }

    /**
     * Check if a Christmas tree can be placed at this position
     *
     * @param  \pocketmine\world\ChunkManager  $world
     * @param  int  $x
     * @param  int  $y
     * @param  int  $z
     *
     * @return bool
     */
    public function canPlaceObject(ChunkManager $world, int $x, int $y, int $z): bool
    {
        for ($yy = 0; $yy < $this->trunkHeight; $yy++) { // check trunk
            if (!$world->getBlockAt($x, $y + $yy, $z)->canBeReplaced()) {
                return false;
            }
        }

        $maxOff = ($this->canopyHeight - 1) / 2;

        for ($xx = $x - $maxOff; $xx <= $x + $maxOff; $xx++) { // check canopy
            for ($zz = $z - $maxOff; $zz <= $z + $maxOff; $zz++) {
                for ($yy = 0; $yy <= $this->canopyHeight; $yy++) {
                    if (!$world->getBlockAt($xx, $y + $this->trunkHeight + $yy, $zz)->canBeReplaced()) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Place Christmas tree trunk
     *
     * @param  int  $x
     * @param  int  $y
     * @param  int  $z
     * @param  \pocketmine\world\BlockTransaction  $transaction
     *
     * @return void
     */
    private function placeTrunk(int $x, int $y, int $z, BlockTransaction $transaction): void
    {
        for ($yy = 0; $yy < $this->trunkHeight; $yy++) {
            if ($transaction->fetchBlockAt($x, $y + $yy, $z)->canBeReplaced()) {
                $transaction->addBlockAt($x, $y + $yy, $z, $this->trunkBlock);
            }
        }
    }

    /**
     * Place Christmas tree canopy
     *
     * @param  int  $x
     * @param  int  $y
     * @param  int  $z
     * @param  \pocketmine\world\BlockTransaction  $transaction
     *
     * @return void
     */
    private function placeCanopy(int $x, int $y, int $z, BlockTransaction $transaction): void
    {
        for ($levelY = 0; $levelY < $this->canopyHeight; $levelY++) { // height levels
            $levelGlobalY = $y + $this->trunkHeight + $levelY;

            $diameter = $this->canopyHeight - (intdiv($levelY, 2) * 2);
            $radius = ($diameter - 1) / 2;

            for ($xx = $x - $radius; $xx <= $x + $radius; $xx++) { // build base line x
                if ($transaction->fetchBlockAt($xx, $levelGlobalY, $z)->canBeReplaced()) {
                    $transaction->addBlockAt($xx, $levelGlobalY, $z, $this->leafBlock);
                }
            }

            for ($zz = $z - $radius; $zz <= $z + $radius; $zz++) { // build base line z
                if ($transaction->fetchBlockAt($x, $levelGlobalY, $zz)->canBeReplaced()) {
                    $transaction->addBlockAt($x, $levelGlobalY, $zz, $this->leafBlock);
                }
            }

            for ($dd = $diameter - 2; $dd > 2; $dd -= 2) { // down to lowest diameter
                $this->placeSquare(
                    $x,
                    $levelGlobalY,
                    $z,
                    $dd,
                    $dd - 2,
                    $this->leafBlock,
                    $transaction
                ); // place first square
                $this->placeSquare(
                    $x,
                    $levelGlobalY,
                    $z,
                    $dd - 2,
                    $dd,
                    $this->leafBlock,
                    $transaction
                ); // place second square
            }
        }
    }

    /**
     * Place some torches on the Christmas tree
     *
     * @param  int  $x
     * @param  int  $y
     * @param  int  $z
     * @param  \pocketmine\world\BlockTransaction  $transaction
     *
     * @return void
     */
    private function placeTorches(int $x, int $y, int $z, BlockTransaction $transaction): void
    {
        $maxDiameter = $this->canopyHeight - 3;
        $maxOff = $maxDiameter / 2;

        $maxY = $y + $this->trunkHeight + $this->canopyHeight;

        $torchesAmount = rand(2, 6);
        for ($i = 0; $i < $torchesAmount; $i++) {
            $randX = rand($x - $maxOff, $x + $maxOff);
            $randZ = rand($z - $maxOff, $z + $maxOff);

            $tY = $this->getHighestTreeBlockAt($randX, $randZ, $maxY, $transaction);

            $transaction->addBlockAt($randX, $tY + 1, $randZ, VanillaBlocks::TORCH());
        }
    }

    /**
     * Place a square
     *
     * @param  int  $x
     * @param  int  $y
     * @param  int  $z
     * @param  int  $length
     * @param  int  $width
     * @param  \pocketmine\block\Block  $block
     * @param  \pocketmine\world\BlockTransaction  $transaction
     *
     * @return void
     */
    private function placeSquare(
        int $x,
        int $y,
        int $z,
        int $length,
        int $width,
        Block $block,
        BlockTransaction $transaction
    ): void {
        $halfLength = ($length - 1) / 2;
        $halfWidth = ($width - 1) / 2;

        for ($xx = $x - $halfLength; $xx <= $x + $halfLength; $xx++) {
            for ($zz = $z - $halfWidth; $zz <= $z + $halfWidth; $zz++) {
                if ($transaction->fetchBlockAt($xx, $y, $zz)->canBeReplaced()) {
                    $transaction->addBlockAt($xx, $y, $zz, $block);
                }
            }
        }
    }

    /**
     * Get the highest tree block y coordinate at an x/z position
     *
     * @param  int  $x
     * @param  int  $z
     * @param  int  $maxY
     * @param  \pocketmine\world\BlockTransaction  $transaction
     *
     * @return int|null
     */
    private function getHighestTreeBlockAt(int $x, int $z, int $maxY, BlockTransaction $transaction): ?int
    {
        $wool = VanillaBlocks::WOOL()->getId();
        $air = VanillaBlocks::AIR()->getId();

        for ($y = $maxY; $y > 0; $y--) {
            $idAt = $transaction->fetchBlockAt($x, $y, $z)->getId();

            if ($idAt === $wool) {
                return $y;
            }

            if ($idAt !== $air) {
                return null;
            }
        }

        return null;
    }
}
