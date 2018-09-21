<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace SimonReitinger\TimeTrackingBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class StopwatchListenerCollection
{
    /**
     * @var array $listeners
     */
    private $listeners;

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->listeners = [];
        $this->container = $container;
    }

    public function setListeners(array $listeners)
    {
        $this->listeners = $listeners;
    }

    public function getListeners(): array
    {
        return $this->listeners;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getSubmitRoute()
    {
        if (!isset($this->listeners['submit'])) {
            throw new \Exception('There is no submit listener set. You have to configure in in your services.');
        }

        $listener = $this->listeners['submit'];

        return $listener['route'];
    }
}