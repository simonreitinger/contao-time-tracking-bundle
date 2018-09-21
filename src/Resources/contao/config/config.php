<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

use SimonReitinger\TimeTrackingBundle\EventListener\HeaderButton;
use SimonReitinger\TimeTrackingBundle\Model\TimeTrackingEntryModel;
use SimonReitinger\TimeTrackingBundle\Model\TimeTrackingModel;

$GLOBALS['TL_HOOKS']['outputBackendTemplate'][] = [HeaderButton::class, 'onOutputBackendTemplate'];

$GLOBALS['TL_MODELS']['tl_time_tracking'] = TimeTrackingModel::class;
$GLOBALS['TL_MODELS']['tl_time_tracking_entry'] = TimeTrackingEntryModel::class;