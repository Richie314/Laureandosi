<?php
require_once __DIR__ . "/modelli/CorsoDiLaurea.php";
class Configurazione
{
    private static array|null $CorsiDiLaureaCache = null;
    public static function CorsiDiLaurea(bool $reload_cache = false) : array|null
    {
        if (
            isset(self::$CorsiDiLaureaCache) && 
            is_array(self::$CorsiDiLaureaCache) &&
            !$reload_cache
        ) {
            return self::$CorsiDiLaureaCache;
        }
        $file_content = file_get_contents(__DIR__ . "/json_files/formule_laurea.json");
        if (!$file_content || strlen(trim($file_content)) === 0) {
            // File mancante
            return null;
        }
        $obj = json_decode($file_content, true);
        if (!$obj) {
            return null;
        }
        $arr = array();
        foreach ($obj as $nome => $cdl) {
            $arr[$nome] = new CorsoDiLaurea(
                $nome,
                $cdl['formula'],
                $cdl['cfu_richiesti'],
                $cdl['valore_lode'],
                $cdl['Tmin'], $cdl['Tmax'], $cdl['Tstep'],
                $cdl['Cmin'], $cdl['Cmax'], $cdl['Cstep'],
                $cdl['formula_email'],
                $cdl['email_commissione'],
                $cdl['durata']
            );
        }
        self::$CorsiDiLaureaCache = $arr;
        return $arr;
    }
    private static array|null $EsamiInformaticiCache = null;
    public static function EsamiInformatici(bool $reload_cache = false) : array|null
    {
        if (
            isset(self::$EsamiInformaticiCache) &&
            is_array(self::$EsamiInformaticiCache) && 
            !$reload_cache
        ) {
            return self::$EsamiInformaticiCache;
        }
        $file_content = file_get_contents(__DIR__ . "/json_files/esami_informatici.json");
        if (!$file_content || strlen(trim($file_content)) === 0) {
            // File mancante
            return null;
        }
        $obj = json_decode($file_content, true);
        if (!$obj) {
            return null;
        }
        if (!is_array($obj['nomi_esami'])) {
            return array();
        }
        return self::$EsamiInformaticiCache = $obj['nomi_esami'];
    }
    private static array $NomiIngInf = array(
        'INGEGNERIA INFORMATICA (IFO-L)',
        'T. ING. INFORMATICA',
    );
    public static function IngInf(string $cdl) : bool
    {
        return in_array(trim(strtoupper($cdl)), self::$NomiIngInf);
    }
}