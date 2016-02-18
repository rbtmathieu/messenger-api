<?php

namespace MessengerBundle\Utils\Traits;

use Doctrine\ORM\EntityManager;
use MessengerBundle\Entity\Conversation;
use MessengerBundle\Entity\Message;
use MessengerBundle\Repository\ConversationRepository;
use MessengerBundle\Repository\MessageRepository;
use UserBundle\Entity\User;
use UserBundle\Repository\UserRepository;

/**
 * Trait GetManagersTrait
 * @package MessengerBundle\Utils\Traits
 */
trait GetManagersTrait
{

    /**
     * @return EntityManager
     */
    private function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return MessageRepository
     */
    private function getMessageRepository()
    {
        $em = $this->getManager();
        return $em->getRepository(Message::class);
    }

    /**
     * @return UserRepository
     */
    private function getUserRepository()
    {
        $em = $this->getManager();
        return $em->getRepository(User::class);
    }

    /**
     * @return ConversationRepository
     */
    private function getConversationRepository()
    {
        $em = $this->getManager();
        return $em->getRepository(Conversation::class);
    }
}
