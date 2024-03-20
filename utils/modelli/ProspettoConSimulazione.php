<?php
require_once __DIR__ . '/ProspettoLaureando.php';

/**
 * @access public
 * @author franc
 */
class ProspettoConSimulazione extends ProspettoLaureando {
	/**
	 * @AssociationType ProspettoPDFCommissione2
	 */


	/**
	 * @access public
	 * @return void
	 * @ReturnType void
	 */
	public function __construct(string|int $matricola, string $cdl_in, string $data_laurea)
    {
		parent::__construct($matricola, $cdl_in, $data_laurea);
	}
    public function generaProspettoConSimulazione(): FPDF
    {
        $pdf = parent::generaProspetto(null);
        $pdf = $this->generaContenuto($pdf);
        return $pdf;
    }
	/**
	 * @access public
	 * @param FPDF aPdf
	 * @return FPDF
	 * @ParamType aPdf FPDF
	 * @ReturnType FPDF
	 */
	public function generaContenuto(FPDF $pdf): FPDF 
    {
        $pdf = parent::generaProspetto($pdf);

        // ------------------------- PARTE DELLA SIMULAZIONE ------------------------------------

        $CFU = $this->CarrieraLaureando->creditiCheFannoMedia();
        $M = $this->CarrieraLaureando->restituisciMedia();

        // aggiungere al pdf le parti necessarie
        $pdf->Ln(4);
        $pdf->Cell(0, 5.5, "SIMULAZIONE DI VOTO DI LAUREA", 1, 1, 'C');
        $width = 190 / 2;
        $height = 4.5;

        // Scorciatoia
        $cdl = $this->CarrieraLaureando->Cdl;

        if ($cdl->C->inUso()) {
            $pdf->Cell($width, $height, "VOTO COMMISSIONE (C)", 1, 0, 'C');
            $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
            $T = 0;

            foreach ($cdl->C->getValues() as $C)
            {
                $voto = $cdl->CalcolaFormula($M, $CFU, $T, $C);
                $pdf->Cell($width, $height, $C, 1, 0, 'C');
                $pdf->Cell($width, $height, number_format($voto, 1), 1, 1, 'C');
            }
        } elseif ($cdl->T->inUso()) {
            $pdf->Cell($width, $height, "VOTO TESI (T)", 1, 0, 'C');
            $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
            $C = 0;

            foreach ($cdl->T->getValues() as $T)
            {
                $voto = $cdl->CalcolaFormula($M, $CFU, $T, $C);
                $pdf->Cell($width, $height, $T, 1, 0, 'C');
                $pdf->Cell($width, $height, number_format($voto, 1), 1, 1, 'C');
            }
        }

        return $pdf;
	}

	/**
	 * @access public
	 * @param FPDF pdf
	 * @return FPDF
	 * @ParamType aPdf FPDF
	 * @ReturnType FPDF
	 */
	public function generaRiga(FPDF $pdf): FPDF {
        $width = 190 / 4;
        $height = 5;
        $pdf->Cell($width, $height, $this->CarrieraLaureando->Cognome, 1, 0, 'L');
        $pdf->Cell($width, $height, $this->CarrieraLaureando->Nome, 1, 0, 'L');
        $pdf->Cell($width, $height, "", 1, 0, 'C');
        // è vuoto apposta, il cdl è scritto sopra. nell'esempio era così
        $pdf->Cell($width, $height, "/110", 1, 1, 'C');
        return $pdf;
	}
}
