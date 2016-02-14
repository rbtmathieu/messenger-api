<?php

namespace UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
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

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myFriends")
     **/
    private $friendsWithMe;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="friends",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
     **/
    private $myFriends;

    public function __construct()
    {
        parent::__construct();
        $this->friendsWithMe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->myFriends = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return mixed
     */
    public function getMyFriends()
    {
        return $this->myFriends;
    }

    /**
     * @param mixed $myFriends
     */
    public function setMyFriends($myFriends)
    {
        $this->myFriends = $myFriends;
    }

    /**
     * Add friends
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
     * Add friendsWithMe
     *
     * @param \UserBundle\Entity\User $friendsWithMe
     * @return User
     */
    public function addFriendsWithMe(\UserBundle\Entity\User $friendsWithMe)
    {
        $this->friendsWithMe[] = $friendsWithMe;

        return $this;
    }

    /**
     * Remove friendsWithMe
     *
     * @param \UserBundle\Entity\User $friendsWithMe
     */
    public function removeFriendsWithMe(\UserBundle\Entity\User $friendsWithMe)
    {
        $this->friendsWithMe->removeElement($friendsWithMe);
    }

    /**
     * Get friendsWithMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriendsWithMe()
    {
        return $this->friendsWithMe;
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
}
