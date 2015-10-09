<?php

namespace Mi\PuliBundlePlugins\Tests\Fixtures;

use Mi\PuliBundlePlugins\ServiceBundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class TestServicePlugin extends ServiceBundlePlugin
{
    protected function loadInternal(array $pluginConfiguration, ContainerBuilder $container)
    {
        $container->setParameter('internal.loaded', true);
    }

    public function name()
    {
        return 'name';
    }
}
