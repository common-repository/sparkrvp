<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\Common;

use Sparkrvp\SparkPlugins\SparkWoo\Common\Loader;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Modules\ModuleInterface;
class PageTemplatesModule implements ModuleInterface
{
    protected array $pageTemplates;
    public function __construct(array $pageTemplates)
    {
        $this->pageTemplates = $pageTemplates;
    }
    public function definePublicHooks(Loader $loader) : void
    {
        $loader->addFilter('theme_page_templates', $this, 'addPageTemplates');
    }
    public function defineAdminHooks(Loader $loader) : void
    {
    }
    public function addPageTemplates($postTemplates)
    {
        $postTemplates = \array_merge($postTemplates, $this->pageTemplates);
        return $postTemplates;
    }
}
