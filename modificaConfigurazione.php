<?php

if (
    !isset($_POST["cdl"]) || !is_string($_POST["cdl"]) || empty($_POST["cdl"]) ||
    !isset($_POST["nome"]) || !is_string($_POST["nome"]) || empty($_POST["nome"]) ||
    !isset($_POST["formula"]) || !is_string($_POST["formula"]) || empty($_POST["formula"]) ||
    !isset($_POST["cfu"]) || !ctype_digit($_POST["cfu"]) ||
    !isset($_POST["lode"]) || !ctype_digit($_POST["lode"]) ||
    !isset($_POST["durata"]) || !ctype_digit($_POST["durata"]) ||
    !isset($_POST["email"]) || !is_string($_POST["email"]) || empty($_POST["email"]) ||
    !isset($_POST["body"]) || !is_string($_POST["body"]) || empty($_POST["body"])
) {
    http_response_code(400);
    exit;
}
$cdl = $_POST["cdl"];
$nome = $_POST["nome"];
$formula = $_POST["formula"];
$cfu = (int)$_POST["cfu"];
$lode = (int)$_POST["lode"];
$durata = (int)$_POST["durata"];
$email = $_POST["email"];
$corpo = $_POST["body"];

require_once __DIR__ . "/utils/Configurazione.php";

$obj = new CorsoDiLaurea(
    $nome, 
    $formula, 
    $cfu, 
    $lode,
    isset($_POST["tMin"]) ? $_POST["tMin"] : null,
    isset($_POST["tMax"]) ? $_POST["tMax"] : null,
    isset($_POST["tStep"]) ? $_POST["tStep"] : null,

    isset($_POST["cMin"]) ? $_POST["cMin"] : null,
    isset($_POST["cMax"]) ? $_POST["cMax"] : null,
    isset($_POST["cStep"]) ? $_POST["cStep"] : null,
    
    $corpo,
    $email,
    $durata
);

$esito = Configurazione::salvaCdl($cdl, $obj) ? "Successo" : "Errore";
$url = $_SERVER['HTTP_REFERER'];
header("Location: $url&esit=" . urlencode($esito));