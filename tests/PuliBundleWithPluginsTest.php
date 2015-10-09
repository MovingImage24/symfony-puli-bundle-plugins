<?php

namespace Mi\PuliBundlePlugins\Tests;

use Matthias\BundlePlugins\BundlePlugin;
use Mi\PuliBundlePlugins\Tests\Fixtures\NormalPlugin;
use Mi\PuliBundlePlugins\Tests\Fixtures\PriorityPlugin;
use Mi\PuliBundlePlugins\Tests\Fixtures\TestPuliBundleWithPlugins;
use Puli\Discovery\Api\Discovery;
use Puli\Discovery\Api\Type\BindingParameter;
use Puli\Discovery\Api\Type\BindingType;
use Puli\Discovery\Binding\ClassBinding;
use Puli\Discovery\Binding\Initializer\ResourceBindingInitializer;
use Puli\Discovery\InMemoryDiscovery;
use Puli\Repository\Api\ResourceRepository;
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
     * @var Discovery
     */
    private $discovery;

    /**
     * @var ResourceRepository
     */
    private $repo;

    /**
     * @test
     */
    public function build_the_plugins()
    {
        $container = new ContainerBuilder();

        $bundle = $this->getBundle();

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

        $bundle = $this->getBundle();
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
        $bundle = $this->getBundle();

        $extension = $bundle->getContainerExtension();

        $registeredPluginsReflection = new \ReflectionProperty($extension, 'registeredPlugins');

        $registeredPluginsReflection->setAccessible(true);

        $registeredPlugins = $registeredPluginsReflection->getValue($extension);

        self::assertEquals(NormalPlugin::class, $this->discovery->findBindings(BundlePlugin::class)[0]->getClassName());
        self::assertInstanceOf(PriorityPlugin::class, $registeredPlugins[0]);
    }

    /**
     * @test
     */
    public function set_the_repository_and_discovery_for_the_aware_interfaces()
    {
        $bundle = $this->getBundle();

        $extension = $bundle->getContainerExtension();

        $registeredPluginsReflection = new \ReflectionProperty($extension, 'registeredPlugins');

        $registeredPluginsReflection->setAccessible(true);

        $registeredPlugins = $registeredPluginsReflection->getValue($extension);

        self::assertEquals($this->repo, $registeredPlugins[0]->getRepository());
        self::assertEquals($this->discovery, $registeredPlugins[1]->getDiscovery());
    }

    protected function setUp()
    {
        $this->repo = new FilesystemRepository(__DIR__, true);

        $this->discovery = new InMemoryDiscovery([new ResourceBindingInitializer($this->repo)]);
        $this->discovery->addBindingType(
            new BindingType(
                BundlePlugin::class,
                [
                    new BindingParameter('bundle-alias', BindingParameter::REQUIRED),
                    new BindingParameter('priority', BindingParameter::OPTIONAL, 0),
                ]
            )
        );
        $this->discovery->addBinding(
            new ClassBinding(
                NormalPlugin::class,
                BundlePlugin::class,
                ['bundle-alias' => 'test']
            )

        );

        $this->discovery->addBinding(
            new ClassBinding(
                NormalPlugin::class,
                BundlePlugin::class,
                ['bundle-alias' => 'test']
            )

        );

        $this->discovery->addBinding(
            new ClassBinding(
                PriorityPlugin::class,
                BundlePlugin::class,
                ['bundle-alias' => 'test', 'priority' => 10]
            )
        );

        return $this->discovery;
    }

    private function getBundle()
    {
        return new TestPuliBundleWithPlugins($this->discovery, $this->repo);
    }
}
