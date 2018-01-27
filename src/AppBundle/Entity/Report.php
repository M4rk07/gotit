<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/24/2018
 * Time: 4:45 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as SRL;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReportRepository")
 * @ORM\Table(name="report")
 * @SRL\XmlRoot("report")
 */
class Report
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SRL\Type("integer")
     */
    private $report_id;
    /**
     * @ORM\ManyToOne(targetEntity="EndUser", inversedBy="reports")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @SRL\Type("AppBundle\Entity\EndUser")
     */
    private $user;
    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="reports")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="item_id")
     * @SRL\Type("AppBundle\Entity\Item")
     */
    private $item;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Description should not be blank.")
     * @Assert\Length(min=3, minMessage="Description should be minimum 3 characters long.")
     * @SRL\Type("string")
     */
    private $description;
    /**
     * @ORM\Column(type="integer")
     * @SRL\Type("integer")
     */
    private $solved = 0;

    /**
     * @return mixed
     */
    public function getReportId()
    {
        return $this->report_id;
    }

    /**
     * @param mixed $report_id
     */
    public function setReportId($report_id)
    {
        $this->report_id = $report_id;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSolved()
    {
        return $this->solved;
    }

    /**
     * @param mixed $solved
     */
    public function setSolved($solved)
    {
        $this->solved = $solved;
        return $this;
    }

}