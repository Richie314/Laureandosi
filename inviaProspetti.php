<?php

//error_reporting(E_ERROR | E_PARSE);
require_once __DIR__ . '/utils/InvioPDFLaureando2.php';
$invio = new InvioPDFLaureando2();

if (isset($_POST['numero_max']) && ctype_digit($_POST['numero_max']))
{
    $inviati = $invio->invioProspetti((int)$_POST['numero_max']);
} else {
    $inviati = $invio->invioProspetti();
}

//header("Content-type: application/json");
// Output risposta
echo json_encode(array(
    'InviiEffettuati' => $inviati
), JSON_PRETTY_PRINT) . PHP_EOL;