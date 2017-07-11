<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 9.7.17.
 * Time: 21.45
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use AppBundle\Entity\Marker;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ConfigurationController extends Controller
{

    /**
     * @Route("/config", name="config")
     * @Method({"GET"})
     */
    public function getConfigAction(Request $request)
    {
        return new JsonResponse(array(
            "maxImageSize" => 800,
        ));
    }

}