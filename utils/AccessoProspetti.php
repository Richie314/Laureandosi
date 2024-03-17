<?php
/**
 * @access public
 * @author franc
 */
class AccessoProspetti
{
    private static string $serverDir = dirname(__DIR__) . '/data/pdf_generati/';
    private static string $websiteDir = '/data/pdf_generati/';
    private static string $nomeFileCommissione = "prospettoCommissione.pdf";
    public static function pathCommissioneWeb() : string
    {
        return self::$websiteDir . self::$nomeFileCommissione;
    }
    public static function pathCommissioneServer() : string
    {
        return self::$serverDir . self::$nomeFileCommissione;
    }
    public static function pathLaureandoServer(string|int $matricola) : string
    {
        return self::$serverDir . "$matricola-prospetto.pdf";
    }
}
