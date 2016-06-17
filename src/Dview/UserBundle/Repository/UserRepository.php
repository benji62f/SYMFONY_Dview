<?php

namespace Dview\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository {

    public function getCount() {
        return $this
                ->createQueryBuilder('u')
                ->select('count (u)')
                ->getQuery()
                ->getSingleScalarResult();
    }
    
    public function getUsers($page, $nbPerPage) {
        $query = $this
                ->createQueryBuilder('u')
                ->leftJoin('u.projets', 'p')
                ->addSelect('p')
                ->orderBy('u.username', 'asc')
                ->getQuery()
        ;
        $query
                ->setFirstResult(($page - 1) * $nbPerPage)
                ->setMaxResults($nbPerPage)
        ;

        return new Paginator($query, true);
    }

}
