<?php

namespace Taskul\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Taskul\MainBundle\Entity\BaseEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task Period
 *
 * @ORM\Table(name="task_period")
 * @ORM\Entity(repositoryClass="Taskul\TaskBundle\Entity\Repository\PeriodRepository") *
 */
class Period extends BaseEntity {

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateBegin", type="datetime")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $begin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnd", type="datetime")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $end;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="Taskul\UserBundle\Entity\User", inversedBy="ownPeriods")
     */
    private $owner;

    private $task;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text")
     * @Assert\NotBlank()
     */
    private $note;

    /**
     * Set begin
     *
     * @param \DateTime $begin
     * @return Period
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * Get begin
     *
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return Period
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set owner
     *
     * @param \Taskul\UserBundle\Entity\User $owner
     * @return Period
     */
    public function setOwner(\Taskul\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Taskul\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set task
     *
     * @param \Taskul\TaskBundle\Entity\Task $task
     * @return Period
     */
    public function setTask(\Taskul\TaskBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Taskul\TaskBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Task
     */
    public function setNote($note) {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote() {
        return $this->note;
    }
}
