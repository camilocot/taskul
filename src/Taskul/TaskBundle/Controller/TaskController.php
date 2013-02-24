<?php

namespace Taskul\TaskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Taskul\TaskBundle\Entity\Task;
use Taskul\TaskBundle\Form\TaskType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
/**
 * Task controller.
 *
 * @Route("/task")
 *
 * @Breadcrumb("Dashboard", route="dashboard")
 * @Breadcrumb("Tasks", route="task")
 */
class TaskController extends Controller {

    /**
     * Lists all Task entities.
     *
     * @Route("/", name="task")
     * @Template()
     */
    public function indexAction() {
        $deleteForm = $this->createDeleteForm(-1);
        $user = $this->get('security.context')->getToken()->getUser();
        $entities = $this->getDoctrine()->getManager()->getRepository('TaskBundle:Task')->findTasks($user);
        foreach ($entities as $e){
            $this->loadTags($e);
        }
        return array(
            'entities' => $entities,
            'entity' => array('id' => -1),
            'delete_form' => $deleteForm->createView(),
            );
    }

    /**
     * Finds and displays a Task entity.
     *
     * @Route("/{id}/show", name="task_show")
     * @Template()
     *
     * @Breadcrumb("Show")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $entity = $em->getRepository('TaskBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }
                    // check for view access
        if (false === $securityContext->isGranted('VIEW', $entity))
        {
            throw new AccessDeniedException();
        }

        $documents = $em->getRepository('FileBundle:Document')->findBy(array('class' => $entity->__toString(),'idObject'=>$entity->getId()));

        $deleteForm = $this->createDeleteForm($id);
        $uploadId = sprintf('%09d', mt_rand(0, 1999999999));

        $fileManager = $this->get('taskul.user.file_manager');
        $fileManager->syncFiles($uploadId, $user, $entity);

        $existingFiles = $fileManager->getEntityFiles($entity);
        $tags = $this->loadTags($entity);
        return array(
            'entity' => $entity,
            'documents' => $documents,
            'uploadId' => $uploadId,
            'existingFiles' => $existingFiles,
            'delete_form' => $deleteForm->createView(),
            'entityClass' =>$entity->__toString(), //@TODO Esto no se xq no va con el set en twig
            );
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/new", name="task_new")
     * @Template()
     *
     * @Breadcrumb("Create")
     */
    public function newAction() {
        $securityContext = $this->get('security.context');
        $owner = $securityContext->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $entity = new Task();
        $entity->setDateEnd(new \DateTime("now"));
        $entity->setOwner($owner);

        $tags = $this->loadTags($entity);
        $formFactory = $this->get('form.factory');
        $form = $formFactory->create(new TaskType($securityContext));

        $newId = sprintf('%09d', mt_rand(0, 1999999999));
        $existingFiles = array();

        return array(
            'entity' => $entity,
            'existingFiles' => $existingFiles,
            'uploadId' => $newId,
            'form' => $form->createView(),
            'defaultTags' => $this->loadAllTags($owner,$em),
            'entityClass' =>$entity->__toString(), //@TODO Esto no se xq no va con el set en twig

            );
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/create/{uploadId}", name="task_create")
     * @Method("POST")
     * @Template("TaskBundle:Task:new.html.twig")
     *
     * @Breadcrumb("Create")
     */
    public function createAction(Request $request) {
        $entity = new Task();
        $securityContext = $this->get('security.context');
        $formFactory = $this->get('form.factory');
        $user = $securityContext->getToken()->getUser();

        $form = $formFactory->create(new TaskType($securityContext),$entity);

        $form->bind($request);

        $formData = $request->request->get($form->getName());

        // Para la carga de ficheros, id único asociado al formulario
        // se usa para mantener los ficheros aunque se recarge la web
        // (form error pej)
        $newId = $this->getRequest()->get('uploadId');
        $existingFiles = array();


        if (preg_match('/^\d+$/', $newId)) {

            $fileManager = $this->get('taskul.user.file_manager');

            $existingFiles = $fileManager->getFiles(array('folder' => 'tmp/attachments/' . $newId));

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $aclManager = $this->get('taskul.acl_manager');
                $tagManager = $this->get('fpn_tag.tag_manager');

                $entity->setOwner($user);
                $em->persist($entity);
                $em->flush();

                $tags = $formData['tags'];
                $this->saveTags($entity, $tags);

                // Asignamos los permisos
                $members = $entity->getMembers();
                $aclManager->grant($entity,$members);
                $members = $entity->getMembers();
                $fileManager->syncUserFiles($newId,$user,$entity,$members);

                return $this->redirect($this->generateUrl('task_show', array('id' => $entity->getId())));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'existingFiles' => $existingFiles,
            'uploadId' => $newId,
            'defaultTags' => $this->loadAllTags($user,$em),
            'entityClass' =>$entity->__toString(), //@TODO Esto no se xq no va con el set en twig
            );
    }

    /**
     * Aniade documentacion desde la ficha de la tarea
     *
     * @Route("/{id}/add-documents/{formId}", name="task_add_document")
     * @Method("POST")
     * @Template()
     */
    public function addDocumentsAction($id) {
        $em = $this->getDoctrine()->getManager();

        $securityContext = $this->get('security.context');
        $formId = $this->getRequest()->get('formId');
        $fileManager = $this->get('taskul.user.file_manager');
        $user = $securityContext->getToken()->getUser();

        if (preg_match('/^\d+$/', $formId)) {
            $entity = $em->getRepository('TaskBundle:Task')->find($id);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Task entity.');
            }

            $members = $entity->getMembers()->toArray();
            $owner = $entity->getOwner();
            if($user->getId() !== $owner->getId())
                array_push($members, $owner);
            $fileManager->syncUserFiles($formId,$user,$entity);

        }
        return $this->redirect($this->generateUrl('task_show', array('id' => $entity->getId())));
    }


    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Route("/{id}/edit", name="task_edit")
     * @Template()
     *
     * @Breadcrumb("Update")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TaskBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $securityContext = $this->get('security.context');

        // check for edit access
        if (false === $securityContext->isGranted('EDIT', $entity))
        {
            throw new AccessDeniedException();
        }

        $tags = $this->loadTags($entity);
        $formFactory = $this->get('form.factory');
        $editForm = $formFactory->create(new TaskType($securityContext),$entity,array('tags'=>$tags));

        $deleteForm = $this->createDeleteForm($id);

        // Genera un id para el form para los sincronizar los ficheros
        $editId = sprintf('%09d', mt_rand(0, 1999999999));

        $user = $this->get('security.context')->getToken()->getUser();

        // @TODO: esto hay que ponerlo sobre el servicio del taskul (el toFolder)
        $fileManager = $this->get('taskul.user.file_manager');
        $fileManager->syncFiles($editId, $user, $entity);

        $existingFiles = $fileManager->getEntityFiles($entity);

        return array(
            'entity' => $entity,
            'uploadId' => $editId,
            'existingFiles' => $existingFiles,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'defaultTags' => $this->loadAllTags($user,$em),
            'entityClass' =>$entity->__toString(), //@TODO Esto no se xq no va con el set en twig
            );
    }

    private function loadTags($entity){
        $tagManager = $this->get('fpn_tag.tag_manager');
        $tagManager->loadTagging($entity);
        $tags = $entity->getTags();
        $tagsNames = array();
        foreach($tags as $t){
            $tagsNames[] = $t->getName();
        }
        $tagsString = implode(', ',$tagsNames);
        return $tagsString;
    }

    private function loadAllTags($user,$em){
        $tagManager = $this->get('fpn_tag.tag_manager');
        $tasks = $em->getRepository('TaskBundle:Task')->findTasks($user);
        $tagsArray = array();
        foreach ($tasks as $task) {
            $tagManager->loadTagging($task);
            $tags = $task->getTags();
            foreach ($tags as $tag) {
                $name = $tag->getName();
                $tagsArray[] = $name;
            }

        }
        return $tagsArray;
    }
    private function saveTags($entity, $tags){
        $tagManager = $this->get('fpn_tag.tag_manager');

        $tagsNames = $tagManager->splitTagNames($tags);
        $tags = $tagManager->loadOrCreateTags($tagsNames);
        $tagManager->replaceTags($tags, $entity);
        $tagManager->saveTagging($entity);
        return true;
    }
    /**
     *
     * @Route("/upload", name="task_upload")
     * @Template()
     *
     *
     */
    public function uploadAction() {
        $securityContext = $this->get('security.context');
        $em = $this->getDoctrine()->getManager();
        $fileManager = $this->get('taskul.user.file_manager');
        $user = $securityContext->getToken()->getUser();


        $uploadId = $this->getRequest()->get('uploadId');
        $class = $this->getRequest()->get('classId');
        $id = $this->getRequest()->get('id');

        if (!preg_match('/^\d+$/', $uploadId)) {
            throw new \Exception("Bad edit id");
        }
        /* Estamos añadiendo */
        if($_SERVER['REQUEST_METHOD'] === 'POST' && (! isset($_REQUEST['_method']) || (isset($_REQUEST['_method']) && $_REQUEST['_method'] !== 'DELETE'))){



            try {
             $existingFiles = $fileManager->handleFileUpload($user, array(
                'folder' => 'tmp/attachments/' . $uploadId,
                'max_upload_data'=>$this->container->getParameter('taskul.user.quota')
                ));
           // Espo es para cuando se pasa el Max Post Content
             if (NULL === $existingFiles) {
                $response = new Response(json_encode(array(array('error'=>'max.upload.filesize'))));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        } catch (\Exception $e) {
            $response = new Response(json_encode(array(array('error'=>$e->getMessage()))));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }else if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || ( isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE')) {

        // Vamos a comprobar que se tiene permiso para borrar el fichero
        if(! empty($class)) {

            $fileName = $this->getRequest()->get('file');
             /*
             Esto es para cuando se esta editando
             para no dejar borrar documentos que no son del propietario
             */
             if(! empty($id))
                $document = $em->getRepository('FileBundle:Document')->findOneBy(array('name'=>$fileName,'idObject'=>$id,'class'=>$class));

            if ( isset($document) && false === $securityContext->isGranted('DELETE', $document))
            {
                $response = new Response(json_encode(array('error'=>'access denied')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }else if(isset($document)){
                // Marcamos el registro como borrado para que no se tenga en cuanta en las sumas de la quota
                $document->setMarkToDelete(TRUE);
                $em->persist($document);
                $em->flush();
            }


            $existingFiles = $fileManager->handleFileUpload($user, array(
                'folder' => 'tmp/attachments/' . $uploadId,
                'max_upload_data'=>$this->container->getParameter('taskul.user.quota')
                ));

        }else{
            $response = new Response(json_encode(array(array('error'=>'faltan variables'))));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }


    }

    $response = new Response(json_encode(array(array('error'=>'metodo no comprendido'))));
    $response->headers->set('Content-Type', 'application/json');
    return $response;

}
    /**
     * Edits an existing Task entity.
     *
     * @Route("/{id}/update/{uploadId}", name="task_update")
     * @Method("POST")
     * @Template("TaskBundle:Task:edit.html.twig")
     *
     * @Breadcrumb("Update")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->get('security.context');


        $entity = $em->getRepository('TaskBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }


        // check for edit access
        if (false === $securityContext->isGranted('EDIT', $entity))
        {
            throw new AccessDeniedException();
        }


        $deleteForm = $this->createDeleteForm($id);

        $formFactory = $this->get('form.factory');
        $editForm = $formFactory->create(new TaskType($securityContext),$entity);

        $editForm->bind($request);

        // Para la carga de ficheros, id único asociado al formulario
        // se usa para mantener los ficheros aunque se recarge la web
        // (form error pej)
        $editId = $this->getRequest()->get('uploadId');

        $fileManager = $this->get('taskul.user.file_manager');
        $existingFiles = $fileManager->getFiles(array('folder' => 'tmp/attachments/' . $editId));

        if (preg_match('/^\d+$/', $editId)) {

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                $formData = $request->request->get($editForm->getName());
                $tags = $formData['tags'];
                $this->saveTags($entity,$tags);

                $aclManager = $this->get('taskul.acl_manager');
                $aclManager->revokeAll($entity);
                $members = $entity->getMembers();
                $aclManager->grant($entity,$members);

                $user = $securityContext->getToken()->getUser();
                $members = $entity->getMembers();
                // Sincronizamos los ficheros
                $fileManager->syncUserFiles($editId,$user,$entity,$members);


                return $this->redirect($this->generateUrl('task_show', array('id' => $id)));
            }
        }

        return array(
            'uploadId' => $editId,
            'entity' => $entity,
            'existingFiles' => $existingFiles,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'defaultTags' => $this->loadAllTags($user,$em),
            'entityClass' =>$entity->__toString(), //@TODO Esto no se xq no va con el set en twig
            );
    }

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}/delete", name="task_delete", options={ "expose": true })
     * @Method("POST")
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $securityContext = $this->get('security.context');
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TaskBundle:Task')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Task entity.');
            }

                    // check for delete access
            if (false === $securityContext->isGranted('DELETE', $entity))
            {
                throw new AccessDeniedException();
            }

            $aclManager = $this->get('taskul.acl_manager');
            $aclManager->revokeAll($entity);

            $user = $this->get('security.context')->getToken()->getUser();
            $fileManager = $this->get('taskul.user.file_manager');
            $fileManager->removeUserFiles($user,$entity,$entity->getMembers());

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('task'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
        ->add('id', 'hidden')
        ->getForm()
        ;
    }


}
