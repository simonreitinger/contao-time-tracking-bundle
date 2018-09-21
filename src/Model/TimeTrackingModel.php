<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace SimonReitinger\TimeTrackingBundle\Model;

use Contao\Model;
use Contao\User;

class TimeTrackingModel extends Model
{
    protected static $strTable = 'tl_time_tracking';

    public function isRunning(): bool
    {
        $entries = TimeTrackingEntryModel::findByTrackingId($this->id);

        /** @var TimeTrackingEntryModel $entry */
        foreach ($entries as $entry) {
            if($entry->end < 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Model\Collection|TimeTrackingEntryModel|null
     */
    public function pause()
    {
        $entry = TimeTrackingEntryModel::findCurrentByTrackingId($this->id);
        $entry->tstamp = time();
        $entry->end = time();
        $entry->save();

        return $entry;
    }

    public function continue(): TimeTrackingEntryModel
    {
        $entry = new TimeTrackingEntryModel();
        $entry->tstamp = time();
        $entry->start = time();
        $entry->trackingId = $this->id;
        $entry->save();

        return $entry;
    }

    /**
     * start is an alias for continue
     *
     * @return TimeTrackingEntryModel
     */
    public function start(): TimeTrackingEntryModel
    {
        $this->continue();
    }
}