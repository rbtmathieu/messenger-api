<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findOneOrNullUserByEmail($email, $slug)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u
                FROM UserBundle:User u
                WHERE u.email = :email OR u.username = :slug
            ')
            ->setParameter('email', $email)
            ->setParameter('slug', $slug)
            ->getOneOrNullResult();
    }
}