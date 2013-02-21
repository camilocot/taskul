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
        $menu->setChildrenAttribute('class','nav nav-tabs nav-stacked main-menu');
        $menu->addChild('Task',array('uri'=>'#','label'=>'<i class="icon-chevron-up"></i><span class="hidden-tablet">Tareas</span>'))->setExtra('safe_label', FALSE);
        $menu['Task']->setLinkAttribute('class', 'dropmenu');

        $menu['Task']->addChild('new_task',array('route'=>'task_new','label'=>'<i class="fa-icon-file-alt"></i><span class="hidden-tablet">Crear Nueva</span>'))->setExtra('safe_label', FALSE);
        $menu['Task']['new_task']->setLinkAttribute('class', 'submenu');
        $menu['Task']->addChild('list_task',array('route'=>'task','label'=>'<i class="fa-icon-list"></i><span class="hidden-tablet"> Ver todas</span>','class'=>'submenu'))->setExtra('safe_label', FALSE);
        $menu->addChild('FriendRequest',array('uri'=>'#'));
        $menu['FriendRequest']->setLinkAttribute('class', 'dropmenu btn-minimize');

        $menu['FriendRequest']->addChild('new_freq',array('route'=>'frequest_new','label'=>'New','class'=>'submenu'));
        $menu['FriendRequest']->addChild('list_freq_recibed',array('route'=>'frequest_recibed','label'=>'List Recibed','class'=>'submenu'));
        $menu['FriendRequest']->addChild('list_freq_sended',array('route'=>'frequest_sended','label'=>'List Sended','class'=>'submenu'));
        $menu['FriendRequest']->addChild('import',array('route'=>'import_fb','label'=>'Import FB','class'=>'submenu'));

        $menu->addChild('Friends',array('uri'=>'#'));
        $menu['Friends']->setLinkAttribute('class', 'dropmenu');

        $menu['Friends']->addChild('list_fri',array('route'=>'myfriends','label'=>'List','class'=>'submenu'));
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
}