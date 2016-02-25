<?php

namespace MessengerBundle\Tests\Controller;

use MessengerBundle\Entity\Conversation;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use UserBundle\Entity\User;

class ConversationControllerTest extends WebTestCase
{
    public function testNormalConversationHandlerUsage()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $conversationHandler = $container->get('messenger.conversation_handler');

        $user1 = new User();
        $user1
            ->setUsername('user1')
            ->setApiKey(sha1('user1'))
            ->setEmail('user1@mail.com')
        ;

        $user2 = new User();
        $user2
            ->setUsername('user2')
            ->setApiKey(sha1('user2'))
            ->setEmail('user2@mail.com')
        ;

        $conversation = $conversationHandler->createConversation($user1, $user2);

        $this->assertInstanceOf(Conversation::class, $conversation);
    }

    public function testSameUsersConversationHandler()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $conversationHandler = $container->get('messenger.conversation_handler');
        $user1 = new User();
        $user1
            ->setUsername('user1')
            ->setApiKey(sha1('user1'))
            ->setEmail('user1@mail.com')
        ;
        $user2 = $user1;
        $exception = null;
        try {
            $conversation = $conversationHandler->createConversation($user1, $user2);
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertNotNull($exception);
        $this->assertInstanceOf(InvalidParameterException::class, $exception);
        $this->assertEquals('Believe me, you don\'t want to talk to yourself !', $exception->getMessage());
    }
}
