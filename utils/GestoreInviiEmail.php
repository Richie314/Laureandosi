<?php
require_once __DIR__ . '/modelli/ProspettoLaureando.php';
require_once __DIR__ . '/AccessoProspetti.php';
require_once dirname(__DIR__) . '/lib/PHPMailer/src/Exception.php';
require_once dirname(__DIR__) . '/lib/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/lib/PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class GestoreInviiEmail {
    private array $Matricole;
    private ?CorsoDiLaurea $Cdl = null;
    private ?string $CdlShort = null;
    private ?string $DataLaurea = null;

    public function __construct()
    {
        $this->Matricole = array();
        $json_content = file_get_contents(AccessoProspetti::pathAusiliario());
        if (!$json_content) {
            return;
        }
        $obj = json_decode($json_content, true);
        if (!isset($obj)) {
            return;
        }
        $this->Matricole = $obj['matricole'];
        $this->CdlShort = $obj['cdl'];
        $this->Cdl = Configurazione::corsiDiLaurea()[$this->CdlShort];
        $this->DataLaurea = $obj['data_laurea'];
    }
    public function invioProspetti(int $max = PHP_INT_MAX): array
    {
        if (!isset($this->Cdl) || !isset($this->DataLaurea)) {
            return array();
        }
        $inviati = array();
        for ($j = 0; $j < min(sizeof($this->Matricole), $max); $j++) {   
            try {
                if ($this->Matricole[$j] === 0) {

                    // Sto inviando alla commissione
                    if ($this->inviaProspetto($this->Cdl->EmailCommissione)) {
                        $inviati[] = 'Commissione';
                        $this->Matricole[$j] = -1;
                    }
                    continue;
                }
                $prospetto = new ProspettoLaureando($this->Matricole[$j], $this->Cdl, $this->DataLaurea);
                if ($this->inviaProspetto($prospetto->CarrieraLaureando)) {
                    unlink(AccessoProspetti::pathLaureandoServer($this->Matricole[$j]));
                    $inviati[] = (int)$this->Matricole[$j];
                    $this->Matricole[$j] = -1;
                }
            } catch (Exception $ex) {};
        }
        $this->Matricole = array_values(array_filter($this->Matricole, function (int $a) { return $a >= 0; }));
        $this->aggiornaFile();
        return $inviati;
    }
    public function inviaProspetto(
        CarrieraLaureando|CarrieraLaureandoInformatica|string $destinatario
    ): bool {
        $messaggio = new PHPMailer();
        $messaggio->Host = "mixer.unipi.it";
        $messaggio->Port = 25;

        $messaggio->IsSMTP();
        $messaggio->SMTPSecure = "tls";
        $messaggio->SMTPAuth = false;

        $messaggio->From = 'noreply-laureandosi@ing.unipi.it';
        $messaggio->FromName = 'Laureandosi 2';

        $messaggio->CharSet = 'UTF-8';
        $messaggio->isHTML();

        if (is_string($destinatario)) {
            $messaggio->AddAddress($destinatario);
        } else {
            $messaggio->AddAddress($destinatario->Email);
        }
        $messaggio->Subject = 'Prospetti per appello di laurea';
        $messaggio->Body = $this->Cdl->FormulaEmail;

        if (is_string($destinatario)) {
            $messaggio->AddAttachment(AccessoProspetti::pathCommissioneServer());
        } else {
            $messaggio->AddAttachment(AccessoProspetti::pathLaureandoServer($destinatario->Matricola));
        }
        
        $res = $messaggio->Send();
        $messaggio->smtpClose();
        return $res;
    }

    private function aggiornaFile(): bool
    {
        return self::saveFile($this->Matricole, $this->CdlShort, $this->DataLaurea);
    }
    private static function saveFile(array $matricole, string $cdl, string $dataLaurea): bool
    {
        if (count($matricole) === 0) {
            // A operazione terminata il file ausiliario viene cancellato
            return unlink(AccessoProspetti::pathAusiliario());
        }
        $json = json_encode(
            array(
                'matricole' => $matricole,
                'cdl' => $cdl,
                'data_laurea' => $dataLaurea,
            ), JSON_PRETTY_PRINT
        );
        $res = file_put_contents(AccessoProspetti::pathAusiliario(), $json);
        if (!$res) {
            return false;
        }
        return $res > 0;
    }
    public static function generate(
        array $matricole, 
        string $cdl, 
        string $dataLaurea,
    ) : ?GestoreInviiEmail {
        $copy = (new ArrayObject(array_map("intval", $matricole)))->getArrayCopy();
        if (count($copy) === 0 || end($copy) !== 0) {
            $copy[] = 0; // 0 significa inviare alla commissione
        }
        if (!self::saveFile($copy, $cdl, $dataLaurea)) {
            return null;
        }
        return new GestoreInviiEmail();
    }
}
