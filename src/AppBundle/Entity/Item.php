<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.7.17.
 * Time: 20.58
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as SRL;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ItemRepository")
 * @ORM\Table(name="item")
 * @SRL\XmlRoot("item")
 */
class Item
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SRL\Type("integer")
     */
    private $item_id;
    /**
     * @ORM\ManyToOne(targetEntity="Marker", inversedBy="items")
     * @ORM\JoinColumn(name="marker_id", referencedColumnName="marker_id", onDelete="SET NULL")
     * @SRL\Type("AppBundle\Entity\Marker")
     * @SRL\Groups({"items_and_markers"})
     */
    private $marker;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Description should not be blank.")
     * @Assert\Length(min=3, minMessage="Description should be minimum 3 characters long.")
     * @SRL\Type("string")
     */
    private $description;
    /**
     * @ORM\Column(type="string")
     * @SRL\Type("string")
     */
    private $image_url;
    /**
     * @Assert\File(
     *     maxSize = "1M",
     *     mimeTypes = {"image/jpeg", "image/png"},
     *     mimeTypesMessage = "Please upload jpg or png image up to 1 MB."
     * )
     */
    private $image;
    /**
     * @ORM\ManyToOne(targetEntity="EndUser", inversedBy="items")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @SRL\Type("AppBundle\Entity\EndUser")
     */
    private $user;
    /**
     * @ORM\ManyToOne(targetEntity="ItemType")
     * @ORM\JoinColumn(name="type", referencedColumnName="type_id")
     * @SRL\Type("AppBundle\Entity\ItemType")
     */
    private $type;
    /**
     * @ORM\Column(type="datetime")
     * @SRL\Type("DateTime")
     */
    private $date_time;
    /**
     * @ORM\Column(type="integer")
     * @SRL\Type("integer")
     */
    private $deleted;
    /**
     * @ORM\OneToMany(targetEntity="Report", mappedBy="item", cascade={"persist", "remove"})
     * @SRL\Type("ArrayCollection<AppBundle\Entity\Report>")
     */
    private $reports;
    /**
     * @ORM\OneToOne(targetEntity="ItemHistory", mappedBy="item")
     * @SRL\Type("AppBundle\Entity\ItemHistory")
     */
    private $history;

    public function __construct()
    {
        $this->type = new ItemType("other");
        $this->date_time = new \DateTime();
        $this->deleted = 0;
    }

    /**
 * Get itemId
 *
 * @return integer
 */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Item
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->date_time;
    }

    /**
     * @param mixed $date_time
     */
    public function setDateTime($date_time)
    {
        $this->date_time = $date_time;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return Item
     */
    public function setImageUrl($imageUrl)
    {
        $this->image_url = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @return mixed
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @param mixed $reports
     */
    public function setReports($reports)
    {
        $this->reports = $reports;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType(\AppBundle\Entity\ItemType $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set marker
     *
     * @param \AppBundle\Entity\Marker $marker
     *
     * @return Item
     */
    public function setMarker(\AppBundle\Entity\Marker $marker = null)
    {
        $this->marker = $marker;

        return $this;
    }

    /**
     * Get marker
     *
     * @return \AppBundle\Entity\Marker
     */
    public function getMarker()
    {
        return $this->marker;
    }

    public function setImage(File $image = null) {
        $this->image = $image;

        return $this;
    }

    public function getImage() {
        return $this->image;
    }

    /**
     * @return mixed
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param mixed $history
     */
    public function setHistory($history)
    {
        $this->history = $history;
        return $this;
    }

    public function saveImage() {
        if($this->getImage() !== null) {
            $imageName = md5(uniqid()).'.'.$this->getImage()->guessExtension();
            $this->getImage()->move(__DIR__ . "/../../../web/images", $imageName);
            $this->setImageUrl($imageName);
        }
    }
}
