<?php

namespace Chess\Figures;

use Chess\AbstractFigure;
use Chess\Interfaces\Figure;
use Chess\Interfaces\Figure as FigureInterface;
use Chess\Interfaces\Figures\King as KingInterface;
use Chess\Player;
use Chess\Traits\Figures\Moves\Askew;
use Chess\Traits\Figures\Moves\Alongside;

class King extends AbstractFigure implements KingInterface
{
    use Askew, Alongside;

    /**
     * {@inheritdoc}
     */
    public function id(): string
    {
        return 'k';
    }

    public function getEnemy(): Player
    {
        $fields = $this->board->fields();
        unset($fields[$this->x][$this->y]);
        $flat = call_user_func_array('array_merge', $fields);

        foreach ($flat as $figure) {
            if ($figure !== null && $figure->getPlayer() !== $this->getPlayer()) {
                return $figure->getPlayer();
            }
        }

        return null;
    }

    public function isCheck(): bool
    {
        return $this->isCheckedBy($this->getEnemyFigures(), $this->x, $this->y, false);
    }

    public function isCheckOnPosition(string $x, int $y): bool
    {
        $check = $this->isCheckedBy($this->getEnemyFigures(), $x, $y, true);
        return $check;
    }

    /**
     * {@inheritdoc}
     */
    public function canMoveTo(string $x, int $y, bool $force): bool
    {
        $rangeX = range($this->x, $x);
        $countX = count($rangeX) - 1;
        $countY = abs($this->y - $y);

        if (
            $countX <= 1
            &&
            $countY <= 1
            &&
            (
                $this->canMoveAskew($x, $y)
                ||
                $this->canMoveAlongside($x, $y)
            )
            &&
            !$this->isCheckedBy($this->getEnemyFigures(), $x, $y, false)
            &&
            ($this->board->get($x, $y) === null || $this->board->get($x, $y)->getPlayer() !== $this->getPlayer())
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isMoveCastling(string $x, int $y): bool
    {
        return $this->getBoardStart() === $y && $this->getDistance($x, $y) === 2;
    }

    /**
     * {@inheritdoc}
     */
    public function canCastle(string $x, int $y): bool
    {
        $range = range($this->x, $x);
        $enemyFigures = $this->getEnemyFigures();

        foreach ($range as $toX) {
            if ($this->isCheckedBy($enemyFigures, $toX, $y, false)) {
                return false;
            }
        }

        if ($this->wasMoved()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCastlingPartnerPosition(string $x, int $y): array
    {
        $width = $this->board->width();

        $toX = $x > $this->x ? array_pop($width) : array_shift($width);

        return [$toX, $y];
    }

    /**
     * {@inheritdoc}
     */
    public function getCastlingPartnerDestination(string $x, int $y): array
    {
        $range = range($this->x, $x);

        return [$range[1], $y];
    }

    /**
     * @param string $x
     * @param int $y
     *
     * @return int
     */
    protected function getDistance(string $x, int $y): int
    {
        return count(range($this->x, $x)) - 1;
    }

    /**
     * Determine whether figure is checked.
     *
     * @param mixed $enemyFigures
     * @param string $x
     * @param int $y
     * @param bool $force
     *
     * @return bool
     */
    protected function isCheckedBy($enemyFigures, string $x, int $y, $force): bool
    {
        foreach ($enemyFigures as $figure) {
            if ($figure->canMoveTo($x, $y, $force)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns array of enemy figures that are on board.
     *
     * @return FigureInterface[]
     */
    protected function getEnemyFigures()
    {
        // Get every field
        $fields = $this->board->fields();

        // Remove current figure
        unset($fields[$this->x][$this->y]);

        // Flatten array
        $flat = call_user_func_array('array_merge', $fields);

        // Filter empty fields and return only enemy figures
        $flat = array_filter($flat, function ($figure) {
            /* @var FigureInterface|null $figure */
            return $figure !== null && $figure->getPlayer() !== $this->getPlayer();
        });

        return $flat;
    }

    /**
     * Returns array of enemy figures that are on board.
     *
     * @return FigureInterface[]
     */
    protected function getMyFigures()
    {
        // Get every field
        $fields = $this->board->fields();

        // Remove current figure
        unset($fields[$this->x][$this->y]);

        // Flatten array
        $flat = call_user_func_array('array_merge', $fields);

        // Filter empty fields and return only enemy figures
        $flat = array_filter($flat, function ($figure) {
            /* @var FigureInterface|null $figure */
            return $figure !== null && $figure->getPlayer() === $this->getPlayer();
        });

        return $flat;
    }

    /**
     * Get starting row of board.
     *
     * @return int
     */
    protected function getBoardStart(): int
    {
        $height = $this->board->height();

        if ($this->getPlayer()->doesMoveUpwards()) {
            return array_shift($height);
        } else {
            return array_pop($height);
        }
    }

    /**
     * can we block attack line.
     *
     * @return bool
     */
    public function canDefendFromCheck(): bool
    {
        $enemyFigures = $this->getEnemyFigures();
        $myFigures = $this->getMyFigures();

        foreach ($enemyFigures as $figure) {
            if ($figure->canMoveTo($this->x, $this->y, false)) {
                foreach ($myFigures as $myFigure) {
                    if ($myFigure->canMoveTo($figure->getX(), $figure->getY(), false)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * can we escape from check.
     *
     * @return bool
     */
    public function canEscapeFromCheck(): bool
    {
        for ($x = chr(ord($this->x) - 1); $x <= chr(ord($this->x) + 1); $x++) {
            for ($y = $this->y - 1; $y <= $this->y + 1; $y++) {
                if ($this->board->isFieldInBoundaries($x, $y)
                    && $x !== $this->x
                    && $y !== $this->y
                    && $this->canMoveTo($x, $y, false)
                    && !$this->isCheckOnPosition($x, $y)) {
                    return true;
                }
            }
        }

        return false;
    }

//    protected function setAttacker($figure)
//    {
//        $this->attacker = $figure;
//    }
//
//    protected function getAttacker(): Figure
//    {
//        return $this->attacker;
//    }
}
