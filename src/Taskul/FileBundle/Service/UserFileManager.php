<?php

namespace Taskul\FileBundle\Service;

use PunkAve\FileUploaderBundle\Services\FileUploader;
use Taskul\FileBundle\Entity\Document;
use Doctrine\ORM\EntityManager;
use Taskul\UserBundle\Security\Manager;
use Taskul\FileBundle\Documentable\Documentable;
use DoctrineExtensions\Taggable\Taggable;

class UserFileManager
{
	protected $em;
	protected $aclManager;
	protected $quota;

	protected $idObject, $user, $class, $entity;

	function __construct(EntityManager $em, Manager $aclManager, $quota)
	{
		$this->em = $em;
		$this->aclManager = $aclManager;
		$this->quota = $quota;

	}


	public function deleteDocument(Document $document)
	{
		$this->aclManager->revokeAll($document);
		$this->em->remove($document);
        $this->em->flush();
        return $this;
	}


	public function createDocument(Documentable $entity, $user, $file)
	{
		if($entity instanceof Documentable) { /* Nos aseguraos que se implemente el metodo getclassname */

			$doc = new Document();
	        $doc->setName($file->name);
	        $doc->setIdObject($entity->getId());
	        $doc->setOwner($user);
	        $doc->setClass($entity->getClassName()); /*@Importante ponerlo en todas la entidades */
	        $doc->setSize($file->size);
	        $this->em->persist($doc);
	        $this->em->flush();

			$this->aclManager->grant($doc,$entity->getMembers());

	        return $doc;
    	}else
    		return FALSE;
	}

	public function getUserQuota($user)
	{
		return $this->em->getRepository('FileBundle:Document')->sums($user);
	}

	public function checkUserQuota($user, $file)
	{
		$sumSizes = $this->getUserQuota($user);
        if($sumSizes[1] + $file->size > $this->quota) //@todo: no se porque el indice es 1 y no 0
        	return true;
        return false;
	}

	public function getPercentQuota($user)
	{
		$sumSizes = $this->getUserQuota($user);
		$percent = round(($sumSizes[1] / $this->quota)*100);//@todo: no se porque el indice es 1 y no 0
        return $percent;
	}

}
