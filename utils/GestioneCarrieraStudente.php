<?php
class GestioneCarrieraStudente
{
    public static function restituisciCarrieraStudente(string|int $matricola)
    {
        $json_carriera = file_get_contents(dirname(__DIR__) . "/data/" . $matricola . "_esami.json");
        return $json_carriera;
    }
    public static function restituisciAnagraficaStudente(string|int $matricola)
    {
        $json_anagrafica = file_get_contents(dirname(__DIR__) . "/data/" . $matricola . "_anagrafica.json");
        return $json_anagrafica;
    }
}