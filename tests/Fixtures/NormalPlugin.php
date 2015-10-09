<?php

namespace Mi\PuliBundlePlugins\Tests\Fixtures;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Mi\PuliBundlePlugins\DiscoveryAwareInterface;
use Puli\Discovery\Api\Discovery;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class NormalPlugin extends SimpleBundlePlugin implements DiscoveryAwareInterface
{
    private $discovery;

    public function setDiscovery(Discovery $discovery)
    {
        $this->discovery = $discovery;
    }

    public function getDiscovery()
    {
        return $this->discovery;
    }

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
