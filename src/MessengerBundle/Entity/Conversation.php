<?php

namespace MessengerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table(name="conversation")
 * @ORM\Entity(repositoryClass="MessengerBundle\Repository\ConversationRepository")
 */
class Conversation
{
    use TimestampableEntity;

    const LIMIT_USERS = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="MessengerBundle\Entity\Message", cascade={"persist"}, mappedBy="conversation")
     */
    private $messages;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", cascade={"persist"}, inversedBy="conversations")
     */
    private $users;

    /**
     * Constructor
     * @param User $user1
     * @param User $user2
     */
    public function __construct(User $user1, User $user2)
    {
        $this->messages = new ArrayCollection();
        $this->users = new ArrayCollection();

        $this->users->add($user1);
        $this->users->add($user2);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add messages
     *
     * @param Message $messages
     * @return Conversation
     */
    public function addMessage(Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param Message $messages
     */
    public function removeMessage(Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Collection $users
     * @return Conversation
     */
    public function setUsers(Collection $users)
    {
        if ($sers->count() >= self::LIMIT_USERS) {
            throw new \BadMethodCallException('Conversations are only composed of '. self::LIMIT_USERS .' users');
        }

        $this->users = $users;

        return $this;
    }

    /**
     * @param User $user
     * @return Conversation
     */
    public function addUser($user)
    {
        if ($this->users->count() >= self::LIMIT_USERS) {
            throw new \BadMethodCallException('Conversations are only composed of '. self::LIMIT_USERS .' users');
        }

        $this->users->add($user);

        return $this;
    }
}
