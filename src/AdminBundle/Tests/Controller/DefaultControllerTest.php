<?php

namespace AdminBundle\Tests\Controller;


use AdminBundle\Service\RefreshApiKeys;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testGenerateApiKey()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $refreshApiKeys = $container->get('admin.refresh_apikeys');
        $generateApiKey = $this->getMethod('generateApiKey');

        $key = $generateApiKey->invoke($refreshApiKeys);

        $this->assertEquals(RefreshApiKeys::API_KEY_LENGTH, strlen($key));
    }

    public function getMethod($name)
    {
        $class = new \ReflectionClass(RefreshApiKeys::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}