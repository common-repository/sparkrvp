<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations;

use Sparkrvp\SparkPlugins\SparkWoo\Common\Admin\AdminPageTrait;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Loader;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Models\PostModelInterface;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Modules\ModuleInterface;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Plugins\GlobalVariables;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Plugins\PluginMeta;
use Sparkrvp\SparkPlugins\SparkWoo\Common\StylesScripts\StylesScriptsModule;
use Sparkrvp\SparkPlugins\SparkWoo\Common\WPHelpers;
use Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations\ProductsManager\ProductsManagerInterface;
class ProductRecommendationsModule implements ModuleInterface
{
    use AdminPageTrait;
    private PluginMeta $pluginMeta;
    private GlobalVariables $globalVariables;
    private string $adminMenuHomeName;
    private PostModelInterface $productRecommendationPostModel;
    private ProductsManagerInterface $productsManager;
    private StylesScriptsModule $stylesScriptsModule;
    public function __construct(PluginMeta $pluginMeta, GlobalVariables $globalVariables, string $adminMenuHomeName, PostModelInterface $productRecommendationPostModel, ProductsManagerInterface $productsManager, StylesScriptsModule $stylesScriptsModule)
    {
        $this->pluginMeta = $pluginMeta;
        $this->globalVariables = $globalVariables;
        $this->adminMenuHomeName = $adminMenuHomeName;
        $this->productRecommendationPostModel = $productRecommendationPostModel;
        $this->productsManager = $productsManager;
        $this->stylesScriptsModule = $stylesScriptsModule;
    }
    public function defineAdminHooks(Loader $loader) : void
    {
        $loader->addAction('before_woocommerce_init', $this, 'setWoocommerceCompatibility');
        $loader->addAction('admin_menu', $this, 'addAdminPage');
        if ($this->isPluginAdminPage()) {
            $loader->addAction('init', $this->stylesScriptsModule, 'enqueueStylesForAdmin');
            $loader->addAction('init', $this->stylesScriptsModule, 'enqueueScriptsForAdmin');
        }
    }
    public function definePublicHooks(Loader $loader) : void
    {
    }
    public function setWoocommerceCompatibility() : void
    {
        if (\class_exists('\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil')) {
            $pluginFile = $this->globalVariables->getPluginFilePath();
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', $pluginFile, \true);
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', $pluginFile, \true);
        }
    }
    public function addAdminPage()
    {
        $adminSettingsName = $this->getAdminPageName('settings');
        if (!WPHelpers::hasMainMenuPage($this->adminMenuHomeName)) {
            add_menu_page('SparkWoo', 'SparkWoo', 'manage_options', $this->adminMenuHomeName, array($this, 'loadAdminPageContent'), 'data:image/svg+xml;base64,' . \base64_encode(\file_get_contents($this->globalVariables->getPluginDirPath() . '/assets/images/sparkplugins-icon-admin.svg')), 30);
        }
        if (!WPHelpers::hasSubMenuPage($this->adminMenuHomeName, $this->adminMenuHomeName)) {
            add_submenu_page($this->adminMenuHomeName, 'Product Recommendations', 'Product Recommendations', 'manage_options', $this->adminMenuHomeName, array($this, 'loadAdminPageContent'));
        }
        if (!WPHelpers::hasSubMenuPage($this->adminMenuHomeName, $adminSettingsName)) {
            add_submenu_page($this->adminMenuHomeName, 'Settings', 'Settings', 'manage_options', $adminSettingsName, array($this, 'loadAdminPageContent'));
        }
    }
    public function getAdminPageName(string $name) : string
    {
        return GlobalVariables::SPARKWOO_PREFIX . $name;
    }
    public function isPluginAdminPage() : bool
    {
        if (!isset($_GET['page'])) {
            return \false;
        }
        return \str_starts_with(sanitize_text_field(\wp_unslash($_GET['page'])), GlobalVariables::SPARKWOO_PREFIX);
    }
}
