<?php

namespace Taskul\TimelineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="notification_message")
 * @ORM\HasLifecycleCallbacks()
 */
class NotificationMessage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="noti_url", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    protected $notiUrl;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="Taskul\UserBundle\Entity\User", inversedBy="notiMessages")
     */
    private $to;

    /**
     * @ORM\Column(name="is_read", type="boolean")
     */
    protected $read;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    protected $context;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     *
     * @ORM\Column(name="id_entity", type="integer")
     */
    private $idEntity;
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set notiUrl
     *
     * @param string $notiUrl
     * @return NotificationMessage
     */
    public function setNotiUrl($notiUrl)
    {
        $this->notiUrl = $notiUrl;

        return $this;
    }

    /**
     * Get notiUrl
     *
     * @return string
     */
    public function getNotiUrl()
    {
        return $this->notiUrl;
    }

    /**
     * Set read
     *
     * @param \bool $read
     * @return NotificationMessage
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Get read
     *
     * @return \bool
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set context
     *
     * @param string $context
     * @return NotificationMessage
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set to
     *
     * @param \Taskul\UserBundle\Entity\User $to
     * @return NotificationMessage
     */
    public function setTo(\Taskul\UserBundle\Entity\User $to = null)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return \Taskul\UserBundle\Entity\User
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->updated = new \DateTime();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Task
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Task
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set idEntity
     *
     * @param integer $idEntity
     * @return NotificationMessage
     */
    public function setIdEntity($idEntity)
    {
        $this->idEntity = $idEntity;

        return $this;
    }

    /**
     * Get idEntity
     *
     * @return integer
     */
    public function getIdEntity()
    {
        return $this->idEntity;
    }
}