<?php

namespace Taskul\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Taskul\TaskBundle\DBAL\Types\TaskStatusType;

class TaskType extends AbstractType
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
        ->add('name')
        ->add('description','purified_textarea',array('required' => false))
        ->add('dateEnd','date', array('widget'=>'single_text', 'format' => 'dd/MM/yyyy','required'=>FALSE))
        ->add('status', 'status', array('expanded' => true,'choices' => TaskStatusType::getChoices()))
        ->add('percent', 'integer')
        ->add('tags','text', array('mapped'=>false,'data'=>$options['tags'],'required'=>FALSE))
        ->add('goto_upload','hidden',array('property_path'=>FALSE,'data'=>0)) // Nos identifica si redireccionar hacia la subida de ficheros o a mostrar la tarea al editarla o crearla
        ->add('members', 'entity', array(

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
            'multiple'      => true,
            'required'      => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Taskul\TaskBundle\Entity\Task',
            'tags' => '',
            'csrf_protection'   => false,
            ));
    }

    public function getName()
    {
        return 'task';
    }
}
