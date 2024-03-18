<?php
require_once __DIR__ . "/modelli/CorsoDiLaurea.php";
class Configurazione
{
    private static array|null $_corsiDiLaureaCache = null;
    public static function CorsiDiLaurea(bool $reload_cache = false) : array|null
    {
        if (
            isset(self::$_corsiDiLaureaCache) && 
            is_array(self::$_corsiDiLaureaCache) &&
            !$reload_cache)
        {
            return self::$_corsiDiLaureaCache;
        }
        $file_content = file_get_contents(__DIR__ . "/json_files/formule_laurea.json");
        if (!$file_content || strlen(trim($file_content)) === 0)
        {
            // File mancante
            return null;
        }
        $obj = json_decode($file_content, true);
        if (!$obj)
        {
            return null;
        }
        $arr = array();
        foreach ($obj as $nome => $cdl) {
            $arr[$nome] = new CorsoDiLaurea(
                $nome,
                $cdl['formula'],
                $cdl['cfu_richiesti'],
                $cdl['valore_lode'],
                $cdl['Tmin'],
                $cdl['Tmin'],
                $cdl['Tstep'],
                $cdl['Cmin'],
                $cdl['Cmax'],
                $cdl['Cstep'],
                $cdl['corpo_email'],
                $cdl['durata']
            );
        }
        self::$_corsiDiLaureaCache = $arr;
        return $arr;
    }
    private static array|null $_esamiInformaticiCache = null;
    public static function EsamiInformatici(bool $reload_cache = false) : array|null
    {
        if (
            isset(self::$_esamiInformaticiCache) &&
            is_array(self::$_esamiInformaticiCache) && 
            !$reload_cache)
        {
            return self::$_esamiInformaticiCache;
        }
        $file_content = file_get_contents(__DIR__ . "/json_files/esami_informatici.json");
        if (!$file_content || strlen(trim($file_content)) === 0)
        {
            // File mancante
            return null;
        }
        $obj = json_decode($file_content, true);
        if (!$obj)
        {
            return null;
        }
        if (!is_array($obj['nomi_esami']))
        {
            return array();
        }
        return self::$_esamiInformaticiCache = $obj['nomi_esami'];
    }
    private static array $NomiIngInf = array(
        'INGEGNERIA INFORMATICA (IFO-L)',
        'T. ING. INFORMATICA'
    );
    public static function IngInf(string $cdl) : bool
    {
        return in_array(trim(strtoupper($cdl)), self::$NomiIngInf);
    }
}