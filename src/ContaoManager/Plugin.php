<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace SimonReitinger\TimeTrackingBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use SimonReitinger\TimeTrackingBundle\SimonReitingerTimeTrackingBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /**
     * @param ParserInterface $parser
     * @return array
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(SimonReitingerTimeTrackingBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
        ];
    }

    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        $routing = __DIR__ . '/../Resources/config/routing.yml';

        return $resolver
            ->resolve($routing)
            ->load($routing);
    }
}