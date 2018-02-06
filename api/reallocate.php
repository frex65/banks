<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$content = trim(file_get_contents("php://input"));
$data = json_decode($content, true);

require_once("classes/bank.php");

$store = new Banks($data["banks"]);
$resp['repeat']  = $store->reallocate();
echo json_encode($resp);
?>