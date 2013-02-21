<?php
namespace Taskul\UserBundle\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Taskul\FriendBundle\Service\FriendRequestService;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{

	protected $frs;
	protected $router;

	public function __construct(FriendRequestService $friendRequestService, RouterInterface $router, SecurityContextInterface $securityContext)
	{
		$this->frs = $friendRequestService;
		$this->router = $router;
		$this->securityContext = $securityContext;
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	{
		$user = $this->securityContext->getToken()->getUser();
		$this->frs->processRequests($user);

		if ($request->isXmlHttpRequest()) {
			$result = array('success' => true,'message' => 'ok_redirect');
			$response = new Response(json_encode($result));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		} else {

			return new RedirectResponse($this->router->generate('homepage'));
		}
	}


	public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {

        if ($request->isXmlHttpRequest()) {
            $result = array('success' => false, 'message' => $exception->getMessage());
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }else{
        	$request->getSession()->setFlash('error', $exception->getMessage());
            $url = $this->router->generate('fos_user_security_login');
            return new RedirectResponse($url);
        }
    }
}