<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\JsonResponse;
use UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class UserApiController extends FOSRestController
{
    /**
     * Create an User with sent data
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Create an User with sent data",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when data has errors"
     *  }
     * )
     *
     * @param ParamFetcher $paramFetcher
     *
     * @RequestParam(name="username", nullable=false, strict=true, description="Username")
     * @RequestParam(name="email", nullable=false, strict=true, description="Email")
     * @RequestParam(name="password", nullable=false, strict=true, description="Password")
     * @RequestParam(name="confirmPassword", nullable=false, strict=true, description="Confirm password")
     *
     * @return View
     */
    public function postUserAction(ParamFetcher $paramFetcher)
    {
        $um = $this->container->get('fos_user.user_manager');

        $user = $um->createUser();
        $user->setUsername($paramFetcher->get('username'));
        $user->setEmail($paramFetcher->get('email'));

        if($paramFetcher->get('confirmPassword') == $paramFetcher->get('password')) {
            $user->setPlainPassword($paramFetcher->get('password'));
        }

        $user->setEnabled(true);

        $view = View::create();

        $errors = $this->get('validator')->validate($user, array('Registration'));

        if(count($errors) == 0) {
            $um->updateUser($user);
            $view->setData($user)->setStatusCode(200);
            return $view;
        } else {
            $view = $this->getErrorsView($errors);
            return $view;
        }
    }

    /**
     * Update an User identified by Username or email with sent data NEED X-AUTH-TOKEN
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Update an User identified by Username or email with sent data NEED X-AUTH-TOKEN",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when data has errors",
     *      401 = "Returned when authentication failed",
     *      404 = "Returned when the User is not found"
     *  }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param Request $request
     *
     * @RequestParam(name="username", nullable=false, strict=true, description="Username")
     * @RequestParam(name="newUsername", nullable=true, strict=true, description="New Username")
     * @RequestParam(name="email", nullable=true, strict=true, description="Email")
     * @RequestParam(name="newEmail", nullable=true, strict=true, description="New email")
     * @RequestParam(name="password", nullable=true, strict=true, description="Password")
     * @RequestParam(name="confirmPassword", nullable=true, strict=true, description="Confirm password")
     *
     * @return View
     */
    public function putUserAction(ParamFetcher $paramFetcher, Request $request)
    {
        $apiKey = $request->headers->get('X-AUTH-TOKEN');

        $query = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')
            ->findOneOrNullUserByEmail(
                $paramFetcher->get('email'),
                $paramFetcher->get('username')
            );

        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserByUsername($query->getUsername());

        if($apiKey !== $user->getApiKey()) {
            throw new AuthenticationException('Not authorized');
        }

        if($paramFetcher->get('newUsername')) { $user->setUsername($paramFetcher->get('newUsername')); }
        if($paramFetcher->get('newEmail')) { $user->setEmail($paramFetcher->get('newEmail')); }
        if($paramFetcher->get('password')) {
            if($paramFetcher->get('confirmPassword') == $paramFetcher->get('password')) {
                $user->setPlainPassword($paramFetcher->get('password'));
            }
        }

        $view = View::create();

        $errors = $this->get('validator')->validate($user, array('Update user'));

        if(count($errors) == 0) {
            $um->updateUser($user);
            $view->setData($user)->setStatusCode(200);
            return $view;
        } else {
            $view = $this->getErrorsView($errors);
            return $view;
        }
    }

    /**
     * Add a friend identified by its ID to an User NEED X-AUTH-TOKEN
     *
     * @ApiDoc(
     *      resource = true,
     *      description = "Add a friend identified by its ID to an User NEED X-AUTH-TOKEN",
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned when data has errors",
     *          401 = "Returned when authentication failed",
     *          404 = "Returned when a user is not found"
     *  }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param Request $request
     *
     * @RequestParam(name="friendId", nullable=false, strict=true, description="Id of the friend")
     *
     * @return View
     */
    public function postFriendsAction(ParamFetcher $paramFetcher, Request $request)
    {
        $apiKey = $request->headers->get('X-AUTH-TOKEN');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->findUserByApiKey($apiKey);
        $friend = $em->getRepository('UserBundle:User')->find($paramFetcher->get('friendId'));

        if(null === $user || null === $friend) {
            throw new NotFoundHttpException('User could not be found');
        }

        $user->addFriend($friend);
        $em->flush();

        $view = View::create();

        return $view->setData($user, $friend)->setStatusCode(200);
    }

    /**
     * Get the validation errors
     *
     * @param ConstraintViolationList $errors
     *
     * @return View
     */
    protected function getErrorsView(ConstraintViolationList $errors)
    {
        $msgs = array();
        $errorIterator = $errors->getIterator();
        foreach ($errorIterator as $validationError) {
            $msg = $validationError->getMessage();
            $params = $validationError->getMessageParameters();
            $msgs[$validationError->getPropertyPath()][] = $this->get('translator')->trans($msg, $params, 'validators');
        }
        $view = View::create($msgs);
        $view->setStatusCode(400);
        return $view;
    }
}