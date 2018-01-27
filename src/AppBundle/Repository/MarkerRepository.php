<?php
namespace AppBundle\Repository;

use AppBundle\Map\Bounds;
use Doctrine\ORM\EntityRepository;

class MarkerRepository extends EntityRepository
{

    public function findByBounds(Bounds $currBounds, $searchTypes = null)
    {
        $baseQuery = 'SELECT m FROM AppBundle:Marker m WHERE
                ((m.lat BETWEEN :currSouth AND :currNorth)
                AND 
                (m.lng BETWEEN :currWest AND :currEast))
                AND m.num_of_items > 0'
            . $this->getSearchClause($searchTypes);

        return $this->getEntityManager()
            ->createQuery(
                $baseQuery
            )
            ->setParameter("currSouth", $currBounds->getSouth())
            ->setParameter("currNorth", $currBounds->getNorth())
            ->setParameter("currWest", $currBounds->getWest())
            ->setParameter("currEast", $currBounds->getEast())
            ->getResult();
    }

    public function getSearchClause ($searchTypes) {
        $searchClause = "";
        if(!empty($searchTypes)) {
            $searchClause .= " AND (";
            foreach($searchTypes as $type)
                $searchClause .= " m.type LIKE '%".strtoupper($type)."%' OR";
            $searchClause = preg_replace('/ OR$/', ')', $searchClause);
        }

        return $searchClause;
    }

}