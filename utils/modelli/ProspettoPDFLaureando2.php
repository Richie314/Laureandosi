<?php
require_once __DIR__ . '/CarrieraLaureandoInformatica2.php';
require_once dirname(__DIR__) . '/Configurazione.php';
require_once dirname(__DIR__, 2) . '/lib/fpdf184/fpdf.php';
/**
 * @access public
 * @author franc
 */

class ProspettoPDFLaureando2 {
	/**
	 * @AttributeType CarrieraLaureando
	 */
	public CarrieraLaureando2|CarrieraLaureandoInformatica2 $_carrieraLaureando;
	/**
	 * @AttributeType int
	 */
	protected int $_matricola;
	/**
	 * @AttributeType string
	 */
	protected string $_dataLaurea;


	/**
	 * @access public
	 * @param int aMatricola
	 * @param string aCdl
	 * @param string aDataLaurea
	 * @ParamType aMatricola int
	 * @ParamType aCdl string
	 * @ParamType aDataLaurea string
	 */
	public function __construct(string|int $aMatricola, string $aCdl, string $aDataLaurea)
    {
        $this->_carrieraLaureando = 
            Configurazione::IngInf($aCdl) ? 
            new CarrieraLaureandoInformatica2($aMatricola, $aCdl, $aDataLaurea) :
            new CarrieraLaureando2($aMatricola, $aCdl);
        $this->_matricola = (int)$aMatricola;
        $this->_dataLaurea = $aDataLaurea;
	}

	/**
	 * @access public
	 * @return FPDF
	 * @ReturnType FPDF
	 */
	public function generaProspetto(FPDF|null $pdf = null) : FPDF
    {
        // genera il prospetto in pdf e lo salva in un percorso specifico
        // dati utili;
        $font_family = "Arial";

        if (!isset($pdf) || !($pdf instanceof FPDF))
            $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont($font_family, "", 16);
        // --------------------- INTESTAZIONE : cdl e scritta prospetto --------------------------

        $pdf->Cell(0, 6, $this->_carrieraLaureando->_cdl, 0, 1, 'C');
        // dimensioni, testo, bordo, a capo, align
        $pdf->Cell(0, 8, 'CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA', 0, 1, 'C');
        $pdf->Ln(2);
        // ------------------------------ INFORMAZIONI ANAGRAFICHE DELLO STUDENTE ------------------------------

        $pdf->SetFont($font_family, "", 9);
        $anagrafica_stringa = "Matricola:                       " . $this->_matricola . //attenzione: quelli che sembrano spazi in realtà sono &Nbsp perché fpdf non stampa spazi
            "\nNome:                            " . $this->_carrieraLaureando->_nome .
            "\nCognome:                      " . $this->_carrieraLaureando->_cognome .
            "\nEmail:                             " . $this->_carrieraLaureando->_email .
            "\nData:                              " . $this->_dataLaurea;
        //aggiungere bonus if inf

        if ($this->_carrieraLaureando instanceof CarrieraLaureandoInformatica2)
        {
            $anagrafica_stringa .= "\nBonus:                            " . $this->_carrieraLaureando->getBonus();
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
        if ($this->_carrieraLaureando instanceof CarrieraLaureandoInformatica2)
        {
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
        for ($i = 0; $i < sizeof($this->_carrieraLaureando->_esami); $i++) {
            $esame = $this->_carrieraLaureando->_esami[$i];
            $this->RigaEsameTabella(
                $larghezza_grande,
                $larghezza_piccola,
                $altezza,
                $esame,
                $pdf);
        }
        $pdf->Ln(5);
        // ------------------------------- PARTE RIASUNTIVA  ----------------------------------------
        $pdf->SetFont($font_family, "", 9);
        $string = "Media Pesata (M):                                                  " . $this->_carrieraLaureando->restituisciMedia() .
            "\nCrediti che fanno media (CFU):                             " . $this->_carrieraLaureando->creditiCheFannoMedia() .
            "\nCrediti curriculari conseguiti:                                  " . $this->_carrieraLaureando->creditiCurricolariConseguiti() .
            "\nFormula calcolo voto di laurea:                               " . $this->_carrieraLaureando->restituisciFormula();
        if ($this->_carrieraLaureando instanceof CarrieraLaureandoInformatica2)
        {
            $string .= "\nMedia pesata esami INF:                                        " . $this->_carrieraLaureando->getMediaEsamiInformatici();
        }

        $pdf->MultiCell(0, 6, $string, 1, "L");

        return $pdf;
	}
    private function RigaEsameTabella(
        int $larghezza_grande, 
        int $larghezza_piccola,
        int $altezza, 
        EsameLaureando2 $esame, 
        FPDF &$pdf)
    {
        $pdf->Cell($larghezza_grande, $altezza, $esame->_nomeEsame /*. " (" . $esame->_dataEsame->format("Y-m-d") . ")"*/, 1, 0, 'L');
        $pdf->Cell($larghezza_piccola, $altezza, $esame->_cfu, 1, 0, 'C');
        $pdf->Cell($larghezza_piccola, $altezza, $esame->_votoEsame, 1, 0, 'C');
        if ($this->_carrieraLaureando instanceof CarrieraLaureandoInformatica2) {
            $pdf->Cell($larghezza_piccola, $altezza, ($esame->_faMedia) ? 'X' : '', 1, 0, 'C');
            $pdf->Cell($larghezza_piccola, $altezza, ($esame->_informatico) ? 'X' : '', 1, 1, 'C');
        } else {
            $pdf->Cell($larghezza_piccola, $altezza, ($esame->_faMedia) ? 'X' : '', 1, 1, 'C');
            // newline
        }
    }
}
