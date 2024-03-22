<?php
require_once __DIR__ . "/modelli/CorsoDiLaurea.php";
class Configurazione
{
    private static array|null $CorsiDiLaureaCache = null;
    public static function corsiDiLaurea(bool $reload_cache = false) : array|null
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
        foreach ($obj as $nome_corto => $cdl) {
            $arr[$nome_corto] = new CorsoDiLaurea(
                $cdl['nome'],
                $cdl['formula'],
                $cdl['cfu_richiesti'],
                $cdl['valore_lode'],
                $cdl['tMin'], $cdl['tMax'], $cdl['tStep'],
                $cdl['cMin'], $cdl['cMax'], $cdl['cStep'],
                $cdl['formula_email'],
                $cdl['email_commissione'],
                $cdl['durata']
            );
        }
        self::$CorsiDiLaureaCache = $arr;
        return $arr;
    }
    private static array|null $EsamiInformaticiCache = null;
    public static function esamiInformatici(bool $reload_cache = false) : array|null
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
        'T-INF',
    );
    public static function ingInf(string $cdl) : bool
    {
        return in_array(trim(strtoupper($cdl)), self::$NomiIngInf);
    }
    public static function salvaCdl(string $short, CorsoDiLaurea $cdl): bool {
        if (empty($short)) {
            return false;
        }
        $esistenti = self::corsiDiLaurea(true);
        if (!isset($esistenti)) {
            return false;
        }
        $esistenti[$short] = $cdl;
        return self::salvaSuFile($esistenti);
    }
    private static function salvaSuFile(array $corsi): bool {
        $array = array_map(function (CorsoDiLaurea $cdl) : array {
            return array (
                'nome' => $cdl->Nome,
                'formula' => $cdl->Formula,
                'cfu_richiesti' => $cdl->CFURichiesti,
                'valore_lode' => $cdl->ValoreLode,
                'formula_email' => $cdl->FormulaEmail,
                'email_commissione' => $cdl->EmailCommissione,
                'durata' => $cdl->Durata,

                'tMin' => $cdl->T->Min,
                'tMax' => $cdl->T->Max,
                'tStep' => $cdl->T->Step,

                'cMin' => $cdl->C->Min,
                'cMax' => $cdl->C->Max,
                'cStep' => $cdl->C->Step,
            );
        }, $corsi);
        $stringa = json_encode($array, JSON_PRETTY_PRINT);
        return (int)file_put_contents(__DIR__ . "/json_files/formule_laurea.json", $stringa) > 0;
    }
}