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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use UserBundle\Controller\LoginApiController;
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
     * @Get("/get/{username}")
     *
     * @throws NotFoundHttpException
     */
    public function cgetAction($username)
    {
        $messageRepository = $this->getMessagesRepository();
        $userRepository = $this->getManager()->getRepository(User::class);

        $user = $userRepository->findOneByUsername($username);

        if (null === $user) {
            throw new NotFoundHttpException('The user provided does not exist');
        }

        /** @var Message[] $messagesFromBase */
        $messagesFromBase = $messageRepository->findByUser($user->getId());
        if (empty($messagesFromBase)) {
            throw new NotFoundHttpException('No message found');
        }

        $messages = [];
        foreach($messagesFromBase as $message) {
            $messages[] = new MessageValueObject(
                $message->getId(),
                $this->populateUserValueObject($message->getUser()),
                $message->getConversation()->getId(),
                $message->getText(),
                $message->getType()
            );
        }

        $view = $this->view($messages);

        return $this->handleView($view);
    }

    // Post

    /**
     * @param ParamFetcher $paramFetcher
     * @param Request $request
     *
     * @return View
     * @throws \HttpInvalidParamException
     *
     * @Post("/new")
     *
     * @RequestParam(name="to", nullable=true, description="Message receiver")
     * @RequestParam(name="text", nullable=true, description="Message content")
     * @RequestParam(name="type", nullable=true, description="Message type")
     * @RequestParam(name="conversationId", nullable=true, description="Id the of conversation")
     */
    public function postAction(ParamFetcher $paramFetcher, Request $request)
    {
        $em = $this->getManager();

        $from = LoginApiController::checkAuthentication($request, $em);

        $message = new Message();

        // Conversation
        $conversation = $this->handleConversation($from, $paramFetcher);
        $message->setConversation($conversation);

        // Type
        $message = $this->handleMessageType($message, $paramFetcher->get('type'));

        // Text
        $message->setText($paramFetcher->get('text'));

        // User
        $message->setUser($from);


        $em->persist($message);
        $em->persist($conversation);
        $em->flush();

        // Don't return the whole message with useless and/or confidential informations
        // Just fill a ValueObject
        $messageValueObject = $this->populateMessageValueObject($message, $from);

        return $this->view($messageValueObject);
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
     * @param $message
     * @param $type
     *
     * @return Message
     * @throws \HttpInvalidParamException
     */
    private function handleMessageType(Message $message, $type)
    {
        if (Message::TYPE_TEXT_STRING === $type) {
            $message->setType(Message::TYPE_TEXT);
        } elseif(Message::TYPE_WIZZ_STRING === $type) {
            $message->setType(Message::TYPE_WIZZ);
        } elseif(null === $type) {
            $message->setType(Message::TYPE_TEXT);
        } else {
            throw new \HttpInvalidParamException('The type of message provided does not exist', 400);
        }

        return $message;
    }

    /**
     * @param User $from
     * @param ParamFetcher $paramFetcher
     *
     * @return Message
     */
    private function handleConversation(User $from, ParamFetcher $paramFetcher)
    {
        /** @var Conversation $conversation */
        $conversation = $this->getManager()->getRepository(Conversation::class)->find($paramFetcher->get('conversationId'));

        if (null === $conversation) {
            if (null === $paramFetcher->get('to')) {
                throw new InvalidParameterException('You must provide a message receiver if the conversation is not already created');
            }

            $to = $this->getManager()->getRepository(User::class)->find($paramFetcher->get('to'));

            if (null !== ($from && $to)) {
                return new Conversation($from, $to);
            } else {
                throw new NotFoundHttpException('Users provided does not exist');
            }
        }

        $conversationParticipants = $conversation->getUsers();

        if (!$conversationParticipants->contains($from)) {
            throw new AccessDeniedHttpException('Users provided don\'t take part of the conversation');
        }

        return $conversation;
    }
}
