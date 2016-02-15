<?php

namespace UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
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

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", mappedBy="myFriends")
     **/
    private $friendWithMe;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", inversedBy="friendWithMe")
     * @ORM\JoinTable(name="friends",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
     **/
    private $myFriends;

    public function __construct()
    {
        parent::__construct();
        $this->friendWithMe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->myFriends = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Add friend
     *
     * @param friend
     * @return user
     */
    public function addFriend(User $friend)
    {
        $this->myFriends[] = $friend;

        return $this;
    }

    /**
     * Add friendWithMe
     *
     * @param \UserBundle\Entity\User $friendWithMe
     * @return User
     */
    public function addFriendWithMe(\UserBundle\Entity\User $friendWithMe)
    {
        $this->friendWithMe[] = $friendWithMe;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFriendWithMe()
    {
        return $this->friendWithMe;
    }

    /**
     * @param mixed $friendWithMe
     */
    public function setFriendWithMe($friendWithMe)
    {
        $this->friendWithMe = $friendWithMe;
    }

    /**
     * @param mixed $myFriends
     */
    public function setMyFriends($myFriends)
    {
        $this->myFriends = $myFriends;
    }

    /**
     * Remove friendWithMe
     *
     * @param \UserBundle\Entity\User $friendWithMe
     */
    public function removeFriendWithMe(\UserBundle\Entity\User $friendWithMe)
    {
        $this->friendWithMe->removeElement($friendWithMe);
    }

    /**
     * Add myFriends
     *
     * @param \UserBundle\Entity\User $myFriends
     * @return User
     */
    public function addMyFriend(\UserBundle\Entity\User $myFriends)
    {
        $this->myFriends[] = $myFriends;

        return $this;
    }

    /**
     * Remove myFriends
     *
     * @param \UserBundle\Entity\User $myFriends
     */
    public function removeMyFriend(\UserBundle\Entity\User $myFriends)
    {
        $this->myFriends->removeElement($myFriends);
    }

    /**
     * Get myFriends
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMyFriends()
    {
        return $this->myFriends;
    }
}
