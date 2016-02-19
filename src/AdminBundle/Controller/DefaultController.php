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

    /**
     * Modify the status of an User identified by ID
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function modifyUserStatusAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id);

        if($user->isEnabled() == true) {
            $user->setEnabled(false);
        } else {
            $user->setEnabled(true);
        }

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin_homepage');
    }

    /**
     * Set a new apiKey to an User identified by ID
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function refreshApiKeyAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id);

        $newApiKey = $this->generateApiKey();

        $user->setApiKey($newApiKey);

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin_homepage');
    }

    /**
     * Generate an apiKey
     *
     * @return string
     */
    private function generateApiKey()
    {
        $apiKey = substr(str_shuffle('0123456789AZERTYUIOPQSDFGHJKLMWXCVBNazertyuiopqsdfghjklmwxcvbn'), 32);

        return $apiKey;
    }
}
