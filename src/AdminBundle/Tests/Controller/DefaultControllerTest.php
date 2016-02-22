<?php

namespace AdminBundle\Tests\Controller;


use AdminBundle\Controller\DefaultController;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
        $generateApiKey = $this->getMethod('generateApiKey');
        $defaultController = new DefaultController();

        $key = $generateApiKey->invoke($defaultController);

        $this->assertEquals(DefaultController::API_KEY_LENGTH, strlen($key));
    }

    public function getMethod($name)
    {
        $class = new \ReflectionClass(DefaultController::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}