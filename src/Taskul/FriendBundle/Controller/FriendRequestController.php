<?php

namespace Taskul\FriendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Taskul\FriendBundle\Entity\FriendRequest;
use Taskul\FriendBundle\Form\FriendRequestType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * FriendRequest controller.
 *
 * @Route("/frequest")
 */
class FriendRequestController extends Controller {

    /**
     * Lists all FriendRequest entities.
     *
     * @Route("/recibed", name="frequest_recibed")
     * @Template()
     */
    public function indexRecibedAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entities = $em->getRepository('FriendBundle:FriendRequest')->findBy(array('to'=>$user, 'active' => FALSE));

        return array(
            'entities' => $entities,
            );
    }

    /**
     * Lists all FriendRequest entities.
     *
     * @Route("/sended", name="frequest_sended")
     * @Template()
     */
    public function indexSendedAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entities = $em->getRepository('FriendBundle:FriendRequest')->findBy(array('from'=>$user, 'active' => FALSE));

        return array(
            'entities' => $entities,
            );
    }
    /**
     * Finds and displays a FriendRequest entity.
     *
     * @Route("/{id}/show", name="frequest_show")
     * @Template()
     */
    public function showAction($id) {

        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $entity = $em->getRepository('FriendBundle:FriendRequest')->showRequest($id, $user);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FriendRequest entity.');
        }

            // check for view access
        if (false === $securityContext->isGranted('VIEW', $entity))
        {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            );
    }

    /**
     * Displays a form to create a new FriendRequest entity.
     *
     * @Route("/new", name="frequest_new")
     * @Template()
     */
    public function newAction() {
        $entity = new FriendRequest();
        $form = $this->createForm(new FriendRequestType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            );
    }

    /**
     * Envia solicitudes de conexion a los contactos seleccionados
     * por el usuario y crea solicitudes de amistad asociado a
     * id de los contactos de fb y los ids de la request realizada (en js)
     *
     * @Route("/importfb", name="import_fb")
     * @Template()
     */
    public function importFacebookAction(Request $request) {
        $fb = $this->get('my.facebook.user');
        $fRequest = array(); //Almacenamos los objectos (si se han creado solicitudes) para darles luego permisos ace


        $fbdata = $fb->get('/me');


        $fbContact = array();

        $defaultData = array('message' => 'Type your message here');
        $formBuilder = $this->createFormBuilder($defaultData)
        ->add('message', 'purified_textarea');



        $imgUrls = array();
        if (null !== $fbdata && isset($fbdata['id'])) {



          //$user = $this->get('security.context')->getToken()->getUser();
          $user = $fb->loadUserByUsername($fbdata['id']);

          $aclManager = $this->get('taskul.acl_manager');
          $em = $this->getDoctrine()->getManager();
          $choices = array();

          $searchContact = array(); // Lo usamos para buscar mas rápido dentro del array de contactos de fb

          $fbContact = $fb->get('/me/friends?fields=name,id,picture');
          $fbContact = $fbContact['data'];

          $i = 0;
          foreach ($fbContact as $fbc) {
            $choices[$i] = array($fbc['id'] => $fbc['name']);
            $imgUrls[$i] = $fbc['picture']['data']['url'];
            $searchContact[$fbc['id']] = $i;
            $i++;
        }

        $formBuilder->add('contacts', 'choice', array(
            'choices'   => $choices,
            'multiple'  => true,
            'expanded' => true,
            ));

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);
            $formData = $request->request->get($form->getName());
            if($formData['sended'] != 'no' && count($formData['contacts'])>0){ /* @TODO hay que  hacer preg_match con el id del facebook */
                foreach ($formData['contacts'] as $f){

                    $friendReq = new FriendRequest();
                    $friendReq->setFrom($user);
                    $friendReq->setFbrequestid($formData['sended']);
                    $friendReq->setFbid($f);
                    $friendReq->setMessage($formData['message']);
                    if(isset($searchContact[$f])){
                      $id = $searchContact[$f];
                      $fbData = array('fbdata'=>array('imgurl'=>$imgUrls[$id],'name'=>$choices[$id][$f]));
                      $friendReq->setAddtionalData($fbData);
                    }
                    $em->persist($friendReq);
                    $fRequest[] = $friendReq;
                }
                $em->flush();

                // Vamos a darles permiso @FIXME: comprobar si hay que hacer flush para los ace o
                // se puede hacer antes con el persist (creo que no)
                //

                foreach ($fRequest as $f){
                    $aclManager->grant($f);
                }
            }
        }
    }else {

        return $this->redirect($this->get('fos_facebook.api')->getLoginUrl() );
    }

    return array(
        'frequest' => $fRequest,
        'contacts' => $fbContact,
        'form' => $form->createView(),
        'imgUrls' => $imgUrls,
        );
}

public function checkFriendsEmail($friends, $email) {
    foreach ($friends as $friend) {
        if ($friend->getEmail() === $email)
            return TRUE;
    }
    return FALSE;
}

    /**
     * Creates a new FriendRequest entity.
     *
     * @Route("/create", name="frequest_create")
     * @Method("POST")
     * @Template("FriendBundle:FriendRequest:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new FriendRequest();
        $owner = $this->get('security.context')->getToken()->getUser();
        $entity->setFrom($owner);
        $time = microtime(true) . '_' . uniqid();
        $entity->setHash(hash("sha256", $time . $owner->getId(), false));

        $form = $this->createForm(new FriendRequestType(), $entity);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $this->_processEntity($owner, $em, $form);


            return $this->redirect($this->generateUrl('frequest_sended'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            );
    }
    /**
     * Comprueba si el destinatario de una peticion de amistad esta dado
     * de alta en el sistema y si es así le asigna el campo TO del destinario
     *
     * @param  [type] $entity [description]
     * @param  [type] $email  [description]
     * @param  [type] $em     [description]
     * @return [type]         [description]
     */
    private function _checkTo($entity, $email, $em) {
        if (null === $entity->getTo()) {
            $to = $em->getRepository('UserBundle:User')->findOneByEmail($email);

            if (null !== $to) {
                $entity->setTo($to);
            }
        }
        return $entity;
    }

    private  function _getHash($id){
        $time = microtime(true) . '_' . uniqid();
        return hash("sha256", $time . $id, false);
    }


    private function _processEntity($owner,$em,$form){
        // Buscamos los emails
        $data = $form->getData();
        $emails = explode(';', $data->getEmail());
        $aclManager = $this->get('taskul.acl_manager');
        foreach ($emails as $email) {


            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Comprobamos si estan en los contactos del usuario
                $friends = $owner->getMyFriends();
                if (FALSE === $this->checkFriendsEmail($friends, $email)) {
                    $entity = $em->getRepository('FriendBundle:FriendRequest')->findOneBy(array('from' => $owner, 'email' => $email, 'active' => FALSE));
                        $newEntity = FALSE; // Nos va a indicar si la crea un nuevo objeto para crearle la ACE del dueño
                        if (null === $entity) {
                            $entity = new FriendRequest();
                            $entity->setFrom($owner);
                            $newEntity = TRUE;

                        }
                        $entity->setEmail($email);
                        $entity->setMessage($data->getMessage());
                        // Comprueba si el email de destino esta dado de alta como usuario
                        $entity = $this->_checkTo($entity, $email, $em);
                        $entity->setHash($this->_getHash($owner->getId()));

                        $em->persist($entity);
                        $em->flush();

                        if(TRUE == $newEntity)
                            $aclManager->grant($entity);

                        $to = $entity->getTo();
                        if(null !== $to)
                            $aclManager->grant($entity, $to->getUsername(), 'Taskul\UserBundle\Entity\User', MaskBuilder::MASK_OPERATOR);


                        /* @TODO: Aqui hay que enviar emails / mirar FOSmessagebundle */
                    }
                }
            }

        }
    /**
     * Deletes a FriendRequest entity.
     *
     * @Route("/{id}/delete", name="frequest_delete")
     *
     */
    public function deleteAction(Request $request, $id) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FriendBundle:FriendRequest')->findOneBy(array('id' => $id, 'active' => FALSE));

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FriendRequest entity.');
            }

            $securityContext = $this->get('security.context');

        // check for edit access
            if (false === $securityContext->isGranted('DELETE', $entity))
            {
                throw new AccessDeniedException();
            }

            $aclManager = $this->get('taskul.acl_manager');
            $aclManager->revokeAll($entity);

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('frequest_recibed'));
    }

    /**
     *
     *
     * @Route("/register/{hash}", name="frequest_register")
     *
     */
    public function registerAction($hash) {
        if (null !== $this->getDoctrine()->getRepository('FriendBundle:FriendRequest')
            ->findOneBy(array('hash' => $hash, 'active' => FALSE))) {

            $this->get('session')->set('request_hash', $hash);
    }
    return $this->redirect(
        $this->generateUrl("fos_user_registration_register")
        );
}

    /**
     *
     *
     * @Route("/activate/{id}", name="frequest_activate")
     *
     */
    public function activateAction($id) {
        $to = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $request = $em->getRepository('FriendBundle:FriendRequest')
        ->findOneBy(array('id' => $id, 'to' => $to, 'active' => FALSE));

        if (!$request) {
            throw $this->createNotFoundException('Unable to find Request entity.');
        }

        $securityContext = $this->get('security.context');

        // check for edit access
        if (false === $securityContext->isGranted('EDIT', $request))
        {
            throw new AccessDeniedException();
        }

        $from = $request->getFrom();
        $to->addMyFriend($from);
        $to->addFriendsWithMe($from);
        $from->addMyFriend($to);
        $from->addFriendsWithMe($to);
        $request->setActive(TRUE);
        $em->persist($to);
        $em->persist($from);
        $em->persist($request);
        $em->flush();

        return $this->redirect($this->generateUrl('myfriends'));
    }

}
