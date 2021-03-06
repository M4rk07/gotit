<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.7.17.
 * Time: 23.16
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as SRL;

/**
 * @ORM\Entity
 * @ORM\Table(name="end_user")
 * @SRL\XmlRoot("user")
 */
class EndUser implements AdvancedUserInterface, \Serializable
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SRL\Type("integer")
     */
    private $user_id;
    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(message="Provided email is not valid", checkMX = true)
     * @SRL\Type("string")
     */
    private $username;
    /**
     * @ORM\Column(type="string")
     * @SRL\Type("string")
     * @SRL\Exclude
     */
    private $password;
    /**
     * @ORM\Column(type="string")
     * @SRL\Type("string")
     * @Assert\Length(min=3, minMessage="First name should be minimum 3 characters long.")
     */
    private $first_name;
    /**
     * @ORM\Column(type="string")
     * @SRL\Type("string")
     * @Assert\Length(min=3, minMessage="Last name should be minimum 3 characters long.")
     */
    private $last_name;
    /**
     * @Assert\Length(min=5, max=30, minMessage="Password should be minimum 5 characters long.", maxMessage="Password should be maximum 30 characters long.")
     * @SRL\Exclude
     */
    private $plainPassword;
    /**
     * @ORM\Column(type="string")
     * @SRL\Type("string")
     * @Assert\Type(type="numeric", message="Phone number is not valid.")
     * @Assert\Length(min=7, max=13, minMessage="Phone number is not valid.", maxMessage="Phone number is not valid.")
     */
    private $phone_number;
    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active = 1;
    /**
     * @ORM\OneToMany(targetEntity="Marker", mappedBy="user", cascade={"persist", "remove"})
     * @SRL\Type("ArrayCollection<AppBundle\Entity\Marker>")
     * @SRL\Groups({"users_and_markers"})
     */
    private $markers;
    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="user", cascade={"persist", "remove"})
     * @SRL\Type("ArrayCollection<AppBundle\Entity\Item>")
     * @SRL\Groups({"users_and_items"})
     */
    private $items;
    /**
     * @ORM\OneToMany(targetEntity="Report", mappedBy="user", cascade={"persist", "remove"})
     * @SRL\Type("ArrayCollection<AppBundle\Entity\Report>")
     * @SRL\Groups({"users_and_reports"})
     */
    private $reports;
    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="user", cascade={"persist", "remove"})
     * @SRL\Type("ArrayCollection<AppBundle\Entity\Activity>")
     * @SRL\Groups({"users_and_activities"})
     */
    private $activities;
    /**
     * @ORM\Column(type="datetime")
     * @SRL\Type("DateTime")
     */
    private $date_time;
    /**
     * @ORM\Column(type="string")
     * @SRL\Type("string")
     * @Assert\Choice(choices={"ROLE_USER", "ROLE_ADMIN"}, message="Choose a valid role.")
     */
    private $role = 'ROLE_USER';

    public function __construct() {
        $this->date_time = new \DateTime();
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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    public function getRoles()
    {
        return array($this->getRole());
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param mixed $phone_number
     */
    public function setPhoneNumber($phone_number)
    {
        $this->phone_number = $phone_number;
        return $this;
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
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * @param mixed $activities
     */
    public function setActivities($activities)
    {
        $this->activities = $activities;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    /**
     * @param mixed $markers
     */
    public function setMarkers($markers)
    {
        $this->markers = $markers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * @param mixed $is_active
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
        return $this;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->is_active;
    }

    public function serialize()
    {
        return serialize(array(
            $this->user_id,
            $this->username,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->user_id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

}