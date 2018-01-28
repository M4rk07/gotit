<?php

namespace AppBundle\EventSubscriber;

/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/28/2018
 * Time: 2:17 AM
 */

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CountrySubscriber implements EventSubscriberInterface
{

    public function onKernelController(FilterControllerEvent $event)
    {

        //$file = 'http://www.geoplugin.net/php.gp?ip='.$event->getRequest()->getClientIp();

        // Srpska IP adresa
        $ip = '37.220.79.255';

        // Nemacka IP adresa
        //$ip = '46.4.96.246';

        $file = 'http://www.geoplugin.net/php.gp?ip='.$ip;

        $geopluginData = unserialize(file_get_contents($file));

        if(!isset($geopluginData['geoplugin_countryCode']) || empty($geopluginData['geoplugin_countryCode']) || $geopluginData['geoplugin_countryCode']!='RS')
            throw new AccessDeniedHttpException('This action needs a valid token!');

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }

}