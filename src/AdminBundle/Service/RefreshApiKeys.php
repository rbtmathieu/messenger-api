<?php

namespace AdminBundle\Service;

use Doctrine\ORM\EntityManager;

class RefreshApiKeys
{
    private $em;

    const API_KEY_LENGTH = 30;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Refresh apiKey of every user
     */
    public function refreshAll()
    {
        $users = $this->em->getRepository('UserBundle:User')->findAll();

        foreach($users as $user) {
            $apiKey = $this->generateApiKey();
            $user->setApiKey($apiKey);

            $this->em->persist($user);
        }

        $this->em->flush();
    }

    /**
     * Refresh the apiKey of an User identified by ID
     *
     * @param int $id Id of the User
     */
    public function refresh($id)
    {
        $user = $this->em->getRepository('UserBundle:User')->find($id);

        $apiKey = $this->generateApiKey();

        $user->setApiKey($apiKey);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Generate an apiKey
     *
     * @return string
     */
    private function generateApiKey()
    {
        $apiKey = substr(str_shuffle('0123456789AZERTYUIOPQSDFGHJKLMWXCVBNazertyuiopqsdfghjklmwxcvbn'), 62 - self::API_KEY_LENGTH);

        return $apiKey;
    }
}