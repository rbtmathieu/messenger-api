<?php

namespace MessengerBundle\Utils\Services;

use MessengerBundle\Entity\Conversation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use UserBundle\Entity\User;

class ConversationHandler
{
    public function createConversation(User $from, User $to)
    {
        if ($from === $to) {
            throw new InvalidParameterException('Believe me, you don\'t want to talk to yourself !');
        }

        return new Conversation($from, $to);
    }
}
