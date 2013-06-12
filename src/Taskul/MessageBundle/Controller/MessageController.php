<?php

namespace Taskul\MessageBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\MessageBundle\Provider\ProviderInterface;
use FOS\MessageBundle\Controller\MessageController as BaseController;
use Taskul\MainBundle\Component\CheckAjaxResponse;
use Taskul\MainBundle\Component\DateClass;

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

    public function getUnreadMessagesAction()
    {
        return new JsonResponse(array('success'=>TRUE, 'total' => $this->getProvider()->getNbUnreadMessages()));
    }

    public function listUnreadMessagesAction()
    {
        $participant = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->container->get("doctrine.orm.entity_manager");

        $result = $em->getRepository('MessageBundle:Message')->findUnreadMessages($participant);
        $msgs = $this->processMessages($result);

        return new JsonResponse(array('success'=>TRUE, 'total' => count($result), 'result'=>$msgs));
    }

    private function createDeleteForm($id) {
      return $this->container->get('form.factory')->createBuilder('form', array('delete_id' => $id))
      ->add('delete_id', 'hidden')
      ->getForm()
      ;
    }

    private function getThreadViewUrl($msg)
    {
        return $this->container->get('router')->generate(
                    'fos_message_thread_view',
                    array('threadId' => $msg->getThread()->getId())
                );
    }

    private function processMessages($result)
    {
        $serializer = $this->container->get('serializer');
        $userRepository = $this->container->get("doctrine.orm.entity_manager")->getRepository('UserBundle:User');
        $msgs = array();
        $senders = array();

        for($i = 0; $i < count($result); $i++)
        {
            $msgs[$i] = json_decode($serializer->serialize($result[$i], 'json'));
            $msgs[$i]->url = $this->getThreadViewUrl($result[$i]);
            $emailSender = $msgs[$i]->sender->email;
            if(!isset($sender[$emailSender]))
                $sender[$emailSender] = $userRepository->findOneByEmail($emailSender);
            $msgs[$i]->sender->facebookId = $sender[$emailSender]->getFacebookId();
            $msgs[$i]->sender->gravatar = md5( strtolower( trim( $emailSender ) ) );

            $msgs[$i]->time = DateClass::getHumanDiff(new \DateTime($msgs[$i]->created_at));

        }

        return $msgs;
    }

}