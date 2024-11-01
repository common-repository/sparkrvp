<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\Common\Factories;

use Sparkrvp\SparkPlugins\SparkWoo\Common\Models\ModelInterface;
interface FactoryInterface
{
    public function default() : array;
    public function create(array $data) : ModelInterface;
}
