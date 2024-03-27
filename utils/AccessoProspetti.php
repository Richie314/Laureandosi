<?php

class AccessoProspetti
{
    private static string $ServerDir;
    public static string $AppPathOnServer = "";
    private const PdfDir = '/data/pdf_generati/';
    private const NomeFileCommissione = "prospettoCommissione.pdf";
    public static function pathCommissioneWeb(): string
    {
        return self::$AppPathOnServer . self::PdfDir . self::NomeFileCommissione;
    }
    public static function pathCommissioneServer(): string
    {
        return self::$ServerDir . self::PdfDir . self::NomeFileCommissione;
    }
    public static function pathLaureandoServer(string|int $matricola): string
    {
        return self::$ServerDir . self::PdfDir . "$matricola-prospetto.pdf";
    }
    /**
     * Imposta la cartella dove il resto del progetto cercherà/creerà i file pdf.
     * Calcola da questo path se l'applicazione wordpress si trova in un sottopath e, se sì, permtte di trovare quel path
     * Esempio:
     * Wordpress installato su "https://laureandosi.it/"
     * -> $ServerDir = "path_macchina_per_root_wordpress"
     * -> $AppPathOnServer = ""
     * Wordpress installato su "https://sito.it/laureandosi/"
     * -> $ServerDir = "path_macchina_per_root_wordpress"
     * -> $AppPathOnServer = "/laureandosi"
     * N.B: nei due esmpi precedenti i valori di $ServerDir saranno diversi
     */
    public static function setServerDir(string $dir): void
    {
        self::$ServerDir = $dir;
        $WebsiteFullPathOnServer = realpath($_SERVER['DOCUMENT_ROOT']);
        if (self::$ServerDir === $WebsiteFullPathOnServer) {
            return;
        }
        $AppPositionLength = strlen(self::$ServerDir);
        $WebsitePositionLength = strlen($WebsiteFullPathOnServer);
        if ($AppPositionLength <= $WebsitePositionLength) {
            throw new LogicException("The php root path cannot be shorter than the application installation path!");
        }
        $diff = substr($AppPositionLength, $WebsitePositionLength);
        self::$AppPathOnServer = str_replace("\\", "/", $diff);
    }
    public static function pathAusiliario(): string
    {
        return dirname(self::pathCommissioneServer(), 2) . "/ausiliario.json";
    }
}
AccessoProspetti::setServerDir(dirname(__DIR__));
