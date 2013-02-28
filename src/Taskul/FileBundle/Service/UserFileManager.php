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

	protected $idObject, $user, $class, $entity;

	function __construct(FileUploader $fu, EntityManager $em, Manager $aclManager, $options)
	{
		$this->fu = $fu;
		$this->em = $em;
		$this->aclManager = $aclManager;
		$this->basePath = $options['base_path'];
		$this->original = $options['original']['folder'];

	}

	/**
	 * Copia los ficheros de una entidad a una localizacion temporal para poder trabajar sobre ellos
	 *
	 * @param  [int] $id     [Identificador del formulario para almacenar temporalmente los ficheros]
	 * @param  [User] $user   [Usuario propietario de los ficheros]
	 * @param  [Object] $entity [Entidad a la que van asociados los ficheros]
	 * @return [void]         []
	 */
	public function syncToTmp($id, $user, $entity)
	{
		// Si hay algun fichero marcado para borrar pero no se ha borrado
		// porque el usuario no ha continuado se restaura
		$this->restoreDeletedItems($user);
		$fromFolder = $this->obtainFolder($entity);
		$this->syncFiles($fromFolder,'tmp/attachments/' . $id, FALSE);
	}


/**
 * Mueve los ficheros de una entidad desde una localizacion temporal a la final para almacenarlos
 * @param  [int] $id      [Identificador del formulario para localizar los ficheros almacenados temporalmente]
 * @param  [User] $user    [Usuario propietario de los ficheros]
 * @param  [Object] $entity  [Entidad a la que van asociados los ficheros]
 * @param  array  $members [Usuarios que tienen acceso a la visualizacion de los ficheros]
 * @return [bool]          []
 */
public function syncFromTmp($id, $user,$entity,$members=array())
{
	// Asignamos las variables relacionadas con los documentos
	// clase, usuario y id entidad
	$this->assignEntityVars($entity);
	$this->assignUserVar($user);

	// Si hay algun fichero marcado para borrar pero no se ha
	// borrado, el usuario
	// no ha continuado se restaura
	$this->restoreDeletedItems($this->user);

	$toFolder = $this->obtainFolder($this->entity);


	// Movemos los ficheros de la localizacion temporal
	//a la definitiva
	$this->syncFiles('/tmp/attachments/' . $id, $toFolder);

	$files = $this->fu->getFiles(
		array('folder'=> $toFolder)
		);

	// Almacenamos nombre y tamanios de los ficheros que hay actualmente en la bbdd
	list($docsNames,$docsSizes) = $this->getNameSizesFiles();

	// Almacenamos los nuevos documentos creados para darles
	// permiso a los miembros de la tarea a verlos.
	$docs = $this->createFiles($files,$docsNames);

	// Actualizamos los tamaños de los ficheros
	// por si alguno ha sido sobreescrito
	$docsNames = $this->updateFilesSizes($files,$docsNames,$docsSizes);

	// Eliminamos los documentos que no esten
	$this->removeDocuments($docsNames);

	$this->em->flush();
	// Le damos permisos a los documentos nuevos
	$this->grantAclDocsMembers($docs,$members);

}

/**
 * Obtine el listado de ficheros de una localizacion
 * @param  array  $options [opciones del servicio file manager]
 * @return [array]          [Listado de ficheros que existe]
 */
public function getFiles($options=array()){
	return $this->fu->getFiles($options);
}

/**
 * Carga o elimina un fichero en el servidor comprobando si el usuario tiene la quota excedida
 * @param  [User] $user    [Usuario asociado al fichero]
 * @param  array  $options [opciones de carga del fichero]
 * @return [json]          [estado de la carga/eliminacion del fichero]
 *
 * @TODO: hay que arreglar lo de los idiomas
 */
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
/**
 * [Obtiene todos los documentos asociados a una entidad]
 * @param  [Object] $entity [Entitdad asociada a los ficheros]
 * @return [array]         [Listado de los nombres de los documentos]
 */
public function getEntityFiles($entity){
	$this->assignEntityVars($entity);
	list($docsNames,$docsSizes) = $this->getNameSizesFiles();
	return $docsNames;

}



/**
 * Elimina todos los ficheros asociados a un entidad
 * Elimina las acls asociadas a los ficheros
 * @param  [Object] $entity [entidad a la que estan asociados los ficheros]
 * @return [json]         [estado del borrado]
 */
public function removeUserFiles($entity)
{
	$toFolder = $this->obtainFolder($entity);
	$this->assignEntityVars($entity);
	list($docsNames,$docsSizes) = $this->getNameSizesFiles();
	$this->removeDocuments($docsNames);
	return $this->fu->removeFiles(array('folder' =>$toFolder));
}

/**
 * Restaura los ficheros marcados para eliminar de un usuario
 * @param  [USer] $user [usuario propietario de los ficheros]
 * @return [void]
 */
protected function restoreDeletedItems($user){
	$documents = $this->em->getRepository('FileBundle:Document')->findBy(array('owner'=>$user,'markToDelete'=>TRUE));
	foreach($documents as $d){
		$d->setMarkToDelete(FALSE);
		$this->em->persist($d);
	}
	$this->em->flush();
}

/**
 * Mueve ficheros de un directorio a otro
 * @param  [string] $fromFolder [directorio origen]
 * @param  [string] $toFolder   [directorio destino]
 * @param  [bool] [Borrar directorio de origen]
 * @return [void]
 */
protected function syncFiles($fromFolder, $toFolder, $deleteFrom=TRUE){
	$this->fu->syncFiles(
		array('from_folder' => $fromFolder,
			'to_folder' => $toFolder,
			'remove_from_folder' => $deleteFrom,
			'create_to_folder' => true));

}
/**
 * Obtine el tamaño y el nombre de los ficheros asociados a una entidad
 * @return [array] [0] nombres de los ficheros [1] Tamaño de los ficheros
 */
protected function getNameSizesFiles(){

	$documents = $this->em->getRepository('FileBundle:Document')->findBy(array('class'=>$this->class,'idObject'=> $this->idObject));

	$docsNames = $docsSizes = array();
		// Almacenamos nombre y tamanios de los ficheros que hay actualmente en la bbdd
	$i=0;
	foreach ($documents as $doc){
		$docsNames[$i] = $doc->getName();
		$docsSizes[$i] = $doc->getSize();
		$i++;
	}
	return array($docsNames,$docsSizes);
}
/**
 * Obtine el nombre de la clase de la entidad
 * @param  [Object] $entity [entidad a obtener el nombre de la clase]
 * @return [string]         [clase]
 */
protected function getClassName($entity){
	return $entity->__toString();
}
/**
 * Obtiene el nombre del directorio donde almacenar los ficheros de una entidad
 * @param  [Object] $entity [entidad]
 * @return [string]         [nombre del directorio]
 */
protected function obtainFolder($entity)
{
	$class = $this->getClassName($entity);
	return '/attachments/'.$class. '/' . $entity->getId();
}
/**
 * Crea un documento
 * @param  [string] $fileName [nombre del documento]
 * @param  [int] $fileSize [tamaño del documento]
 * @return [Object]           [documento]
 */
protected function createDocument($fileName, $fileSize){
	$doc = new Document();
	$doc->setName($fileName);
	$doc->setIdObject($this->idObject);
	$doc->setOwner($this->user);
	$doc->setClass($this->class);
	$doc->setSize($fileSize);
	$this->em->persist($doc);
	return $doc;
}

/**
 * Obtinene el tamaño de un fichero del sistema de ficheros
 * @param  [string] $folder [directorio origen]
 * @param  [string] $file   [nombre del fichero]
 * @return [int]         [tamaño del fichero]
 */
protected function getFileSize($folder,$file)
{
	return filesize($this->basePath.$folder.'/'.$this->original.'/'.$file);
}

/**
 * Crea las acls de visualizacion de documentos para un grupo de usuarios
 * @param  [array] $docs    [array de objectos]
 * @param  [array] $members [array de usuarios]
 * @return [void]          []
 */
protected function grantAclDocsMembers($docs,$members){
	foreach ($docs as $d){
		$this->aclManager->grant($d,$members);
	}

}
/**
 * Elimina documentos por su nombre
 * Elimina las acls asociadas a estos documentos
 * @param  [array] $docsNames [array de noombres de documentos]
 * @return [void]            []
 */
protected function removeDocuments($docsNames){
	foreach($docsNames as $value){
		$doc = $this->em->getRepository('FileBundle:Document')->findOneBy(array('class'=>$this->class,'idObject'=> $this->idObject,'name'=>$value));
		$this->aclManager->revokeAll($doc);
		$this->em->remove($doc);
	}
}

	/**
	 * Asigna variables relacionadas con una entidad
	 * @param  [Object] $entity [entidad]
	 * @return [void]         []
	 */
	protected function assignEntityVars($entity){
		$this->class = $this->getClassName($entity);
		$this->idObject = $entity->getId();
		$this->entity = $entity;
	}
/**
 * Asigna variables reliacionadas con el usuario
 * @param  [User] $user [usuario]
 * @return [void]       []
 */
protected function assignUserVar($user){
	$this->user = $user;
}
/**
 * Crea ficheros, comprobando antes que no exista
 * @param  [array] $files     [array de ficheros a crear]
 * @param  [array] $docsNames [array de nombres de ficheros existentes]
 * @return [array]            [array de objectos creados]
 */
protected function createFiles($files,$docsNames){
	$docs = array();
	$toFolder = $this->obtainFolder($this->entity);
	foreach($files as $fileName){
		if(FALSE === array_search($fileName, $docsNames)) {
			$fileSize = $this->getFileSize($toFolder, $fileName);
			$docs[] = $this->createDocument($fileName, $fileSize);

		}

	}
	return $docs;
}
/**
 * Actualiza el tamaño de ficheros sobreescritos
 * Devuelve un array de los ficheros que no se encuentran dados de alta.
 * @param  [array] $files     [array de ficheros a comprobar]
 * @param  [array] $docsNames [array de nombres ficheros existentes]
 * @param  [array] $docsSizes [array de tamaños de ficheros existentes]
 * @return [array]            [array de ficheros que no se encuentran dados de alta]
 */
protected function updateFilesSizes($files,$docsNames,$docsSizes){
	$toFolder = $this->obtainFolder($this->entity);
	foreach($files as $fileName){
		$fileSize = $this->getFileSize($toFolder, $fileName);
		$id = array_search($fileName, $docsNames);
		if(FALSE !== $id && $docsSizes[$id] !== $fileSize){
			$doc = $this->em->getRepository('FileBundle:Document')->findOneBy(array('class'=>$this->class,'idObject'=> $this->idObject,'name'=>$fileName));
			$doc->setSize($fileSize);
			$this->em->persist($doc);
			unset($docsNames[$id]);
		}else if(FALSE !== $id){
			unset($docsNames[$id]);
		}
	}
	return $docsNames;
}


}
