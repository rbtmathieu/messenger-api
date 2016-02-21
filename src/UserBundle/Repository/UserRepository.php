<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MessengerBundle\Entity\Conversation;

/**
 * Class UserRepository
 * @package UserBundle\Repository
 */
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
     * @param string $apiKey
     *
     * @return Conversation
     */
    public function findUserByApiKey($apiKey)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u
                FROM UserBundle:User u
                WHERE u.apiKey = :apiKey
            ')
            ->setParameter('apiKey', $apiKey)
            ->getSingleResult();
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

    /**
     * @param $username
     * @return array
     */
    public function findOneByUsernameWithConversations($username)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->from('UserBundle:User', 'user')
            ->innerJoin('user.conversations', 'conversations')
            ->select([
                'user',
                'conversations',
            ])
            ->where('user.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getSingleResult()
        ;
    }
}
