<?php
namespace MessengerBundle\Utils\Traits;

use MessengerBundle\Entity\Conversation;
use MessengerBundle\Entity\Message;
use MessengerBundle\Utils\ValueObject\ConversationValueObject;
use MessengerBundle\Utils\ValueObject\MessageValueObject;
use MessengerBundle\Utils\ValueObject\UserValueObject;
use UserBundle\Entity\User;

trait PopulateValueObjectsTrait
{
    /**
     * @param User $user
     * @return UserValueObject
     */
    private function populateUserValueObject(User $user)
    {
        return new UserValueObject($user->getId(), $user->getUsername(), $user->getEmail());
    }

    /**
     * @param Message $message
     * @param User $from
     *
     * @return MessageValueObject
     */
    private function populateMessageValueObject(Message $message, User $from)
    {
        $from = $this->populateUserValueObject($from);
        $conversation = $message->getConversation()->getId();

        return new MessageValueObject($message->getId(), $from, $conversation, $message->getText());
    }

    /**
     * @param Conversation $conversation
     *
     * @return ConversationValueObject
     * @throws NotFoundHttpException
     */
    private function populateConversationValueObject(Conversation $conversation)
    {
        $conversationValueObject = new ConversationValueObject(
            $conversation->getId(),
            $conversation->getMessages()->count()
        );

        $users = [];

        if (2 > $conversation->getUsers()->count()) {
            throw new NotFoundHttpException('This conversation does not have enough participants');
        }

        foreach($conversation->getUsers() as $user) {
            $users[] = $this->populateUserValueObject($user);
        }

        $conversationValueObject->setUsers($users);

        return $conversationValueObject;
    }
}
