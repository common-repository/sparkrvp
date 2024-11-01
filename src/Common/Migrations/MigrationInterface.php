<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\Common\Migrations;

interface MigrationInterface
{
    public function up() : void;
    public function down() : void;
    public function getVersion();
}
