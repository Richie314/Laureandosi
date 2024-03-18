<?php

class Test {
    private string $FunzioneDaChiamare;
    private array $ParametriChiamata;
    private $RisultatoCorretto;
    private string $details = "";
    public function __construct(
        string $nome,
        array $parametri,
        $ris
    )
    {
        $this->FunzioneDaChiamare = $nome;
        $this->ParametriChiamata = $parametri;
        $this->RisultatoCorretto = $ris;
    }

    public function Test() : bool
    {
        $this->details = "";
        $ret = true;
        ob_start();
        try {
            echo "Chiamata di '$this->FunzioneDaChiamare'" . PHP_EOL;
            $out = call_user_func_array(get_class($this) . '::' . $this->FunzioneDaChiamare, $this->ParametriChiamata);
            if ($out != $this->RisultatoCorretto)
            {
                $ret = false;
                echo "Risultato scorretto." . PHP_EOL;
                echo "\tOttenuto: " . json_encode($out, JSON_PRETTY_PRINT) . PHP_EOL; 
                echo "\tAtteso: " . json_encode($this->RisultatoCorretto, JSON_PRETTY_PRINT) . PHP_EOL; 
            }
        } catch (Exception|Error $ex)
        {
            echo "\nErrore: " . $ex->getMessage() . PHP_EOL;
            echo "\n\tErrore: " . $ex->getTraceAsString() . PHP_EOL;
            $ret = false;
        }
        $out = ob_get_clean();
        if (is_string($out))
            $this->details = $out;
        return $ret;
    }
    public function LastCallDetails() : string
    {
        return $this->details;
    }
}