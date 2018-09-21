<?php

/*
 * This file is part of the Contao Time Tracking Bundle.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace SimonReitinger\TimeTrackingBundle\Controller;

use Contao\BackendUser;
use Contao\System;
use SimonReitinger\TimeTrackingBundle\Model\TimeTrackingEntryModel;
use SimonReitinger\TimeTrackingBundle\Model\TimeTrackingModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StopwatchController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $this->addAssets();
        return $this->render('@SimonReitingerTimeTracking/time_tracking.html.twig');
    }

    /**
     * adds scripts and css by using the globals array
     * adding them in be_page.html.twig is not possible at the moment
     */
    public function addAssets()
    {
        $GLOBALS['TL_MOOTOOLS']['axios'] = '<script src="https://unpkg.com/axios/dist/axios.min.js"></script>';
        $GLOBALS['TL_MOOTOOLS']['vue'] = '<script src="https://cdn.jsdelivr.net/npm/vue"></script>';
        $GLOBALS['TL_MOOTOOLS']['time_tracking'] = '<script src="/bundles/simonreitingertimetracking/dist/time-tracking.min.js"></script>';
        $GLOBALS['TL_CSS']['icons'] = 'https://fonts.googleapis.com/icon?family=Material+Icons';
    }

    public function loadAction(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->noXmlHttpRequestError();
        }

        $this->get('contao.framework')->initialize();
        $user = BackendUser::getInstance();
        $ttm = TimeTrackingModel::findBy('user', $user->id);
        $data = [];
        /** @var TimeTrackingModel $timer */
        foreach ($ttm as $timer) {
            $trackedTimes = TimeTrackingEntryModel::findBy('trackingId', $timer->id);
            $times = [];
            $running = false;

            /** @var TimeTrackingEntryModel $time */
            foreach ($trackedTimes as $time) {
                $times[] = [
                    'id' => $time->id,
                    'start' => $time->start,
                    'end' => $time->end
                ];

                if (!$time->end) {
                    $running = true;
                }

            }

            if ($times) {
                $data[] = [
                    'id' => $timer->id,
                    'description' => $timer->description,
                    'times' => $times,
                    'running' => $running,
                    'submitRoute' => $this->generateSubmitRoute($timer->id)
                ];
            }
        }

        return new JsonResponse(['trackingList' => $data]);
    }

    /**
     * @Method("POST")
     * @return JsonResponse
     */
    public function newAction(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->noXmlHttpRequestError();
        }

        $time = time();
        $this->get('contao.framework')->initialize();
        $user = BackendUser::getInstance();
        $userId = $user->id;
        $description = $request->get('description') ?? '';

        // save timer
        $timer = new TimeTrackingModel();
        $timer->user = $userId;
        $timer->tstamp = $time;
        $timer->description = $description;
        $timer->save();

        $entry = $timer->continue();

        return new JsonResponse([
            'timer' => [
                'id' => (int)$timer->id,
                'times' => [[
                    'id' => (int)$entry->id,
                    'start' => (int)$time,
                    'end' => 0
                ]],
                'description' => $description,
                'running' => true,
                'submitRoute' => $this->generateSubmitRoute($timer->id)
            ],
            'time' => $time
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function pauseAction(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->noXmlHttpRequestError();
        }

        $time = time();
        $this->get('contao.framework')->initialize();
        $user = BackendUser::getInstance();

        $id = $request->get('id');

        $timer = TimeTrackingModel::findByPk($id);

        $newestTime = $timer->pause();

        if ($newestTime) {
            return new JsonResponse([
                'id' => (int)$newestTime->id,
                'start' => (int)$newestTime->start,
                'end' => (int)$newestTime->end
            ]);
        }

        return new JsonResponse(['error' => 'Time not found'], 400);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function continueAction(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->noXmlHttpRequestError();
        }

        $time = time();
        $this->get('contao.framework')->initialize();
        $user = BackendUser::getInstance();
        $trackingId = $request->get('id');

        $timer = TimeTrackingModel::findByPk($trackingId);
        $entry = $timer->continue();

        return new JsonResponse([
            'timer' => [
                'id' => (int)$entry->id,
                'start' => (int)$entry->start,
                'end' => (int)$entry->end
            ],
            'time' => $time
        ]);
    }

    public function updateDescriptionAction(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->noXmlHttpRequestError();
        }
        $this->get('contao.framework')->initialize();
        $user = BackendUser::getInstance();

        $id = $request->get('id');
        $description = trim($request->get('description')) ?? '';

        $tt = TimeTrackingModel::findByPk($id);
        if ($tt) {
            $tt->tstamp = time();
            $tt->description = $description;
            $tt->save();
        }

        return new JsonResponse([]);
    }

    /**
     * @Method("DELETE")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteTimeAction(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->noXmlHttpRequestError();
        }

        $this->get('contao.framework')->initialize();
        $user = BackendUser::getInstance();

        $entryId = $request->get('id');

        $tte = TimeTrackingEntryModel::findByPk($entryId);

        if ($tte && $tte->isAllowedToDelete($user)) {
            $tte->delete();
            return new JsonResponse(['success' => true]);
        }

        $this->addFlash('info', 'not allowed to delete this entry');
        return new JsonResponse(['success' => false]);
}

    /**
     * @Method("DELETE")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteTrackingAction(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->noXmlHttpRequestError();
        }

        $this->get('contao.framework')->initialize();
        $user = BackendUser::getInstance();

        $trackingId = $request->get('id');
        $deleted = [];

        // only delete the child records, tracking gets deleted automatically with the last one
        $tte = TimeTrackingEntryModel::findBy('trackingId', $trackingId);
        /** @var TimeTrackingEntryModel $entry */
        foreach ($tte as $entry) {
            if ($entry->isAllowedToDelete($user)) {
                $deleted[] = $entry->id;
                $entry->delete();
            }
        }
        return new JsonResponse(['success' => true, 'deleted' => $deleted]);
    }

    /**
     * @param         $trackingId
     * @param Request $request
     *
     * @return JsonResponse|Response
     * @throws \Exception
     */
    public function submitAction($trackingId, Request $request)
    {
        $this->get('contao.framework')->initialize();

        return $this->redirectToRoute($this->get('time_tracking.listener.collection')->getSubmitRoute(), [
            'request' => $request,
            'trackingId' => $trackingId
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function noXmlHttpRequestError()
    {
        return new JsonResponse(['error' => 'You can only use the time tracking through the UI'], 400);
    }

    public function generateSubmitRoute($timerId)
    {
        return $this->get('router')->generate('time_tracking_submit', ['trackingId' => $timerId]);
    }
}
