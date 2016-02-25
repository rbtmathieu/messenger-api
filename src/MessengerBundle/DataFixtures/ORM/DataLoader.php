<?php

namespace MessengerBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader;
use Nelmio\Alice\Fixtures;

class DataLoader extends AbstractLoader
{
    public function getFixtures()
    {
        return [
            __DIR__.'/../resources/fixtures/user.yml',
            __DIR__.'/../resources/fixtures/conversation.yml',
            __DIR__.'/../resources/fixtures/message.yml',
        ];
    }
}
