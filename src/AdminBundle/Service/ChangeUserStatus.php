<?php

namespace AdminBundle\Service;

use Doctrine\ORM\EntityManager;

class ChangeUserStatus
{
    private $em;

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
     * Change the status of an User identified by ID
     *
     * @param int $id Id of the User
     */
    public function changeStatus($id)
    {
        $user = $this->em->getRepository('UserBundle:User')->find($id);

        if($user->isEnabled() == true) {
            $user->setEnabled(false);
        } else {
            $user->setEnabled(true);
        }

        $this->em->persist($user);
        $this->em->flush();
    }
}