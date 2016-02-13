<?php

namespace MessengerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use MessengerBundle\Entity\Message;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table(name="conversation")
 * @ORM\Entity(repositoryClass="MessengerBundle\Repository\MessageRepository")
 */
class Conversation
{
    use TimestampableEntity;

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
     * @ORM\ManyToMany(targetEntity="MessengerBundle\Entity\Message", cascade={"persist"})
     */
    private $messages;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User")
     *
     * @Assert\NotBlank()
     */
    private $user1;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User")
     *
     * @Assert\NotBlank()
     */
    private $user2;

    /**
     * Constructor
     * @param User $user1
     * @param User $user2
     */
    public function __construct(User $user1, User $user2)
    {
        $this->messages = new ArrayCollection();

        $this->user1 = $user1;
        $this->user2 = $user2;
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
}
