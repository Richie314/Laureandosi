<?php
require_once __DIR__ . '/ProspettoLaureando.php';

class ProspettoConSimulazione extends ProspettoLaureando {
	
    public function __construct(string|int $matricola, string|CorsoDiLaurea $cdl, string $dataLaurea)
    {
		parent::__construct($matricola, $cdl, $dataLaurea);
	}
    public function generaProspettoConSimulazione(): FPDF
    {
        $pdf = parent::generaProspetto(null);
        $pdf = $this->generaContenuto($pdf);
        return $pdf;
    }
	
    public function generaContenuto(FPDF|null $pdf): FPDF 
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
            $divisore = ($cdl->C->numeroValoriPossibili() >= 10) ? 2 : 1;
            for ($i = 0; $i < $divisore; $i++) {
                $pdf->Cell($width / $divisore, $height, "VOTO COMMISSIONE (C)", 1, 0, 'C');
                $pdf->Cell($width / $divisore, $height, "VOTO LAUREA", 1, ($divisore === 2) ? $i : 1, 'C');
            }

            $nuovaRiga = (int)((3 - $divisore) / 2);
            foreach ($cdl->C->valoriPossibili() as $C) {
                $voto = $cdl->calcolaFormula($M, $CFU, 0, $C);
                $pdf->Cell($width / $divisore, $height, $C, 1, 0, 'C');
                $pdf->Cell($width / $divisore, $height, number_format($voto, 3), 1, $nuovaRiga, 'C');
                if ($divisore === 2)
                    $nuovaRiga = 1 - $nuovaRiga;
            }

            if ($cdl->T->inUso()) {
                $min = $cdl->T->Min;
                $max = $cdl->T->Max;
                $spiegazione = 
                    "VOTO DI LAUREA FINALE: scegli voto commissione, ". 
                    "prendi il corrispondente voto di laurea e somma il voto di tesi tra $min e $max, quindi arrotonda";
            } else {
                $spiegazione = 
                    "VOTO DI LAUREA FINALE: scegli voto commissione, ". 
                    "prendi il corrispondente voto di laurea, quindi arrotonda";
            }
        } elseif ($cdl->T->inUso()) {
            $divisore = ($cdl->T->numeroValoriPossibili() >= 10) ? 2 : 1;
            for ($i = 0; $i < $divisore; $i++) {
                $pdf->Cell($width / $divisore, $height, "VOTO TESI (T)", 1, 0, 'C');
                $pdf->Cell($width / $divisore, $height, "VOTO LAUREA", 1, ($divisore === 2) ? $i : 1, 'C');
            }

            $nuovaRiga = (int)((3 - $divisore) / 2);
            foreach ($cdl->T->valoriPossibili() as $T) {
                $voto = $cdl->calcolaFormula($M, $CFU, $T, 0);
                $pdf->Cell($width / $divisore, $height, $T, 1, 0, 'C');
                $pdf->Cell($width / $divisore, $height, number_format($voto, 3), 1, $nuovaRiga, 'C');
                if ($divisore === 2)
                    $nuovaRiga = 1 - $nuovaRiga;
            }

            $spiegazione = "VOTO DI LAUREA FINALE: scegli voto di tesi, prendi il corrispondente voto di laurea ed arrotonda";
        }
        if (isset($spiegazione)) {   
            $pdf->Ln(6);
            $pdf->SetFont('Arial', "", 9);
            $pdf->MultiCell(0, 5, $spiegazione, 0, 'L');
        }
        return $pdf;
	}

	public function generaRiga(FPDF $pdf): FPDF
    {
        $width = 190 / 4;
        $height = 5;
        $pdf->Cell($width, $height, $this->CarrieraLaureando->Cognome, 1, 0, 'L');
        $pdf->Cell($width, $height, $this->CarrieraLaureando->Nome, 1, 0, 'L');
        $pdf->Cell($width, $height, "", 1, 0, 'C');
        // è vuoto volutamente, il cdl è scritto sopra. nell'esempio era così
        $pdf->Cell($width, $height, "/110", 1, 1, 'C');
        return $pdf;
	}
}
