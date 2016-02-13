<?php

namespace UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="MessengerBundle\Entity\Conversation", cascade={"persist"})
     */
    protected $conversation;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add conversation
     *
     * @param \MessengerBundle\Entity\Conversation $conversation
     * @return User
     */
    public function addConversation(\MessengerBundle\Entity\Conversation $conversation)
    {
        $this->conversation[] = $conversation;

        return $this;
    }

    /**
     * Remove conversation
     *
     * @param \MessengerBundle\Entity\Conversation $conversation
     */
    public function removeConversation(\MessengerBundle\Entity\Conversation $conversation)
    {
        $this->conversation->removeElement($conversation);
    }

    /**
     * Get conversation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConversation()
    {
        return $this->conversation;
    }
}
