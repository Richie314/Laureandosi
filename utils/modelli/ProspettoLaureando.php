<?php
require_once __DIR__ . '/CarrieraLaureandoInformatica.php';
require_once dirname(__DIR__) . '/Configurazione.php';
require_once dirname(__DIR__, 2) . '/lib/fpdf184/fpdf.php';

class ProspettoLaureando {
	public CarrieraLaureando|CarrieraLaureandoInformatica $CarrieraLaureando;
	private string $DataLaurea;
    private static array $CarriereCache = array();

    public function __construct(string|int $matricola, CorsoDiLaurea|string $cdl, string $dataLaurea)
    {
        if (array_key_exists((int)$matricola, self::$CarriereCache)) {
            // Cache hit
            $this->CarrieraLaureando = self::$CarriereCache[(int)$matricola];
        } else {
            // Cache miss
            $this->CarrieraLaureando = 
                Configurazione::ingInf($cdl) ? 
                new CarrieraLaureandoInformatica($matricola, $cdl, $dataLaurea) :
                new CarrieraLaureando($matricola, $cdl);
            // Salvo in cache per non dover ricaricare dopo
            self::$CarriereCache[(int)$matricola] = $this->CarrieraLaureando;  
        }
        $this->DataLaurea = $dataLaurea;
	}

	public function generaProspetto(FPDF|null $pdf = null): FPDF
    {
        // genera il prospetto in pdf e lo salva in un percorso specifico
        // dati utili;
        $font_family = "Arial";

        if (!isset($pdf) || !($pdf instanceof FPDF))
            $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont($font_family, "", 16);
        // --------------------- INTESTAZIONE : cdl e scritta prospetto --------------------------

        $pdf->Cell(0, 6, $this->CarrieraLaureando->Cdl, 0, 1, 'C');
        // dimensioni, testo, bordo, a capo, align
        $pdf->Cell(0, 8, 'CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA', 0, 1, 'C');
        $pdf->Ln(2);
        // ------------------------------ INFORMAZIONI ANAGRAFICHE DELLO STUDENTE ------------------------------

        $pdf->SetFont($font_family, "", 9);
        $anagrafica_stringa = "Matricola:                       " . $this->CarrieraLaureando->Matricola . //attenzione: quelli che sembrano spazi in realtà sono &Nbsp perché fpdf non stampa spazi
            "\nNome:                            " . $this->CarrieraLaureando->Nome .
            "\nCognome:                      " . $this->CarrieraLaureando->Cognome .
            "\nEmail:                             " . $this->CarrieraLaureando->Email .
            "\nData:                              " . $this->DataLaurea;
        //aggiungere bonus if inf

        if ($this->CarrieraLaureando instanceof CarrieraLaureandoInformatica) {
            $anagrafica_stringa .= "\nBonus:                            " . $this->CarrieraLaureando->getBonus();
        }

        $pdf->MultiCell(0, 6, $anagrafica_stringa, 1, 'L');
        //$pdf->Cell(0, 100 ,$anagrafica_stringa, 1 ,1, '');
        $pdf->Ln(3);
        // spazio bianco

        // ------------------------------- INFORMAZIONI SUGLI ESAMI ----------------------------------------
        // 1 pag = 190 = 21cm con bordi di 1cm
        $larghezza_piccola = 12;
        $altezza = 5.5;
        $larghezza_grande = 190 - (3 * $larghezza_piccola);
        if ($this->CarrieraLaureando instanceof CarrieraLaureandoInformatica) {
            $larghezza_piccola -= 1;
            $larghezza_grande = 190 - (4 * $larghezza_piccola);
            $pdf->Cell($larghezza_grande, $altezza, "ESAME", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "CFU", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "VOT", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "MED", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "INF", 1, 1, 'C');
            // newline
        } else {
            $pdf->Cell($larghezza_grande, $altezza, "ESAME", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "CFU", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "VOT", 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, "MED", 1, 1, 'C');
            // newline
        }

        $altezza = 4;
        $pdf->SetFont($font_family, "", 8);
        for ($i = 0; $i < sizeof($this->CarrieraLaureando->Esami); $i++) {
            $esame = $this->CarrieraLaureando->Esami[$i];
            $this->rigaEsameTabella(
                $larghezza_grande,
                $larghezza_piccola,
                $altezza,
                $esame,
                $pdf
            );
        }
        $pdf->Ln(5);
        // ------------------------------- PARTE RIASUNTIVA  ----------------------------------------
        $pdf->SetFont($font_family, "", 9);
        $string = 
            "Media Pesata (M):                                                  " . number_format($this->CarrieraLaureando->restituisciMedia(), 3) .
            "\nCrediti che fanno media (CFU):                             " . $this->CarrieraLaureando->creditiCheFannoMedia() .
            "\nCrediti curriculari conseguiti:                                  " . $this->CarrieraLaureando->creditiCurricolariConseguiti() .
            "\nFormula calcolo voto di laurea:                               " . $this->CarrieraLaureando->restituisciFormula();
        if ($this->CarrieraLaureando instanceof CarrieraLaureandoInformatica) {
            $string .= "\nMedia pesata esami INF:                                        " . number_format($this->CarrieraLaureando->getMediaEsamiInformatici(), 3);
        }

        $pdf->MultiCell(0, 6, $string, 1, "L");

        return $pdf;
	}
    private function rigaEsameTabella(
        int $larghezzaGrande, 
        int $larghezzaPiccola,
        int $altezza, 
        EsameLaureando $esame, 
        FPDF $pdf,
    ):void {
        if (!$esame->Curricolare) {
            return;
        }
        $pdf->Cell($larghezzaGrande, $altezza, 
            $esame->NomeEsame 
            /*. " (" . $esame->DataEsame->format("Y-m-d") . ")"*/, 1, 0, 'L');
        $pdf->Cell($larghezzaPiccola, $altezza, $esame->Cfu, 1, 0, 'C');
        $pdf->Cell($larghezzaPiccola, $altezza, $esame->VotoEsame, 1, 0, 'C');

        if ($this->CarrieraLaureando instanceof CarrieraLaureandoInformatica) {
            $pdf->Cell($larghezzaPiccola, $altezza, ($esame->FaMedia) ? 'X' : '', 1, 0, 'C');
            $pdf->Cell($larghezzaPiccola, $altezza, ($esame->Informatico) ? 'X' : '', 1, 1, 'C');
        } else {
            $pdf->Cell($larghezzaPiccola, $altezza, ($esame->FaMedia) ? 'X' : '', 1, 1, 'C');
            // newline
        }
    }
}
