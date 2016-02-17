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
     *      400 = "Returned when errors occured"
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