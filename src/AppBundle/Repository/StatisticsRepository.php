<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/25/2018
 * Time: 12:55 AM
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class StatisticsRepository extends EntityRepository
{

    public function findAll() {
        $baseQuery = "SELECT s FROM AppBundle:Statistics s WHERE s.statistics_id != 'MAIN' ORDER BY STR_TO_DATE(s.statistics_id, '%d.%m.%Y') ASC";

        return $this->getEntityManager()
            ->createQuery(
                $baseQuery
            )
            ->setMaxResults(30)
            ->getResult();
    }

}