<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param $email
     * @param $slug
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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

    /**
     * @param $username
     * @return array
     */
    public function searchFriend($username)
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder();

        $query->select('u')
            ->from('UserBundle:User', 'u')
            ->where($query->expr()->like('u.username', ':username'))
            ->setParameter('username', '%'.$username.'%');

        $results = $query->getQuery();
        $username = $results->getResult();

        return $username;
    }
}