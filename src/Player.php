<?php

namespace Chess;

use Chess\Figures\King;
use Chess\Interfaces\Player as PlayerInterface;
use Chess\Interfaces\MovesManager as MovesManagerInterface;

class Player implements PlayerInterface
{
    /**
     * @var King
     */
    public $king;

    /**
     * @var MovesManager
     */
    protected $manager;

    /**
     * @var bool
     */
    public $movesUpwards = false;

    /**
     * Players counter so we can create unique id
     * It depends on implementation
     * Feel free to change it in your own implementation
     *
     * @var int
     */
    public static $playersCount = 1;

    /**
     * {@inheritdoc}
     */
    public function __construct(MovesManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->manager->setPlayer($this);
        $this->id = self::$playersCount++;
    }

    /**
     * {@inheritdoc}
     */
    public function id(): string
    {
        return $this->id;
    }

    public function getKing()
    {
        return $this->king;
    }

    public function setKing($king)
    {
        $this->king = $king;
    }

    /**
     * {@inheritdoc}
     */
    public function move(...$args)
    {
        $argc = count($args);

        if ($argc === 0) {
            return $this->manager;
        }

        return $this->manager->notation(array_shift($args));
    }

    /**
     * {@inheritdoc}
     */
    public function setMovesUpwards(bool $flag = true)
    {
        $this->movesUpwards = $flag;
    }

    /**
     * {@inheritdoc}
     */
    public function doesMoveUpwards(): bool
    {
        return $this->movesUpwards;
    }

    /**
     * {@inheritdoc}
     */
    public function checkForWin(): bool
    {
        $enemy = $this->king->getEnemy();
        $canEscape = $enemy->king->canEscapeFromCheck();
        $canDefend = $enemy->king->canDefendFromCheck();

        if (!$canDefend && !$canEscape){
            return true;
        }

        return false;
    }

//    protected function getAnotherPlayer()
//    {
//        $this
//
//        return $player;
//    }
}