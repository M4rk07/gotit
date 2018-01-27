<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/24/2018
 * Time: 4:51 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as SRL;

/**
 * @ORM\Entity
 * @ORM\Table(name="activity")
 * @SRL\XmlRoot("activity")
 */
class Activity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SRL\Type("integer")
     */
    private $activity_id;
    /**
     * @ORM\ManyToOne(targetEntity="EndUser", inversedBy="activities")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @SRL\Type("AppBundle\Entity\EndUser")
     */
    private $user;
    /**
     * @ORM\Column(type="string")
     * @SRL\Type("string")
     * @Assert\Choice({"REGISTRATION", "ITEM_CREATION", "ITEM_DELETION", "USER_REPORT"})
     */
    private $activity_type;
    /**
     * @ORM\Column(type="datetime")
     * @SRL\Type("DateTime")
     */
    private $time_created;

    public function __construct() {
        $this->time_created = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getActivityId()
    {
        return $this->activity_id;
    }

    /**
     * @param mixed $activity_id
     */
    public function setActivityId($activity_id)
    {
        $this->activity_id = $activity_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActivityType()
    {
        return $this->activity_type;
    }

    /**
     * @param mixed $activity_type
     */
    public function setActivityType($activity_type)
    {
        $this->activity_type = $activity_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeCreated()
    {
        return $this->time_created;
    }

    /**
     * @param mixed $time_created
     */
    public function setTimeCreated($time_created)
    {
        $this->time_created = $time_created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActivityMessage()
    {
        $baseMessage = $this->getUser()->getFirstName() . " " . $this->getUser()->getLastName();

        if($this->getActivityType() == "REGISTRATION")
            return $baseMessage . " registered.";
        else if($this->getActivityType() == "ITEM_CREATION")
            return $baseMessage . " created new item.";
        else if($this->getActivityType() == "ITEM_DELETION")
            return $baseMessage . " created new item.";
        else if($this->getActivityType() == "USER_REPORT")
            return $baseMessage . " reported an user.";

    }
}