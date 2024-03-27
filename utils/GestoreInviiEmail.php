<?php
require_once __DIR__ . '/AccessoProspetti.php';
require_once __DIR__ . '/Configurazione.php';
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
    private array $Email = array();

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
        $this->Email = $obj['email'];
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
                $emailLaureando = $this->Email[$this->Matricole[$j]];
                if ($this->inviaProspetto($emailLaureando, $this->Matricole[$j])) {
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
    public function inviaProspetto(string $destinatario, int $matricola = 0): bool {
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

        $messaggio->AddAddress($destinatario);
        $messaggio->Subject = 'Prospetti per appello di laurea';

        if ($matricola === 0) {
            $messaggio->Body = "Gentilissima commissione di laurea,<br>Vengono allegati i prospetti per il prossimo appello di laurea.<br>";
            $messaggio->AddAttachment(AccessoProspetti::pathCommissioneServer());
        } else {
            $messaggio->Body = $this->Cdl->FormulaEmail;
            $messaggio->AddAttachment(AccessoProspetti::pathLaureandoServer($matricola));
        }
        
        $res = $messaggio->Send();
        $messaggio->smtpClose();
        return $res;
    }

    private function aggiornaFile(): bool
    {
        return self::saveFile($this->Matricole, $this->CdlShort, $this->DataLaurea, $this->Email);
    }
    private static function saveFile(array $matricole, string $cdl, string $dataLaurea, array $email): bool
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
                'email' => $email,
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
        array $email,
    ) : bool {
        $copy = (new ArrayObject(array_map("intval", $matricole)))->getArrayCopy();
        if (count($copy) === 0 || end($copy) !== 0) {
            $copy[] = 0; // 0 significa inviare alla commissione
        }
        if (!self::saveFile($copy, $cdl, $dataLaurea, $email)) {
            return false;
        }
        return true;
    }
}
