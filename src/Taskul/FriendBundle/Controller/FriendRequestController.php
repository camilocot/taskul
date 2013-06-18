<?php

namespace Taskul\FriendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Taskul\FriendBundle\Entity\FriendRequest;
use Taskul\FriendBundle\Form\FriendRequestType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * FriendRequest controller.
 *
 * @Route("/frequest")
 * @Breadcrumb("Dashboard", route="dashboard")
 * @Breadcrumb("Friend Request", route="frequest")
 */
class FriendRequestController extends Controller {

  /**
   * List all friend request send and recibed
   *
   * @Route("/", name="frequest", options={ "expose": true })
   * @Template()
   */
  public function indexAction() {

    $recibed = $this->getRecibed();
    $sended = $this->getSended();
    $deleteForm = $this->createDeleteForm(-1);
    $activateForm = $this->createActivateForm(-1);

    return array(
      'sended' => count($sended),
      'recibed' => count($recibed),
      'entity' => array('id' => -1),
      'delete_form' => $deleteForm->createView(),
      'activate_form' => $activateForm->createView(),
      );
  }


  /**
   * Lists all FriendRequest entities.
   *
   * @Route("/recibed", name="frequest_recibed")
   * @Template()
   *
   * @Breadcrumb("Recibidas")
   */
  public function indexRecibedAction() {

    $deleteForm = $this->createDeleteForm(-1);
    $activateForm = $this->createActivateForm(-1);

    $entities = $this->getRecibed();

    return array(
      'entities' => $entities,
      'entity' => array('id' => -1),
      'delete_form' => $deleteForm->createView(),
      'activate_form' => $activateForm->createView(),
      );
  }

  /**
   * Lists all FriendRequest entities.
   *
   * @Route("/sended", name="frequest_sended", options={ "expose": true })
   * @Template()
   *
   * @Breadcrumb("Enviadas")
   */
  public function indexSendedAction() {
    $deleteForm = $this->createDeleteForm(-1);
    $entities = $this->getSended();

    return array(
      'entities' => $entities,
      'entity' => array('id' => -1),
      'delete_form' => $deleteForm->createView(),
      'activate_form' => $this->createActivateForm(-1)->createView(),
      );
  }
  /**
   * Finds and displays a FriendRequest entity.
   *
   * @Route("/{id}/show", name="frequest_show")
   * @Template()
   *
   * @Breadcrumb("Ver")
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
    $activateForm = $this->createActivateForm(-1);

    return array(
      'entity' => $entity,
      'delete_form' => $deleteForm->createView(),
      'activate_form' => $activateForm->createView(),
      );
  }

  /**
   * Displays a form to create a new FriendRequest entity.
   *
   * @Route("/new", name="frequest_new")
   * @Template()
   *
   * @Breadcrumb("Nueva")
   */
  public function newAction() {
    $entity = new FriendRequest();
    $form = $this->createForm(new FriendRequestType(), $entity);

      return array(
        'entity' => $entity,
        'form' => $form->createView(),
        'delete_form' => $this->createDeleteForm(-1)->createView(),
        'activate_form' => $this->createActivateForm(-1)->createView(),
        );
  }

  /**
   * Envia solicitudes de conexion a los contactos seleccionados
   * por el usuario y crea solicitudes de amistad asociado a
   * id de los contactos de fb y los ids de la request realizada (en js)
   *
   * @Route("/importfb", name="import_fb")
   * @Template()
   *
   * @Breadcrumb("Facebook")
   */
  public function importFacebookAction(Request $request) {
    $fb = $this->get('my.facebook.user');
    $fbdata = $fb->get('/me');
    $fRequest = array();

    if (null !== $fbdata && isset($fbdata['id'])) {
      /* Obtenemos el listado de amigos */
      list($choices,$imgUrls,$searchContact,$fbContact) = $this->getFriendsChoices();

      $defaultData = array('message' => 'Type your message here');

      $formBuilder = $this->createFormBuilder($defaultData)
      ->add('message', 'purified_textarea')
      ->add('contacts', 'choice', array(
        'choices'   => $choices,
        'multiple'  => true,
        'expanded' => true,
        ));

      $form = $formBuilder->getForm();
      if ($request->isMethod('POST')) {
        $form->bind($request);
        $formData = $request->request->get($form->getName());
        $fRequest = $this->processFriendRequestsFBForm($formData,$choices,$searchContact,$imgUrls);
      }

      return array(
        'frequest' => $fRequest,
        'contacts' => $fbContact,
        'form' => $form->createView(),
        'imgUrls' => $imgUrls,
        'delete_form' => $this->createDeleteForm(-1)->createView(),
        'activate_form' => $this->createActivateForm(-1)->createView(),
        'entity' => array('id' => -1),
        );

    }
    else {
      return $this->redirect($this->get('fos_facebook.api')->getLoginUrl() );
    }
  }



  /**
   * Comprueba si un email esta dentro del array de amigos
   *
   * @param  [type] $friends [description]
   * @param  [type] $email   [description]
   * @return [type]          [description]
   */
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
   *
   * @Breadcrumb("Nueva")
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

      $this->processFriendRequestsEmailForm($owner, $em, $form);

      if ($request->isXmlHttpRequest()){
        return new JsonResponse(array('success' => TRUE,'message'=>'OK'));
      }
      else {
        return $this->redirect($this->generateUrl('frequest_sended'));
      }
    }

    return array(
      'entity' => $entity,
      'form' => $form->createView(),
      'delete_form' => $this->createDeleteForm(-1)->createView(),
      'activate_form' => $this->createActivateForm(-1)->createView(),
      );
  }

  /**
   * Deletes a FriendRequest entity.
   *
   * @Route("/{id}/delete", name="frequest_delete", options={ "expose": true })
   * @Method("POST")
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
    if ($request->isXmlHttpRequest()){
        return new JsonResponse(array('success' => TRUE,'message'=>'OK'));
    }
    else {
        return $this->redirect($this->generateUrl('frequest_sended'));
    }
    return $this->redirect($this->generateUrl('frequest_recibed'));
  }

  /**
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
   * @Route("/activate/{id}", name="frequest_activate", options={ "expose": true })
   * @Method("POST")
   *
   */
  public function activateAction(Request $request,$id) {
    $to = $this->get('security.context')->getToken()->getUser();
    $em = $this->getDoctrine()->getEntityManager();

    $frequest = $em->getRepository('FriendBundle:FriendRequest')
    ->findOneBy(array('id' => $id, 'to' => $to, 'active' => FALSE));

    if (!$frequest) {
      throw $this->createNotFoundException('Unable to find Request entity.');
    }

    $this->processFriendRequestActivate($frequest);
    $frequest->setActive(TRUE);

    $em->persist($frequest);
    $em->flush();
    if ($request->isXmlHttpRequest()){
        return new JsonResponse(array('success' => TRUE,'message'=>'Solicitud aceptadsa correctamente'));
    }
    else {
        return $this->redirect($this->generateUrl('myfriends'));
    }
  }

  /**
   * Crea la relacion de amistad entre los usuarios perteneceentes a la slocitud
   * @param  [type] $request [description]
   * @return [type]          [description]
   */
  private function processFriendRequestActivate($request)
  {
    $em = $this->getDoctrine()->getEntityManager();

    $to = $request->getTo();
    $from = $request->getFrom();

    if(! $this->checkFriends($to,$from)){
      $to->addMyFriend($from);
      $to->addFriendsWithMe($from);
      $em->persist($to);
    }
    if(! $this->checkFriends($from,$to)){
      $from->addMyFriend($to);
      $from->addFriendsWithMe($to);
      $em->persist($from);
    }

    return $this;
  }

  private function checkFriends($user,$friend)
  {
    $friends = $user->getMyFriends();
    foreach ($friends as $f) {
      if($f->getId() === $friend->getId())
        return TRUE;
    }
    return FALSE;
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
  private function checkTo($entity, $email) {
    $em = $this->getDoctrine()->getEntityManager();
    if (null === $entity->getTo()) {
      $to = $em->getRepository('UserBundle:User')->findOneByEmail($email);

      if (null !== $to) {
        $entity->setTo($to);
      }
    }
    return $entity;
  }

  private  function getHash($id){
    $time = microtime(true) . '_' . uniqid();
    return hash("sha256", $time . $id, false);
  }

  private function createDeleteForm($id) {
    return $this->createFormBuilder(array('delete_id' => $id))
    ->add('delete_id', 'hidden')
    ->getForm()
    ;
  }

  private function createActivateForm($id) {
    return $this->createFormBuilder(array('activate_id' => $id))
    ->add('activate_id', 'hidden')
    ->getForm()
    ;
  }

  /**
   * Procesa un formulario de envio de solicitudes de amistad por email
   * @param  [type] $owner [description]
   * @param  [type] $em    [description]
   * @param  [type] $form  [description]
   * @return [type]        [description]
   */
  private function processFriendRequestsEmailForm($owner,$em,$form){
    // Buscamos los emails
    $data = $form->getData();
    $emails = explode(';', $data->getEmail());
    foreach ($emails as $email) {
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Comprobamos si estan en los contactos del usuario
        $friends = $owner->getMyFriends();
        if (FALSE === $this->checkFriendsEmail($friends, $email)) {
          $entity = $this->processFriendRequestEmail($owner,$email,$data);
          /* @TODO: Aqui hay que enviar emails / mirar FOSmessagebundle */
        }
      }
    }
    return TRUE;

  }

  /**
   * Procesa una solicitud de amistad por email
   * @param  [type] $owner [description]
   * @param  [type] $email [description]
   * @return [type]        [description]
   */
  private function processFriendRequestEmail($owner,$email,$data)
  {
    $aclManager = $this->get('taskul.acl_manager');
    $em = $this->getDoctrine()->getEntityManager();
    $actionManager = $this->get('taskul_timeline.action_manager.orm');

    $entity = new FriendRequest();
    $entity->setFrom($owner);
    $entity->setEmail($email);
    $entity->setMessage($data->getMessage());

    // Comprueba si el email de destino esta dado de alta como usuario
    $entity = $this->checkTo($entity, $email, $em);
    $entity->setHash($this->getHash($owner->getId()));

    $em->persist($entity);
    $em->flush();

    if(NULL !== $entity->getTo())
      $actionManager->handle($owner,'POST',$entity,$entity->getTo());

    $this->grantAclsFriendRequest(array($entity));

    return $entity;
  }

  /**
   * Obtiene el listado de solicitudes recibidas
   * @return [type] [description]
   */
  private function getRecibed(){
    $em = $this->getDoctrine()->getManager();
    $user = $this->get('security.context')->getToken()->getUser();
    $entities = $em->getRepository('FriendBundle:FriendRequest')->findBy(array('to'=>$user, 'active' => FALSE));
    return $entities;
  }

  /**
   * Obtiene el listado de solicitudes enviadas
   * @return [type] [description]
   */
  private function getSended(){
    $em = $this->getDoctrine()->getManager();
    $user = $this->get('security.context')->getToken()->getUser();
    $entities = $em->getRepository('FriendBundle:FriendRequest')->findBy(array('from'=>$user, 'active' => FALSE));

    return $entities;
  }

  /**
   * Se procesa el formulario de las solicitudes de amistad
   *
   * @param  [type] $formData [description]
   * @return [type]           [description]
   */
  private function processFriendRequestsFBForm($formData,$choices,$searchContact,$imgUrls)
  {
    $fRequest = array(); //Almacenamos los objectos (si se han creado solicitudes) para darles luego permisos ace
    $em = $this->getDoctrine()->getManager();
    if($formData['sended'] != 'no' && count($formData['contacts'])>0){ /* @TODO hay que  hacer preg_match con el id del facebook */
      foreach ($formData['contacts'] as $f){
        $fRequest[] = $friendReq = $this->processFriendRequestFB($f,$choices,$searchContact,$imgUrls,$formData);
        $em->persist($friendReq);
      }
      $em->flush();

      $this->grantAclsFriendRequest($fRequest);
    }
    return $this;
  }

  /**
   * Se crea una nueva solicitu de amistad
   * @param  [type] $f             [description]
   * @param  [type] $choices       [description]
   * @param  [type] $searchContact [description]
   * @param  [type] $imgUrls       [description]
   * @return [type]                [description]
   */
  private function processFriendRequestFB($f,$choices,$searchContact,$imgUrls,$formData)
  {

    $user = $this->getUserDataFB();
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
    return $friendReq;
  }

  /**
   * Obtine los datos asociados al usuario activo de FB
   * @return [type] [description]
   */
  private function getUserDataFB()
  {
    $fb = $this->get('my.facebook.user');
    $fbdata = $fb->get('/me');
    $user = $fb->loadUserByUsername($fbdata['id']);
    return $user;
  }

  /**
   * Se le otorga permisos al propietario de la solicitud
   *
   * @param  [type] $fRequest [description]
   * @return [type]           [description]
   */
  private function grantAclsFriendRequest($fRequest)
  {
    $aclManager = $this->get('taskul.acl_manager');
    foreach ($fRequest as $f){
        $aclManager->grant($f);
        $to = $f->getTo();
        if(NULL !== $to){
          $aclManager->grantUser($f, $to->getUsername(), 'Taskul\UserBundle\Entity\User', MaskBuilder::MASK_OPERATOR);
        }
    }
    return $this;
  }

  /**
   * Se obtiene un listado de los amigos de fb con otras variables para optimizar el proceso.
   *
   * @return [type] [description]
   */
  private function getFriendsChoices()
  {
      $fb = $this->get('my.facebook.user');

      $choices = array();
      $imgUrls = array();
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
      return array($choices,$imgUrls,$searchContact,$fbContact);
  }
}
