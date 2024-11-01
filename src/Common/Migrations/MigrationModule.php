<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\Common\Migrations;

use Sparkrvp\SparkPlugins\SparkWoo\Common\Loader;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Modules\ModuleInterface;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Options\StringOption;
use Sparkrvp\SparkPlugins\SparkWoo\Common\Plugins\PluginMeta;
class MigrationModule implements ModuleInterface
{
    protected array $migrations;
    protected PluginMeta $pluginMeta;
    protected StringOption $versionOption;
    public function __construct(iterable $migrations, PluginMeta $pluginMeta, StringOption $versionOption)
    {
        $this->migrations = \iterator_to_array($migrations);
        $this->pluginMeta = $pluginMeta;
        $this->versionOption = $versionOption;
    }
    public function definePublicHooks(Loader $loader) : void
    {
        $loader->addAction('init', $this, 'runMigrations');
    }
    public function defineAdminHooks(Loader $loader) : void
    {
    }
    public function runMigrations()
    {
        $newVersion = $this->pluginMeta->version;
        $noVersion = '0.0.0';
        $previousVersion = $this->versionOption->getValue($noVersion);
        $firstInstall = $previousVersion === $noVersion;
        if ($firstInstall) {
            $this->versionOption->setValue($newVersion);
            return;
        }
        if ($newVersion === $previousVersion) {
            return;
        }
        $upgrade = \version_compare($newVersion, $previousVersion, '>');
        foreach ($this->migrations as $migration) {
            /** @var MigrationInterface $migration */
            $migrationVersion = $migration->getVersion();
            if ($migrationVersion === null) {
                continue;
            }
            if ($upgrade && \version_compare($migrationVersion, $previousVersion, '>') && \version_compare($migrationVersion, $newVersion, '<=')) {
                $migration->up();
            }
            if (!$upgrade && \version_compare($migrationVersion, $newVersion, '>') && \version_compare($migrationVersion, $previousVersion, '<=')) {
                $migration->down();
            }
        }
        $this->versionOption->setValue($newVersion);
    }
}
