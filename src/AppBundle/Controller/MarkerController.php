<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.7.17.
 * Time: 00.01
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use AppBundle\Entity\EndUser;
use AppBundle\Entity\Item;
use AppBundle\Entity\ItemHistory;
use AppBundle\Entity\ItemType;
use AppBundle\Entity\Marker;
use AppBundle\Entity\Report;
use AppBundle\Entity\Statistics;
use AppBundle\Exception\ApiException;
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
     * @Route("/items", name="items_insert")
     * @Method({"POST"})
     *
     * REST ROUTE
     */
    public function insertItem(Request $request) {

        $lat = $request->request->get("lat");
        $lng = $request->request->get("lng");
        $description = $request->request->get("description");
        $image = $request->files->get("image");
        $itemType = $request->request->get("type");

        if(empty($lat) || empty($lng) || empty($description) || empty($itemType))
            throw new ApiException(400, 'Invalid request.');

        $em = $this->getDoctrine()->getManager();
        $validator = $this->get('validator');

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
        if(empty($itemType))
            throw new ApiException(400, 'Invalid request');

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
        if(count($mErrors) > 0){
            throw new ApiException(400, $mErrors[0]->getMessage());
        }
        if(count($iErrors) > 0){
            throw new ApiException(400, $iErrors[0]->getMessage());
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

        $statistics = $em->getRepository('AppBundle:Statistics')->find('MAIN');
        $statistics->incNumOfItems();

        $todayDate = date("d.m.Y");
        $statisticsToday = $em->getRepository('AppBundle:Statistics')->find($todayDate);
        if(empty($statisticsToday)) {
            $statisticsToday = new Statistics();
            $statisticsToday->setStatisticsId($todayDate);
            $statisticsToday->incNumOfItems();
            $em->persist($statisticsToday);
        } else
            $statisticsToday->incNumOfItems();

        $activity = new Activity();
        $activity->setUser($user)
            ->setActivityType("ITEM_CREATION");
        $em->persist($activity);

        $em->flush();

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($item, 'json',
            SerializationContext::create()->setGroups(array('Default', 'items_and_markers')));

        return new Response($jsonContent, 200, array("Content-Type" => "application/json"));
    }

    /**
     * @Route("/items", name="items_get")
     * @Method({"GET"})
     *
     * REST ROUTE
     */
    function getItems(Request $request) {
        $markerId = $request->get('markerId');
        $em = $this->getDoctrine()->getManager();

        if(empty($markerId))
            throw new ApiException(400, 'Invalid request.');

        $marker = $em->getRepository('AppBundle:Marker')->find($markerId);

        if(empty($marker))
            throw new ApiException(400, 'Invalid request');

        $items = $em->getRepository('AppBundle:Item')->findBy(
            array('marker' => $marker)
        );

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($items, 'json',
            SerializationContext::create()->setGroups(array('Default')));

        return new Response($jsonContent, 200, array("Content-Type" => "application/json"));
    }

    /**
     * @Route("/items/{itemId}", name="items_delete")
     * @Method({"DELETE"})
     *
     * REST ROUTE
     */
    function deleteItem ($itemId) {
        $em = $this->getDoctrine()->getManager();

        $item = $em->getRepository('AppBundle:Item')->findOneBy(
            array('item_id' => $itemId)
        );

        if(empty($item))
            throw new ApiException(400, 'Invalid request.');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($item->getUser()->getUserId() != $user->getUserId())
            throw new ApiException(403, 'Action denied.');

        $item->setDeleted(1);

        $marker = $em->getRepository('AppBundle:Marker')->find($item->getMarker()->getMarkerId());

        $itemHistory = new ItemHistory();
        $itemHistory->setItem($item)
            ->setLat($marker->getLat())
            ->setLng($marker->getLng())
            ->setReason("DONE");

        $em->persist($itemHistory);

        $marker->decNumOfItems();

        if($marker->getNumOfItems() == 0) {
            $em->remove($marker);
        }

        $statistics = $em->getRepository('AppBundle:Statistics')->find('MAIN');
        $statistics->incNumOfFound();

        $todayDate = date("d.m.Y");
        $statisticsToday = $em->getRepository('AppBundle:Statistics')->find($todayDate);
        if(empty($statisticsToday)) {
            $statisticsToday = new Statistics();
            $statisticsToday->setStatisticsId($todayDate);
            $statisticsToday->incNumOfFound();
            $em->persist($statisticsToday);
        } else
            $statisticsToday->incNumOfFound();

        $activity = new Activity();
        $activity->setUser($user)
            ->setActivityType("ITEM_DELETION");
        $em->persist($activity);

        $em->flush();

        return new Response(null, 200, array("Content-Type" => "application/json"));
    }

    /**
     * @Route("/items/reports/{itemId}", name="items_report")
     * @Method({"POST"})
     *
     * REST ROUTE
     */
    public function reportItem($itemId, Request $request) {
        $description = $request->request->get("description");
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $validator = $this->get('validator');

        $item = $em->getRepository('AppBundle:Item')->find($itemId);

        if(empty($item))
            throw new ApiException(400, 'Invalid request.');

        if($item->getUser()->getUserId() == $user->getUserId())
            throw new ApiException(403, "Can't report your own post.");

        $report = new Report();
        $report->setUser($user)
            ->setItem($item)
            ->setDescription($description);

        $rErrors = $validator->validate($report);
        if(count($rErrors) > 0)
            throw new ApiException(400, $rErrors[0]->getMessage());

        $em->persist($report);

        $activity = new Activity();
        $activity->setUser($user)
            ->setActivityType("USER_REPORT");

        $em->persist($activity);

        $em->flush();

        return new Response(null, 200, array("Content-Type" => "application/json"));
    }

    /**
     * @Route("/markers", name="markers")
     * @Method({"GET"})
     *
     * REST ROUTE
     */
    public function getMarkers(Request $request) {
        $reqBounds = json_decode($request->get("bounds"));
        $searchType = $request->get("searchType");
        $withOthers = $request->get("withOthers");

        $validator = $this->get('validator');

        if(empty($reqBounds) || (empty($searchType && empty($withOthers))))
            throw new ApiException(400, 'Invalid request.');

        $bounds = new Bounds();
        try {
            $bounds->setSouth($reqBounds->south)
                ->setWest($reqBounds->west)
                ->setNorth($reqBounds->north)
                ->setEast($reqBounds->east);
        } catch (\Exception $e) {
            throw new ApiException(400, 'Invalid request.');
        }

        $vErrors = $validator->validate($bounds);
        if(count($vErrors) > 0)
            throw new ApiException(400, $vErrors[0]->getMessage());

        $searchTypes = array();

        if(!empty($searchType))
            $searchTypes[] = $searchType;

        if(!empty($withOthers))
            $searchTypes[] = "other";

        $markers = $this->getDoctrine()->getManager()->getRepository('AppBundle:Marker')
                           ->findByBounds($bounds, $searchTypes);


        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($markers, 'json', SerializationContext::create()
            ->setGroups(array('Default')));

        return new Response($jsonContent, 200, array("Content-Type" => "application/json"));
    }

}