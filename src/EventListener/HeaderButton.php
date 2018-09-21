<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace SimonReitinger\TimeTrackingBundle\EventListener;

use Contao\BackendTemplate;
use Contao\BackendUser;
use Contao\User;
use Contao\System;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;

class HeaderButton
{
    /**
     * adds the stopwatch button to the backend header.
     *
     * @param string $buffer
     * @param string $template
     */
    public function onOutputBackendTemplate(string $buffer, string $template)
    {
        if ($template === 'be_main') {
            $container = System::getContainer();
            $router = $container->get('router');
            $stopwatchRoute = $router->generate('time_tracking');



            $button = sprintf('<li><a href="%s" class="icon-stopwatch" title="%s">%s</a></li>',
                $stopwatchRoute,
                "Stoppuhr",
                "Stoppuhr"
            );

            return str_replace('<ul id="tmenu">', '<ul id="tmenu">' . $button, $buffer);
        }

        return $buffer;
    }
}