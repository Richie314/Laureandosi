<?php


/**
 * @access public
 * @author franc
 */
class EsameLaureando2
{
	/**
	 * @AttributeType string
	 */
	public string $_nomeEsame;
	/**
	 * @AttributeType int
	 */
	public int $_votoEsame;
	/**
	 * @AttributeType int
	 */
	public int $_cfu;
	/**
	 * @AttributeType boolean
	 */
	public bool $_faMedia;
	/**
	 * @AttributeType boolean
	 */
	public bool $_curricolare;
	/**
	 * @AttributeType boolean
	 */
	public bool $_informatico = false;
	public static array $EsamiDaIgnorare = array(
		"LIBERA SCELTA PER RICONOSCIMENTI",
		"PROVA FINALE",
		"TEST DI VALUTAZIONE DI INGEGNERIA",
		"PROVA DI LINGUA INGLESE B2"
	);

	private static function VotoConLode(string $voto) : bool
	{
		if (ctype_digit($voto))
		{
			return (int)$voto > 30;
		}
		return preg_match('/((30)\\s(e)\\s(lode))|(30L)/i', trim($voto));
	}
	public static function ParseVoto(string $voto, int $valore_lode = 33) : int
	{
		if (!isset($voto) || empty($voto))
		{
			return 0;
		}
		return self::VotoConLode($voto) ? $valore_lode : (int)$voto;
	}

	public function __construct(
		string $nome, 
		string|int|null $voto,
		string|int $cfu,
		string|int|bool $faMedia,
		string|int|bool $curricolare,
		string|int $valore_lode = 33)
	{
		$this->_nomeEsame = strtoupper(trim($nome));
		$faMedia = $faMedia && !in_array($nome, self::$EsamiDaIgnorare) && (int)$voto !== 0;
		$this->_cfu = (int)$cfu;
		$this->_curricolare = (bool)$curricolare;
		$this->_faMedia = $faMedia && $this->_curricolare;
		$voto = !isset($voto) ? 0 : $voto;
		$this->_votoEsame = self::ParseVoto($voto, $valore_lode);
	}

	public function Credito() : bool
	{
		return ($this->_faMedia && $this->_curricolare) ? $this->_cfu : 0;
	}
}