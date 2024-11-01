<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\RecentlyViewedProducts;

use Sparkrvp\SparkPlugins\SparkWoo\Common\Admin\UserProfileSectionModule;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Cache\CacheManager;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Loader;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Modules\ModuleInterface;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Plugins\GlobalVariables;
use Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\Models\ProductRecommendationPostModel;
use Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\ProductsManager\AbstractProductsManager;
use Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\ProductsManager\ProductsManagerInterface;
use Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\ProductsManager\RecommendedProductIdsCollection;
class RecentlyViewedProductsManager extends AbstractProductsManager implements ProductsManagerInterface, ModuleInterface
{
    protected string $cookieName;
    protected CacheManager $cacheManager;
    protected int $expiry;
    protected GlobalVariables $globalVariables;
    public function __construct($slug, $title, $description, $shortcode, $cookieName, $cacheManager)
    {
        parent::__construct($slug, $title, $description, $shortcode);
        $this->cookieName = $cookieName;
        $this->cacheManager = $cacheManager;
        $this->expiry = 60 * 60 * 24 * 30;
    }
    public function defineAdminHooks(Loader $loader) : void
    {
        $loader->addAction(UserProfileSectionModule::USER_PROFILE_ACTION, $this, 'addUserProfileRVP');
    }
    public function definePublicHooks(Loader $loader) : void
    {
        $loader->addAction('wp', $this, 'recordProductVisit');
    }
    public function recordProductVisit()
    {
        if (!is_single()) {
            return;
        }
        if (get_post_type() !== 'product') {
            return;
        }
        $event = array('timestamp' => \time(), 'productId' => get_the_ID());
        $history = $this->getHistory();
        \array_unshift($history, $event);
        $cacheKey = $this->getCacheKey();
        $this->cacheManager->set($cacheKey, $history, $this->expiry);
        \setcookie($this->cookieName, $cacheKey, \time() + $this->expiry, '/');
        // Also save to account when user is logged in.
        $userId = get_current_user_id();
        if ($userId > 0) {
            update_user_meta($userId, $this->cookieName, $history);
        }
    }
    public function getHistory()
    {
        // First check if user has products in his account, if none (or not logged in) then use cookie.
        $history = $this->getHistoryFromUser();
        if (\count($history) == 0) {
            if ($this->isDeprecatedCookie()) {
                $history = \array_map(function ($h) {
                    return array('productId' => $h, 'timestamp' => \time());
                }, \array_map('intval', \preg_split('/,/', $this->getCookieValue(), -1, \PREG_SPLIT_DELIM_CAPTURE)));
            } else {
                $history = $this->cacheManager->get($this->getCacheKey(), \true);
            }
            if (!$history) {
                $history = array();
            }
        }
        return $history;
    }
    public function addUserProfileRVP($user)
    {
        $visits = $this->getHistoryFromUser($user->ID);
        echo '<div class="col-span-12 sm:col-span-6 xl:col-span-4 rounded-lg border bg-white shadow-sm focus:outline-none  px-4 py-5 text-sm text-gray-700 sm:rounded-md sm:p-6">';
        echo '<span class="font-bold text-base">SparkRVP</span>';
        echo '<p>Last 10 visted products for this user</p>';
        if (\count($visits) == 0) {
            echo '<p><em>User has not visited any products yet</em></p>';
        } else {
            echo '<ol>';
            $visits = \array_slice($visits, 0, 10);
            foreach ($visits as $visit) {
                $product = \wc_get_product($visit['productId']);
                if (!$product) {
                    continue;
                }
                $permalink = $product->get_permalink();
                $title = $product->get_title();
                echo '<li><a target="_blank" href="' . esc_attr($permalink) . '">' . esc_html($title) . '</a></li>';
            }
            echo '</ol>';
        }
        echo '</div>';
    }
    public function getRecommendedProductIdsCollection(ProductRecommendationPostModel $postModel) : RecommendedProductIdsCollection
    {
        $productIds = \array_unique(\array_filter(\array_map(function ($event) {
            if (!isset($event['productId']) || !\is_int($event['productId'])) {
                return null;
            }
            return $event['productId'];
        }, $this->getHistory())));
        return (new RecommendedProductIdsCollection($productIds))->filterCurrentlyInCart()->filterCurrent()->takeFirst(30);
    }
    private function getHistoryFromUser($userId = null) : array
    {
        if (null === $userId) {
            $userId = get_current_user_id();
        }
        // If user is not logged in, return empty array.
        if (0 == $userId) {
            return array();
        }
        $userHistory = get_user_meta($userId, $this->cookieName, \true);
        if (!$userHistory) {
            return array();
        }
        return $userHistory;
    }
    private function getCookieValue()
    {
        if (isset($_COOKIE[$this->cookieName])) {
            return sanitize_text_field(\wp_unslash($_COOKIE[$this->cookieName]));
        }
        return \false;
    }
    private function isDeprecatedCookie()
    {
        $cookieValue = $this->getCookieValue();
        return $cookieValue !== \false && !\str_starts_with($cookieValue, 'cache-');
    }
    private function getCacheKey()
    {
        if ($this->isDeprecatedCookie() || !$this->getCookieValue()) {
            return 'cache-' . \base64_encode(\random_bytes(18));
        }
        return $this->getCookieValue();
    }
}
