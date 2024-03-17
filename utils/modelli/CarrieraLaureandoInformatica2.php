<?php
require_once __DIR__ . '/CarrieraLaureando2.php';
class CarrieraLaureandoInformatica2 extends CarrieraLaureando2
{
    private float $mediaEsamiInformatici;
    private float $mediaConBonus = 0.0;
    private bool $bonus = false;
    
    public function __construct(string|int $matricola, string|CorsoDiLaurea $cdl_in, string $dataLaurea)
    {
        parent::__construct($matricola, $cdl_in);
        
        if ($dataLaurea < $this->_cdl->FineBonus($this->_anno_immatricolazione))
        {
            $this->bonus = true;
            $this->mediaConBonus = $this->applicaBonus();
        }

        $esami_info = Configurazione::EsamiInformatici();
        if (!isset($esami_info))
        {
            throw new Exception("Impossibile caricare gli esami informatici");
        }

        for ($i = 0; $i < sizeof($this->_esami); $i++)
        {
            if (in_array($this->_esami[$i]->_nomeEsame, $esami_info))
            {
                $this->_esami[$i]->_informatico = true;
            }
        }
        $this->mediaEsamiInformatici = $this->calcolaMediaEsamiInformatici();
    }

    public function getMediaEsamiInformatici() : float
    {
        return $this->mediaEsamiInformatici;
    }
    private function calcolaMediaEsamiInformatici() : float
    {
        $somma = 0;
        $numero = 0;
        for ($i = 0; $i < sizeof($this->_esami); $i++) {
            if ($this->_esami[$i]->_faMedia && $this->_esami[$i]->_informatico)
            {
                $somma += $this->_esami[$i]->_votoEsame;
                $numero++;
            }
        }
        if ($numero === 0)
            return 0.0;
        return (float)$somma / $numero;
    }
    public function getBonus() : string
    {
        return $this->bonus ? "SI" : "NO";
    }
    private function applicaBonus() : float
    {
        $voto_min = null;
        $indice_min = null;

        for ($i = 0; $i < sizeof($this->_esami); $i++)
        {
            if ($this->_esami[$i]->_faMedia)
            {
                if (!isset($voto_min) || $this->_esami[$i]->_votoEsame < $voto_min)
                {
                    $indice_min = $i;
                    $voto_min = (int)$this->_esami[$i]->_votoEsame;
                }
            }
        }
        $this->_esami[$indice_min]->_faMedia = false; // Rimuovo l'esame peggiore
        $media_bonus = $this->calcola_media(); // Calcolo la media vendo escluso l'esame
        $this->_esami[$indice_min]->_faMedia = true; // Reinserisco l'esame che avevo rimosso
        return $media_bonus;
    }
    public function restituisciMedia() : float
    {
        return $this->bonus ? $this->mediaConBonus : parent::restituisciMedia();
    }
}