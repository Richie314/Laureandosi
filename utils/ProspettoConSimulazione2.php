<?php
require_once __DIR__ . '/ProspettoPDFCommissione2.php';
require_once __DIR__ . '/ProspettoPDFLaureando2.php';

/**
 * @access public
 * @author franc
 */
class ProspettoConSimulazione2 extends ProspettoPDFLaureando2 {
	/**
	 * @AssociationType ProspettoPDFCommissione2
	 */


	/**
	 * @access public
	 * @return void
	 * @ReturnType void
	 */
	public function __construct(string|int $matricola, string $cdl_in, string $data_laurea) {
		parent::__construct($matricola, $cdl_in, $data_laurea);
	}
    public function generaProspettoConSimulazione(){
        $pdf = new FPDF();
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
	public function generaContenuto(FPDF $pdf) : FPDF 
    {
        $pdf = parent::generaProspetto($pdf);

        // ------------------------- PARTE DELLA SIMULAZIONE ------------------------------------

        $CFU = $this->_carrieraLaureando->creditiCheFannoMedia();
        $M = $this->_carrieraLaureando->restituisciMedia();

        // aggiungere al pdf le parti necessarie
        $pdf->Ln(4);
        $pdf->Cell(0, 5.5, "SIMULAZIONE DI VOTO DI LAUREA", 1, 1, 'C');
        $width = 190 / 2;
        $height = 4.5;

        // Scorciatoia
        $cdl = $this->_carrieraLaureando->_cdl;

        if ($cdl->C->InUso())
        {
            $pdf->Cell($width, $height, "VOTO COMMISSIONE (C)", 1, 0, 'C');
            $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
            $T = 0;

            for ($C = $cdl->C->Min; $C <= $cdl->C->Max; $C += $cdl->C->Step) {
                $voto = $cdl->CalcolaFormula($M, $CFU, $T, $C);
                //$voto = intval($voto);
                $pdf->Cell($width, $height, $C, 1, 0, 'C');
                $pdf->Cell($width, $height, $voto, 1, 1, 'C');
            }
        } elseif ($cdl->T->InUso()) {
            $pdf->Cell($width, $height, "VOTO TESI (T)", 1, 0, 'C');
            $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
            $C = 0;

            for ($T = $cdl->T->Min; $T <= $cdl->T->Max; $T += $cdl->T->Step) {
                $voto = $cdl->CalcolaFormula($M, $CFU, $T, $C);
                $pdf->Cell($width, $height, $T, 1, 0, 'C');
                $pdf->Cell($width, $height, $voto, 1, 1, 'C');
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
	public function generaRiga(FPDF $pdf) {
        $width = 190 / 4;
        $height = 5;
        $pdf->Cell($width, $height, $this->_carrieraLaureando->_cognome, 1, 0, 'L');
        $pdf->Cell($width, $height, $this->_carrieraLaureando->_nome, 1, 0, 'L');
        $pdf->Cell($width, $height, "", 1, 0, 'C');
        // è vuoto apposta, il cdl è scritto sopra. nell'esempio era così
        $pdf->Cell($width, $height, "/110", 1, 1, 'C');
        return $pdf;
	}
}
