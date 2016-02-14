<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('UserBundle:Default:index.html.twig');
    }

    /**
     * @param $user1id
     * @param $user2id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function addFriendAction($user1id, $user2id)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);

        $user1 = $userRepository->find($user1id);
        $user2 = $userRepository->find($user2id);

        if (null === $user1 || null === $user2 ) {
            throw new NotFoundHttpException('Friends not found');
        }

        $user1->addFriend($user2);

        dump($user1->getMyFriends());
        return new Response("Friend add");
    }


    public function searchFriendAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);

        $user = $userRepository->searchFriend($username);

        if (null === $user ) {
            throw new NotFoundHttpException('User not found');
        }

        $this->username = $user;

        dump($user);
        return new Response("Search");
    }
}
