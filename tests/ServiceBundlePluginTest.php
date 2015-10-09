<?php

namespace Mi\PuliBundlePlugins\Tests;

use Mi\PuliBundlePlugins\Tests\Fixtures\TestServicePlugin;
use Puli\Discovery\Api\Type\BindingType;
use Puli\Discovery\Binding\Initializer\ResourceBindingInitializer;
use Puli\Discovery\Binding\ResourceBinding;
use Puli\Discovery\InMemoryDiscovery;
use Puli\Repository\FilesystemRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 *
 * @covers Mi\PuliBundlePlugins\ServiceBundlePlugin
 */
class ServiceBundlePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestServicePlugin
     */
    private $plugin;

    /**
     * @test
     */
    public function load()
    {
        $container = new ContainerBuilder();

        $this->plugin->load([], $container);

        self::assertTrue($container->hasParameter('internal.loaded'));
        self::assertTrue($container->hasParameter('xml.loaded'));
        self::assertTrue($container->hasParameter('php.loaded'));
        self::assertTrue($container->hasParameter('yml.loaded'));
    }

    protected function setUp()
    {
        $this->plugin = new TestServicePlugin();
        $repo = new FilesystemRepository(__DIR__, true);

        $discovery = new InMemoryDiscovery([new ResourceBindingInitializer($repo)]);
        $discovery->addBindingType(new BindingType('mi/service'));
        $discovery->addBinding(
            new ResourceBinding(
                '/Fixtures/service.*',
                'mi/service'
            )
        );

        $this->plugin->setDiscovery($discovery);
        $this->plugin->setRepository($repo);
    }
}
