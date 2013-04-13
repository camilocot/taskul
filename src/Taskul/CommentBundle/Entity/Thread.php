<?php

namespace Taskul\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Thread as BaseThread;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="thread")
 */
class Thread extends BaseThread
{
    /**
     * @var string $id
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;

	/**
     * @var integer
     *
     * @ORM\Column(name="entity_id", type="integer")
     */
    protected $entityId;
    /**
     * @var string
     *
     * @ORM\Column(name="entity_type", type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    protected $entityType;



    /**
     * Set id
     *
     * @param string $id
     * @return Thread
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     * @return Thread
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set entityType
     *
     * @param string $entityType
     * @return Thread
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;

        return $this;
    }

    /**
     * Get entityType
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }
}