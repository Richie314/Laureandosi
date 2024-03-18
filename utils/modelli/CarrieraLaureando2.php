<?php
require_once __DIR__ . '/EsameLaureando2.php';
require_once __DIR__ . '/CorsoDiLaurea.php';
require_once dirname(__DIR__) . '/GestioneCarrieraStudente2.php';

class CarrieraLaureando2
{

	public int $_matricola;

	public string $_nome;

	public string $_cognome;

	public CorsoDiLaurea $_cdl;

	public string $_email;

	public array $_esami;

	private float $_media;
    public ?int $_anno_immatricolazione = null;

	public function __construct(string|int $matricola, string|CorsoDiLaurea $cdl_in)
    {
        $this->_matricola = (int)$matricola;
        
        if ($cdl_in instanceof CorsoDiLaurea)
        {
            $this->_cdl = $cdl_in;
        } else {
            $this->_cdl = Configurazione::CorsiDiLaurea()[$cdl_in];
        }

        $anagrafica_json = GestioneCarrieraStudente::restituisciAnagraficaStudente($matricola);
        if (!$anagrafica_json || strlen(trim($anagrafica_json)) === 0)
        {
            throw new Exception("Impossibile trovare dati anagrafici per la matricola '$matricola'");
        }
        $anagrafica = json_decode($anagrafica_json, true);

        $this->_nome = $anagrafica["Entries"]["Entry"]["nome"];
        $this->_cognome = $anagrafica["Entries"]["Entry"]["cognome"];
        $this->_email = $anagrafica["Entries"]["Entry"]["email_ate"];

        $carriera_json = GestioneCarrieraStudente::restituisciCarrieraStudente($matricola);
        if (!$carriera_json || strlen(trim($carriera_json)) === 0)
        {
            throw new Exception("Impossibile trovare carriera per la matricola '$matricola'");
        }
        $carriera = json_decode($carriera_json, true);
        $this->_esami = array();
        for ($i = 0; $i < sizeof($carriera["Esami"]["Esame"]); $i++)
        {
            if (!is_string($carriera["Esami"]["Esame"][$i]["DES"]))
            {
                continue;
            }
            $esame = new EsameLaureando2(
                $carriera["Esami"]["Esame"][$i]["DES"],
                $carriera["Esami"]["Esame"][$i]["VOTO"],
                $carriera["Esami"]["Esame"][$i]["PESO"],
                $carriera["Esami"]["Esame"][$i]["DATA_ESAME"],
                true, true,
                $this->_cdl->ValoreLode
            );
            $this->_esami[] = $esame;
            $this->_anno_immatricolazione = (int)$carriera["Esami"]["Esame"][$i]["ANNO_IMM"];
            
            /*
            if (
                $esame->_faMedia && 
                is_string($carriera["Esami"]["Esame"][$i]["CORSO"]) &&
                (string)$this->_cdl !== $carriera["Esami"]["Esame"][$i]["CORSO"])
            {
                $this_cdl = (string)$this->_cdl;
                $cdl_iscritto = (string)$carriera["Esami"]["Esame"][$i]["CORSO"];
                throw new LogicException(
                    "$matricola è iscritta a '$this_cdl' ma ha richiesto la laurea in '$cdl_iscritto'");
            }
            */
        }

        usort($this->_esami, function (EsameLaureando2 $a, EsameLaureando2 $b) {
            return ($a->_dataEsame < $b->_dataEsame) ? (-1) : 1;
        });

        if (!isset($this->_anno_immatricolazione))
        {
            throw new Exception("Nessun esame trovato per matricola '$matricola'");
        }
        $this->_media = $this->calcola_media();
    }
    public function calcola_media() : float
    {
        $esami = $this->_esami;
        $somma_voto_cfu = 0;
        $somma_cfu_tot = 0;

        for ($i = 0; $i < sizeof($this->_esami); $i++)
        {
            if ($esami[$i]->_faMedia)
            {
                $somma_voto_cfu += $esami[$i]->_votoEsame * $this->_esami[$i]->_cfu;
                //devi convertire il voto in un int prima
                $somma_cfu_tot += $this->_esami[$i]->_cfu;
            }
        }
        return (float)$somma_voto_cfu / $somma_cfu_tot;
    }
    public function restituisciMedia(): float
    {
        return $this->_media;
    }

	public function creditiCurricolariConseguiti() : int
    {
        $crediti = 0;
        for ($i = 0; sizeof($this->_esami) > $i; $i++) {
            if (
                $this->_esami[$i]->_nomeEsame != "PROVA FINALE" &&  
                $this->_esami[$i]->_nomeEsame != "LIBERA SCELTA PER RICONOSCIMENTI") {
                $crediti += ($this->_esami[$i]->_curricolare == 1) ? $this->_esami[$i]->_cfu : 0;
            }
        }
        return $crediti;
	}

	public function restituisciFormula() : string
    {
		return $this->_cdl->Formula;
	}
    public function creditiCheFannoMedia() : int
    {
        $crediti = 0;

        for ($i = 0; $i < sizeof($this->_esami); $i++)
        {
            $crediti += $this->_esami[$i]->Credito();
        }

        return $crediti;
    }
}