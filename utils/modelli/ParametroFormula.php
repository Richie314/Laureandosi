<?php
class ParametroFormula
{
    public int $Min = 0;
    public int $Max = 0;
    public int $Step = 1;
    public function InUso() : bool
    {
        if ($this->Min < 0 && $this->Step < 0)
            return false;
        if ($this->Step === 0)
            return $this->Max === $this->Min && $this->Min > 0;
        return $this->Max > $this->Min;
    }
    public function __construct(string|int|null $min, string|int|null $max, string|int|null $step)
    {
        if (!isset($min)) $min = 0;
        if (!isset($max)) $max = 0;
        if (!isset($step)) $step = 0;
        $this->Min = (int)$min;
        $this->Max = (int)$max;
        $this->Step = (int)$step;
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