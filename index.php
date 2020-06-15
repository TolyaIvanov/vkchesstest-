<?php

require 'vendor/autoload.php';
require_once "./src/api/ChessApi.php";

try {
    $api = new ChessApi();
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
