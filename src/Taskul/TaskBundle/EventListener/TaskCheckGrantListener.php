<?php
namespace Taskul\TaskBundle\Eventlistener;

use Doctrine\ORM\EntityManager;
use Taskul\TaskBundle\Controller\PeriodRestController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class TaskCheckGrantListener
{
    private $em;
    private $sc;

    public function __construct(EntityManager $em, SecurityContextInterface $securityContext)
    {
        $this->em = $em;
        $this->sc = $securityContext;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof PeriodRestController) {
            $idTask = $event->getRequest()->query->get('idTask');
            $params = $event->getRequest()->attributes->get('_route_params');
            if (!$params['idTask']) {
                throw new NotFoundHttpException('Unable to find Task entity.');
            }

            $task = $this->em->getRepository('TaskBundle:Task')->find($params['idTask']);

            if (!$task) {
                throw new NotFoundHttpException('Unable to find Task entity.');
            }
            if (FALSE === $this->sc->isGranted('VIEW', $task))
            {
                throw new AccessDeniedException();
            }
        }
    }
}