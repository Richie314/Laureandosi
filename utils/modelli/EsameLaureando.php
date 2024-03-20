<?php

class EsameLaureando
{
	public string $NomeEsame;
	
	public int $VotoEsame;
	/**
	 * @AttributeType int
	 */
	public int $Cfu;
	/**
	 * @AttributeType boolean
	 */
	public bool $FaMedia;
	/**
	 * @AttributeType boolean
	 */
	public bool $Curricolare;
	public DateTime $DataEsame;
	/**
	 * @AttributeType boolean
	 */
	public bool $Informatico = false;
	public static array $EsamiDaIgnorare = array(
		//"LIBERA SCELTA PER RICONOSCIMENTI",
		"PROVA FINALE",
		"TEST DI VALUTAZIONE DI INGEGNERIA",
	);

	private static function votoConLode(string $voto): bool
	{
		if (ctype_digit($voto)) {
			return (int)$voto > 30;
		}
		return preg_match('/(30\\s+e\\s+lode)|(30L)/i', trim($voto));
	}
	public static function parseVoto(string $voto, int $valore_lode = 33): int
	{
		if (!isset($voto) || empty($voto)) {
			return 0;
		}
		return self::votoConLode($voto) ? $valore_lode : (int)$voto;
	}

	public function __construct(
		string $nome, 
		string|int|null $voto,
		string|int $cfu,
		string $data,
		string|int|bool $faMedia,
		string|int|bool $curricolare,
		string|int $valore_lode = 33,
	) {
		$this->NomeEsame = strtoupper(trim($nome));
		$this->Cfu = (int)$cfu;
		$this->DataEsame = DateTime::createFromFormat("d/m/Y", $data);
		$voto = !isset($voto) ? 0 : $voto;
		$this->VotoEsame = self::ParseVoto($voto, $valore_lode);

		$this->Curricolare = (bool)$curricolare && !in_array($this->NomeEsame, self::$EsamiDaIgnorare);
		$this->FaMedia = (bool)$faMedia && $this->Curricolare && $this->VotoEsame !== 0;
	}

	public function creditoMedia(): int
	{
		return $this->FaMedia ? $this->Cfu : 0;
	}
	public function creditoCurriculare(): int
	{
		return $this->Curricolare ? $this->Cfu : 0;
	}
}