<?php

namespace Mi\PuliBundlePlugins\Tests;

use Matthias\BundlePlugins\BundlePlugin;
use Mi\PuliBundlePlugins\Tests\Fixtures\NormalPlugin;
use Mi\PuliBundlePlugins\Tests\Fixtures\PriorityPlugin;
use Mi\PuliBundlePlugins\Tests\Fixtures\TestPuliBundleWithPlugins;
use Puli\Discovery\Api\Type\BindingParameter;
use Puli\Discovery\Api\Type\BindingType;
use Puli\Discovery\Binding\ClassBinding;
use Puli\Discovery\Binding\Initializer\ResourceBindingInitializer;
use Puli\Discovery\InMemoryDiscovery;
use Puli\Repository\FilesystemRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 *
 * @covers Mi\PuliBundlePlugins\PuliBundleWithPlugins
 */
class PuliBundleWithPluginsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function build_the_plugins()
    {
        $container = new ContainerBuilder();

        $bundle = new TestPuliBundleWithPlugins($this->getDiscoery());

        $bundle->build($container);

        self::assertTrue($container->hasParameter('normal.build'));
        self::assertTrue($container->hasParameter('priority.build'));

    }

    /**
     * @test
     */
    public function boot_the_plugins()
    {
        $container = new ContainerBuilder();

        $bundle = new TestPuliBundleWithPlugins($this->getDiscoery());
        $bundle->setContainer($container);
        $bundle->boot();

        self::assertTrue($container->hasParameter('normal.boot'));
        self::assertTrue($container->hasParameter('priority.boot'));
    }

    /**
     * @test
     */
    public function sort_the_plugins_correct()
    {
        $discovery = $this->getDiscoery();

        $bundle = new TestPuliBundleWithPlugins($discovery);

        $extension = $bundle->getContainerExtension();

        $registeredPluginsReflection = new \ReflectionProperty($extension, 'registeredPlugins');

        $registeredPluginsReflection->setAccessible(true);

        $registeredPlugins = $registeredPluginsReflection->getValue($extension);

        self::assertEquals(NormalPlugin::class, $discovery->findBindings(BundlePlugin::class)[0]->getClassName());
        self::assertInstanceOf(PriorityPlugin::class, $registeredPlugins[0]);
    }

    /**
     * @return InMemoryDiscovery
     */
    private function getDiscoery()
    {
        $repo = new FilesystemRepository(__DIR__, true);

        $discovery = new InMemoryDiscovery([new ResourceBindingInitializer($repo)]);
        $discovery->addBindingType(
            new BindingType(
                BundlePlugin::class,
                [
                    new BindingParameter('bundle-alias', BindingParameter::REQUIRED),
                    new BindingParameter('priority', BindingParameter::OPTIONAL, 0)
                ]
            )
        );
        $discovery->addBinding(
            new ClassBinding(
                NormalPlugin::class,
                BundlePlugin::class,
                ['bundle-alias' => 'test']
            )

        );

        $discovery->addBinding(
            new ClassBinding(
                NormalPlugin::class,
                BundlePlugin::class,
                ['bundle-alias' => 'test']
            )

        );

        $discovery->addBinding(
            new ClassBinding(
                PriorityPlugin::class,
                BundlePlugin::class,
                ['bundle-alias' => 'test', 'priority' => 10]
            )
        );

        return $discovery;
    }
}
