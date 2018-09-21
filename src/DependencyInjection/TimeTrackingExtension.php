<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace SimonReitinger\TimeTrackingBundle\DependencyInjection;

use SimonReitinger\TimeTrackingBundle\DependencyInjection\Compiler\StopwatchSubmitPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TimeTrackingExtension extends Extension
{
    /**
     * @param array            $mergedConfig
     * @param ContainerBuilder $container
     */
    public function load(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yml');
    }
}