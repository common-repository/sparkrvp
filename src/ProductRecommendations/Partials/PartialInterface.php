<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\Partials;

use Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\Models\ProductRecommendationPostModel;
use Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\ProductPlacementHooks\ProductPlacementHookInterface;
interface PartialInterface
{
    /**
     * @var ProductRecommendationPostModel $productRecommendationPostModel
     * @var \WC_Product[] $products
     */
    public function render(ProductRecommendationPostModel $productRecommendationPostModel, array $products, ProductPlacementHookInterface $placementHook = null);
}
