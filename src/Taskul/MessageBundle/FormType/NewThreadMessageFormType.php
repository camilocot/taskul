<?php

namespace Taskul\MessageBundle\FormType;

use FOS\MessageBundle\FormType\NewThreadMessageFormType as BaseFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class NewThreadMessageFormType extends BaseFormType
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     *
     *
     * @param SecurityContextInterface $context
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->securityContext->getToken()->getUser();
        $builder
            ->add('recipient', 'entity', array(

            'class'         => 'Taskul\UserBundle\Entity\User',

            'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use($user) {
                $qb = $er->createQueryBuilder('u');
                $friendsObj = $user->getMyFriends();
                $friends = array();
                foreach($friendsObj as $f)
                    $friends[] = $f->getId();
                //$ids = array_map(create_function('$o', 'return $o->getId();'), (array)$friends); //@FIXME: no va esto

                $qb->select('u');
                if(count($friends)>0)
                    return $qb->add('where', $qb->expr()->in('u.id', '?1'))->setParameter('1',$friends);
                else
                    return $qb->add('where','1=2'); // Para que no salga ninguno
            },
            ))
            ->add('subject', 'text')
            ->add('body', 'purified_textarea');
    }
}

