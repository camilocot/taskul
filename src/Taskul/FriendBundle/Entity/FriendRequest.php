<?php

namespace Taskul\FriendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


//@ORM\Table(name="friend_request", uniqueConstraints={@ORM\UniqueConstraint(name="request_idx", columns={"from_id", "email"})})
/**
 * FriendtRequest
 *
 *
 * @ORM\Entity(repositoryClass="Taskul\FriendBundle\Entity\Repository\FriendRequestRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="friend_request")
 */
class FriendRequest {

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
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="hash", type="string", length=255, nullable=true)
     */
    private $hash;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="\Taskul\UserBundle\Entity\User", inversedBy="friReqSend")
     */
    private $from;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="\Taskul\UserBundle\Entity\User", inversedBy="friReqRecb")
     * @ORM\JoinColumn(name="to_id", referencedColumnName="id", nullable=true)
     */
    private $to;

    /**
     * @var string Identificador de contacto en fb
     * @ORM\Column(name="fb_id", type="string", length=255, nullable=true)
     */
    private $fbid;

     /**
     * @var string id de la solicitud enviada
     * @ORM\Column(name="fb_request_id", type="string", length=255, nullable=true)
     */
    private $fbrequestid;

    /**
     * @ORM\Column(type="boolean")
     * @var type Boolean
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $addtionalData;


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

    public function __construct() {
        $this->active = FALSE;
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return ContactRequest
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ContactRequest
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set from
     *
     * @param \Taskul\UserBundle\Entity\User $from
     * @return ContactRequest
     */
    public function setFrom(\Taskul\UserBundle\Entity\User $from = null) {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return \Taskul\UserBundle\Entity\User
     */
    public function getFrom() {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param \Taskul\UserBundle\Entity\User $to
     * @return ContactRequest
     */
    public function setTo(\Taskul\UserBundle\Entity\User $to = null) {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return \Taskul\UserBundle\Entity\User
     */
    public function getTo() {
        return $this->to;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return FriendRequest
     */
    public function setHash($hash) {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash() {
        return $this->hash;
    }


    /**
     * Set active
     *
     * @param boolean $active
     * @return FriendRequest
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set fbid
     *
     * @param string $fbid
     * @return FriendRequest
     */
    public function setFbid($fbid)
    {
        $this->fbid = $fbid;

        return $this;
    }

    /**
     * Get fbid
     *
     * @return string
     */
    public function getFbid()
    {
        return $this->fbid;
    }

    /**
     * Set fbrequestid
     *
     * @param string $fbrequestid
     * @return FriendRequest
     */
    public function setFbrequestid($fbrequestid)
    {
        $this->fbrequestid = $fbrequestid;

        return $this;
    }

    /**
     * Get fbrequestid
     *
     * @return string
     */
    public function getFbrequestid()
    {
        return $this->fbrequestid;
    }

    /**
     * Set addtionalData
     *
     * @param string $addtionalData
     * @return FriendRequest
     */
    public function setAddtionalData($addtionalData)
    {
        $this->addtionalData = base64_encode(serialize($addtionalData));

        return $this;
    }

    /**
     * Get addtionalData
     *
     * @return string
     */
    public function getAddtionalData()
    {
        return unserialize(base64_decode($this->addtionalData));
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

}