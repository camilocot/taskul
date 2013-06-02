<?php

namespace Taskul\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Taskul\TaskBundle\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\Model\ParticipantInterface;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 *
 * @ExclusionPolicy("all")
 */
class User extends BaseUser implements ParticipantInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct() {
        parent::__construct();
        $this->ownTasks = new ArrayCollection();
        $this->friReqRecb = new ArrayCollection();
        $this->friReqSend = new ArrayCollection();
        $this->friendWithMe = new ArrayCollection();
        $this->myFriends = new ArrayCollection();
        $this->tasksMember = new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255,nullable=true)
     *
     * @Expose
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255,nullable=true)
     *
     * @Expose
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255, nullable=true)
     */
    protected $facebookId;

    /**
     * @var TaskBundle:Task
     *
     * @ORM\OneToMany(targetEntity="\Taskul\TaskBundle\Entity\Task", mappedBy="owner")
     * @return type
     */
    protected $ownTasks;

    /**
     * @var FileBundle:Document
     *
     * @ORM\OneToMany(targetEntity="\Taskul\FileBundle\Entity\Document", mappedBy="owner")
     * @return type
     */
    protected $documents;

    /**
     * @var FriendBundle:FriendRequest
     *
     * @ORM\OneToMany(targetEntity="\Taskul\FriendBundle\Entity\FriendRequest", mappedBy="from")
     * @return type
     */
    protected $friReqSend;

    /**
     * @var FriendBundle:FriendRequest
     *
     * @ORM\OneToMany(targetEntity="\Taskul\FriendBundle\Entity\FriendRequest", mappedBy="to")
     * @return type
     */
    protected $friReqRecb;


    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myFriends")
     */
    private $friendsWithMe;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="friends",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
     */
    private $myFriends;

    /**
     * @var TaskBundle:Task
     *
     * @ORM\ManyToMany(targetEntity="\Taskul\TaskBundle\Entity\Task", mappedBy="members")
     * @ORM\JoinTable(name="tasks_memebers")
     *
     * @return type
     */
    private $tasksMember;
    /**
     * [$codeUpload description]
     * @var [type]
     *
     * @ORM\Column(name="code_upload", type="string", length=255, nullable=true)
     */
    private $codeUpload;


    public function serialize() {
        return serialize(array($this->facebookId, parent::serialize()));
    }

    public function unserialize($data) {
        list($this->facebookId, $parentData) = unserialize($data);
        parent::unserialize($parentData);
    }

    /**
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    /**
     * Get the full name of the user (first + last name)
     * @return string
     */
    public function getFullName() {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    /**
     * @param string $facebookId
     * @return void
     */
    public function setFacebookId($facebookId) {
        $this->facebookId = $facebookId;
        $this->setUsername($facebookId);
        $this->salt = '';
    }

    /**
     * @return string
     */
    public function getFacebookId() {
        return $this->facebookId;
    }

    /**
     * @param Array
     */
    public function setFBData($fbdata) {
        if (isset($fbdata['id'])) {
            $this->setFacebookId($fbdata['id']);
            $this->addRole('ROLE_FACEBOOK');
        }
        if (isset($fbdata['first_name'])) {
            $this->setFirstname($fbdata['first_name']);
        }
        if (isset($fbdata['last_name'])) {
            $this->setLastname($fbdata['last_name']);
        }
        if (isset($fbdata['email'])) {
            $this->setEmail($fbdata['email']);
        }
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
     * Add ownTasks
     *
     * @param \Taskul\TaskBundle\Entity\Task $ownTasks
     * @return User
     */
    public function addOwnTask(Task $ownTasks) {
        $this->ownTasks[] = $ownTasks;

        return $this;
    }

    /**
     * Remove ownTasks
     *
     * @param \Taskul\TaskBundle\Entity\Task $ownTasks
     */
    public function removeOwnTask(Task $ownTasks) {
        $this->ownTasks->removeElement($ownTasks);
    }

    /**
     * Get ownTasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnTasks() {
        return $this->ownTasks;
    }

    /**
     * Add friReqSend
     *
     * @param \Taskul\FriendBundle\Entity\FriendRequest $friReqSend
     * @return User
     */
    public function addFriReqSend(\Taskul\FriendBundle\Entity\FriendRequest $friReqSend) {
        $this->contFriSend[] = $friReqSend;

        return $this;
    }

    /**
     * Remove friReqSend
     *
     * @param \Taskul\FriendBundle\Entity\FriendRequest $friReqSend
     */
    public function removeFriReqSend(\Taskul\FriendBundle\Entity\FriendRequest $friReqSend) {
        $this->friReqSend->removeElement($friReqSend);
    }

    /**
     * Get friReqSend
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFriReqSend() {
        return $this->friReqSend;
    }

    /**
     * Add friReqRecb
     *
     * @param \Taskul\FriendBundle\Entity\FriendRequest $friReqRecb
     * @return User
     */
    public function addFriReqRecb(\Taskul\FriendBundle\Entity\FriendRequest $friReqRecb) {
        $this->friReqRecb[] = $friReqRecb;

        return $this;
    }

    /**
     * Remove friReqRecb
     *
     * @param \Taskul\FriendBundle\Entity\FriendRequest $friReqRecb
     */
    public function removeFriReqRecb(\Taskul\FriendBundle\Entity\FriendRequest $friReqRecb) {
        $this->friReqRecb->removeElement($friReqRecb);
    }

    /**
     * Get friReqRecb
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFriReqRecb() {
        return $this->friReqRecb;
    }

    /**
     * Add friendsWithMe
     *
     * @param \Taskul\UserBundle\Entity\User $friendsWithMe
     * @return User
     */
    public function addFriendsWithMe(\Taskul\UserBundle\Entity\User $friendsWithMe) {
        $this->friendsWithMe[] = $friendsWithMe;

        return $this;
    }

    /**
     * Remove friendsWithMe
     *
     * @param \Taskul\UserBundle\Entity\User $friendsWithMe
     */
    public function removeFriendsWithMe(\Taskul\UserBundle\Entity\User $friendsWithMe) {
        $this->friendsWithMe->removeElement($friendsWithMe);
    }

    /**
     * Get friendsWithMe
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFriendsWithMe() {
        return $this->friendsWithMe;
    }

    /**
     * Add myFriends
     *
     * @param \Taskul\UserBundle\Entity\User $myFriends
     * @return User
     */
    public function addMyFriend(\Taskul\UserBundle\Entity\User $myFriends) {
        $this->myFriends[] = $myFriends;

        return $this;
    }

    /**
     * Remove myFriends
     *
     * @param \Taskul\UserBundle\Entity\User $myFriends
     */
    public function removeMyFriend(\Taskul\UserBundle\Entity\User $myFriends) {
        $this->myFriends->removeElement($myFriends);
    }

    /**
     * Get myFriends
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMyFriends() {
        return $this->myFriends;
    }


    /**
     * Add tasksMember
     *
     * @param \Taskul\TaskBundle\Entity\Task $tasksMember
     * @return User
     */
    public function addTasksMember(\Taskul\TaskBundle\Entity\Task $tasksMember)
    {
        $this->tasksMember[] = $tasksMember;

        return $this;
    }

    /**
     * Remove tasksMember
     *
     * @param \Taskul\TaskBundle\Entity\Task $tasksMember
     */
    public function removeTasksMember(\Taskul\TaskBundle\Entity\Task $tasksMember)
    {
        $this->tasksMember->removeElement($tasksMember);
    }

    /**
     * Get tasksMember
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasksMember()
    {
        return $this->tasksMember;
    }

    public function __toString() {
        return $this->getFirstname().' '.$this->getLastname();
    }

    /**
     * Set codeUpload
     *
     * @param string $codeUpload
     * @return User
     */
    public function setCodeUpload($codeUpload)
    {
        $this->codeUpload = $codeUpload;

        return $this;
    }

    /**
     * Get codeUpload
     *
     * @return string
     */
    public function getCodeUpload()
    {
        return $this->codeUpload;
    }

    /**
     * Add documents
     *
     * @param \Taskul\FileBundle\Entity\Document $documents
     * @return User
     */
    public function addDocument(\Taskul\FileBundle\Entity\Document $documents)
    {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Remove documents
     *
     * @param \Taskul\FileBundle\Entity\Document $documents
     */
    public function removeDocument(\Taskul\FileBundle\Entity\Document $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    public function setEmail($email)
    {
       parent::setEmail($email);
       $this->setUsername($email);
   }

}