<?php



if (!\defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';
use Sparkrvp\Symfony\Component\Config\FileLocator;
use Sparkrvp\Symfony\Component\DependencyInjection\ContainerBuilder;
use Sparkrvp\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
\defined('WP_UNINSTALL_PLUGIN') || exit;
$container = new ContainerBuilder();
$fileLocator = new FileLocator(__DIR__);
$loader = new YamlFileLoader($container, $fileLocator);
$loader->load('config/services-sparkrvp.yaml');
$container->compile();
$uninstaller = $container->get('uninstaller');
$uninstaller->uninstall();
