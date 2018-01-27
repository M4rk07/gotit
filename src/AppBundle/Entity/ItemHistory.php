<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/24/2018
 * Time: 10:21 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as SRL;

/**
 * @ORM\Entity
 * @ORM\Table(name="item_history")
 * @SRL\XmlRoot("item_history")
 */
class ItemHistory
{
    /**
     * @ORM\OneToOne(targetEntity="Item", inversedBy="history")
     * @ORM\Id
     * @ORM\JoinColumn(name="item_id", referencedColumnName="item_id")
     * @SRL\Type("AppBundle\Entity\Item")
     */
    private $item;
    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Latitude should not be blank.")
     * @SRL\Type("float")
     */
    private $lat;
    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Longitude should not be blank.")
     * @SRL\Type("float")
     */
    private $lng;
    /**
     * @ORM\Column(type="datetime")
     * @SRL\Type("DateTime")
     */
    private $time_deleted;
    /**
     * @ORM\Column(type="string")
     * @SRL\Type("string")
     */
    private $reason;

    public function __construct() {
        $this->time_deleted = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param mixed $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param mixed $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeDeleted()
    {
        return $this->time_deleted;
    }

    /**
     * @param mixed $time_deleted
     */
    public function setTimeDeleted($time_deleted)
    {
        $this->time_deleted = $time_deleted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param mixed $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
        return $this;
    }



}