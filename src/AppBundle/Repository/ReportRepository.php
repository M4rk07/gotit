<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/25/2018
 * Time: 12:38 PM
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ReportRepository extends EntityRepository
{

    public function findAll() {
        $baseQuery = "SELECT r FROM AppBundle:Report r WHERE r.solved = 0";

        return $this->getEntityManager()
            ->createQuery(
                $baseQuery
            )
            ->getResult();
    }

}