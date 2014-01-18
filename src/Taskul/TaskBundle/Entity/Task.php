<?php

namespace Taskul\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Taskul\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Taskul\TaskBundle\DBAL\Types\TaskStatusType;
use Fresh\Bundle\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Taskul\FileBundle\Documentable\Documentable;
use Taskul\MainBundle\Entity\BaseEntity;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="Taskul\TaskBundle\Entity\Repository\TaskRepository")
 *
 */
class Task extends BaseEntity implements Documentable {

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnd", type="datetime", nullable=true)
     */
    private $dateEnd;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Taskul\UserBundle\Entity\User", inversedBy="tasksMember")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $members;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="Taskul\UserBundle\Entity\User", inversedBy="ownTasks")
     */
    private $owner;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Taskul\TaskBundle\Entity\Tag", inversedBy="tasks")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", nullable=true)
     */
    private $tags;

    /**
     * [$status description]
     * @var [type]
     *
     * @DoctrineAssert\Enum(entity="Taskul\TaskBundle\DBAL\Types\TaskStatusType")
     * @ORM\Column(name="status", type="TaskStatusType", nullable=false)
     *
     * @Assert\NotBlank()
     *
     */
    private $status;


    private $className;

    /**
     * Add tag
     *
     * @param \Taskul\TaskBundle\Entity\Tag $tag
     * @return Task
     */
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;
        return $this;
    }

    /**
     * Remove tag
     *
     * @param \Taskul\TaskBundle\Entity\Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {

        return $this->tags;
    }

    /**
     * Add member
     *
     * @param \Taskul\UserBundle\Entity\User $member
     * @return Task
     */
    public function addMember(\Taskul\UserBundle\Entity\User $member)
    {
        $this->members[] = $member;
        return $this;
    }

    /**
     * Remove member
     *
     * @param \Taskul\UserBundle\Entity\User $member
     */
    public function removeMember(\Taskul\UserBundle\Entity\User $member)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {

        return $this->members;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Task
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Task
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     * @return Task
     */
    public function setDateEnd($dateEnd) {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd() {
        return $this->dateEnd;
    }

    /**
     * Set owner
     *
     * @param \Taskul\UserBundle\Entity\User $owner
     * @return Task
     */
    public function setOwner(User $owner = null) {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Taskul\UserBundle\Entity\User
     */
    public function getOwner() {
        return $this->owner;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getClassName()
    {
        if(null === $this->className) {
            $class = explode('\\', __CLASS__);
            $this->setClassName(end($class));
        }
        return $this->className;
    }

    public function setClassName($className)
    {
        $this->className=$className;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->members = new ArrayCollection();
        $this->tags = new ArrayCollection();

        $this->setStatus('inprogress'); // Se le pone un estado por defecto
    }

    /**
     * Set status
     *
     * @param TaskStatusType $status
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;


        return $this;
    }

    /**
     * Get status
     *
     * @return TaskStatusType
     */
    public function getStatus()
    {
        return $this->status;
    }

    public static function getEntityName()
    {
        return 'TaskBundle:Task';
    }

    public function getMembersWithoutOwner()
    {
        $members = new ArrayCollection();
        foreach ($this->members as $m) {
            if ($m->getId() != $this->owner->getId()) {
                $members[] = $m;
            }
        }
        return $members;
    }
}
