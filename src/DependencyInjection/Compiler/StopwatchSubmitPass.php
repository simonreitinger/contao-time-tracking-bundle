<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace SimonReitinger\TimeTrackingBundle\DependencyInjection\Compiler;

use SimonReitinger\TimeTrackingBundle\EventListener\StopwatchSubmitListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class StopwatchSubmitPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('time_tracking.listener.collection')) {
            return;
        }

        $listeners = $this->getListeners($container);

        $definition = $container->getDefinition('time_tracking.listener.collection');
        $definition->addMethodCall('setListeners', [$listeners]);
    }

    /**
     * if the method attribute is explicitly set, return it
     *
     * otherwise the type gets used
     * e.g. type 'submit' -> method onSubmit
     *
     * @param array $attributes
     *
     * @return string
     */
    public function getMethod(array $attributes): string
    {
        if (isset($attributes['method'])) {
            return (string) $attributes['method'];
        }

        return $attributes['type'] . 'Action';
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    public function getListeners(ContainerBuilder $container): array
    {
        $references = $container->findTaggedServiceIds('time_tracking.listener');
        $listeners = [];

        // set the listener
        foreach ($references as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $listeners[$attributes['type']] = [
                    'id' => $serviceId,
                    'method' => $this->getMethod($attributes),
                    'route' => $attributes['route'] ?? 'app_time_tracking_submit'
                ];
            }
        }

        return $listeners;
    }
}