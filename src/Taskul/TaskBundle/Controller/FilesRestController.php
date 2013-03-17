<?php

namespace Taskul\TaskBundle\Controller;

use Taskul\TaskBundle\Entity\Task;
use Taskul\TaskBundle\Form\TaskType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

use FOS\RestBundle\Controller\Annotations\RouteResource;

use Taskul\TaskBundle\Controller\Base\TasksRestBaseController as BaseController;
use Taskul\FileBundle\Entity\Document;

/**
 * Task Rest controller.
 * @RouteResource("File")
 *
 * @Breadcrumb("Dashboard", route="dashboard")
 * @Breadcrumb("Tasks", route="api_get_tasks")
 *
 * @todo : en el breadcrump hay que meter un enlace a ver la tarea que estamos editando
 */
class FilesRestController extends BaseController {

	public function cgetAction($id)
	{
		$em = $this->getEntityManager();
		$task = $this->checkGrant($id,'VIEW');
		$id = $task->getId();

		$data['documents'] = $em->getRepository('FileBundle:Document')->findBy(array('class' => $task->__toString(),'idObject'=>$id));
		$data['taskId'] = $id;
        $data['delete_id'] = -1;
        $data['delete_form'] = $this->createDeleteForm(-1)->createView();

		$view = $this->view($data, 200)
		->setTemplate("TaskBundle:Task:api/listFiles.html.twig")
		;
var_dump($this->get('kernel')->getRootDir());
		return $this->handleView($view);

    } // "api_get_task_files"   [GET] /api/tasks/{id}/files

    public function postAction($id)
    {
    	$task = $this->checkGrant($id,'VIEW');
        $aclManager = $this->getAclManager();

    	$data = $this->processRequest();
        $file = $data['files'][0];

        $doc = $this->createDocument($task,$file);
        $aclManager->grant($doc,$task->getMembers());

        $file->id = $doc->getId();
        $file->taskid = $task->getId();

        return $this->processView($data);

    } // "api_post_task_files"   [POST] /api/tasks/{id}/files

    public function deleteAction($idTask, $idDocument)
    {
        $aclManager = $this->getAclManager();
        $em = $this->getEntityManager();

        $task = $this->checkGrant($idTask,'DELETE');
        $document = $this->checkGrant($idDocument,'DELETE','FileBundle:Document');

        $_GET['file'] = $document->getName(); // Para el handler del upload
        $data = $this->processRequest();
        $aclManager->revokeAll($document);

        $em->remove($document);
        $em->flush();

        return $this->processView($data);
    } // "api_delete_task_file" [DELETE] /api/tasks/{idtask}/files/{iddocument}

    protected function processRequest()
    {

    	$upload_handler = new \Taskul\FileBundle\BlueImp\UploadHandler(
    		array(
    			'upload_dir' => $_SERVER['DOCUMENT_ROOT'].'/uploads/',
    			'upload_url' => $this->getRequest()->getScheme().'://'.$this->getRequest()->getHost().'/uploads/',
    			'script_url' => $this->getRequest()->getUri()
    			), false);


        // From https://github.com/blueimp/jQuery-File-Upload/blob/master/server/php/index.php
        // There's lots of REST fanciness here to support different upload methods, so we're
        // keeping the blueimp implementation which goes straight to the PHP standard library.
        // TODO: would be nice to port that code fully to Symfonyspeak.



    	$data = array();

    	switch ($_SERVER['REQUEST_METHOD']) {
    		case 'OPTIONS':
    		break;
    		case 'HEAD':
    		case 'GET':
    		$data = $upload_handler->get(false);
    		break;
    		case 'POST':
    		if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
    			$data = $upload_handler->delete(false);
    		} else {
    			$data = $upload_handler->post(false);



            }
            break;
            case 'DELETE':
            $data = $upload_handler->delete(false);
            break;
            default:
    		//header('HTTP/1.1 405 Method Not Allowed');
        }
        return $data;

    }

    protected function processView($data)
    {
        $json = json_decode(json_encode($data),TRUE);
        $view = $this->view($json, 200)
        ;
        return $this->handleView($view);
    }

    protected function createDocument($entity, $file)
    {
                $doc = new Document();
                $em = $this->getEntityManager();

                $doc->setName($file->name);
                $doc->setIdObject($entity->getId());
                $doc->setOwner($this->getLoggedUser());
                $doc->setClass($entity->__toString());
                $doc->setSize($file->size);
                $em->persist($doc);
                $em->flush();

                return $doc;
    }


}
