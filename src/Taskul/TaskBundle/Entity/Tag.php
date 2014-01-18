<?php

namespace Taskul\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Taskul\MainBundle\Entity\BaseEntity;
use Taskul\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Tag
 *
 * @ORM\Table(name="Tag")
 * @ORM\Entity(repositoryClass="Taskul\TaskBundle\Entity\Repository\TagRepository")
 * @ExclusionPolicy("all")
 */
class Tag extends BaseEntity
{
    /**
     * @var TaskBundle:Task
     *
     * @ORM\ManyToMany(targetEntity="\Taskul\TaskBundle\Entity\Task", mappedBy="tags")
     * @ORM\JoinTable(name="tasks_tags")
     *
     */
    private $tasks;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="Taskul\UserBundle\Entity\User", inversedBy="tags")
     */
    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->tasks = new ArrayCollection();
    }
    /**
     * Add task
     *
     * @param \Taskul\TaskBundle\Entity\Task $task
     * @return User
     */
    public function addTasks(Task $task)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \Taskul\TaskBundle\Entity\Task $task
     */
    public function removeTasks(Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Expose
     */
    private $name;

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
     * Set user
     *
     * @param \Taskul\UserBundle\Entity\User $user
     * @return Tag
     */
    public function setUser(User $user) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Taskul\UserBundle\Entity\User
     */
    public function getUset() {
        return $this->user;
    }
}
