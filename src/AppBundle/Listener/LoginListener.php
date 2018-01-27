<?php

/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 1/24/2018
 * Time: 1:45 AM
 */
namespace AppBundle\Listener;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginListener implements AuthenticationSuccessHandlerInterface
{

    protected $router;
    protected $security;

    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authChecker)
    {
        $this->router = $router;
        $this->security = $authChecker;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {

        if ($this->security->isGranted('ROLE_ADMIN'))
            $response = new RedirectResponse($this->router->generate('admin_dashboard'));
        else
            $response = new RedirectResponse($this->router->generate('homepage'));

        return $response;
    }

}