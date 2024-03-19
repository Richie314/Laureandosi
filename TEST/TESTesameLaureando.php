<?php
require_once dirname(__DIR__) . "/utils/modelli/EsameLaureando2.php";
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
        $esame = new EsameLaureando2(
            'Esame', 
            $voto,
            $cfu, 
            $data,
            true, true, $lode
        );
        return array(
            $esame->CreditoCurriculare(),
            $esame->CreditoMedia(),
            $esame->_votoEsame,
            $esame->_dataEsame->format("Y-m-d")
        );
    }
}