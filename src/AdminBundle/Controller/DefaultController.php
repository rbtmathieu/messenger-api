<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    /**
     * Get all users
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('UserBundle:User')->findAll();

        return $this->render('AdminBundle:Default:users.html.twig', array(
            'users' => $users
        ));
    }
}
