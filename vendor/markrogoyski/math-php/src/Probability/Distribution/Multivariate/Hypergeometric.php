<?php

namespace Sparkrvp\MathPHP\Probability\Distribution\Multivariate;

use Sparkrvp\MathPHP\Probability\Combinatorics;
use Sparkrvp\MathPHP\Exception;
use Sparkrvp\MathPHP\Functions\Support;
/**
 * Multivariate Hypergeometric distribution
 *
 * https://en.wikipedia.org/wiki/Hypergeometric_distribution#Multivariate_hypergeometric_distribution
 */
class Hypergeometric
{
    /**
     * Distribution parameter bounds limits
     * Kᵢ ∈ [1,∞)
     * @var array{K: string}
     */
    public const PARAMETER_LIMITS = ['K' => '[1,∞)'];
    /**
     * Distribution parameter bounds limits
     * kᵢ ∈ [0,Kᵢ]
     * @var array<string, array<string>>
     */
    protected $supportLimits = [];
    /** @var array<int|float> */
    protected $quantities;
    /**
     * Multivariate Hypergeometric constructor
     *
     * @param   array<int|float> $quantities
     *
     * @throws Exception\BadDataException if the quantities are not positive integers.
     */
    public function __construct(array $quantities)
    {
        if (\count($quantities) === 0) {
            throw new Exception\BadDataException("Array cannot be empty.");
        }
        foreach ($quantities as $K) {
            if (!\is_int($K)) {
                throw new Exception\BadDataException("Quantities must be positive integers.");
            }
            Support::checkLimits(self::PARAMETER_LIMITS, ['K' => $K]);
            $this->supportLimits['k'][] = "[0,{$K}]";
        }
        $this->quantities = $quantities;
    }
    /**
     * Probability mass function
     *
     * @param  array<int|float> $picks
     *
     * @return float
     *
     * @throws Exception\BadDataException if the number of picks do not match the number of quantities.
     * @throws Exception\BadDataException if the picks are not whole numbers or greater than the corresponding quantity.
     */
    public function pmf(array $picks) : float
    {
        // Must have a pick for each quantity
        if (\count($picks) !== \count($this->quantities)) {
            throw new Exception\BadDataException('Number of quantities does not match number of picks.');
        }
        foreach ($picks as $i => $k) {
            if (!\is_int($k)) {
                throw new Exception\BadDataException("Picks must be whole numbers.");
            }
            Support::checkLimits(['k' => $this->supportLimits['k'][$i]], ['k' => $k]);
        }
        $n = \array_sum($picks);
        $total = \array_sum($this->quantities);
        $product = \array_product(\array_map(
            // @phpstan-ignore-next-line (Parameter #1 $callback of function array_map expects (callable(float|int, float|int): mixed)|null, Closure(int, int): float given.)
            function (int $quantity, int $pick) {
                return Combinatorics::combinations($quantity, $pick);
            },
            $this->quantities,
            $picks
        ));
        return $product / Combinatorics::combinations((int) $total, (int) $n);
    }
}
