<?php

namespace MessengerBundle\Controller;

use MessengerBundle\Entity\Conversation;
use MessengerBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UserBundle\Entity\User;

/**
 * Class DefaultController
 * @package MessengerBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        // This is currently totally for testing, this method is still useless
        // Real epicness comming soon
        $em = $this->getDoctrine()->getManager();

        $userRepo = $em->getRepository(User::class);
        $user[0] = $userRepo->findOneByUsername('bapthf');
        $user[1] = $userRepo->findOneByUsername('remi');

        if (null === $user[0] || null === $user[1]) {
            throw new NotFoundHttpException('Users not found');
        }

        $message = new Message('Hello world');
        $message->setUser($user[0]);
        $message->setType(Message::TYPE_TEXT);

        $messageArray = [
            'text' => 'mem',
            'user' => $user[1]->getId(),
            'type' => Message::TYPE_TEXT,
        ];

        $messageJson = json_encode($messageArray);


        $conversation = $this->newConversationAction($user[0], $user[1]);

        $this->newMessageAction($conversation, $messageJson);

        return $this->render('MessengerBundle:Default:index.html.twig');
    }

    /**
     * @ParamConverter("conversation", class="MessengerBundle:Conversation")
     * @param Conversation $conversation
     *
     * @return Conversation
     */
    public function newMessageAction(Conversation $conversation, $jsonMessage)
    {
        $em = $this->getManager();

        $stdMessage = json_decode($jsonMessage);

        $user = $em->getRepository(User::class)->find($stdMessage->user);

        if (null === $user) {
            throw new NotFoundHttpException('No user found');
        }

        $message = new Message($stdMessage->text);
        $message
            ->setType($stdMessage->type)
            ->setUser($user)
        ;

        $conversation->addMessage($message);

        $em->persist($conversation);
        $em->flush();

        return $conversation;
    }

    /**
     * @param int $user1id
     * @param int $user2id
     *
     * @return Conversation
     */
    public function newConversationAction($user1id, $user2id)
    {
        $em = $this->getManager();

        $userRepository = $em->getRepository(User::class);
        $user1 = $userRepository->find($user1id);
        $user2 = $userRepository->find($user2id);

        $conversation = $em->getRepository(Conversation::class)->findOneBy(['user1' => $user1, 'user2' => $user2]);

        if (null !== $conversation) {
            // if conversation already exists, don't create it again
            return $conversation;
        }

        if (null === $user1 or null === $user2) {
            throw new NotFoundHttpException('Users not found');
        }

        $conversation = new Conversation($user1, $user2);

        $em->persist($conversation);

        $em->flush();

        return $conversation;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param $class
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRepository($class)
    {
        $em = $this->getManager();

        return $em->getRepository($class);
    }
}
