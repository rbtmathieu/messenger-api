<?php
namespace MessengerBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use MessengerBundle\Entity\Conversation;
use MessengerBundle\Entity\Message;
use MessengerBundle\Utils\ValueObject\MessageValueObject;
use MessengerBundle\Utils\ValueObject\UserValueObject;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UserBundle\Entity\User;

/**
 * Class MessagesController
 * @package MessengerBundle\Controller
 *
 * @RouteResource("Message")
 */
class MessagesController extends FOSRestController
{

    // Get

    /**
     * Get all messages
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Returns all messages",
     *  statusCodes = {
     *      200 = "Returned when sucessful",
     *      404 = "Returned when no messages are found"
     *  }
     * )
     *
     * @Get("/get/all")
     *
     * @throws NotFoundHttpException
     */
    public function cgetAction()
    {
        $messageRepository = $this->getMessagesRepository();

        /** @var Message[] $data */
        $messages = $messageRepository->findAll();
        if (empty($messages)) {
            throw new NotFoundHttpException('No message found');
        }

        $messages = [];
        foreach($messages as $message) {
            $messages[] = new MessageValueObject(
                $message->getId(),
                $this->populateUserValueObject($message->getUser()),
                $message->getText(),
                $message->getType()
            );
        }

        $view = $this->view($messages, 200);

        return $this->handleView($view);
    }

    // Personnal

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return \MessengerBundle\Repository\MessageRepository
     */
    private function getMessagesRepository()
    {
        return $this->getManager()->getRepository(Message::class);
    }

    private function populateUserValueObject(User $user)
    {
        return new UserValueObject($user->getId(), $user->getUsername(), $user->getEmail());
    }
}
