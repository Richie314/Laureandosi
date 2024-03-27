<?php
require_once dirname(__DIR__) . "/utils/GestioneCarrieraStudente.php";
require_once dirname(__DIR__) . "/utils/modelli/Test.php";
class TestGestioneCarrieraStudente_restituisciAnagraficaStudente extends Test
{
    public function __construct()
    {
        parent::__construct(
            'Func', 
            array('123456'), 
            array(
                'nome' => 'GIUSEPPE',
                'cognome' => 'ZEDDE',
                'cod_fis' => 'ABCDEFX12X12X123X',
                'data_nascita' => '1997-06-14T00:00:00.000+02:00',
                'email_ate' => 'g.zedde@studenti.unipi.it'
            )
        );
    }
    public static function Func(string $matricola) : array
    {
        $s = GestioneCarrieraStudente::restituisciAnagraficaStudente($matricola);
        return json_decode($s, true)["Entries"]["Entry"];
    }
}
class TestGestioneCarrieraStudente_restituisciCarrieraStudente extends Test
{
    public function __construct()
    {
        parent::__construct(
            'Func', 
            array('123456'), 
            array(
                '073II',
                'ELETTROTECNICA',
                0
            )
        );
    }
    public static function Func(string $matricola) : array
    {
        $s = GestioneCarrieraStudente::restituisciCarrieraStudente($matricola);
        $esame = json_decode($s, true)["Esami"]["Esame"][0];
        return array(
            $esame['COD'],
            $esame['DES'],
            $esame['SOVRAN_FLG']
        );
    }
}