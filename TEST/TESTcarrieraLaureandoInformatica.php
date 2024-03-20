<?php
require_once dirname(__DIR__) . "/utils/modelli/CarrieraLaureandoInformatica.php";
require_once dirname(__DIR__) . "/utils/modelli/Test.php";
class TestCarrieraLaureandoInformatica_NoBonus extends Test
{
    public function __construct()
    {
        parent::__construct('Func', array(
            123456,
            'T. Ing. Informatica',
            date("Y-m-d")
        ), array(
            177,
            174,
            '23.655',
            'GIUSEPPE',
            'ZEDDE',
            2016,
            123456,
            'FONDAMENTI DI PROGRAMMAZIONE 23',
            "NO"
        ));
    }
    public static function Func(int $matricola, string $cdl, string $data_laurea) : array
    {
        $carriera = new CarrieraLaureandoInformatica($matricola, $cdl, $data_laurea);
        return array(
            $carriera->creditiCurricolariConseguiti(),
            $carriera->creditiCheFannoMedia(),
            number_format($carriera->restituisciMedia(), 3),
            $carriera->Nome,
            $carriera->Cognome,
            $carriera->AnnoImmatricolazione,
            $carriera->Matricola,
            $carriera->Esami[0]->NomeEsame . " " . $carriera->Esami[0]->VotoEsame,
            $carriera->getBonus());
    }
}
class TestCarrieraLaureandoInformatica_ConBonus extends Test
{
    public function __construct()
    {
        parent::__construct('Func', array(
            123456,
            'T. Ing. Informatica',
            '2019-04-01'
        ), array(
            177,
            174,
            '23.909',
            'GIUSEPPE',
            'ZEDDE',
            2016,
            123456,
            'FONDAMENTI DI PROGRAMMAZIONE 23',
            "SI"
        ));
    }
    public static function Func(int $matricola, string $cdl, string $data_laurea) : array
    {
        $carriera = new CarrieraLaureandoInformatica($matricola, $cdl, $data_laurea);
        return array(
            $carriera->creditiCurricolariConseguiti(),
            $carriera->creditiCheFannoMedia(),
            number_format($carriera->restituisciMedia(), 3),
            $carriera->Nome,
            $carriera->Cognome,
            $carriera->AnnoImmatricolazione,
            $carriera->Matricola,
            $carriera->Esami[0]->NomeEsame . " " . $carriera->Esami[0]->VotoEsame,
            $carriera->getBonus());
    }
}