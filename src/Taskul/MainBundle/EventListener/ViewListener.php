<?php

namespace Taskul\MainBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Taskul\MainBundle\Component\CheckAjaxResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/* Listener utilizado para comprobar si una peticion es ajax o normal */
class ViewListener
{

/**
* @param GetResponseForControllerResultEvent $event A GetResponseForControllerResultEvent instance
*/
	public function onKernelView(GetResponseForControllerResultEvent $event)
	{
		$request = $event->getRequest();
		$result = $event->getControllerResult();

		if ($result instanceof CheckAjaxResponse) {
	        if ($request->isXmlHttpRequest()) {
	            $event->setResponse(new JsonResponse($result->getAjaxData()));
	        } else {
	            $event->setResponse(new RedirectResponse($result->getRedirectUrl()));
	        }
    	}
	}
}