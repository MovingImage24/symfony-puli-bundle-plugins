<?php

namespace Mi\PuliBundlePlugins;

use Matthias\BundlePlugins\SimpleBundlePlugin;
use Puli\Discovery\Api\Discovery;
use Puli\Discovery\Binding\ResourceBinding;
use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Resource\FileResource;
use Puli\SymfonyBridge\Config\PuliFileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
abstract class ServiceBundlePlugin extends SimpleBundlePlugin implements DiscoveryAwareInterface, ResourceRepositoryAwareInterface
{
    /**
     * @var Discovery
     */
    private $discovery;

    /**
     * @var ResourceRepository
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    public function setDiscovery(Discovery $discovery)
    {
        $this->discovery = $discovery;
    }

    /**
     * {@inheritdoc}
     */
    public function setRepository(ResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loaders[] = new XmlFileLoader($container, new PuliFileLocator($this->repository));
        $loaders[] = new YamlFileLoader($container, new PuliFileLocator($this->repository));
        $loaders[] = new PhpFileLoader($container, new PuliFileLocator($this->repository));

        $loaderResolver = new LoaderResolver($loaders);
        $delegateLoader = new DelegatingLoader($loaderResolver);

        $bindinigs = $this->discovery->findBindings('mi/service');

        /** @var ResourceBinding $bindinig */
        foreach ($bindinigs as $bindinig) {
            /** @var FileResource $resource */
            foreach ($bindinig->getResources()->toArray() as $resource) {
                $delegateLoader->load($resource->getFilesystemPath());
            }
        }

        $this->loadInternal($pluginConfiguration, $container);
    }

    /**
     * @param array            $pluginConfiguration The part of the bundle configuration for this plugin
     * @param ContainerBuilder $container
     */
    abstract protected function loadInternal(array $pluginConfiguration, ContainerBuilder $container);
}
