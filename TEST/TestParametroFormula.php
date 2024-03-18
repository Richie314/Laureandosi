<?php
require_once dirname(__DIR__) . "/utils/modelli/ParametroFormula.php";
require_once dirname(__DIR__) . "/utils/modelli/Test.php";
class TestParametroFormula extends Test
{
    public function __construct()
    {
        parent::__construct(
            'Func', 
            array(
                array(
                    array(0, 0, 0),
                    array(0, 3, 1),
                    array(5, 6, 0),
                    array(1, 1, 0),
                    array(1, 5, 0.5),
                    array(2, 4, -1),
                    array(-1, 3, 1),
                    array(18, 33, 1)
                )
            ), 
            array(
                false,
                true,
                false,
                true,
                true,
                false,
                false,
                true
            ));
    }
    public static function Func(array $params) : array
    {
        return array_map(function (array $p) {
            return (new ParametroFormula($p[0], $p[1], $p[2]))->InUso();
        }, $params);
    }
}
