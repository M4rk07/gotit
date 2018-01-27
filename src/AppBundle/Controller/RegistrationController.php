<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.7.17.
 * Time: 00.02
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use AppBundle\Entity\EndUser;
use AppBundle\Entity\Item;
use AppBundle\Entity\Marker;
use AppBundle\Entity\Statistics;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RegistrationController extends Controller
{

    /**
     * @Route("/register", name="user_registration")
     * @Method({"POST"})
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $username = $request->request->get("email");
        $plainPassword = $request->request->get("password");
        $phoneNumber = $request->request->get("phoneNumber");
        $firstName = $request->request->get("firstName");
        $lastName = $request->request->get("lastName");

        $user = new EndUser();
        // 3) Encode the password (you could also do this via Doctrine listener)
        $user->setPlainPassword($plainPassword)
            ->setPhoneNumber($phoneNumber)
            ->setUsername($username)
            ->setFirstName($firstName)
            ->setLastName($lastName);

        $validator = $this->get('validator');

        // Validate properties
        $euErrors = $validator->validate($user);
        if(count($euErrors) > 0) {
            throw new \Exception();
        }
        $password = $passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($password);

        // 4) save the User!
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        $statistics = $em->getRepository('AppBundle:Statistics')->find('MAIN');
        $statistics->incNumOfUsers();

        $todayDate = date("d.m.Y");
        $statisticsToday = $em->getRepository('AppBundle:Statistics')->find($todayDate);
        if(empty($statisticsToday)) {
            $statisticsToday = new Statistics();
            $statisticsToday->setStatisticsId($todayDate);
            $statisticsToday->incNumOfUsers();
            $em->persist($statisticsToday);
        } else
            $statisticsToday->incNumOfUsers();


        $activity = new Activity();
        $activity->setUser($user)
            ->setActivityType("REGISTRATION");

        $em->persist($activity);
        $em->flush();

        // ... do any other work - like sending them an email, etc
        // maybe set a "flash" success message for the user

        // replace this example code with whatever you need
        return $this->render('root/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'login' => true,
            'last_username' => $username,
            'success_message' => "Thank you for registration! You can log in now."
        ]);
    }

}