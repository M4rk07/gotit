<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as SRL;


/**
 * @ORM\Entity
 * @ORM\Table(name="marker")
 * @SRL\XmlRoot("marker")
 */
class Marker
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SRL\Type("integer")
     */
    private $marker_id;
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
     * @ORM\Column(type="integer")
     * @SRL\Type("integer")
     */
    private $num_of_items = 1;
    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="marker", cascade={"persist", "remove"})
     * @SRL\Type("ArrayCollection<AppBundle\Entity\Item>")
     * @SRL\Groups({"markers_and_items"})
     */
    private $items;
    /**
     * @ORM\ManyToOne(targetEntity="EndUser", inversedBy="markers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @SRL\Type("AppBundle\Entity\EndUser")
     */
    private $user;
    /**
     * @ORM\Column(type="string")
     * @SRL\Type("string")
     */
    private $type = "OTHER";

    public function __construct() {
        $this->items = new ArrayCollection();
    }

    /**
     * Get markerId
     *
     * @return integer
     */
    public function getMarkerId()
    {
        return $this->marker_id;
    }

    /**
     * Set lat
     *
     * @param float $lat
     *
     * @return Marker
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     *
     * @return Marker
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set numOfItems
     *
     * @param integer $numOfItems
     *
     * @return Marker
     */
    public function setNumOfItems($numOfItems)
    {
        $this->num_of_items = $numOfItems;

        return $this;
    }

    /**
     * Set numOfItems
     *
     * @param integer $numOfItems
     *
     * @return Marker
     */
    public function incNumOfItems()
    {
        $this->num_of_items += 1;

        return $this;
    }

    /**
     * Get numOfItems
     *
     * @return integer
     */
    public function getNumOfItems()
    {
        return $this->num_of_items;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * Add item
     *
     * @param \AppBundle\Entity\Item $item
     *
     * @return Marker
     */
    public function addItem(\AppBundle\Entity\Item $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \AppBundle\Entity\Item $item
     */
    public function removeItem(\AppBundle\Entity\Item $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
}
