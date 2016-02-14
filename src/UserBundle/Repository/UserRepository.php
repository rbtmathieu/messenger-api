<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findOneOrNullUserByEmail($email)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u
                FROM UserBundle:User u
                WHERE u.email = :email
            ')
            ->setParameter('email', $email)
            ->getOneOrNullResult();
    }
}