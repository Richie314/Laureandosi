<?php
require_once dirname(__DIR__) . "/utils/Configurazione.php";
require_once dirname(__DIR__) . "/utils/modelli/Test.php";
class TestConfigurazione_CorsiDiLaurea extends Test
{
    public function __construct()
    {
        parent::__construct(
            'Func', 
            array('T. Ing. Elettronica'), 
            array(
                'T. Ing. Elettronica',
                'T. Ing. Elettronica',
                '2 + 4 * ( M * CFU + T * 3) / ( CFU + 3)',
                33,
                '2026-05-01',
                177,
                true, 
                false
            ));
    }
    public static function Func(string $cdl) : array
    {
        $obj = Configurazione::CorsiDiLaurea()[$cdl];
        return array(
            $obj->Nome,
            (string)$obj,
            $obj->Formula,
            $obj->ValoreLode,
            $obj->FineBonus(2022),
            $obj->CFURichiesti,
            $obj->T->InUso(),
            $obj->C->InUso()
        );
    }
}

class TestConfigurazione_IngInf extends Test
{
    public function __construct()
    {
        parent::__construct(
            'Func', 
            array(
                array(
                    'T. Ing. Elettronica',
                    'T. Ing. Informatica',
                    'boh'
                )
            ), 
            array(
                false, 
                true, 
                false
            ));
    }
    public static function Func(array $nomi) : array
    {
        return array_map(function (string $nome) {
            return Configurazione::IngInf($nome);
        }, $nomi);
    }
}
class TestConfigurazione_EsamiInformatici extends Test
{
    public function __construct()
    {
        parent::__construct(
            'Func', 
            array(
                array(
                    'Fondamenti di Programmazione',
                    'Elettrotecnica',
                    'Reti Logiche',
                    'Ingegneria del Software',
                    'Algebra Lineare'
                )
            ), 
            array(
                true,
                false,
                true,
                true,
                false
            ));
    }
    public static function Func(array $esami) : array
    {
        return array_map(function (string $esame) {
            return in_array(strtoupper($esame), Configurazione::EsamiInformatici());
        }, $esami);
    }
}