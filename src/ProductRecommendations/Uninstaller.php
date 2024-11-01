<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\ProductRecommendations;

use Sparkrvp\SparkPlugins\SparkWoo\Common\Cache\CacheManager;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Installation\AbstractUninstaller;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Installation\UninstallerInterface;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Options\OptionInterface;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Options\OptionsCollection;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Plugins\PluginMeta;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Plugins\PluginMetaCollection;
class Uninstaller extends AbstractUninstaller implements UninstallerInterface
{
    private string $pluginGroup;
    public function __construct(PluginMeta $pluginMeta, PluginMetaCollection $pluginMetaCollection, OptionInterface $deleteOptionsOption, OptionInterface $deletePostsOption, iterable $postModels, OptionsCollection $options, CacheManager $cacheManager, string $pluginGroup)
    {
        parent::__construct($pluginMeta, $pluginMetaCollection, $deleteOptionsOption, $deletePostsOption, $postModels, $options, $cacheManager);
        $this->pluginGroup = $pluginGroup;
    }
    public function uninstall() : void
    {
        if (!$this->pluginMetaCollection->hasOthersInstalled($this->pluginMeta, $this->pluginGroup)) {
            $this->removePosts();
        }
        $this->removeOptions();
        $this->cacheManager->clear();
    }
}
