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

class TimeTrackingEntryModel extends Model
{
    protected static $strTable = 'tl_time_tracking_entry';

    public static function findCurrentByTrackingId(int $trackingId)
    {
        return parent::findBy(['trackingId = ' . $trackingId, 'end = 0'], []);
    }

    public static function findByTrackingId($trackingId)
    {
        return parent::findBy(['trackingId = ' . $trackingId], ['order' => 'id DESC']);
    }

    /**
     * @param User $user
     *
     * @return bool
     * @throws \Exception
     */
    public function isAllowedToDelete(User $user): bool
    {
        if (self::getRelated('trackingId')->user === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * delete the parent record if no rows exist
     *
     * @return int|void
     */
    public function delete()
    {
        parent::delete();

        // when no other times exist, delete the parent record
        $otherTimes = self::findByTrackingId($this->trackingId);
        if (!$otherTimes) {
            $tracking = TimeTrackingModel::findByPk($this->trackingId);
            echo "<pre>";
            print_r($tracking->id);
            exit;

            $tracking->delete();
        }
    }
}