<?php

namespace Sparkrvp\MathPHP\Probability\Distribution\Multivariate;

use Sparkrvp\MathPHP\Exception;
use Sparkrvp\MathPHP\Functions\Map;
use Sparkrvp\MathPHP\LinearAlgebra\MatrixFactory;
use Sparkrvp\MathPHP\LinearAlgebra\Vector;
use Sparkrvp\MathPHP\LinearAlgebra\NumericMatrix;
/**
 * Normal distribution
 * https://en.wikipedia.org/wiki/Multivariate_normal_distribution
 */
class Normal
{
    /** @var array<int|float> location */
    protected $μ;
    /** @var NumericMatrix covariance matrix */
    protected $∑;
    /**
     * Constructor
     *
     * @param array<int|float> $μ ∈ Rᵏ   location
     * @param NumericMatrix $∑ ∈ Rᵏˣᵏ covariance matrix
     *
     * @throws Exception\BadDataException if the covariance matrix does not have the same number of rows and columns as number of elements in μ
     * @throws Exception\BadDataException if the covariance matrix is not positive definite
     */
    public function __construct(array $μ, NumericMatrix $∑)
    {
        $k = \count($μ);
        if ($∑->getM() !== $k || $∑->getN() !== $k) {
            throw new Exception\BadDataException('Covariance matrix ∑ must have the the same number of rows and columns as there are X elements. ' . "X has {$k} elements. Covariance matrix ∑ has " . $∑->getM() . ' rows and ' . $∑->getN() . ' columns.');
        }
        if (!$∑->isPositiveDefinite()) {
            throw new Exception\BadDataException("Covariance matrix ∑ is not positive definite:\n{$∑}");
        }
        $this->μ = $μ;
        $this->∑ = $∑;
    }
    /**
     * Probability density function
     *
     *                 exp(−½(x − μ)ᵀ∑⁻¹(x − μ))
     * fx(x₁,...,xk) = -------------------------
     *                        √(2π)ᵏ│∑│
     *
     * x is a real k-dimensional column vector
     * μ is a real k-dimensinoal column vector of means
     * │∑│ ≡ det(∑)
     *
     * @param array<int|float>  $X ∈ Rᵏ   k-dimensional random vector
     *
     * @return float density
     *
     * @throws Exception\BadDataException if X and μ do not have the same number of elements
     */
    public function pdf(array $X) : float
    {
        $k = \count($X);
        $μ = $this->μ;
        $∑ = $this->∑;
        if (\count($μ) !== $k) {
            throw new Exception\BadDataException("X and μ must have the same number of elements. X has {$k} and μ has " . \count($μ));
        }
        $π = \M_PI;
        $│∑│ = $∑->det();
        $√⟮2π⟯ᵏ│∑│ = \sqrt((2 * $π) ** $k * $│∑│);
        $Δ = Map\Multi::subtract($X, $μ);
        $⟮x − μ⟯ = new Vector($Δ);
        /** @var NumericMatrix $⟮x − μ⟯ᵀ */
        $⟮x − μ⟯ᵀ = MatrixFactory::createFromRowVector($Δ);
        $∑⁻¹ = $∑->inverse();
        $exp⟮−½⟮x − μ⟯ᵀ∑⁻¹⟮x − μ⟯⟯ = \exp($⟮x − μ⟯ᵀ->scalarDivide(-2)->multiply($∑⁻¹)->multiply($⟮x − μ⟯)->get(0, 0));
        return $exp⟮−½⟮x − μ⟯ᵀ∑⁻¹⟮x − μ⟯⟯ / $√⟮2π⟯ᵏ│∑│;
    }
}
