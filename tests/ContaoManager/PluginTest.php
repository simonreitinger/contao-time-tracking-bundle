<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 21.09.18
 * Time: 12:32
 */

namespace SimonReitinger\TimeTrackingBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\DelegatingParser;
use PHPUnit\Framework\TestCase;
use SimonReitinger\TimeTrackingBundle\SimonReitingerTimeTrackingBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class PluginTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf('SimonReitinger\TimeTrackingBundle\ContaoManager\Plugin', $plugin);
    }

    public function testReturnsTheBundle(): void
    {
        $plugin = new Plugin();
        /** @var BundleConfig[]|array $bundles */
        $bundles = $plugin->getBundles(new DelegatingParser());

        echo "<pre>";
        print_r($bundles);
        exit;

        $this->assertSame(SimonReitingerTimeTrackingBundle::class, $bundles[0]->getName());
    }

    public function testGetRouteCollection()
    {
        $loader = $this->createMock(LoaderInterface::class);
        $loader
            ->expects($this->once())
            ->method('load')
        ;
        $resolver = $this->createMock(LoaderResolverInterface::class);
        $resolver
            ->method('resolve')
            ->willReturn($loader)
        ;
        $plugin = new Plugin();
        $plugin->getRouteCollection($resolver, $this->createMock(KernelInterface::class));
    }
}
