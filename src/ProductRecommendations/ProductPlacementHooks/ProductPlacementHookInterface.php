<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\ProductPlacementHooks;

use Sparkrvp\SparkPlugins\SparkWoo\Common\Modules\ModuleInterface;
use Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\Models\ProductRecommendationPostModel;
use Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\ProductsManager\ProductsManagerInterface;
interface ProductPlacementHookInterface extends \JsonSerializable, ModuleInterface
{
    public function getKey();
    public function render();
    public function renderSingle(ProductRecommendationPostModel $productRecommendationPostModel);
    public function getProductsManager() : ProductsManagerInterface;
    public function getAnalyticsEventKey() : string;
    public function getAnalyticsEventEncodedValue(ProductRecommendationPostModel $productRecommendationPostModel) : string;
}
