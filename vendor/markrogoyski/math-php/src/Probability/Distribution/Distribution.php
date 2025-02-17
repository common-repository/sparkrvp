<?php

namespace Sparkrvp\MathPHP\Probability\Distribution;

use Sparkrvp\MathPHP\Functions\Support;
abstract class Distribution
{
    // Overridden by implementing classes
    public const PARAMETER_LIMITS = [];
    /**
     * Constructor
     *
     * @param int|float ...$params
     */
    public function __construct(...$params)
    {
        $new_params = static::PARAMETER_LIMITS;
        $i = 0;
        foreach ($new_params as $key => $value) {
            $this->{$key} = $params[$i];
            $new_params[$key] = $params[$i];
            $i++;
        }
        Support::checkLimits(static::PARAMETER_LIMITS, $new_params);
    }
}
