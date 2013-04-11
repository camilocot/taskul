<?php

namespace Taskul\CommentBundle\EventListener;

use Taskul\CommentBundle\Controller\CommentAuthenticatedController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
/**
 * This code gets executed everytime Kernel sends a event to a ApiBundle Controller
 * Here tokens are checked and if token is not ok, exception is thrown
 *
 * @throws AccessDeniedHttpException in case token is not valid
 */
class AuthCommentListener
{

    /**
     * This method handles kernelControllerEvent checking if token is valid
     *
     * @param FilterControllerEvent $event
     * @throws AccessDeniedHttpException in case token is not valid
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /**
         * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happien.
         * If it is a class, it comes in array format, so this works as @stof said
         * @see https://github.com/symfony/symfony/issues/1975
         */
        if(!is_array($controller)) return;

        /**
         * @todo This works because right now all API actions need a token and are available to all devices.
         * A cleaner method will need to be done if we want to restrict actions depending on agent token.
         * This code gets executed every time, even when a Exception occurs, where Symfony2 executes ExceptionController, so on that case, no actions based on tokens needs to be done
         */
        if($controller[0] instanceof ExceptionController)  return;
        if ($controller[0] instanceof CommentAuthenticatedController) {
            $event->getRequest()->attributes->set('auth_token', rand()); // Identificamos la peticion
            // $token = $event->getRequest()->query->get('token');
            // if (!in_array($token, $this->tokens)) {
            //     throw new AccessDeniedHttpException('This action needs a valid token!');
            // }
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {

    // check to see if onKernelController marked this as a token "auth'ed" request
        if (!$token = $event->getRequest()->attributes->get('auth_token')) {
            return;
        }
        $response = $event->getResponse();

    // create a hash and set it as a response header
        $hash = sha1($response->getContent().$token);
        $response->headers->set('X-Content-Hash', $hash);
    }
}
?>