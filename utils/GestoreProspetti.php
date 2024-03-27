<?php
require_once __DIR__ . '/modelli/ProspettoConSimulazione.php';
require_once __DIR__ . '/AccessoProspetti.php';

class GestoreProspetti {
	private array $Matricole = array();
	private string $DataLaurea;
	private string $Cdl;
    private array $ListaEmail = array();

	public function __construct(array $matricole, string $dataLaurea, string $cdl)
    {
		$this->Matricole = array_map("intval", $matricole);
        $this->DataLaurea = $dataLaurea;
        $this->Cdl = $cdl;
	}

	public function generaProspettiCommissione(): bool
    {
        $pdf = new FPDF();
        $font_family = "Arial";
        $pdf->AddPage();
        $pdf->SetFont($font_family, "", 14);
        // --------  PRIMA PAGINA CON LA LISTA ---------------------
        $pdf->Cell(0, 6, Configurazione::corsiDiLaurea()[$this->Cdl], 0, 1, 'C');
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
        for ($i = 0; $i < sizeof($this->Matricole); $i++) {
            try {
                $pag_con_simulazione = new ProspettoConSimulazione(
                    $this->Matricole[$i], $this->Cdl, $this->DataLaurea);
                $pdf = $pag_con_simulazione->generaRiga($pdf);
            } catch (Exception $ex) {}
        }

        // --------  PAGINE CON LA CARRIERA ---------------------
        // aggiungo la pagina di ogni laureando
        for ($i = 0; $i < sizeof($this->Matricole); $i++) {
            try {
                $pag_con_simulazione = new ProspettoConSimulazione(
                    $this->Matricole[$i], $this->Cdl, $this->DataLaurea);
                $pdf = $pag_con_simulazione->generaContenuto($pdf);
            } catch (Exception $ex) {};
        }

        $path = AccessoProspetti::pathCommissioneServer();
        $pdf->Output('F', $path);
        return file_exists($path);
    }
    public function generaProspettiLaureandi(): int
    {
        $totale = 0;
        for ($i = 0; $i < sizeof($this->Matricole); $i++) {
            try {
                $prospetto = new ProspettoLaureando($this->Matricole[$i], $this->Cdl, $this->DataLaurea);
                // Memorizzo l'email per dopo
                $this->ListaEmail[$this->Matricole[$i]] = $prospetto->CarrieraLaureando->Email;
                $pdf = $prospetto->generaProspetto();
                
                $path = AccessoProspetti::pathLaureandoServer($this->Matricole[$i]);
                $pdf->Output('F', $path);
                if (file_exists($path)) {
                    $totale++;
                }
            } catch (Exception $ex) { }
        }
        return $totale;
    }
    public function generaProspetti(): int {
        $tot = $this->generaProspettiLaureandi();
        if ($tot === 0) {
            return 0;
        }
        if ($this->generaProspettiCommissione()) {
            return $tot + 1;
        }
        return $tot;
    }
    public function popolaJSON(): bool
    {
        require_once __DIR__ . '/GestoreInviiEmail.php';
        return GestoreInviiEmail::generate($this->Matricole, $this->Cdl, $this->DataLaurea, $this->ListaEmail);
    }
}
