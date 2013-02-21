<?php

namespace Taskul\FileBundle\Service;

use PunkAve\FileUploaderBundle\Services\FileUploader;
use Taskul\FileBundle\Entity\Document;
use Doctrine\ORM\EntityManager;
use Taskul\UserBundle\Security\Manager;

class UserFileManager
{
	protected $fu;
	protected $em;
	protected $basePath;
	protected $original;
	protected $aclManager;

	function __construct(FileUploader $fu, EntityManager $em, Manager $aclManager, $options)
	{
		$this->fu = $fu;
		$this->em = $em;
		$this->aclManager = $aclManager;
		$this->basePath = $options['base_path'];
		$this->original = $options['original']['folder'];

	}

	public function syncFiles($id,$user,$entity)
	{
		// Si hay algun fichero marcado para borrar pero no se ha borrado por el usuario
		// no ha continuado se restaura
		$this->restoreDeletedItems($user);
		$fromFolder = $this->toFolder($entity);
		$this->fu->syncFiles(
			array('from_folder' => $fromFolder,
				'to_folder' => 'tmp/attachments/' . $id,
				'create_to_folder' => true));
	}

	public function syncUserFiles($id, $user,$entity,$members=array())
	{

		// Si hay algun fichero marcado para borrar pero no se ha
		// borrado, el usuario
		// no ha continuado se restaura
		$this->restoreDeletedItems($user);


		$toFolder = $this->toFolder($entity);

		$class = $this->getClassName($entity);
		$idObject = $entity->getId();
		$this->fu->syncFiles(
			array('from_folder' => '/tmp/attachments/' . $id,
				'to_folder' => $toFolder,
				'remove_from_folder' => true,
				'create_to_folder' => true));


		$files = $this->fu->getFiles(
			array('folder'=> $toFolder)
			);

		$documents = $this->em->getRepository('FileBundle:Document')->findBy(array('class'=>$class,'idObject'=> $idObject));

		$docsNames = $docsSizes = array();

		// Alamacenamos nombre y tamanios de los ficheros que hay actualmente en la bbdd
		$i=0;
		foreach ($documents as $doc){
			$docsNames[$i] = $doc->getName();
			$docsSizes[$i] = $doc->getSize();
			$i++;
		}

		// Almacenamos los nuevos documentos creados para darles
		// permiso a los miembros de la tarea a verlos.
		$docs = array();

		// Para los ficheros subidos comprobamos cuales estan en la bbdd
		// y los que no se crean
		foreach($files as $file){

			$fileSize = filesize($this->basePath.$toFolder.'/'.$this->original.'/'.$file);

			$id = array_search($file, $docsNames);
			if(FALSE === $id) {
				$doc = new Document();
				$doc->setName($file);
				$doc->setIdObject($idObject);
				$doc->setOwner($user);
				$doc->setClass($class);
				$doc->setSize($fileSize);
				$this->em->persist($doc);

				$docs[] = $doc;

			}else if($docsSizes[$id] !== $fileSize){
				$doc = $this->em->getRepository('FileBundle:Document')->findOneBy(array('class'=>$class,'idObject'=> $idObject,'name'=>$file));
				$doc->setSize($fileSize);
				$this->em->persist($doc);
				unset($docsNames[$id]);
			}else{
				unset($docsNames[$id]);
			}

		}

		// Eliminamos los documentos que no esten
		foreach($docsNames as $value){
			$doc = $this->em->getRepository('FileBundle:Document')->findOneBy(array('class'=>$class,'idObject'=> $idObject,'name'=>$value));
			$this->em->remove($doc);
		}

		$this->em->flush();
		// Le damos permisos a los documentos nuevos
		foreach ($docs as $d){
			$this->aclManager->grant($d,$members);
		}

	}

	public function getFiles($options=array()){
		return $this->fu->getFiles($options);
	}

// @TODO: hay que arreglar lo de los idiomas
	public function handleFileUpload($user, $options=array()){
		if($_SERVER['REQUEST_METHOD'] === 'POST' && (! isset($_REQUEST['_method']) || (isset($_REQUEST['_method']) && $_REQUEST['_method'] !== 'DELETE'))){
			if(isset($_FILES['files']['size'][0])){
				if(! isset($options['max_upload_data']))
					throw new \Exception("max_upload_data option looks empty, bailing out");

				$fileSize = $_FILES['files']['size'][0];
				$sum = $this->em->getRepository('FileBundle:Document')->sums($user);
				$max = $options['max_upload_data'];
			// @TODO: no se xq el indice es 1
				if(($sum[1] + $fileSize) <= $max)
					return $this->fu->handleFileUpload($options);
				else {
					unlink($_FILES['files']['tmp_name'][0]);
					throw new \Exception("Max Quota");
				}
			}else{
				throw new \Exception("File not found");
			}
		}else{
			/* @TODO: aqui hay que marcar el registro para borrar y no tenerlo en cuenta en las sumas */

			return $this->fu->handleFileUpload($options);
		}

	}

	public function getEntityFiles($entity){
		$class = $this->getClassName($entity);
		$idObject = $entity->getId();
		$documents = $this->em->getRepository('FileBundle:Document')->findBy(array('class'=>$class,'idObject'=> $idObject));

		$docsNames = array();

		foreach($documents as $doc){
			$docsNames[] = $doc->getName();
		}
		return $docsNames;

	}


	private function getClassName($entity){
		return $entity->__toString();
	}

	private function toFolder($entity)
	{
		$class = $this->getClassName($entity);
		return '/attachments/'.$class. '/' . $entity->getId();
	}

	public function removeUserFiles($user,$entity,$members=array())
	{
		$toFolder = $this->toFolder($entity);
		if(count($members) >0)
			foreach($members as $m)
				$this->fu->removeFiles(array('folder' =>$toFolder));

			return $this->fu->removeFiles(array('folder' =>$toFolder));
	}
	private function restoreDeletedItems($user){
			$documents = $this->em->getRepository('FileBundle:Document')->findBy(array('owner'=>$user,'markToDelete'=>TRUE));
			foreach($documents as $d){
				$d->setMarkToDelete(FALSE);
				$this->em->persist($d);
			}
			$this->em->flush();
	}


}
