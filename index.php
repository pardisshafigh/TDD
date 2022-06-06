<?php

use App\Helpers\Config;
use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;

include_once "vendor/autoload.php";

$result = Config::get("database");
$pdoConnection = new PDODatabaseConnection($config);
$queryBuilder = new PDOQueryBuilder($pdoConnection->Connect());

function json_response($data = null, $statusCode = 200){
    header("Content-Type: application/json");
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}


function request(){
    return json_decode(file_get_contents("php://input"), true);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $queryBuilder->table("bugs")->create(request());
    json_response(null, 200);
}



if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $queryBuilder->table("bugs")
    ->where("id", request()["id"])
    ->update(request());
    json_response(null, 200);
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $bug = $queryBuilder->table("bugs")
    ->find(request()["id"]);
    json_response($bug, 200);
}



if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $bug = $queryBuilder->table("bugs")
    ->where("id", request()["id"])
    ->delete();
    json_response(null, 204);
}