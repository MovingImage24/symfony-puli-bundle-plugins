<?php

namespace Mi\PuliBundlePlugins\Tests\Fixtures;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class PriorityPlugin extends SimpleBundlePlugin
{
    public function name()
    {
        return 'priority';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
    }

    public function build(ContainerBuilder $container)
    {
        $container->setParameter('priority.build', true);
    }

    public function boot(ContainerInterface $container)
    {
        $container->setParameter('priority.boot', true);
    }

}
