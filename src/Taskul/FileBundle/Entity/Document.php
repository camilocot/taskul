<?php
namespace Taskul\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
/**
 * Taskul\FileBundle\Entity\Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="Taskul\FileBundle\Entity\Repository\DocumentRepository")
 */
class Document
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

        /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
        private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="\Taskul\UserBundle\Entity\User", inversedBy="documents", cascade={"remove"})
     */
    private $owner;
    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255)
     */
        private $class;
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id_object", type="integer")
     */
    private $idObject;

    /**
     * @var boolean
     *
     * @ORM\Column(name="to_delete", type="boolean")
     */
    private $markToDelete;

    public function __construct()
    {
        $this->setMarkToDelete(FALSE);
    }
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
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set size
     *
     * @param \int $size
     * @return Document
     */
    public function setSize( $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return \int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set owner
     *
     * @param \Taskul\UserBundle\Entity\User $owner
     * @return Document
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
     * Set class
     *
     * @param string $class
     * @return Document
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set idObject
     *
     * @param integer $idObject
     * @return Document
     */
    public function setIdObject($idObject)
    {
        $this->idObject = $idObject;

        return $this;
    }

    /**
     * Get idObject
     *
     * @return integer
     */
    public function getIdObject()
    {
        return $this->idObject;
    }

    /**
     * Set markToDelete
     *
     * @param \bolean $markToDelete
     * @return Document
     */
    public function setMarkToDelete($markToDelete)
    {
        $this->markToDelete = $markToDelete;

        return $this;
    }

    /**
     * Get markToDelete
     *
     * @return \bolean
     */
    public function getMarkToDelete()
    {
        return $this->markToDelete;
    }

    public function getDocument($codeUpload)
    {
        return new File($this->getUploadRootDir($codeUpload).$this->getName());
    }

    public function getUploadRootDir($codeUpload)
    {
        return $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$codeUpload.'/';
    }

    public function __toString()
    {
        return $this->name;
    }
}