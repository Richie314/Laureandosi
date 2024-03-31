<?php

class Test {
    private string $FunzioneDaChiamare;
    private array $ParametriChiamata;
    private $RisultatoCorretto;
    private string $Details = "";
    public function __construct(
        string $nome,
        array $parametri,
        $ris,
    ) {
        $this->FunzioneDaChiamare = $nome;
        $this->ParametriChiamata = $parametri;
        $this->RisultatoCorretto = $ris;
    }

    public function test(): bool
    {
        $this->Details = "";
        $ret = true;
        ob_start();
        try {
            echo "Chiamata di '$this->FunzioneDaChiamare'" . PHP_EOL;
            $out = call_user_func_array(
                get_class($this) . '::' . $this->FunzioneDaChiamare, 
                $this->ParametriChiamata);
            if ($out != $this->RisultatoCorretto) {
                $ret = false;
                echo "Risultato scorretto." . PHP_EOL;
                if (isset($out)) {
                    echo 
                        "\tOttenuto: " . PHP_EOL . 
                        str_replace(PHP_EOL, PHP_EOL . "\t", json_encode($out, JSON_PRETTY_PRINT)) . PHP_EOL; 
                } else {
                    echo "\tOttenuto: null" . PHP_EOL; 
                }
                if (isset($this->RisultatoCorretto)) {
                    echo 
                        "\tAtteso: " . PHP_EOL . 
                        str_replace(PHP_EOL, PHP_EOL . "\t", json_encode($this->RisultatoCorretto, JSON_PRETTY_PRINT)) . PHP_EOL; 
                } else {
                    echo "\tAtteso: null" . PHP_EOL; 
                }
            }
        } catch (Exception|Error $ex) {
            echo PHP_EOL . "Errore: " . $ex->getMessage() . PHP_EOL . PHP_EOL;
            echo "\tStackTrace: " . $ex->getTraceAsString() . PHP_EOL;
            $ret = false;
        }
        $out = ob_get_clean();
        if (is_string($out))
            $this->Details = $out;
        return $ret;
    }
    public function lastCallDetails(): string
    {
        return $this->Details;
    }
}