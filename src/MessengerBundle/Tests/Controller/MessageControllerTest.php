<?php

namespace MessengerBundle\Tests\Controller;

use MessengerBundle\Controller\MessagesController;
use MessengerBundle\Entity\Message;

class MessageControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testTextType()
    {
        $message = new Message('Text');

        $handleMessageType = $this->getMethod('handleMessageType');
        $defaultController = new MessagesController();

        /** @var Message $message */
        $message = $handleMessageType->invokeArgs($defaultController, [
            $message,
            Message::TYPE_TEXT_STRING,
        ]);

        $this->assertEquals(Message::TYPE_TEXT, $message->getType());
    }
    public function testWizzType()
    {
        $message = new Message();

        $handleMessageType = $this->getMethod('handleMessageType');
        $defaultController = new MessagesController();

        /** @var Message $message */
        $message = $handleMessageType->invokeArgs($defaultController, [
            $message,
            Message::TYPE_WIZZ_STRING,
        ]);

        $this->assertEquals(Message::TYPE_WIZZ, $message->getType());
    }

    public function getMethod($name)
    {
        $class = new \ReflectionClass(MessagesController::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
