<?php

namespace UserBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\JsonResponse;
use UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class LoginApiController
 * @RouteResource("User")
 */
class LoginApiController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Return User object and apiKey with credentials
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Return User object and apiKey with credentials",
     *  statusCodes = {
     *      200 = "Returned when sucessful",
     *      401 = "Returned when bad credentials were sent"
     *  }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param string $slug Username or email
     *
     * @RequestParam(name="password", nullable=false, strict=true, description="Password")
     *
     * @return View
     */
    public function putApiKeyAction(ParamFetcher $paramFetcher, $slug)
    {
        $email = $slug;
        $password = $paramFetcher->get('password');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->findOneOrNullUserByEmail($email, $slug);

        if($user) {
            $encoderService = $this->get('security.encoder_factory');
            $encoder = $encoderService->getEncoder($user);

            if($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
                $view = $this->view($user, 200);
                return $this->handleView($view);
            } else {
                throw new AuthenticationException('Bad credentials');
            }
        } else {
            throw new AuthenticationException('User does not exist');
        }

    }

    /**
     * @param Request $request
     * @param EntityManager $em
     *
     * @return User
     * @throws AuthenticationException
     */
    public static function checkAuthentication(Request $request, EntityManager $em)
    {
        $apiKey = $request->headers->get('X-AUTH-TOKEN');

        $currentUser = $em->getRepository(User::class)->findUserByApiKey($apiKey);

        if (null === $currentUser) {
            throw new AuthenticationException('Not authorized');
        }

        return $currentUser;
    }
}
