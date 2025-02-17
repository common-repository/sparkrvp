<?php

namespace Sparkrvp\MathPHP\LinearAlgebra;

use Sparkrvp\MathPHP\Exception;
use Sparkrvp\MathPHP\Number\Complex;
use Sparkrvp\MathPHP\Number\ObjectArithmetic;
class ComplexMatrix extends ObjectMatrix
{
    /** @var Complex[][] Matrix array of arrays */
    protected $A;
    public function __construct(array $A)
    {
        $this->validateComplexData($A);
        parent::__construct($A);
    }
    /**
     * Validate the matrix is entirely complex
     *
     * @param array<array<object>> $A
     *
     * @throws Exception\IncorrectTypeException if all elements are not complex
     */
    protected function validateComplexData(array $A) : void
    {
        foreach ($A as $i => $row) {
            foreach ($row as $object) {
                if (!$object instanceof Complex) {
                    throw new Exception\IncorrectTypeException("All elements in the complex matrix must be complex. Got " . \get_class($object));
                }
            }
        }
    }
    /**
     * Zero value: [[0 + 0i]]
     *
     * @return ComplexMatrix
     */
    public static function createZeroValue() : ObjectArithmetic
    {
        return new ComplexMatrix([[new Complex(0, 0)]]);
    }
    /**
     * Conjugate Transpose - Aᴴ, also denoted as A*
     *
     * Take the transpose and then take the complex conjugate of each complex-number entry.
     *
     * https://en.wikipedia.org/wiki/Conjugate_transpose
     *
     * @return ComplexMatrix
     */
    public function conjugateTranspose() : Matrix
    {
        return $this->transpose()->map(function (Complex $c) {
            return $c->complexConjugate();
        });
    }
}
