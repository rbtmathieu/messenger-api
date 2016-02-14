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
     * @ORM\Column(type="string", unique=true)
     */
    private $apiKey;

    /**
     * @ORM\ManyToMany(targetEntity="MessengerBundle\Entity\Conversation", cascade={"persist"})
     */
    protected $conversation;

    public function __construct()
    {
        parent::__construct();
        $this->apiKey = substr(str_shuffle('0123456789AZERTYUIOPQSDFGHJKLMWXCVBNazertyuiopqsdfghjklmwxcvbn'), 32);
        $this->roles = array(
            'ROLE_USER',
        );
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

    /**
     * Set apiToken
     *
     * @param string $apiToken
     *
     * @return User
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
