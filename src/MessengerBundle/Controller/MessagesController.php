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
use MessengerBundle\Utils\Traits\GetManagersTrait;
use MessengerBundle\Utils\Traits\PopulateValueObjectsTrait;
use MessengerBundle\Utils\ValueObject\MessageValueObject;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use UserBundle\Controller\LoginApiController;
use UserBundle\Entity\User;

/**
 * Class MessagesController.
 *
 * @RouteResource("Message")
 */
class MessagesController extends FOSRestController
{
    use GetManagersTrait;
    use PopulateValueObjectsTrait;

    // Get

    /**
     * @ApiDoc(
     *  resource = true,
     *  description = "Returns all messages of a given user",
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
        $messageRepository = $this->getMessageRepository();
        $userRepository = $this->getUserRepository();

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
        foreach ($messagesFromBase as $message) {
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
     * @ApiDoc(
     *  resource = true,
     *  description = "Post a new message NEED X-AUTH-TOKEN",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      403 = "Returned when forbidden"
     *  }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param Request      $request
     *
     * @return View
     *
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
     * @param $message
     * @param $type
     *
     * @return Message
     *
     * @throws \HttpInvalidParamException
     */
    private function handleMessageType(Message $message, $type)
    {
        if (Message::TYPE_TEXT_STRING === $type) {
            $message->setType(Message::TYPE_TEXT);
        } elseif (Message::TYPE_WIZZ_STRING === $type) {
            $message->setType(Message::TYPE_WIZZ);
        } elseif (null === $type) {
            $message->setType(Message::TYPE_TEXT);
        } else {
            throw new \HttpInvalidParamException('The type of message provided does not exist', 400);
        }

        return $message;
    }

    /**
     * @param User         $from
     * @param ParamFetcher $paramFetcher
     *
     * @return Message
     */
    private function handleConversation(User $from, ParamFetcher $paramFetcher)
    {
        /** @var Conversation $conversation */
        $conversation = $this->getConversationRepository()->find($paramFetcher->get('conversationId'));

        if (null === $conversation) {
            $to = $paramFetcher->get('to');
            if (null === $to) {
                throw new InvalidParameterException('You must provide a message receiver if the conversation is not already created');
            }

            $to = $this->getUserRepository()->find($paramFetcher->get('to'));

            $conversationHandler = $this->get('messenger.conversation_handler');
            $conversation = $conversationHandler->createConversation($from, $to);
        }

        $conversationUser = $conversation->getUsers();

        if (!in_array($from, $conversationUser->toArray())) {
            throw new AccessDeniedHttpException('Users provided don\'t take part of the conversation');
        }

        return $conversation;
    }
}
