<?php
namespace AppBundle\Map;
/**
 * Created by PhpStorm.
 * User: m4rko
 * Date: 19.7.17.
 * Time: 16.27
 */
class Bounds
{
    private $south;
    private $west;
    private $north;
    private $east;

    /**
     * @return mixed
     */
    public function getSouth()
    {
        return $this->south;
    }

    /**
     * @param mixed $south
     */
    public function setSouth($south)
    {
        $this->south = $south;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWest()
    {
        return $this->west;
    }

    /**
     * @param mixed $west
     */
    public function setWest($west)
    {
        $this->west = $west;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNorth()
    {
        return $this->north;
    }

    /**
     * @param mixed $north
     */
    public function setNorth($north)
    {
        $this->north = $north;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEast()
    {
        return $this->east;
    }

    /**
     * @param mixed $east
     */
    public function setEast($east)
    {
        $this->east = $east;
        return $this;
    }



}