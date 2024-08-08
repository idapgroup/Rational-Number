<?php declare(strict_types=1);

final class Rational
{
    private int $significand;
    private int $denominator;
    private int $base;

    public function __construct(int $significand, int $base = 0, int $denominator = 1)
    {
        if ($denominator === 0) {
            throw new \DivisionByZeroError('Denominator cannot be zero.');
        }
        $this->significand = $significand;
        $this->denominator = $denominator;
        $this->base = $base;

        $this->normalize();
    }

    public function mul(Rational $that): Rational
    {
        return new self(
            $this->significand * $that->significand,
            $this->base + $that->base,
            $this->denominator * $that->denominator
        );
    }

    public function div(Rational $that): Rational
    {
        $ret = new self($this->significand * $that->denominator, $this->base - $that->base);
        $ret->denominator = $this->denominator * $that->significand;
        $ret->normalize();

        return $ret;
    }

    public function add(Rational $that): Rational
    {
        $baseDiff = $that->base - $this->base;
        if ($baseDiff === 0) {
            return new self(
                $this->significand * $that->denominator + $that->significand * $this->denominator,
                $this->base,
                $this->denominator * $that->denominator
            );
        }
        if ($baseDiff > 0) {
            return new self(
                $this->significand * $that->denominator + $that->significand * 10 ** $baseDiff * $this->denominator,
                $this->base,
                $this->denominator * $that->denominator
            );
        }

        return new self(
            $this->significand * 10 ** -$baseDiff * $that->denominator + $that->significand * $this->denominator,
            $that->base,
            $this->denominator * $that->denominator
        );
    }

    public function sub(Rational $that): Rational
    {
        return $this->add(new Rational(-$that->significand, $that->base, $that->denominator));
    }

    public function equals(Rational $that): bool
    {
        $d = $this->sub($that);
        return $d->significand === 0 && $d->denominator === 1;
    }

    public static function zero(): Rational
    {
        return new self(0, 0);
    }

    public static function one(): Rational
    {
        return new self(1, 0);
    }

    public static function fromNumber(int|float $number, int $digits = -1): Rational
    {
        if (is_int($number)) {
            return new self($number, 0);
        }

        if ($digits >= 0) {
            return new self((int) round($number * 10 ** $digits), -$digits);
        }

        $significand = $number;
        $base = 0;
        while ($significand - (int) $significand !== 0.0) {
            $significand *= 10;
            $base--;
        }

        return new self((int) $significand, $base);
    }

    public function toFloat(int $precision = null): float
    {
        if ($precision !== null) {
            return round($this->significand / $this->denominator * 10 ** $this->base, $precision);
        }

        return $this->significand / $this->denominator * 10 ** $this->base;
    }

    /**
     * @return int[]
     */
    public function toSource(): array
    {
        return [$this->significand, $this->base, $this->denominator];
    }

    private static function gcd(int $a, int $b): int
    {
        if ($a === $b) {
            return $a;
        }
        if ($a === 0 || $b === 0) {
            return 0;
        }
        if ($a === 1 || $b === 1) {
            return 1;
        }
        if (!($a & 1 || $b & 1)) {
            return self::gcd($a >> 1, $b >> 1) << 1;
        }
        if (!($a & 1)) {
            return self::gcd($a >> 1, $b);
        }
        if (!($b & 1)) {
            return self::gcd($a, $b >> 1);
        }
        if ($a > $b) {
            return self::gcd(($a - $b) >> 1, $b);
        }
        return self::gcd(($b - $a) >> 1, $a);
    }

    private function normalize(): void
    {
        if ($this->significand === 0) {
            $this->denominator = 1;
            $this->base = 0;

            return;
        }

        $gcd = self::gcd(abs($this->significand), abs($this->denominator));
        $this->significand = intdiv($this->significand, $gcd);
        $this->denominator = intdiv($this->denominator, $gcd);

        while ($this->significand - ($ts = (int)($this->significand * .1)) * 10 === 0) {
            $this->significand = $ts;
            $this->base++;
        }

        while ($this->denominator - ($td = (int)($this->denominator * .1)) * 10 === 0) {
            $this->denominator = $td;
            $this->base--;
        }
    }
}
