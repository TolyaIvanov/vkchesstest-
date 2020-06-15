<?php

namespace Chess\Interfaces;

interface PlayersManager
{
    /**
     * @param Player $white white figures player
     * @param Player $black black figures player
     */
    public function __construct(Player $white, Player $black);

    /**
     * Gets player instance
     *
     * @param string $key Usually either 'white' or 'black'. Could vary on implementation
     *
     * @return Player
     */
    public function get(string $key) : Player;
}
