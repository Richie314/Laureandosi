<?php
require_once __DIR__ . '/modelli/ProspettoPDFLaureando2.php';
require_once __DIR__ . '/AccessoProspetti.php';
require_once dirname(__DIR__) . '/lib/PHPMailer/src/Exception.php';
require_once dirname(__DIR__) . '/lib/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/lib/PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/**
 * @access public
 * @author franc
 */

class InvioPDFLaureando2 {
    /**
     * @AttributeType int[]
     */
    private array $_matricole;
    /**
     * @AssociationType ProspettoPDFLaureando2
     */
    private CorsoDiLaurea $_cdl;
    private string $_dataLaurea;

    /**
     * @access public
     * @param int[] aMatricole
     * @ParamType aMatricole int[]
     */

    public function __construct()
    {
        $json_content = file_get_contents(AccessoProspetti::pathAusiliario());
        $obj = json_decode($json_content, true);
        $this->_matricole = $obj['matricole'];
        $this->_cdl = Configurazione::CorsiDiLaurea()[$obj['cdl']];
        $this->_dataLaurea = $obj['data_laurea'];
    }
    public function invioProspetti(int $max = PHP_INT_MAX) : array
    {
        $inviati = array();
        for ($j = 0; $j < min(sizeof($this->_matricole), $max); $j++)
        {   
            try {
                $prospetto = new ProspettoPdfLaureando2($this->_matricole[$j], $this->_cdl, $this->_dataLaurea);
                if ($this->inviaProspetto($prospetto->_carrieraLaureando))
                {
                    unlink(AccessoProspetti::pathLaureandoServer($this->_matricole[$j]));
                    $this->_matricole[$j] = 0;
                    $inviati[] = $this->_matricole[$j];
                }
            } catch (Exception $ex) {};
        }
        $this->_matricole = array_filter($this->_matricole, function (int $a) { return $a !== 0; });
        $this->AggiornaFile();
        return $inviati;
    }
    /**
     * @access public
     * @return bool
     * @ReturnType bool
     */
    public function inviaProspetto(
        CarrieraLaureando2|CarrieraLaureandoInformatica2 $studente_carriera) : bool {

        $messaggio = new PHPMailer();
        $messaggio->IsSMTP();
        $messaggio->Host = "mixer.unipi.it";
        $messaggio->SMTPSecure = "tls";
        $messaggio->SMTPAuth = false;
        $messaggio->Port = 25;

        $messaggio->From = 'no-reply-laureandosi@ing.unipi.it';
        $messaggio->AddAddress($studente_carriera->_email);
        $messaggio->Subject = 'Appello di laurea in Ing. TEST- indicatori per voto di laurea';
        $messaggio->Body = $this->_cdl->FormulaEmail;

        $messaggio->AddAttachment(AccessoProspetti::pathLaureandoServer($studente_carriera->_matricola));
        
        $res = $messaggio->Send();
        $messaggio->smtpClose();
        return $res;
    }

    public function AggiornaFile() : bool
    {
        return self::SaveFile($this->_matricole, $this->_cdl->Nome, $this->_dataLaurea);
    }
    private static function SaveFile(array $matricole, string $cdl, string $data_laurea) : bool
    {
        $json = json_encode(
            array(
                'matricole' => $matricole,
                'cdl' => $cdl,
                'data_laurea' => $data_laurea
            ), JSON_PRETTY_PRINT
        );
        $res = file_put_contents(AccessoProspetti::pathAusiliario(), $json);
        if (!$res)
        {
            return false;
        }
        return $res > 0;
    }
    public static function Generate(array $matricole, string $cdl, string $data_laurea) : InvioPDFLaureando2|null
    {
        if (!self::SaveFile($matricole, $cdl, $data_laurea))
            return null;
        return new InvioPDFLaureando2();
    }
}
