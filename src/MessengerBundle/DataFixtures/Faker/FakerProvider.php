<?php
namespace MessengerBundle\DataFixtures\Faker;

use AdminBundle\Service\RefreshApiKeys;
use MessengerBundle\Entity\Message;

class FakerProvider
{
    /** @var RefreshApiKeys $apiKeys */
    protected $apiKeys;

    /**
     * DataLoader constructor.
     * @param RefreshApiKeys $apiKeys
     */
    public function __construct(RefreshApiKeys $apiKeys)
    {
        $this->apiKeys = $apiKeys;
    }

    public function adminRoles()
    {
        return [
            'ROLE_ADMIN',
        ];
    }

    public function apiKey()
    {
        return $this->apiKeys->generateApiKey();
    }

    public function setMessageText($type)
    {
        if ($type === Message::TYPE_TEXT) {
            return simplexml_load_file('http://www.lipsum.com/feed/xml?amount=1&what=paras&start=0')->lipsum;
        } elseif ($type === Message::TYPE_WIZZ) {
            return null;
        }
    }
}
