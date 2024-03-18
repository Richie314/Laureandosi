<?php
require_once dirname(__DIR__) . "/utils/AccessoProspetti.php";
require_once dirname(__DIR__) . "/utils/modelli/Test.php";
class TestAccessoProspetti_PathAusiliario extends Test
{
    public function __construct()
    {
        parent::__construct('Func', array(), true);
    }
    public static function Func() : bool
    {
        return is_dir(dirname(AccessoProspetti::pathAusiliario()));
    }
}
class TestAccessoProspetti_PathCommissioneWeb extends Test
{
    public function __construct()
    {
        parent::__construct('Func', array(), true);
    }
    public static function Func() : bool
    {
        return strlen(AccessoProspetti::pathCommissioneWeb()) > 0;
    }
}
class TestAccessoProspetti_PathLaureandoServer extends Test
{
    public function __construct()
    {
        parent::__construct('Func', array(), true);
    }
    public static function Func() : bool
    {
        return is_dir(dirname(AccessoProspetti::pathLaureandoServer(0)));
    }   
}