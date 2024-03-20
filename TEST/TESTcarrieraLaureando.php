<?php
require_once dirname(__DIR__) . "/utils/modelli/CarrieraLaureando.php";
require_once dirname(__DIR__) . "/utils/modelli/Test.php";
class TestCarrieraLaureando extends Test
{
    public function __construct()
    {
        parent::__construct('Func', array(
            234567,
            'M. Ing. Elettronica'
        ), array(
            102,
            '24.559',
            'GIOVANNI',
            'ATZENI',
            2018,
            234567,
            'TELECOMUNICAZIONI 25'
        ));
    }
    public static function Func(int $matricola, string $cdl) : array
    {
        $carriera = new CarrieraLaureando($matricola, $cdl);
        return array(
            $carriera->creditiCurricolariConseguiti(),
            number_format($carriera->restituisciMedia(), 3),
            $carriera->Nome,
            $carriera->Cognome,
            $carriera->AnnoImmatricolazione,
            $carriera->Matricola,
            $carriera->Esami[0]->NomeEsame . " " . $carriera->Esami[0]->VotoEsame);
    }
}
