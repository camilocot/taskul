<?php

namespace Taskul\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('main');

        // Menu de tareas
        $menu->setChildrenAttribute('class','nav nav-tabs nav-stacked main-menu');
        $menu->addChild('Task',array('uri'=>'#','label'=>'<i class="icon-chevron-down icon-white"></i><span class="hidden-tablet">Tareas</span>'))->setExtra('safe_label', FALSE);
        $menu['Task']->setLinkAttribute('class', 'dropmenu');
        $menu['Task']->setChildrenAttribute('id','task_ops');
        $menu['Task']->addChild('new_task',array('route'=>'api_new_task','label'=>'<i class="fa-icon-file-alt"></i><span class="hidden-tablet">Crear Nueva</span>','class'=>'submenu'))->setExtra('safe_label', FALSE);
        $menu['Task']->addChild('list_task',array('route'=>'api_get_tasks','label'=>'<i class="fa-icon-list"></i><span class="hidden-tablet"> Ver todas</span>','class'=>'submenu'))->setExtra('safe_label', FALSE);

        // Menu de solicitudes de amistad
        $menu->addChild('FriendRequest',array('uri'=>'#','label'=>'<i class="icon-chevron-down icon-white"></i><span class="hidden-tablet">Solicitudes amistad</span>'))->setExtra('safe_label', FALSE);
        $menu['FriendRequest']->setLinkAttribute('class', 'dropmenu');
        $menu['FriendRequest']->setChildrenAttribute('id','friendreq_ops');
        $menu['FriendRequest']->addChild('new_freq',array('route'=>'frequest_new','label'=>'<i class="fa-icon-file-alt"></i><span class="hidden-tablet">Crear</span>','class'=>'submenu'))->setExtra('safe_label', FALSE);
        $menu['FriendRequest']->addChild('list_freq_recibed',array('route'=>'frequest_recibed','label'=>'<i class="fa-icon-file-alt"></i><span class="hidden-tablet">Recibidas</span>','class'=>'submenu'))->setExtra('safe_label', FALSE);
        $menu['FriendRequest']->addChild('list_freq_sended',array('route'=>'frequest_sended','label'=>'<i class="fa-icon-file-alt"></i><span class="hidden-tablet">Enviadas</span>','class'=>'submenu'))->setExtra('safe_label', FALSE);
        $menu['FriendRequest']->addChild('import',array('route'=>'import_fb','label'=>'<i class="fa-icon-file-alt"></i><span class="hidden-tablet">Invitar amigos de Facebook</span>','class'=>'submenu'))->setExtra('safe_label', FALSE);

        $menu->addChild('Friends',array('uri'=>'#','label'=>'<i class="icon-chevron-down icon-white"></i><span class="hidden-tablet">Amigos</span>'))->setExtra('safe_label', FALSE);
        $menu['Friends']->setLinkAttribute('class', 'dropmenu');
        $menu['Friends']->setChildrenAttribute('id','friends_ops');
        $menu['Friends']->addChild('list_fri',array('route'=>'myfriends','label'=>'<i class="fa-icon-file-alt"></i><span class="hidden-tablet">Listado</span>','class'=>'submenu'))->setExtra('safe_label', FALSE);
        return $menu;
    }

        public function createPublicMenu(Request $request)
    {
        $menu = $this->factory->createItem('public');
        $menu->setChildrenAttribute('class','nav');
        $menu->addChild('Home',array('route'=>'homepage'));
        $menu->addChild('Login',array('route'=>'fos_user_security_login'));
        $menu->addChild('Register',array('route'=>'fos_user_registration_register'));
        return $menu;
    }

    public function createPublicLoggedMenu(Request $request)
    {
        $menu = $this->factory->createItem('public_logged');
        $menu->setChildrenAttribute('class','nav');
        $menu->addChild('Home',array('route'=>'homepage'));
        $menu->addChild('DashBoard',array('route'=>'dashboard'));
        return $menu;
    }
}