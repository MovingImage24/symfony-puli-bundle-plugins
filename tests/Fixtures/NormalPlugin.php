<?php

namespace Mi\PuliBundlePlugins\Tests\Fixtures;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class NormalPlugin extends SimpleBundlePlugin
{
    public function name()
    {
        return 'normal';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
    }

    public function build(ContainerBuilder $container)
    {
        $container->setParameter('normal.build', true);
    }

    public function boot(ContainerInterface $container)
    {
        $container->setParameter('normal.boot', true);
    }
}
