<?php

namespace Chess\Interfaces;

interface Figure
{
    /**
     * Figure constructor.
     *
     * @param string $x Figure x-axis position
     * @param int $y Figure y-axis position
     * @param Board $board Board that this figure belongs to
     * @param Player $player Player which this figure belongs to
     */
    public function __construct(string $x, int $y, Board $board, Player $player);

    /**
     * Get figure id (usually one letter, like n for knight).
     *
     * @return string
     */
    public function id(): string;

    /**
     * Get figure owner instance.
     *
     * @return Player
     */
    public function getPlayer(): Player;

    /**
     * Sets figure x coordinates.
     *
     * @param string $x
     */
    public function setX(string $x);

    /**
     * Sets figure y coordinates.
     *
     * @param int $y
     */
    public function setY(int $y);

    /**
     * get figure x coordinates.
     *
     * @return  string $x
     */
    public function getX(): string;

    /**
     * get figure y coordinates.
     *
     * @return  int $y
     */
    public function getY(): int;

    /**
     * Checks whether figure was already moved.
     *
     * @return bool
     */
    public function wasMoved(): bool;

    /**
     * Checks if figure can move to given field (including attacks).
     *
     * @param string $x
     * @param int $y
     * @param bool $force
     *
     * @return bool
     */
    public function canMoveTo(string $x, int $y, bool $force): bool;

    /**
     * Checks if move to given field is attack.
     *
     * @param string $x
     * @param int $y
     *
     * @return bool
     */
    public function isAttack(string $x, int $y): bool;
}
