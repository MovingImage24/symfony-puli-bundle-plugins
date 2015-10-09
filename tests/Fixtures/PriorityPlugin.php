<?php

namespace Mi\PuliBundlePlugins\Tests\Fixtures;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Mi\PuliBundlePlugins\ResourceRepositoryAwareInterface;
use Puli\Repository\Api\ResourceRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class PriorityPlugin extends SimpleBundlePlugin implements ResourceRepositoryAwareInterface
{
    private $repository;

    public function setRepository(ResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }


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
