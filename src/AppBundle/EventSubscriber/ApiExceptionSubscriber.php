<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/28/2018
 * Time: 2:39 AM
 */

namespace AppBundle\EventSubscriber;

use AppBundle\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();

        if ($e instanceof ApiException) {

            $response = new JsonResponse(array(
                'error_message' => $e->getMessage()
            ), $e->getStatusCode());

            $event->setResponse($response);

        }

    }
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }
}