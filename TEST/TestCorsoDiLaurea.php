<?php
require_once dirname(__DIR__) . "/utils/modelli/CorsoDiLaurea.php";
require_once dirname(__DIR__) . "/utils/modelli/Test.php";
class TestCorsoDiLaurea extends Test
{
    public function __construct()
    {
        parent::__construct(
            'Func', 
            array(
                'T. Ing. Elettronica',
                '2 + 4 * ( M * CFU + T * 3) / ( CFU + 3)',
                177,
                33,
                3,
                24.559,
                2022,
                12,
            ), 
            array(
                'T. Ing. Elettronica',
                true,
                false,
                '94.989',
                '2026-05-01',
            ));
    }
    public static function Func(
        string $nome, 
        string $formula,
        int $cfu,
        int $lode,
        int $durata,
        float $M,
        int $anno,
        int $cfu_media,
    ): array {
        $cdl = new CorsoDiLaurea($nome, $formula, $cfu, $lode, 18, 33, 1, 0, 0, 0, null, '', $durata);
        return array(
            (string)$cdl,
            $cdl->T->inUso(),
            $cdl->C->inUso(),
            number_format($cdl->calcolaFormula($M, $cfu_media, 18), 3),
            $cdl->fineBonus($anno)
        );
    }
}
