<?php

namespace Taskul\MessageBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\MessageBundle\Provider\ProviderInterface;
use FOS\MessageBundle\Controller\MessageController as BaseController;
use Taskul\MainBundle\Component\CheckAjaxResponse;

class MessageController extends BaseController
{
    /**
     * Displays the authenticated participant inbox
     *
     * @return Response
     */
    public function inboxAction()
    {
        $threads = $this->getProvider()->getInboxThreads();
        $deleteForm = $this->createDeleteForm(-1);

        return $this->container->get('templating')->renderResponse('MessageBundle:Message:inbox.html.twig', array(
            'threads' => $threads,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays the authenticated participant sent mails
     *
     * @return Response
     */
    public function sentAction()
    {
        $threads = $this->getProvider()->getSentThreads();
        $deleteForm = $this->createDeleteForm(-1);

        return $this->container->get('templating')->renderResponse('MessageBundle:Message:sent.html.twig', array(
            'threads' => $threads,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a thread, also allows to reply to it
     *
     * @param strind $threadId the thread id
     * @return Response
     */
    public function threadAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $form = $this->container->get('fos_message.reply_form.factory')->create($thread);
        $formHandler = $this->container->get('fos_message.reply_form.handler');

        $deleteForm = $this->createDeleteForm($thread->getId());

        if ($message = $formHandler->process($form)) {
            return new CheckAjaxResponse(
                    $this->container->get('router')->generate('fos_message_thread_view', array(
                    'threadId' => $message->getThread()->getId()
                    )),
                    array('success'=>TRUE,'threadid'=>$message->getThread()->getId())
                );
        }

        return $this->container->get('templating')->renderResponse('MessageBundle:Message:thread.html.twig', array(
            'form' => $form->createView(),
            'thread' => $thread,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Create a new message thread
     *
     * @return Response
     */
    public function newThreadAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friends = $user->getMyFriends();

        if(count($friends) == 0){
            return $this->container->get('templating')->renderResponse('MessageBundle:Message:nofriends.html.twig');
        }
        else {
            $form = $this->container->get('fos_message.new_thread_form.factory')->create();
            $formHandler = $this->container->get('fos_message.new_thread_form.handler');

            $deleteForm = $this->createDeleteForm(-1);
            if ($message = $formHandler->process($form)) {
                return new CheckAjaxResponse(
                    $this->container->get('router')->generate('fos_message_thread_view', array(
                        'threadId' => $message->getThread()->getId()
                    )),
                    array('success' => TRUE, 'message'=>'OK')
                );
            }

            return $this->container->get('templating')->renderResponse('MessageBundle:Message:newThread.html.twig', array(
                'form' => $form->createView(),
                'data' => $form->getData(),
                'delete_form' => $deleteForm->createView(),
            ));
        }
    }

    /**
     * Deletes a thread
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $thread = $this->getProvider()->getThread($id);
        $this->container->get('fos_message.deleter')->markAsDeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);
        return new CheckAjaxResponse(
                    $this->container->get('router')->generate('fos_message_inbox'),
                    array('success'=>TRUE,'message'=>'OK')
                );
    }

    /**
     * Searches for messages in the inbox and sentbox
     *
     * @return Response
     */
    public function searchAction()
    {
        $query = $this->container->get('fos_message.search_query_factory')->createFromRequest();
        $threads = $this->container->get('fos_message.search_finder')->find($query);

        return $this->container->get('templating')->renderResponse('FOSMessageBundle:Message:search.html.twig', array(
            'query' => $query,
            'threads' => $threads
        ));
    }

    /**
     * Gets the provider service
     *
     * @return ProviderInterface
     */
    protected function getProvider()
    {
        return $this->container->get('fos_message.provider');
    }


    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();

        if ($form->hasChildren()) {
            foreach ($form->getChildren() as $child) {
                if ($child->isBound() && !$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrorMessages($child);
                }
            }
        } else {
            foreach ($form->getErrors() as $key => $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $errors;
    }

    private function createDeleteForm($id) {
      return $this->container->get('form.factory')->createBuilder('form', array('delete_id' => $id))
      ->add('delete_id', 'hidden')
      ->getForm()
      ;
    }
}
