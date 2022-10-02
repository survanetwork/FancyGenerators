<?php

/**
 * FancyGenerators | Christmas gift object
 */

namespace surva\fancygenerators\generator\winterwonder\object;

use pocketmine\block\Block;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;

class Gift
{
    private const GIFT_WIDTH = 3;
    private const CENTER_OFF = 1;

    private const COL_1 = 1;
    private const COL_2 = 2;
    private const COL_3 = 3;

    private Block $baseBlock;
    private Block $ribbonBlock;

    public function __construct()
    {
        $base = VanillaBlocks::WOOL();
        $ribbon = VanillaBlocks::WOOL();

        $randColorScheme = rand(self::COL_1, self::COL_3);

        switch ($randColorScheme) {
            case self::COL_1:
                $base->setColor(DyeColor::RED());
                $ribbon->setColor(DyeColor::YELLOW());
                break;
            case self::COL_2:
                $base->setColor(DyeColor::YELLOW());
                $ribbon->setColor(DyeColor::PURPLE());
                break;
            case self::COL_3:
                $base->setColor(DyeColor::LIGHT_BLUE());
                $ribbon->setColor(DyeColor::PINK());
                break;
        }

        $this->baseBlock = $base;
        $this->ribbonBlock = $ribbon;
    }

    /**
     * Get block transaction for gift object
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

        $this->placeGift($x, $y, $z, $transaction);

        return $transaction;
    }

    /**
     * Check if a Christmas gift can be placed at this position
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
        $maxOff = self::CENTER_OFF;

        for ($xx = $x - $maxOff; $xx <= $x + $maxOff; $xx++) {
            for ($zz = $z - $maxOff; $zz <= $z + $maxOff; $zz++) {
                for ($yy = 0; $yy < self::GIFT_WIDTH; $yy++) {
                    if (!$world->getBlockAt($xx, $y + $yy, $zz)->canBeReplaced()) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Place gift object
     *
     * @param  int  $x
     * @param  int  $y
     * @param  int  $z
     * @param  \pocketmine\world\BlockTransaction  $transaction
     *
     * @return void
     */
    private function placeGift(int $x, int $y, int $z, BlockTransaction $transaction): void
    {
        for ($xx = -self::CENTER_OFF; $xx <= self::CENTER_OFF; $xx++) {
            for ($yy = 0; $yy < self::GIFT_WIDTH; $yy++) {
                if ($transaction->fetchBlockAt($x + $xx, $y + $yy, $z)->canBeReplaced()) {
                    $transaction->addBlockAt($x + $xx, $y + $yy, $z, $this->ribbonBlock);
                }
            }
        }

        for ($zz = -self::CENTER_OFF; $zz <= self::CENTER_OFF; $zz++) {
            for ($yy = 0; $yy < self::GIFT_WIDTH; $yy++) {
                if ($transaction->fetchBlockAt($x, $y + $yy, $z + $zz)->canBeReplaced()) {
                    $transaction->addBlockAt($x, $y + $yy, $z + $zz, $this->ribbonBlock);
                }
            }
        }

        for ($xx = -self::CENTER_OFF; $xx <= self::CENTER_OFF; $xx++) {
            for ($zz = -self::CENTER_OFF; $zz <= self::CENTER_OFF; $zz++) {
                if ($transaction->fetchBlockAt($x + $xx, $y + self::CENTER_OFF, $z + $zz)->canBeReplaced()) {
                    $transaction->addBlockAt($x + $xx, $y + self::CENTER_OFF, $z + $zz, $this->ribbonBlock);
                }
            }
        }

        if ($transaction->fetchBlockAt($x, $y + self::GIFT_WIDTH, $z)->canBeReplaced()) {
            $transaction->addBlockAt($x, $y + self::GIFT_WIDTH, $z, $this->ribbonBlock);
        }

        $baseBlocksOffs = [
          [-1, 0, -1],
          [-1, 0, 1],
          [1, 0, -1],
          [1, 0, 1],
          [-1, 2, -1],
          [-1, 2, 1],
          [1, 2, -1],
          [1, 2, 1],
        ];

        foreach ($baseBlocksOffs as $baseBlockOff) {
            if (
                $transaction->fetchBlockAt($x + $baseBlockOff[0], $y + $baseBlockOff[1], $z + $baseBlockOff[2])
                            ->canBeReplaced()
            ) {
                $transaction->addBlockAt(
                    $x + $baseBlockOff[0],
                    $y + $baseBlockOff[1],
                    $z + $baseBlockOff[2],
                    $this->baseBlock
                );
            }
        }
    }
}
