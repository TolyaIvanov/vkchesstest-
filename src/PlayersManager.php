<?php

namespace Chess;

use Chess\Exceptions\PlayerException;
use Chess\Interfaces\PlayersManager as PlayersManagerInterface;
use Chess\Interfaces\Player as PlayerInterface;

class PlayersManager implements PlayersManagerInterface
{
    /**
     * @var players[]
     */
    protected $players;

    /**
     * @param PlayerInterface $white
     * @param PlayerInterface $black
     */
    public function __construct(PlayerInterface $white, PlayerInterface $black)
    {
        $white->setMovesUpwards(true);
        $black->setMovesUpwards(false);

        $this->players = compact('white', 'black');
    }

    /**
     * @param string $key
     * @return Player
     *
     * @throws PlayerException
     */
    public function get(string $key) : PlayerInterface
    {
        if (! array_key_exists($key, $this->players)) {
            throw new PlayerException("Player key {$key} is invalid");
        }

        return $this->players[$key];
    }
}
