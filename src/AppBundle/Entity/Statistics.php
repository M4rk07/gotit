<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/24/2018
 * Time: 4:39 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as SRL;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatisticsRepository")
 * @ORM\Table(name="statistics")
 * @SRL\XmlRoot("statistics")
 */
class Statistics
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     * @SRL\Type("string")
     */
    private $statistics_id;
    /**
     * @ORM\Column(type="integer")
     * @SRL\Type("integer")
     */
    private $num_of_users = 0;
    /**
     * @ORM\Column(type="integer")
     * @SRL\Type("integer")
     */
    private $num_of_items = 0;
    /**
     * @ORM\Column(type="integer")
     * @SRL\Type("integer")
     */
    private $num_of_found = 0;
    /**
     * @ORM\Column(type="integer")
     * @SRL\Type("integer")
     */
    private $num_of_banned = 0;

    /**
     * @return mixed
     */
    public function getStatisticsId()
    {
        return $this->statistics_id;
    }

    /**
     * @param mixed $statistics_id
     */
    public function setStatisticsId($statistics_id)
    {
        $this->statistics_id = $statistics_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfUsers()
    {
        return $this->num_of_users;
    }

    /**
     * @param mixed $num_of_users
     */
    public function setNumOfUsers($num_of_users)
    {
        $this->num_of_users = $num_of_users;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfItems()
    {
        return $this->num_of_items;
    }

    /**
     * @param mixed $num_of_items
     */
    public function setNumOfItems($num_of_items)
    {
        $this->num_of_items = $num_of_items;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfFound()
    {
        return $this->num_of_found;
    }

    /**
     * @param mixed $num_of_found
     */
    public function setNumOfFound($num_of_found)
    {
        $this->num_of_found = $num_of_found;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfBanned()
    {
        return $this->num_of_banned;
    }

    /**
     * @param mixed $num_of_banned
     */
    public function setNumOfBanned($num_of_banned)
    {
        $this->num_of_banned = $num_of_banned;
        return $this;
    }

    public function incNumOfUsers()
    {
        $this->num_of_users += 1;

        return $this;
    }

    public function decNumOfUsers()
    {
        $this->num_of_users -= 1;

        return $this;
    }

    public function incNumOfItems()
    {
        $this->num_of_items += 1;

        return $this;
    }

    public function decNumOfItems()
    {
        $this->num_of_items -= 1;

        return $this;
    }

    public function incNumOfFound()
    {
        $this->num_of_found += 1;

        return $this;
    }

    public function decNumOfFound()
    {
        $this->num_of_found -= 1;

        return $this;
    }

    public function incNumOfBanned()
    {
        $this->num_of_banned += 1;

        return $this;
    }

    public function decNumOfBanned()
    {
        $this->num_of_banned -= 1;

        return $this;
    }

}