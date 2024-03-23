<?php
require_once __DIR__ . '/CarrieraLaureando.php';
class CarrieraLaureandoInformatica extends CarrieraLaureando
{
    private float $MediaEsamiInformatici;
    private float $MediaConBonus = 0.0;
    private bool $Bonus = false;
    
    public function __construct(string|int $matricola, string|CorsoDiLaurea $cdl, string $dataLaurea)
    {
        parent::__construct($matricola, $cdl);
        
        if ($dataLaurea < $this->Cdl->fineBonus($this->AnnoImmatricolazione)) {
            $this->Bonus = true;
            $this->MediaConBonus = $this->applicaBonus();
        }

        $esami_info = Configurazione::esamiInformatici();
        if (!isset($esami_info)) {
            throw new Exception("Impossibile caricare gli esami informatici");
        }

        for ($i = 0; $i < sizeof($this->Esami); $i++)
        {
            if (in_array($this->Esami[$i]->NomeEsame, $esami_info)) {
                $this->Esami[$i]->Informatico = true;
            }
        }
        $this->MediaEsamiInformatici = $this->calcolaMediaEsamiInformatici();
    }

    public function getMediaEsamiInformatici(): float
    {
        return $this->MediaEsamiInformatici;
    }
    private function calcolaMediaEsamiInformatici(): float
    {
        $somma = 0;
        $numero = 0;
        for ($i = 0; $i < sizeof($this->Esami); $i++) {
            if ($this->Esami[$i]->FaMedia && $this->Esami[$i]->Informatico) {
                $somma += $this->Esami[$i]->VotoEsame;
                $numero++;
            }
        }
        if ($numero === 0)
            return 0.0;
        return (float)$somma / $numero;
    }
    public function getBonus(): string
    {
        return $this->Bonus ? "SI" : "NO";
    }
    private function applicaBonus(): float
    {
        $voto_min = null;
        $indice_min = null;

        for ($i = 0; $i < sizeof($this->Esami); $i++)
        {
            if ($this->Esami[$i]->FaMedia) {
                if (!isset($voto_min) || $this->Esami[$i]->VotoEsame < $voto_min) {
                    $indice_min = $i;
                    $voto_min = (int)$this->Esami[$i]->VotoEsame;
                }
            }
        }
        $this->Esami[$indice_min]->FaMedia = false; // Rimuovo l'esame peggiore
        $media_bonus = $this->calcolaMedia(); // Calcolo la media vendo escluso l'esame
        $this->Esami[$indice_min]->FaMedia = true; // Reinserisco l'esame che avevo rimosso
        return $media_bonus;
    }
    public function restituisciMedia(): float
    {
        return $this->Bonus ? $this->MediaConBonus : parent::restituisciMedia();
    }
}