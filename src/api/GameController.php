<?php
namespace Chess;

use Chess\Board;
use Chess\Game;
use Chess\Player;
use Chess\MovesManagerFactory;
use Chess\PlayerFactory;
use Chess\Figures\Knight;
use Chess\Figures\King;
use Chess\Figures\Queen;
use Chess\Figures\Bishop;
use Chess\Figures\Pawn;
use Chess\Figures\Rook;
use Chess\PlayersManager;

class GameController
{
    public $board;
    public $movesFactory;
    public $playerFactory;
    public $playerOne;
    public $playerTwo;
    public $players;

    public $game;

    public function __construct(Board $board)
    {
        $this->board = $board;
        $this->movesFactory = $movesFactory = new MovesManagerFactory($board);
        $this->playerFactory = new PlayerFactory($movesFactory);
        $this->playerOne = $this->playerFactory->make();
        $this->playerTwo = $this->playerFactory->make();
        $this->players = new PlayersManager($this->playerOne, $this->playerTwo);
        //$this->game = new Game($this->board, $this->players);
    }

    public function startGame()
    {
        foreach ($this->board->width() as $letter) {
            foreach ($this->board->height() as $number) {
                if ($number == 2 || $number == 7) {
                    $this->board->add($letter, $number, $number == 2 ? $this->playerOne : $this->playerTwo, Pawn::class);
                }

                if (($number == 1 || $number == 8) && ($letter == "a" || $letter == "h")) {
                    $this->board->add($letter, $number, $number == 1 ? $this->playerOne : $this->playerTwo, Rook::class);
                }

                if (($number == 1 || $number == 8) && ($letter == "b" || $letter == "g")) {
                    $this->board->add($letter, $number, $number == 1 ? $this->playerOne : $this->playerTwo, Knight::class);
                }

                if (($number == 1 || $number == 8) && ($letter == "c" || $letter == "f")) {
                    $this->board->add($letter, $number, $number == 1 ? $this->playerOne : $this->playerTwo, Bishop::class);
                }

                if (($number == 1 || $number == 8) && $letter == "d") {
                    $this->board->add($letter, $number, $number == 1 ? $this->playerOne : $this->playerTwo, Queen::class);
                }

                if (($number == 1 || $number == 8) && $letter == "e") {
                    $this->board->add($letter, $number, $number == 1 ? $this->playerOne : $this->playerTwo, King::class);
                }
            }
        }

        $this->game = new Game($this->board, $this->players);
    }

    public function movePlayer($from, $to, $color)
    {
        $this->game->player($color)->move()->from($from)->to($to);
    }

    public function restoreField($game)
    {
        $this->game = $game;
    }
}