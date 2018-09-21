<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace SimonReitinger\TimeTrackingBundle;

use SimonReitinger\TimeTrackingBundle\DependencyInjection\Compiler\StopwatchSubmitPass;
use SimonReitinger\TimeTrackingBundle\DependencyInjection\TimeTrackingExtension;
use SimonReitinger\TimeTrackingBundle\EventListener\StopwatchSubmitInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimonReitingerTimeTrackingBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new TimeTrackingExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new StopwatchSubmitPass());
    }
}