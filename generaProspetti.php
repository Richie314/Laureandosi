<?php
require_once __DIR__ . '/utils/ProspettoPdfCommissione2.php';
if (!isset($_POST["matricole"]) || 
    !isset($_POST["data_laurea"]) || !is_string($_POST["data_laurea"]) &&
    !isset($_POST["cdl"]) || !is_string($_POST["cdl"])) {
    http_response_code(400);
    exit;
}

header("Content-type: application/json");

$matricole_array = array_map("intval", explode(",", $_POST["matricole"])); //stringa in array di interi
$data_laurea = $_POST["data_laurea"];
$cdl = $_POST["cdl"];

$messaggio = null;
$esito = null;

$prospetto = new ProspettoPdfCommissione2($matricole_array, $data_laurea, $cdl);
$prospetto->generaProspettiCommissione();
$prospetto->generaProspettiLaureandi();


if (!$prospetto->popolaJSON(__DIR__ . '/data/ausiliario.json'))
{
    $messaggio = "Impossibile salvare dati sull'operazione";
    $esito = "Errore";
    
}

$esito = "Successo";
$messaggio = "$prospetti_generati prospetti generati";

// Output risposta
echo json_encode(array(
    'Esito' => $esito,
    'Messaggio' => $messaggio
), JSON_PRETTY_PRINT) . PHP_EOL;