<?php
namespace MessengerBundle\Utils\ValueObject;

use MessengerBundle\Entity\Conversation;

/**
 * Class MessageValueObject
 * @package MessengerBundle\ValueObject
 */
class MessageValueObject
{
    /**
     * @var
     */
    private $id;

    /**
     * @var null
     */
    private $text;

    /**
     * @var string
     */
    private $type;

    /**
     * @var UserValueObject
     */
    private $user;

    /**
     * @var int
     */
    private $conversationId;

    /**
     * MessageValueObject constructor.
     * @param $id
     * @param UserValueObject $user
     * @param $conversationId
     * @param $text
     * @param string $type
     */
    public function __construct($id, UserValueObject $user, $conversationId, $text = null, $type = 'text')
    {
        $this->id = $id;
        $this->user = $user;
        $this->conversationId = $conversationId;
        $this->text = $text;
        $this->type = $type;

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param null $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return UserValueObject
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserValueObject $user
     */
    public function setUser(UserValueObject $user)
    {
        $this->user = $user;
    }

    /**
     * @return Conversation
     */
    public function getConversation()
    {
        return $this->conversationId;
    }

    /**
     * @param $conversationId
     */
    public function setConversation($conversationId)
    {
        $this->conversation = $conversationId;
    }
}
