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
        $fileManager = $this->getFileManager();
        $user = $this->getLoggedUser();

		$task = $this->checkGrant($id,'VIEW');
		$id = $task->getId();

		$data['documents'] = $em->getRepository('FileBundle:Document')->findBy(array('class' => $task->getClassName(),'idObject'=>$id));
		$data['taskId'] = $id;
        $data['delete_id'] = -1;
        $data['delete_form'] = $this->createDeleteForm(-1)->createView();
        $data['current_quota'] = $fileManager->getPercentQuota($user);

		$view = $this->view($data, 200)
		->setTemplate("TaskBundle:Task:api/listFiles.html.twig")
		;
		return $this->handleView($view);

    } // "api_get_task_files"   [GET] /api/tasks/{id}/files

    public function postAction($id)
    {
    	$task = $this->checkGrant($id,'VIEW');
        $aclManager = $this->getAclManager();
        $fileManager = $this->getFileManager();
        $user = $this->getLoggedUser();


    	$data = $this->processRequest($user->getCodeUpload());
        $file = $data['files'][0];

        if(isset($file->error)){
            $data = array('success'=>FALSE,'message'=>$file->error,'statusCode' => 400);
        }else if($fileManager->checkUserQuota($user, $file)){
            unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$file->name);
            $data = array('success'=>FALSE,'message'=>'Quota excedida','statusCode' => 400);
        }else{
            $doc = $fileManager->createDocument($task, $user, $file);
            if($doc){
                $file->id = $doc->getId();
                $file->taskid = $task->getId();

                $actionManager = $this->get('taskul_timeline.action_manager.orm');
                $actionManager->handle($user,'POST',$doc,$task);
            }
            else{
                $data = array('success'=>FALSE,'message'=>'WTF','statusCode' => 400);
            }
        }

        return $this->processView($data,$data['statusCode']);

    } // "api_post_task_files"   [POST] /api/tasks/{id}/files

    public function deleteAction($idTask, $idDocument)
    {
        $aclManager = $this->getAclManager();
        $em = $this->getEntityManager();
        $fileManager = $this->getFileManager();
        $user = $this->getLoggedUser();

        $task = $this->checkGrant($idTask,'VIEW');
        $document = $this->checkGrant($idDocument,'DELETE','FileBundle:Document');

        $_GET['file'] = $document->getName(); // Para el handler del upload
        $data = $this->processRequest($user->getCodeUpload());

        if(isset($data['error']))
            $data = array('success'=>FALSE,'message'=>$data['error']);
        else {
            $fileManager->deleteDocument($document);
        }

        return $this->processView($data,$data['statusCode']);
    } // "api_delete_task_file" [DELETE] /api/tasks/{idtask}/files/{iddocument}

    protected function processRequest($codeUpload)
    {

    	$upload_handler = new \Taskul\FileBundle\BlueImp\UploadHandler(
    		array(
    			'upload_dir' => $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$codeUpload.'/',
    			'upload_url' => $this->getRequest()->getUri().'/',
    			'script_url' => $this->getRequest()->getUri()
    			), false);



    	$data = array();

    	switch ($_SERVER['REQUEST_METHOD']) {
    		case 'OPTIONS':
    		break;
    		case 'HEAD':
    		case 'GET':
    		$data = $upload_handler->get(false);
            $data['success'] = TRUE;
            $data['message'] = 'Operacion relaizada correctamente';
            $data['statusCode'] = 200;
    		break;
    		case 'POST':
    		if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
    			$data = $upload_handler->delete(false);
    		} else {
    			$data = $upload_handler->post(false);
            }
            $data['success'] = TRUE;
            $data['message'] = 'Operacion relaizada correctamente';
            $data['statusCode'] = 200;
            break;
            case 'DELETE':
            $data = $upload_handler->delete(false);
            break;
            default:
    		//header('HTTP/1.1 405 Method Not Allowed');
            $data['success'] = FALSE;
            $data['message'] = 'Method Not Allowed';
            $data['statusCode'] = 405;
        }
        if(isset($data['files'])) {
            for ($i=0; $i < count($data['files']); $i++) {
                if(property_exists($data['files'][$i],'url'))
                    unset($data['files'][$i]->url);
                if(property_exists($data['files'][$i],'delete_url'))
                    unset($data['files'][$i]->delete_url);
            }
        }
        return $data;

    }

}
