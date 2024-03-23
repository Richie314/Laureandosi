<?php
require_once __DIR__ . '/EsameLaureando.php';
require_once __DIR__ . '/CorsoDiLaurea.php';
require_once dirname(__DIR__) . '/GestioneCarrieraStudente2.php';
require_once dirname(__DIR__) . '/Configurazione.php';

class CarrieraLaureando
{

	public int $Matricola;

	public string $Nome;

	public string $Cognome;

	public CorsoDiLaurea $Cdl;

	public string $Email;

	public array $Esami;

	private float $Media;
    public ?int $AnnoImmatricolazione = null;

	public function __construct(string|int $matricola, string|CorsoDiLaurea $cdl)
    {
        $this->Matricola = $matricola = (int)$matricola;
        
        if ($cdl instanceof CorsoDiLaurea) {
            $this->Cdl = $cdl;
        } else {
            $this->Cdl = Configurazione::corsiDiLaurea()[$cdl];
        }

        $anagrafica_json = GestioneCarrieraStudente::restituisciAnagraficaStudente($matricola);
        if (!$anagrafica_json || strlen(trim($anagrafica_json)) === 0) {
            throw new Exception("Impossibile trovare dati anagrafici per la matricola '$matricola'");
        }
        $anagrafica = json_decode($anagrafica_json, true);

        $this->Nome = $anagrafica["Entries"]["Entry"]["nome"];
        $this->Cognome = $anagrafica["Entries"]["Entry"]["cognome"];
        $this->Email = $anagrafica["Entries"]["Entry"]["email_ate"];

        $carriera_json = GestioneCarrieraStudente::restituisciCarrieraStudente($matricola);
        if (!$carriera_json || strlen(trim($carriera_json)) === 0) {
            throw new Exception("Impossibile trovare carriera per la matricola '$matricola'");
        }
        $carriera = json_decode($carriera_json, true);
        $this->Esami = array();
        for ($i = 0; $i < sizeof($carriera["Esami"]["Esame"]); $i++) {
            if (!is_string($carriera["Esami"]["Esame"][$i]["DES"])) {
                continue;
            }
            $esame = new EsameLaureando(
                $carriera["Esami"]["Esame"][$i]["DES"],
                $carriera["Esami"]["Esame"][$i]["VOTO"],
                $carriera["Esami"]["Esame"][$i]["PESO"],
                $carriera["Esami"]["Esame"][$i]["DATA_ESAME"],
                true, 
                (int)$carriera["Esami"]["Esame"][$i]["SOVRAN_FLG"] === 0,
                $this->Cdl->ValoreLode
            );
            $this->Esami[] = $esame;
            $this->AnnoImmatricolazione = (int)$carriera["Esami"]["Esame"][$i]["ANNO_IMM"];
            
            /*
            if (
                $esame->_faMedia && 
                is_string($carriera["Esami"]["Esame"][$i]["CORSO"]) &&
                (string)$this->Cdl !== $carriera["Esami"]["Esame"][$i]["CORSO"])
            {
                $this_cdl = (string)$this->Cdl;
                $cdl_iscritto = (string)$carriera["Esami"]["Esame"][$i]["CORSO"];
                throw new LogicException(
                    "$matricola è iscritta a '$this_cdl' ma ha richiesto la laurea in '$cdl_iscritto'");
            }
            */
        }

        usort($this->Esami, function (EsameLaureando $a, EsameLaureando $b) {
            return $a->DataEsame <=> $b->DataEsame;
        });

        if (!isset($this->AnnoImmatricolazione)){
            throw new Exception("Nessun esame trovato per matricola '$matricola'");
        }
        $this->Media = $this->calcolaMedia();
    }
    public function calcolaMedia(): float
    {
        $somma_voto_cfu = 0;
        $somma_cfu_tot = 0;

        for ($i = 0; $i < sizeof($this->Esami); $i++)
        {
            if (!$this->Esami[$i]->FaMedia) {
                continue;
            }
            $somma_voto_cfu += $this->Esami[$i]->VotoEsame * $this->Esami[$i]->Cfu;
            //devi convertire il voto in un int prima
            $somma_cfu_tot += $this->Esami[$i]->Cfu;
        }
        return (float)$somma_voto_cfu / $somma_cfu_tot;
    }
    public function restituisciMedia(): float
    {
        return $this->Media;
    }

	public function creditiCurricolariConseguiti() : int
    {
        $crediti = 0;

        for ($i = 0; $i < sizeof($this->Esami); $i++)
        {
            $crediti += $this->Esami[$i]->CreditoCurriculare();
        }

        return $crediti;
	}

	public function restituisciFormula(): string
    {
		return $this->Cdl->Formula;
	}
    public function creditiCheFannoMedia(): int
    {
        $crediti = 0;

        for ($i = 0; $i < sizeof($this->Esami); $i++)
        {
            $crediti += $this->Esami[$i]->CreditoMedia();
        }

        return $crediti;
    }
}