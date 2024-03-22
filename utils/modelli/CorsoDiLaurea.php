<?php
require_once __DIR__ . "/ParametroFormula.php";
class CorsoDiLaurea
{
    public string $Formula;
    public int $CFURichiesti = 180;
    public ParametroFormula $T;
    public ParametroFormula $C;
    public int $ValoreLode = 33;
    public int $Durata = 3;
    public string $Nome;
    public string $FormulaEmail;
    public string $EmailCommissione;

    public function CalcolaFormula(string|float $M, string|int $CFU, string|int|float $T = 0, string|int|float $C = 0) : float|null
    {
        if (strlen(trim($this->Formula)) === 0)
        {
            return null;
        }
        if (!isset($T) || !$this->T->valido($T))
        {
            $T = 0;
        }
        if (!isset($C) || !$this->C->valido($C))
        {
            $C = 0;
        }
        try {
            $M = (float)$M;
            $CFU = (int)$CFU;
            $T = (float)$T;
            $C = (float)$C;
            return (float)eval('return ' . preg_replace_callback('/([A-Z]+)/i', function ($matches) {
                $a = $matches[1];
                return "$$a";
            }, $this->Formula) . ';');
        } catch (Exception $ex) {
            return null;
        }
    }
    public function FineBonus(int $anno_immatricolazione) : string
    {
        return ($anno_immatricolazione + 1 + $this->Durata) . "-05-01";
    }
    public function __construct(
        string $nome,
        string|null $formula, 
        string|int|null $cfu, 
        string|int $valoreLode,
        string|int|null $tMin, string|int|null $tMax, string|int|null $tStep,
        string|int|null $cMin, string|int|null $cMax, string|int|null $cStep,
        string|null $corpo_email,
        string $email_commissione,
        string|int|null $durata = null)
    {
        $this->Nome = trim($nome);
        if (isset($cfu))
        {
            $this->CFURichiesti = (int)$cfu;
        }
        $this->ValoreLode = (int)$valoreLode;
        $this->Formula = isset($formula) ? $formula : '110 * M / 30';
        
        $this->T = new ParametroFormula($tMin, $tMax, $tStep);
        $this->C = new ParametroFormula($cMin, $cMax, $cStep);
        if (isset($durata))
        {
            $this->Durata = (int)$durata;
        }
        $this->FormulaEmail = isset($corpo_email) ? $corpo_email : "Ecco i prospetti";
        $this->EmailCommissione = $email_commissione;
    }
    public function __toString() : string
    {
        return $this->Nome;
    }
};
