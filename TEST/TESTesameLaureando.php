<?php
require_once dirname(__DIR__) . "/utils/modelli/EsameLaureando.php";
require_once dirname(__DIR__) . "/utils/modelli/Test.php";
class TestEsameLaureando extends Test
{
    public function __construct()
    {
        $cfu = 12;
        $lode = 31;
        parent::__construct(
            'Func', 
            array(
                '30 e    Lode',
                date("d/m/Y"),
                $cfu,
                $lode
            ), 
            array(
                $cfu,
                $cfu,
                $lode,
                date("Y-m-d")
            ));
    }
    public static function Func(
        string $voto,
        string $data,
        int $cfu,
        int $lode) : array
    {
        $esame = new EsameLaureando(
            'Esame', 
            $voto,
            $cfu, 
            $data,
            true, true, $lode
        );
        return array(
            $esame->creditoCurriculare(),
            $esame->creditoMedia(),
            $esame->VotoEsame,
            $esame->DataEsame->format("Y-m-d")
        );
    }
}