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
     *
     * REST ROUTE
     */
    public function itemsAction(Request $request) {

        if($request->getMethod() == "POST")
            return $this->insertItem($request);
        else if($request->getMethod() == "GET")
            return $this->getItems($request);

    }

    public function insertItem(Request $request) {

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

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($item->getUser()->getUserId() != $user->getUserId())
            throw new Exception();

        $item->setDeleted(1);
        $em->flush();

        $marker = $em->getRepository('AppBundle:Marker')->find($item->getMarker()->getMarkerId());

        $itemHistory = new ItemHistory();
        $itemHistory->setItem($item)
            ->setLat($marker->getLat())
            ->setLng($marker->getLng())
            ->setReason("DONE");

        $em->persist($itemHistory);
        $em->flush();

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

        $item = $em->getRepository('AppBundle:Item')->find($itemId);

        $report = new Report();
        $report->setUser($user)
            ->setItem($item)
            ->setDescription($description);

        $em->persist($report);

        $activity = new Activity();
        $activity->setUser($user)
            ->setActivityType("USER_REPORT");
        $em->persist($activity);

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

        $searchTypes = array();
        if(!empty($searchType)) {
            $searchTypes[] = $searchType;
                   }
        if(!empty($withOthers))
            $searchTypes[] = "other";

        $bounds = new Bounds();
        $bounds->setSouth($reqBounds->south)
            ->setWest($reqBounds->west)
            ->setNorth($reqBounds->north)
            ->setEast($reqBounds->east);

        $markers = $this->getDoctrine()->getManager()->getRepository('AppBundle:Marker')
                           ->findByBounds($bounds, $searchTypes);


        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($markers, 'json', SerializationContext::create()
            ->setGroups(array('Default')));

        return new Response($jsonContent, 200, array("Content-Type" => "application/json"));
    }

}