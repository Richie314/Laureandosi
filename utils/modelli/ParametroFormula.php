<?php
class ParametroFormula
{
    public int|float $Min = 0;
    public int|float $Max = 0;
    public int|float $Step = 1;
    public function InUso() : bool
    {
        if ($this->Min < 0 || $this->Step < 0)
            return false;
        if (is_int($this->Step) && $this->Step === 0)
            return $this->Max === $this->Min && $this->Min > 0;
        return $this->Max > $this->Min;
    }
    private static function ParseArg(string|int|float $n) : int|float
    {
        if (!is_string($n))
        {
            return $n;
        }
        if (ctype_digit($n))
        {
            return (int)$n;
        }
        return (float)$n;
    }
    public function __construct(string|int|float|null $min, string|int|float|null $max, string|int|float|null $step)
    {
        if (!isset($min)) $min = 0;
        if (!isset($max)) $max = 0;
        if (!isset($step)) $step = 0;
        $this->Min = self::ParseArg($min);
        $this->Max = self::ParseArg($max);
        $this->Step = self::ParseArg($step);
    }
    public function Valido(string|int|float|null $param) : bool
    {
        if (!isset($param) || !$this->InUso())
        {
            return false;
        }
        if (is_string($param) && strlen(trim($param)) === 0)
        {
            return false;
        }
        $param = (int)$param;

        if ($param < $this->Min || $param > $this->Max)
        {
            return false;
        }
        if ($this->Step === 0)
        {
            return $param === $this->Min || $param === $this->Max;
        }
        return ($param - $this->Min) % $this->Step === 0;
    }
    public function GetValues() : array
    {
        if (!$this->InUso())
            return array();
        if ($this->Step === 0)
        {
            return array($this->Min, $this->Max);
        }
        return range($this->Min, $this->Max, $this->Step);
    }
}