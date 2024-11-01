<?php



/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sparkplugins.com
 *
 * @wordpress-plugin
 * Plugin Name:           SparkRVP - WooCommerce Recently Viewed Products
 * Plugin URI:            https://www.sparkplugins.com/sparkrvp
 * Description:           SparkRVP: Boost sales and engage your webshop visitors with personalized product promotions based on their recently viewed items.
 * Version:               1.3.1
 * Author:                SparkPlugins
 * Author URI:            https://sparkplugins.com/
 * License:               GPL-2.0+
 * License URI:           http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:           sparkrvp
 * Domain Path:           /languages
 * Requires at least:     6.4
 * Requires PHP:          7.4
 * Requires Plugins:      woocommerce
 * WC requires at least:  8.0
 * WC tested up to:       8.8
 */
if (!\defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';
use Sparkrvp\Symfony\Component\Config\ConfigCache;
use Sparkrvp\Symfony\Component\Config\FileLocator;
use Sparkrvp\Symfony\Component\DependencyInjection\ContainerBuilder;
use Sparkrvp\Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Sparkrvp\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
// If this file is called directly, abort.
if (!\defined('WPINC')) {
    die;
}
if (!\function_exists('Sparkrvp\\Sparkrvp_getSparkPluginsContainer')) {
    function Sparkrvp_getSparkPluginsContainer()
    {
        $file = __DIR__ . '/cache/container.php';
        $containerConfigCache = new ConfigCache($file, \SPARK_DEV_MODE);
        if (!$containerConfigCache->isFresh()) {
            $container = new ContainerBuilder();
            $fileLocator = new FileLocator(__DIR__);
            $loader = new YamlFileLoader($container, $fileLocator);
            $loader->load('config/services-sparkrvp.yaml');
            $container->compile();
            $dumper = new PhpDumper($container);
            $containerConfigCache->write($dumper->dump(['class' => 'Sparkrvp_Container']), $container->getResources());
        }
        require_once $file;
        return new Sparkrvp_Container();
    }
}
if (!\function_exists('Sparkrvp\\Sparkrvp_activateSparkPlugin')) {
    function Sparkrvp_activateSparkPlugin()
    {
        $container = Sparkrvp_getSparkPluginsContainer();
        $activator = $container->get('activator');
        $activator->activate();
    }
}
if (!\function_exists('Sparkrvp\\Sparkrvp_deactivateSparkPlugin')) {
    function Sparkrvp_deactivateSparkPlugin()
    {
        $container = Sparkrvp_getSparkPluginsContainer();
        $deactivator = $container->get('deactivator');
        $deactivator->deactivate();
    }
}
register_activation_hook(__FILE__, 'Sparkrvp_activateSparkPlugin');
register_deactivation_hook(__FILE__, 'Sparkrvp_deactivateSparkPlugin');
if (!\defined('SPARK_DEV_MODE')) {
    \define('SPARK_DEV_MODE', \false);
}
if (!\defined('SPARK_DEV_HOST_ADMIN')) {
    \define('SPARK_DEV_HOST_ADMIN', \false);
}
if (!\defined('SPARK_DEV_HOST_PUBLIC')) {
    \define('SPARK_DEV_HOST_PUBLIC', \false);
}
if (!\function_exists('Sparkrvp\\Sparkrvp_runSparkPlugin')) {
    function Sparkrvp_runSparkPlugin()
    {
        $container = Sparkrvp_getSparkPluginsContainer();
        $globalVariables = $container->get('global-variables');
        $globalVariables->setPluginDir(\dirname(plugin_basename(__FILE__)));
        $globalVariables->setPluginDirPath(plugin_dir_path(__FILE__));
        $globalVariables->setPluginUrl(plugin_dir_url(__FILE__));
        $globalVariables->setPluginFilePath(__FILE__);
        $plugin = $container->get('plugin');
        $plugin->run();
    }
}
Sparkrvp_runSparkPlugin();
