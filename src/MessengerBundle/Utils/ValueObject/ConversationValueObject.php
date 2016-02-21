<?php
namespace MessengerBundle\Utils\ValueObject;

use Doctrine\Common\Collections\Collection;

/**
 * Class ConversationValueObject
 * @package MessengerBundle\Utils\ValueObject
 */
class ConversationValueObject
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $messagesCount;

    /**
     * @var array
     */
    private $users;

    /**
     * ConversationValueObject constructor.
     * @param int $id
     * @param int $messagesCount
     */
    public function __construct($id, $messagesCount)
    {
        $this->id = $id;
        $this->messagesCount = $messagesCount;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getMessagesCount()
    {
        return $this->messagesCount;
    }

    /**
     * @param int $messagesCount
     */
    public function setMessagesCount($messagesCount)
    {
        $this->messagesCount = $messagesCount;
    }

    /**
     * @return Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param array $users
     */
    public function setUsers(array $users)
    {
        $this->users = $users;
    }
}
