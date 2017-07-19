<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.7.17.
 * Time: 00.01
 */

namespace AppBundle\Controller;

use AppBundle\Entity\EndUser;
use AppBundle\Entity\Item;
use AppBundle\Entity\ItemType;
use AppBundle\Entity\Marker;
use AppBundle\Map\Bounds;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;
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

class MarkerController extends Controller
{

    /**
     * @Route("/items", name="items")
     * @Method({"POST", "GET"})
     */
    public function itemsAction(Request $request) {

        if($request->getMethod() == "POST")
            return $this->insertItem($request);
        else if($request->getMethod() == "GET")
            return $this->getItems($request);

    }

    public function insertItem(Request $request) {

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');

        $lat = $request->request->get("lat");
        $lng = $request->request->get("lng");
        $description = $request->request->get("description");
        $image = $request->files->get("image");
        $itemType = $request->request->get("type");

        $em = $this->getDoctrine()->getManager();
        $validator = $this->get('validator');

        $em->getConnection()->beginTransaction();

        try {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            // Create Marker object
            $marker = new Marker();
            $marker->setLat(number_format($lat, 6))
                ->setLng(number_format($lng, 6));

            // Create Item object
            $item = new Item();
            $item->setDescription($description)
                ->setImage($image);

            $itemType = $em->getRepository('AppBundle:ItemType')->find($itemType);

            $item->setType($itemType);
            $marker->setType($itemType->getTypeId());

            // Set their relations
            $item->setMarker($marker);
            $marker->addItem($item);
            $marker->setUser($user);
            $item->setUser($user);

            // Validate properties
            $mErrors = $validator->validate($marker);
            $iErrors = $validator->validate($item);
            if(count($mErrors) > 0 || count($iErrors) > 0){
                throw new \Exception();
            }

            $markerDuplicate = $em
                ->getRepository('AppBundle:Marker')
                ->findOneBy(array('lat' => $marker->getLat(), 'lng' => $marker->getLng()));

            if(!empty($markerDuplicate)) {
                $markerDuplicate->incNumOfItems();
                $currTypes = explode(',', $markerDuplicate->getType());
                if(empty($currTypes))
                    $currTypes = array($markerDuplicate->getType());
                if(!in_array($itemType->getTypeId(), $currTypes)) {
                    $currTypes[] = $itemType->getTypeId();
                    $currTypes = implode(',', $currTypes);
                    $markerDuplicate->setType($currTypes);
                }
                $em->merge($markerDuplicate);

                $item->setMarker($markerDuplicate);
                $em->persist($item);
            }
            else
                $em->persist($marker);

            // Generate a unique name for the file before saving it
            $item->saveImage();

            $em->flush();
            $em->getConnection()->commit();
        }
        catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($item, 'json',
            SerializationContext::create()->setGroups(array('Default', 'items_and_markers')));

        return new Response($jsonContent, 200, array("Content-Type" => "application/json"));
    }

    function getItems(Request $request) {
        $markerId = $request->get('markerId');
        $em = $this->getDoctrine()->getManager();

        $marker = $em->getRepository('AppBundle:Marker')->find($markerId);

        $items = $em->getRepository('AppBundle:Item')->findBy(
            array('marker' => $marker)
        );

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($items, 'json',
            SerializationContext::create()->setGroups(array('Default')));

        return new Response($jsonContent, 200, array("Content-Type" => "application/json"));
    }

    /**
     * @Route("/markers", name="markers")
     * @Method({"GET"})
     */
    public function getMarkers(Request $request) {
        $bounds = json_decode($request->get("bounds"));
        $searchType = $request->get("searchType");
        $withOthers = $request->get("withOthers");

        $searchTypes = array();
        if(!empty($searchType)) {
            $searchTypes[] = $searchType;
        }
        if(!empty($withOthers))
            $searchTypes[] = "other";

        $currBounds = new Bounds();

        $currBounds->setSouth($bounds->currBounds->south)
            ->setWest($bounds->currBounds->west)
            ->setNorth($bounds->currBounds->north)
            ->setEast($bounds->currBounds->east);

        if(!empty($bounds->prevBounds)) {
            $prevBounds = new Bounds();

            $prevBounds->setSouth($bounds->prevBounds->south)
                ->setWest($bounds->prevBounds->west)
                ->setNorth($bounds->prevBounds->north)
                ->setEast($bounds->prevBounds->east);

            $markers = $this->getDoctrine()->getManager()->getRepository('AppBundle:Marker')
                ->findByBoundsWithPrev($currBounds, $prevBounds, $searchTypes);
        } else
            $markers = $this->getDoctrine()->getManager()->getRepository('AppBundle:Marker')
                ->findByBounds($currBounds, $searchTypes);

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($markers, 'json', SerializationContext::create()->setGroups(array('Default')));

        return new Response($jsonContent, 200, array("Content-Type" => "application/json"));
    }

}