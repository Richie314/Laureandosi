<?php
/**
 * @access public
 * @author franc
 */
class AccessoProspetti
{
    private static string $ServerDir;
    private const WebsiteDir = '/data/pdf_generati/';
    private const NomeFileCommissione = "prospettoCommissione.pdf";
    public static function pathCommissioneWeb() : string
    {
        return self::WebsiteDir . self::NomeFileCommissione;
    }
    public static function pathCommissioneServer() : string
    {
        return self::$ServerDir . self::NomeFileCommissione;
    }
    public static function pathLaureandoServer(string|int $matricola) : string
    {
        return self::$ServerDir . "$matricola-prospetto.pdf";
    }
    public static function setServerDir(string $dir) : void
    {
        self::$ServerDir = $dir . self::WebsiteDir;
    }
    public static function pathAusiliario() : string
    {
        return dirname(self::pathCommissioneServer(), 2) . "/ausiliario.json";
    }
}
AccessoProspetti::setServerDir(dirname(__DIR__));
