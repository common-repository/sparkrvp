<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\ProductPlacementHooks;

class ShopPageAfterProductsPlacementHook extends DefaultWooCommercePlacementHook implements ProductPlacementHookInterface
{
    protected $actionName;
    public function render()
    {
        if (is_single()) {
            return;
        }
        parent::render();
    }
}
