<?php
namespace Taskul\CommentBundle\Acl;

use FOS\CommentBundle\Acl\SecurityVoteAcl as BaseAcl;

use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\SignedVoteInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Da un error de acl ya creada
 *
 * @author Camilo Cota <camilocot@gmail.com>
 */
class SecurityVoteAcl extends BaseAcl
{

    public function setDefaultAcl(VoteInterface $vote)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($vote);
        try {
            $acl = $this->aclProvider->findAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            $acl = $this->aclProvider->createAcl($objectIdentity);
        }

        if ($vote instanceof SignedVoteInterface &&
            null !== $vote->getVoter()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($vote->getVoter());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

}