<?php
/**
 * @access public
 * @author franc
 */
class AccessoProspetti
{
    private static string $serverDir;
    private const websiteDir = '/data/pdf_generati/';
    private const nomeFileCommissione = "prospettoCommissione.pdf";
    public static function pathCommissioneWeb() : string
    {
        return self::websiteDir . self::nomeFileCommissione;
    }
    public static function pathCommissioneServer() : string
    {
        return self::$serverDir . self::nomeFileCommissione;
    }
    public static function pathLaureandoServer(string|int $matricola) : string
    {
        return self::$serverDir . "$matricola-prospetto.pdf";
    }
    public static function setServerDir(string $dir) : void
    {
        self::$serverDir = $dir;
    }
}
AccessoProspetti::setServerDir(dirname(__DIR__) . '/data/pdf_generati/');
