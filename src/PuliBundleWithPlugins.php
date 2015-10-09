<?php

namespace Mi\PuliBundlePlugins;

use Matthias\BundlePlugins\BundlePlugin;
use Matthias\BundlePlugins\ExtensionWithPlugins;
use Puli\Discovery\Api\Discovery;
use Puli\Discovery\Binding\ClassBinding;
use Puli\Repository\Api\ResourceRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Webmozart\Expression\Expr;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 *
 * Extend your bundle from this class. It allows users to register plugins for this bundle by providing them as
 * puli class binding.
 *
 * The bundle itself can have no container extension or configuration anymore. Instead, you can introduce something
 * like a `CorePlugin`, which is registered as a `BundlePlugin` for this bundle.
 *
 * This class is inspired by Matthias\BundlePlugins\BundleWithPlugins class.
 */
abstract class PuliBundleWithPlugins extends Bundle
{
    /**
     * @var BundlePlugin[]
     */
    private $registeredPlugins = [];

    /**
     * @return string
     */
    abstract protected function getAlias();

    /**
     * @param Discovery          $discovery
     * @param ResourceRepository $repository
     */
    final public function __construct(Discovery $discovery, ResourceRepository $repository)
    {
        $expr = Expr::method('getParameterValue', 'bundle-alias', Expr::same($this->getAlias()));

        $classBindings = $discovery->findBindings(BundlePlugin::class, $expr);
        $sortBindingsByPriority = function (ClassBinding $a, ClassBinding $b) {
            if ($a->getParameterValue('priority') === $b->getParameterValue('priority')) {
                return 0;
            }

            return ($a->getParameterValue('priority') < $b->getParameterValue('priority')) ? 1 : -1;
        };

        usort($classBindings, $sortBindingsByPriority);

        /** @var ClassBinding $binding */
        foreach ($classBindings as $binding) {
            $pluginClass = $binding->getClassName();
            $plugin = new $pluginClass();

            if ($plugin instanceof DiscoveryAwareInterface) {
                $plugin->setDiscovery($discovery);
            }

            if ($plugin instanceof ResourceRepositoryAwareInterface) {
                $plugin->setRepository($repository);
            }

            $this->registerPlugin($plugin);
        }
    }

    /**
     * {@inheritdoc}
     */
    final public function build(ContainerBuilder $container)
    {
        foreach ($this->registeredPlugins as $plugin) {
            $plugin->build($container);
        }
    }

    /**
     * {@inheritdoc}
     */
    final public function boot()
    {
        foreach ($this->registeredPlugins as $plugin) {
            $plugin->boot($this->container);
        }
    }

    /**
     * {@inheritdoc}
     */
    final public function getContainerExtension()
    {
        return new ExtensionWithPlugins($this->getAlias(), $this->registeredPlugins);
    }

    /**
     * Register a plugin for this bundle.
     *
     * @param BundlePlugin $plugin
     */
    private function registerPlugin(BundlePlugin $plugin)
    {
        $this->registeredPlugins[] = $plugin;
    }
}
