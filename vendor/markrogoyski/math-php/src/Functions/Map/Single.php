<?php

namespace Sparkrvp\MathPHP\Functions\Map;

use Sparkrvp\MathPHP\Exception;
/**
 * Map functions against a single array
 */
class Single
{
    /**
     * Map addition
     *
     * @param  array<int|float> $xs
     * @param  int|float     $k Number to add to each element
     *
     * @return array<int|float>
     */
    public static function add(array $xs, $k) : array
    {
        return \array_map(function ($x) use($k) {
            return $x + $k;
        }, $xs);
    }
    /**
     * Map subtract
     *
     * @param  array<int|float> $xs
     * @param  int|float     $k Number to subtract from each element
     *
     * @return array<int|float>
     */
    public static function subtract(array $xs, $k) : array
    {
        return \array_map(function ($x) use($k) {
            return $x - $k;
        }, $xs);
    }
    /**
     * Map multiply
     *
     * @param  array<int|float> $xs
     * @param  int|float     $k Number to multiply to each element
     *
     * @return array<int|float>
     */
    public static function multiply(array $xs, $k) : array
    {
        return \array_map(function ($x) use($k) {
            return $x * $k;
        }, $xs);
    }
    /**
     * Map Divide
     *
     * @param  array<int|float> $xs
     * @param  int|float     $k Number to divide each element by
     *
     * @return array<float>
     */
    public static function divide(array $xs, $k) : array
    {
        return \array_map(function ($x) use($k) {
            return $x / $k;
        }, $xs);
    }
    /**
     * Map square
     *
     * @param  array<int|float>  $xs
     *
     * @return array<int|float>
     */
    public static function square(array $xs) : array
    {
        return \array_map(function ($x) {
            return $x ** 2;
        }, $xs);
    }
    /**
     * Map cube
     *
     * @param  array<int|float>  $xs
     *
     * @return array<int|float>
     */
    public static function cube(array $xs) : array
    {
        return \array_map(function ($x) {
            return $x ** 3;
        }, $xs);
    }
    /**
     * Map reciprocal
     * x := 1/x
     *
     * @param  array<int|float>  $xs
     *
     * @return array<float>
     *
     * @throws Exception\BadDataException if 0 is one of the numbers
     */
    public static function reciprocal(array $xs) : array
    {
        $zeros = \array_filter($xs, function (float $x) {
            return $x == 0;
        });
        if (!empty($zeros)) {
            throw new Exception\BadDataException('Cannot compute the reciprocal of 0');
        }
        return \array_map(function ($x) {
            return 1 / $x;
        }, $xs);
    }
    /**
     * Map raise to a power
     *
     * @param  array<int|float> $xs
     * @param  int|float     $n
     *
     * @return array<int|float>
     */
    public static function pow(array $xs, $n) : array
    {
        return \array_map(function ($x) use($n) {
            return $x ** $n;
        }, $xs);
    }
    /**
     * Map square root
     *
     * @param  array<int|float> $xs
     *
     * @return array<float>
     */
    public static function sqrt(array $xs) : array
    {
        return \array_map(function ($x) {
            return \sqrt($x);
        }, $xs);
    }
    /**
     * Map absolute value
     *
     * @param  array<int|float> $xs
     *
     * @return array<int|float>
     */
    public static function abs(array $xs) : array
    {
        return \array_map(function ($x) {
            return \abs($x);
        }, $xs);
    }
    /**
     * Map min value
     * Each element in array is compared against the value,
     * and the min of each is returned.
     *
     * @param  array<int|float> $xs
     * @param  int|float     $value
     *
     * @return array<int|float>
     */
    public static function min(array $xs, $value) : array
    {
        return \array_map(function ($x) use($value) {
            return \min($x, $value);
        }, $xs);
    }
    /**
     * Map max value
     * Each element in the array is compared against the value,
     * and the max of each is returned.
     *
     * @param  array<int|float> $xs
     * @param  int|float     $value
     *
     * @return array<int|float>
     */
    public static function max(array $xs, $value) : array
    {
        return \array_map(function ($x) use($value) {
            return \max($x, $value);
        }, $xs);
    }
}
