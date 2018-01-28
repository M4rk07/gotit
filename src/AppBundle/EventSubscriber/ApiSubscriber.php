<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/28/2018
 * Time: 2:39 AM
 */

namespace AppBundle\EventSubscriber;

use AppBundle\Exception\ApiException;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;

class ApiSubscriber implements EventSubscriberInterface
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

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if($request->headers->get('Accept') == 'application/xml') {
            try {

                $responseContent = json_decode($response->getContent());

                $serializer = SerializerBuilder::create()->build();

                $responseContent = $serializer->serialize($responseContent, 'xml');

                $response->setContent($responseContent);
                $response->headers->set('Content-Type', 'application/xml');

                $event->setResponse($response);
            } catch (\Exception $e) {}
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException',
            KernelEvents::RESPONSE => 'onKernelResponse'
        );
    }
}