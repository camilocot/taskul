<?php
namespace Taskul\UserBundle\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Taskul\FriendBundle\Service\FriendRequestService;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Translation\TranslatorInterface;
use Taskul\MainBundle\Component\CheckAjaxResponse;
use JMS\TranslationBundle\Annotation\Ignore;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{

	protected $frs;
	protected $router;
	protected $securityContext;
	protected $translator;

	public function __construct(FriendRequestService $friendRequestService, RouterInterface $router, SecurityContextInterface $securityContext, TranslatorInterface $translator)
	{
		$this->frs = $friendRequestService;
		$this->router = $router;
		$this->securityContext = $securityContext;
		$this->translator = $translator;
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	{
		$user = $token->getUser();
		$this->frs->processRequests($user);
		$url = $this->router->generate('dashboard');


		if ($request->isXmlHttpRequest()) {
			$result = array(
					'success' => TRUE,
					'message' => $this->translator->trans('form.login.success',array(),'UserBundle'),
					'forceredirect'=>TRUE,
					'url'=>$url,
				);
			return new JsonResponse($result);
		} else {

			return new RedirectResponse($url);
		}
	}


	public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {

        if ($request->isXmlHttpRequest()) {
            $result = array(
	            	'success' => false,
	            	'message' => $this->translator->trans(/** @Ignore */$exception->getMessage(),array(),'FOSUserBundle')
            	);
            return new JsonResponse($result);
        }else{
        	$request->getSession()->setFlash('error', $this->translator->trans(/** @Ignore */$exception->getMessage(),array(),'FOSUserBundle'));
            $url = $this->router->generate('fos_user_security_login');
            return new RedirectResponse($url);
        }
    }
}