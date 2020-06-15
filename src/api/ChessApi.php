<?php

use Chess\GameController;

require_once 'Api.php';
include 'db.php';

class ChessApi extends Api
{
    public $dbhost = "localhost";
    public $dbuser = "root";
    public $dbpass = "";
    public $dbname = "example";

    public $apiName = 'chess';
    public $db;

    public function getall()
    {
        $this->db = new db($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        $game = new Chess\GameController(new Chess\Board());

        $token = $this->requestParams["token"];

        if ($token !== null && strlen($token) > 0) {
            $field = $this->db->query('SELECT * FROM games WHERE token = ?', $token);
        } else {
            return $this->response("invalid game token, try to create new game", 404);
        }

        return $this->response($game, 200);
    }

    public function startnew()
    {
        $this->db = new db($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);

        $game = new Chess\GameController(new Chess\Board());
        $game->startGame();

        $token = $this->random_strings();
        $json_game = json_encode($game->game);

        $this->db->query('INSERT INTO games (token, game_data, step) VALUES (?,?,?)', $token, $json_game, 'white');

        return $this->response($json_game, 200);
    }

    public function move()
    {
        $this->db = new db($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        $game = new Chess\GameController(new Chess\Board());
        $game->startGame();
        $token = $this->requestParams["token"];
        $from = $this->requestParams["from"];
        $to = $this->requestParams["to"];

        if ($token !== null && strlen($token) > 0) {
            $field = $this->db->query('SELECT * FROM games WHERE token = ?', $token)->fetchArray();
        } else {
            return $this->response("invalid game token, try to create new game", 404);
        }

        $dbGame = $field;

        $game->restoreField(json_decode($dbGame["game_data"]));
        $game->movePlayer($from, $to, $dbGame["color"]);

        $this->db->query('SELECT * FROM games WHERE token = ?', $token)->fetchArray();
        $this->changeStepColor($this->db, $dbGame);
    }

    function random_strings()
    {
        return substr(sha1(time()), 0, 8);
    }

    function changeStepColor($db, $gameData)
    {
        if ($gameData["color"] === "white")
        {
            $db->query('UPDATE games SET color = ? WHERE token = ?', "black", $gameData["token"]);
        } else {
            $db->query('UPDATE games SET color = ? WHERE token = ?', "white", $gameData["token"]);
        }
    }
}