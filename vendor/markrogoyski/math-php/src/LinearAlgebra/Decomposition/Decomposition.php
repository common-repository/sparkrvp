<?php

namespace Sparkrvp\MathPHP\LinearAlgebra\Decomposition;

use Sparkrvp\MathPHP\LinearAlgebra\NumericMatrix;
abstract class Decomposition
{
    /**
     * @param NumericMatrix $M
     * @return static
     */
    public static abstract function decompose(NumericMatrix $M);
}
