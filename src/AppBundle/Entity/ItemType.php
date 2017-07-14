<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.7.17.
 * Time: 22.21
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as SRL;

/**
 * @ORM\Entity
 * @ORM\Table(name="item_type")
 * @SRL\XmlRoot("item_type")
 */
class ItemType
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SRL\Type("string")
     */
    private $type_id;

    /**
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * @param mixed $type_id
     */
    public function setTypeId($type_id)
    {
        $this->type_id = $type_id;
        return $this;
    }

}