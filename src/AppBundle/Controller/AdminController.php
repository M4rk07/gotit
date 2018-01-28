<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/24/2018
 * Time: 12:31 AM
 */

namespace AppBundle\Controller;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{

    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function dashboardAction (Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $statistics = $em->getRepository('AppBundle:Statistics')->find("MAIN");
        $activities = $em->getRepository('AppBundle:Activity')->findBy(array(),array('activity_id' => "DESC"),10);
        $parameters = [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'statistics' => $statistics,
            'activities' => $activities
        ];

        return $this->render('admin/dashboard.html.twig', $parameters);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function usersAction (Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:EndUser')->findAll();

        $parameters = [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'users' => $users
        ];

        return $this->render('admin/users.html.twig', $parameters);
    }

    /**
     * @Route("/admin/reports", name="admin_reports")
     */
    public function reportsAction (Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $reports = $em->getRepository('AppBundle:Report')->findAll();

        $parameters = [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'reports' => $reports
        ];

        return $this->render('admin/reports.html.twig', $parameters);
    }

    /**
     * @Route("/admin/statistics", name="admin_statistics")
     * @Method({"GET"})
     *
     * REST ROUTE
     */
    public function statisticsAction (Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $statistics = $em->getRepository('AppBundle:Statistics')->findAll();

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($statistics, 'json', SerializationContext::create()
            ->setGroups(array('Default')));

        return new Response($jsonContent, 200, array("Content-Type" => "application/json"));
    }

    /**
     * @Route("/admin/users/{userId}/ban", name="admin_ban_user")
     * @Method({"PUT"})
     *
     * REST ROUTE
     */
    public function banUserAction ($userId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:EndUser')->find($userId);
        $user->setIsActive(0);

        $reportId = $request->get('reportId');

        if(!empty($reportId))
            $this->solveReportAction($reportId, $request);

        $em->flush();

        return new Response(null, 200, array("Content-Type" => "application/json"));
    }

    /**
     * @Route("/admin/users/{userId}/ban", name="admin_unban_user")
     * @Method({"DELETE"})
     *
     * REST ROUTE
     */
    public function unbanUserAction ($userId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:EndUser')->find($userId);
        $user->setIsActive(1);

        $em->flush();

        return new Response(null, 200, array("Content-Type" => "application/json"));
    }

    /**
     * @Route("/admin/reports/{reportId}/solve", name="admin_solve_report")
     * @Method({"PUT"})
     *
     * REST ROUTE
     */
    public function solveReportAction ($reportId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:Report')->find($reportId);
        $user->setSolved(1);

        $em->flush();

        return new Response(null, 200, array("Content-Type" => "application/json"));
    }

}