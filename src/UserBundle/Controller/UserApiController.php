<?php

namespace UserBundle\Controller;

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use MessengerBundle\Utils\Traits\GetManagersTrait;
use MessengerBundle\Utils\Traits\PopulateValueObjectsTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\ConstraintViolationList;
use UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class UserApiController extends FOSRestController
{
    use GetManagersTrait;
    use PopulateValueObjectsTrait;

    /**
     * @ApiDoc(
     *  resource = true,
     *  description = "Returns all conversations of an user",
     *  statusCodes = {
     *      200 = "Returned when sucessful",
     *      404 = "Returned when no messages are found"
     *  }
     * )
     *
     * @Get("/user/get/conversation/{username}")
     *
     * @throws NotFoundHttpException
     */
    public function getUserConversationsAction($username)
    {
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByUsernameWithConversations($username);

        if (null === $user) {
            throw new NotFoundHttpException('The user provided does not exist');
        }

        /** @var Conversation[] $conversationsFromBase */
        $conversationsFromBase = $user->getConversations();

        if (empty($conversationsFromBase)) {
            throw new NotFoundHttpException('No message found');
        }

        $conversations = [];
        foreach ($conversationsFromBase as $conversation) {
            $conversationValueObject = $this->populateConversationValueObject($conversation);

            $conversations[] = $conversationValueObject;
        }

        $view = $this->view($conversations);

        return $this->handleView($view);
    }

    /**
     * Create an User with sent data.
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

        if ($paramFetcher->get('confirmPassword') == $paramFetcher->get('password')) {
            $user->setPlainPassword($paramFetcher->get('password'));
        }

        $user->setEnabled(true);

        $view = View::create();

        $errors = $this->get('validator')->validate($user, array('Registration'));

        if (count($errors) == 0) {
            $um->updateUser($user);
            $view->setData($user)->setStatusCode(200);

            return $view;
        } else {
            $view = $this->getErrorsView($errors);

            return $view;
        }
    }

    /**
     * Update an User identified by Username or email with sent data NEED X-AUTH-TOKEN.
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
     * @param Request      $request
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

        if ($apiKey !== $user->getApiKey()) {
            throw new AuthenticationException('Not authorized');
        }

        if ($paramFetcher->get('newUsername')) {
            $user->setUsername($paramFetcher->get('newUsername'));
        }
        if ($paramFetcher->get('newEmail')) {
            $user->setEmail($paramFetcher->get('newEmail'));
        }
        if ($paramFetcher->get('password')) {
            if ($paramFetcher->get('confirmPassword') == $paramFetcher->get('password')) {
                $user->setPlainPassword($paramFetcher->get('password'));
            }
        }

        $view = View::create();

        $errors = $this->get('validator')->validate($user, array('Update user'));

        if (count($errors) == 0) {
            $um->updateUser($user);
            $view->setData($user)->setStatusCode(200);

            return $view;
        } else {
            $view = $this->getErrorsView($errors);

            return $view;
        }
    }

    /**
     * Returns all friends of an User NEED X-AUTH-TOKEN.
     *
     * @ApiDoc(
     *  resource = true,
     *  description= "Returns all friends of an User NEED X-AUTH-TOKEN",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      403 = "Returned when forbidden"
     *  }
     * )
     *
     * @Get("/user/friends")
     *
     * @param Request $request
     *
     * @return View
     */
    public function getUserFriendsAction(Request $request)
    {
        $apiKey = $request->headers->get('X-AUTH-TOKEN');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->findUserByApiKey($apiKey);

        $getFriends = $user->getMyFriends();

        $view = View::create();

        foreach ($getFriends as $friend) {
            $friends[] = $this->populateUserValueObject($friend);
        }

        return $view->setData($friends)->setStatusCode(200);
    }

    /**
     * Return a friend identified by ID of an User NEED X-AUTH-TOKEN.
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Return a friend identified by ID of an User NEED X-AUTH-TOKEN",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      403 = "Returned when forbidden",
     *      404 = "Returned when data is not found",
     *      500 = "Returned when user given is not in User's friends list"
     *  }
     * )
     *
     * @param Request $request
     *
     * @RequestParam(name="friendId", nullable=false, strict=true, description="Id of the friend")
     *
     * @throws NotFoundHttpException    If friend not found
     * @throws UnexpectedValueException If supposed friend is not a friend with the user
     *
     * @return View
     */
    public function getUserFriendAction(Request $request)
    {
        $apiKey = $request->headers->get('X-AUTH-TOKEN');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->findUserByApiKey($apiKey);
        $friend = $em->getRepository('UserBundle:User')->find($request->get('friendId'));

        if (null === $friend) {
            throw new NotFoundHttpException('User could not be found');
        }

        if ($user->getMyFriends()->contains($friend)) {
            $view = View::create();

            $friend = $this->populateUserValueObject($friend);

            return $view->setData($friend)->setStatusCode(200);
        } else {
            throw new UnexpectedValueException($friend->getUsername().' is not in the friends list of '.$user->getUsername());
        }
    }

    /**
     * Add a friend identified by its ID to an User NEED X-AUTH-TOKEN.
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
     * @param Request      $request
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

        if (null === $user || null === $friend) {
            throw new NotFoundHttpException('User could not be found');
        }

        $user->addFriend($friend);
        $em->flush();

        $users[] = $this->populateUserValueObject($user);
        $users[] = $this->populateUserValueObject($friend);

        $view = View::create();

        return $view->setData($users)->setStatusCode(200);
    }

    /**
     * Remove an User's friend identified by its ID NEED X-AUTH-TOKEN.
     *
     * @ApiDoc(
     *      resource = true,
     *      description = "Remove an User's friend identified by its ID NEED X-AUTH-TOKEN",
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned when data has errors",
     *          401 = "Returned when authentication failed",
     *          404 = "Returned when an user is not found"
     *  }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param Request      $request
     *
     * @RequestParam(name="friendId", nullable=false, strict=true, description="Id of the friend")
     *
     * @return View
     */
    public function deleteFriendsAction(ParamFetcher $paramFetcher, Request $request)
    {
        $apiKey = $request->headers->get('X-AUTH-TOKEN');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->findUserByApiKey($apiKey);
        $friend = $em->getRepository('UserBundle:User')->find($paramFetcher->get('friendId'));

        if (null === $user || null == $friend) {
            throw new NotFoundHttpException('User could not be found');
        }

        $user->removeMyFriend($friend);
        $em->flush();

        $users[] = $this->populateUserValueObject($user);
        $users[] = $this->populateUserValueObject($friend);

        $view = View::create();

        return $view->setData($users)->setStatusCode(200);
    }

    /**
     * Search for users by username.
     *
     * @ApiDoc(
     *  resource = true,
     *  description= "Search for users by username",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when data has errors"
     *  }
     * )
     *
     * @param Request $request
     *
     * @RequestParam(name="q", nullable=false, strict=true, description="Query searched")
     *
     * @return View
     */
    public function searchFriendAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usersSearch = $em->getRepository('UserBundle:User')
            ->searchFriend($request->get('q'));

        $view = View::create();

        foreach ($usersSearch as $user) {
            $users[] = $this->populateUserValueObject($user);
        }

        return $view->setData($users)->setStatusCode(200);
    }

    /**
     * Get the validation errors.
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
