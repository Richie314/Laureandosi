<?php
error_reporting(E_ERROR | E_PARSE);
require_once __DIR__ . '/utils/ProspettoPdfCommissione2.php';
if (!isset($_POST["matricole"]) || 
    !isset($_POST["data_laurea"]) || !is_string($_POST["data_laurea"]) &&
    !isset($_POST["cdl"]) || !is_string($_POST["cdl"])) {
    http_response_code(400);
    exit;
}


$matricole_array = array_map("intval", array_map("trim", explode(",", $_POST["matricole"]))); //stringa in array di interi
$data_laurea = $_POST["data_laurea"];
$cdl = $_POST["cdl"];

$messaggio = null;
$esito = null;

$prospetto = new ProspettoPdfCommissione2($matricole_array, $data_laurea, $cdl);
$prospetti_generati = $prospetto->generaProspettiLaureandi();
if ($prospetto->generaProspettiCommissione())
{
    $prospetti_generati++;
}

$esito = "Successo";
$messaggio = "$prospetti_generati prospetti generati";

if (!$prospetto->popolaJSON())
{
    $messaggio = "Impossibile salvare dati sull'operazione";
    $esito = "Errore";   
}

if (count($matricole_array) === 0)
{
    $messaggio = "Nessuna matricola inviata";
    $esito = "Errore";
} elseif ($prospetti_generati < count($matricole_array) + 1)
{
    $pdf_previsti = count($matricole_array) + 1;
    $messaggio = "$prospetti_generati / $pdf_previsti prospetti generati";
    $esito = "Errore";
}

header("Content-type: application/json");
// Output risposta
echo json_encode(array(
    'Esito' => $esito,
    'Messaggio' => $messaggio,
    'NumeroProspetti' => $prospetti_generati
), JSON_PRETTY_PRINT) . PHP_EOL;