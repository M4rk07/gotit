<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/23/2018
 * Time: 9:42 PM
 */

namespace AppBundle\Repository;


use AppBundle\Entity\EndUser;
use AppBundle\Entity\Marker;
use Doctrine\ORM\EntityRepository;

class ItemRepository extends EntityRepository
{

    public function findByUser (EndUser $user, $deleted = 0)
    {
        $baseQuery = 'SELECT i FROM AppBundle:Item i WHERE i.user = :userId AND i.deleted = :deleted';

        return $this->getEntityManager()
            ->createQuery(
                $baseQuery
            )
            ->setParameter("userId", $user->getUserId())
            ->setParameter("deleted", $deleted)
            ->getResult();
    }

    public function findByMarker (Marker $marker, $deleted = 0)
    {
        $baseQuery = 'SELECT i FROM AppBundle:Item i WHERE i.marker = :markerId AND i.deleted = :deleted';

        return $this->getEntityManager()
            ->createQuery(
                $baseQuery
            )
            ->setParameter("markerId", $marker->getMarkerId())
            ->setParameter("deleted", $deleted)
            ->getResult();
    }

}