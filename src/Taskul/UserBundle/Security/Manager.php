<?php

/*
 * Copyright (C) 2011 David Mann
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Taskul\UserBundle\Security;

use Symfony\Component\Security\Acl\Domain\Entry;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Easily work with Symfony ACL
 *
 * This class abstracts some of the ACL layer and
 * gives you very easy "Grant" and "Revoke" methods
 * which will update existing ACL's and create new ones
 * when required
 *
 * @author CodinNinja
 */
class Manager {

    protected $provider;
    protected $context;

    /**
     * Constructor
     *
     * @param AclProviderInterface $provider
     * @param SecurityContextInterface $context
     */
    public function __construct(MutableAclProviderInterface $provider, SecurityContextInterface $context) {
        $this->provider = $provider;
        $this->context = $context;
    }


    /**
     * Grant a permission
     *
     * @param Object $entity The DomainObject to add the permissions for
     * @param string $username
     * @param string $class
     * @param integer|string $mask The initial mask
     * @return Object The original Entity
     */
    public function grant($entity, $members = array(), $mask = MaskBuilder::MASK_VIEW, $class = 'Taskul\UserBundle\Entity\User') {
        $acl = $this->getAcl($entity);

        $securityContext = $this->context;
        $user = $securityContext->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $this->addMask($securityIdentity, MaskBuilder::MASK_OWNER, $acl);

        if(! is_string($members) && count($members)>0){
            foreach($members as $m){
                $securityIdentity = new UserSecurityIdentity($m->getUsername(),$class);
                $this->addMask($securityIdentity, $mask, $acl);
            }
        }

        return $entity;


    }


    public function grantUser($entity, $username, $class = 'Taskul\UserBundle\Entity\User', $mask = MaskBuilder::MASK_OPERATOR) {
        $acl = $this->getAcl($entity);

        $securityIdentity = new UserSecurityIdentity($username,$class);
        $this->addMask($securityIdentity, $mask, $acl);
    }
    /**
     * Get or create an ACL object
     *
     * @param object $entity The Domain Object to get the ACL for
     *
     * @return Acl The found / craeted ACL
     */
    protected function getAcl($entity) {
// creating the ACL
        $aclProvider = $this->provider;
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        try {
            $acl = $aclProvider->createAcl($objectIdentity);
        } catch (\Exception $e) {
            $acl = $aclProvider->findAcl($objectIdentity);
        }

        return $acl;
    }

    /**
     * Revoke a permission
     *
     * <pre>
     * $manager->revoke($myDomainObject, 'delete'); // Remove "delete" permission for the $myDomainObject
     * </pre>
     *
     * @param Object $entity The DomainObject that we are revoking the permission for
     * @param string $username
     * @param string $class
     * @param int|string $mask The mask to revoke
     *
     * @return \Taskul\UserBundle\Security\Manager Reference to $this for fluent interface
     */
    public function revoke($entity, $username = null, $class = null, $mask = MaskBuilder::MASK_OWNER) {
        $acl = $this->getAcl($entity);
        $aces = $acl->getObjectAces();

        if(null !== $username && null !== $class)
            $securityIdentity = new UserSecurityIdentity($username,$class);
        else {
            $user = $this->context->getToken()->getUser();
            $securityIdentity = UserSecurityIdentity::fromAccount($user);
        }

        foreach ($aces as $i => $ace) {
            if ($securityIdentity->equals($ace->getSecurityIdentity())) {
                $this->revokeMask($i, $acl, $ace, $mask);
            }
        }

        $this->provider->updateAcl($acl);

        return $this;
    }
    /**
     * [revokeAll description]
     * @param  [type] $entity [description]
     * @return [type]         [description]
     */
    public function revokeAll($entity)
    {
        $acl = $this->getAcl($entity);
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $this->provider->deleteAcl($objectIdentity);
        return $this;
    }
    /**
     * Remove a mask
     *
     * @param Acl $acl The ACL to update
     * @param Entry $ace The ACE to remove the mask from
     * @param unknown_type $mask The mask to remove
     *
     * @return \Taskul\UserBundle\Security\Manager Reference to $this for fluent interface
     */
    protected function revokeMask($index, Acl $acl, Entry $ace, $mask) {
        $acl->updateObjectAce($index, $ace->getMask() & ~$mask);

        return $this;
    }

    /**
     * Add a mask
     *
     * @param SecurityIdentityInterface $securityIdentity The ACE to add
     * @param integer|string $mask The initial mask to set
     * @param ACL $acl The ACL to update
     *
     * @return \Taskul\UserBundle\Security\Manager Reference to $this for fluent interface
     */
    protected function addMask($securityIdentity, $mask, $acl) {
        $acl->insertObjectAce($securityIdentity, $mask);
        $this->provider->updateAcl($acl);

        return $this;
    }

}