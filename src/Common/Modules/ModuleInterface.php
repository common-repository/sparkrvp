<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\Common\Modules;

use Sparkrvp\SparkPlugins\SparkWoo\Common\Loader;
interface ModuleInterface
{
    public function defineAdminHooks(Loader $loader) : void;
    public function definePublicHooks(Loader $loader) : void;
}
