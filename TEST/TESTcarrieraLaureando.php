<?php
require_once dirname(__DIR__) . "/utils/modelli/CarrieraLaureando2.php";
require_once dirname(__DIR__) . "/utils/modelli/Test.php";
class TestCarrieraLaureando extends Test
{
    public function __construct()
    {
        parent::__construct('Func', array(
            234567,
            'T. Ing. Elettronica'
        ), array(
            102,
            '24.559',
            'GIOVANNI',
            'ATZENI',
            2018,
            234567,
            new EsameLaureando2(
                'TELECOMUNICAZIONI',
                25,
                9,
                '29/01/2019',
                true,
                true,
                33
            )
        ));
    }
    public static function Func(int $matricola, string $cdl) : array
    {
        $carriera = new CarrieraLaureando2($matricola, $cdl);
        return array(
            $carriera->creditiCurricolariConseguiti(),
            number_format($carriera->restituisciMedia(), 3),
            $carriera->_nome,
            $carriera->_cognome,
            $carriera->_anno_immatricolazione,
            $carriera->_matricola,
            $carriera->_esami[0]);
    }
}
