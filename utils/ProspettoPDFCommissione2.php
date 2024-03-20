<?php
require_once __DIR__ . '/modelli/ProspettoConSimulazione2.php';
require_once __DIR__ . '/AccessoProspetti.php';

/**
 * @access public
 * @author franc
 */
class ProspettoPDFCommissione2 {
	/**
	 * @AttributeType int[]
	 */
	private array $_matricole = array();
	/**
	 * @AttributeType string
	 */
	private string $_dataLaurea;
	/**
	 * @AttributeType string
	 */
	private string $_cdl;
	/**
	 * @AssociationType ProspettoConSimulazione2
	 * @AssociationKind Composition
	 */


	/**
	 * @access public
	 * @param int[] aMatricole
	 * @param string aDataLaurea
	 * @param string aCdl
	 * @ParamType aMatricole int[]
	 * @ParamType aDataLaurea string
	 * @ParamType aCdl string
	 */
	public function __construct(array $aMatricole, string $aDataLaurea, string $aCdl)
    {
		$this->_matricole = (new ArrayObject(array_map("intval", $aMatricole)))->getArrayCopy();
        $this->_dataLaurea = $aDataLaurea;
        $this->_cdl = $aCdl;
	}

	/**
	 * @access public
	 * @return bool
	 * @ReturnType bool
	 */
	public function generaProspettiCommissione() : bool
    {
        $pdf = new FPDF();
        $font_family = "Arial";
        $pdf->AddPage();
        $pdf->SetFont($font_family, "", 14);
        // --------  PRIMA PAGINA CON LA LISTA ---------------------
        $pdf->Cell(0, 6, $this->_cdl, 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->SetFont($font_family, "", 16);
        $pdf->Cell(0, 6, 'LISTA LAUREANDI', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont($font_family, "", 14);
        $width = 190 / 4;
        $height = 5;
        $pdf->Cell($width, $height, "COGNOME", 1, 0, 'C');
        $pdf->Cell($width, $height, "NOME", 1, 0, 'C');
        $pdf->Cell($width, $height, "CDL", 1, 0, 'C');
        $pdf->Cell($width, $height, "VOTO LAUREA", 1, 1, 'C');
        $pdf->SetFont($font_family, "", 12);
        for ($i = 0; $i < sizeof($this->_matricole); $i++) {
            try {
                $pag_con_simulazione = new ProspettoConSimulazione2(
                    $this->_matricole[$i], $this->_cdl, $this->_dataLaurea);
                $pdf = $pag_con_simulazione->generaRiga($pdf);
            } catch (Exception $ex) {}
        }

        // --------  PAGINE CON LA CARRIERA ---------------------
        // aggiungo la pagina di ogni laureando
        for ($i = 0; $i < sizeof($this->_matricole); $i++) {
            try {
                $pag_con_simulazione = new ProspettoConSimulazione2(
                    $this->_matricole[$i], $this->_cdl, $this->_dataLaurea);
                $pdf = $pag_con_simulazione->generaContenuto($pdf);
            } catch (Exception $ex) {};
        }

        $path = AccessoProspetti::pathCommissioneServer();
        $pdf->Output('F', $path);
        return file_exists($path);
    }
    public function generaProspettiLaureandi() : int
    {
        $totale = 0;
        for ($i = 0; $i < sizeof($this->_matricole); $i++)
        {
            try {
                $prospetto = new ProspettoPdfLaureando2($this->_matricole[$i], $this->_cdl, $this->_dataLaurea);
                $pdf = $prospetto->generaProspetto();
                
                $path = AccessoProspetti::pathLaureandoServer($this->_matricole[$i]);
                $pdf->Output('F', $path);
                if (file_exists($path))
                    $totale++;
            } catch (Exception $ex) { }
        }
        return $totale;
    }
    public function generaProspetti(): int {
        $tot = $this->generaProspettiLaureandi();
        if ($tot === 0)
            return 0;
        if ($this->generaProspettiCommissione()) {
            return $tot + 1;
        }
        return $tot;
    }
    public function popolaJSON() : bool
    {
        require_once __DIR__ . '/InvioPDFLaureando2.php';
        $obj = InvioPDFLaureando2::Generate($this->_matricole, $this->_cdl, $this->_dataLaurea);
        return isset($obj);
    }
}
