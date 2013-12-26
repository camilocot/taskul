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
        $menu->addChild('Task',array(
            'uri'=>'#',
            'label'=>'menu.tasks'
            ))->setExtra('safe_label', FALSE)->setExtra('icon','<i class="icon-chevron-down icon-white"></i>')->setLinkAttribute('title', 'menu.title.tasks')->setLinkAttribute('rel', 'tooltip');;

        $menu['Task']->setLinkAttribute('class', 'dropmenu no-ajaxy');
        $menu['Task']->setChildrenAttribute('id','task_ops');
        $menu['Task']->addChild('new_task',array(
            'route'=>'api_new_task',
            'label'=>'menu.task.new',
            'class'=>'submenu',
            'attributes' => array('id' => 'task_ops_new')
            ))->setExtra('safe_label', FALSE)->setLinkAttribute('class','ajaxy')->setExtra('icon','<i class="fa-icon-file-alt"></i>')->setLinkAttribute('title', 'menu.title.task.new');
        $menu['Task']->addChild('list_task',array(
            'route'=>'api_get_tasks',
            'label'=>'menu.task.list',
            'class'=>'submenu',
            'attributes' => array('id' =>'task_ops_list')
            ))->setExtra('safe_label', FALSE)->setLinkAttribute('class','ajaxy')->setExtra('icon','<i class="fa-icon-list"></i>')->setLinkAttribute('title', 'menu.title.task.list');

        // Menu de solicitudes de amistad
        $menu->addChild('FriendRequest',array(
            'uri'=>'#',
            'label'=>'menu.frequests'
            ))->setExtra('safe_label', FALSE)->setExtra('icon','<i class="icon-chevron-down icon-white"></i>')->setLinkAttribute('title', 'menu.title.frequests')->setLinkAttribute('rel', 'tooltip');;

        $menu['FriendRequest']->setLinkAttribute('class', 'dropmenu no-ajaxy');
        $menu['FriendRequest']->setChildrenAttribute('id','friendreq_ops');
        $menu['FriendRequest']->addChild('new_freq',array(
            'route'=>'frequest_new',
            'label'=>'menu.frequest.new',
            'class'=>'submenu',
            'attributes' => array('id' => 'freq_ops_new'),
            ))->setExtra('safe_label', FALSE)->setLinkAttribute('class','ajaxy')->setExtra('icon','<i class="fa-icon-file-alt"></i>')->setLinkAttribute('title', 'menu.title.frequest.new');

        $menu['FriendRequest']->addChild('list_freq_recibed',array(
            'route'=>'frequest_recibed',
            'label'=>'menu.frequest.recibed',
            'class'=>'submenu',
            'attributes' => array('id' => 'freq_ops_recibed')
            ))->setExtra('safe_label', FALSE)->setLinkAttribute('class','ajaxy')->setExtra('icon','<i class="fa-icon-file-alt"></i>')->setLinkAttribute('title', 'menu.title.frequest.recibed');

        $menu['FriendRequest']->addChild('list_freq_sended',array(
            'route'=>'frequest_sended',
            'label'=>'menu.frequest.sended',
            'class'=>'submenu',
            'attributes' => array('id' => 'freq_ops_sended')
            ))->setExtra('safe_label', FALSE)->setLinkAttribute('class','ajaxy')->setExtra('icon','<i class="fa-icon-file-alt"></i>')->setLinkAttribute('title', 'menu.title.frequest.sended');

        $menu['FriendRequest']->addChild('import',array(
            'route'=>'import_fb',
            'label'=>'menu.frequest.import_fb',
            'class'=>'submenu',
            'attributes' => array('id' => 'freq_ops_import')
            ))->setExtra('safe_label', FALSE)->setLinkAttribute('class','ajaxy')->setExtra('icon','<i class="fa-icon-file-alt"></i>')->setLinkAttribute('title', 'menu.title.frequest.import_fb');

        $menu->addChild('Friends',array(
            'uri'=>'#',
            'label'=>'menu.friends'
            ))->setExtra('safe_label', FALSE)->setExtra('icon','<i class="icon-chevron-down icon-white"></i>')->setLinkAttribute('title', 'menu.title.friends')->setLinkAttribute('rel', 'tooltip');;
        $menu['Friends']->setLinkAttribute('class', 'dropmenu no-ajaxy');
        $menu['Friends']->setChildrenAttribute('id','friends_ops');
        $menu['Friends']->addChild('list_fri',array(
            'route'=>'myfriends',
            'label'=>'menu.friend.list',
            'class'=>'submenu',
            'attributes' => array('id' => 'friends_index')
            ))->setExtra('safe_label', FALSE)->setLinkAttribute('class','ajaxy')->setExtra('icon','<i class="fa-icon-file-alt"></i>')->setLinkAttribute('title', 'menu.title.friend.list');

        $menu->addChild('Messages',array(
            'uri'=>'#',
            'label'=>'menu.messages',
            ))->setExtra('safe_label', FALSE)->setExtra('icon','<i class="icon-chevron-down icon-white"></i>')->setLinkAttribute('title', 'menu.title.messages');

        $menu['Messages']->setLinkAttribute('class', 'dropmenu no-ajaxy')->setLinkAttribute('rel', 'tooltip');
        $menu['Messages']->setChildrenAttribute('id','msgs_ops');
        $menu['Messages']->addChild('sended_msg',array(
            'route'=>'fos_message_sent',
            'label'=>'menu.message.sended',
            'class'=>'submenu'
            ))->setExtra('safe_label', FALSE)->setLinkAttribute('class','ajaxy')->setExtra('icon','<i class="fa-icon-file-alt"></i>')->setLinkAttribute('title', 'menu.title.message.sended');
        $menu['Messages']->addChild('recibed_msg',array(
            'route'=>'fos_message_inbox',
            'label'=>'menu.message.recibed',
            'class'=>'submenu'
            ))->setExtra('safe_label', FALSE)->setLinkAttribute('class','ajaxy')->setExtra('icon','<i class="fa-icon-file-alt"></i>')->setLinkAttribute('title', 'menu.title.message.recibed');

        return $menu;
    }

    public function createPublicMenu(Request $request)
    {
        $menu = $this->factory->createItem('public');

        $menu->setChildrenAttribute('class','nav');
        $menu->addChild('Login',array(
            'route'=>'fos_user_security_login',
            'label'=>'menu.login',
            ));
        $menu['Login']->setLinkAttribute('class', 'ajaxy')->setLinkAttribute('title', 'menu.title.login');
        $menu->addChild('Register',array(
            'route'=>'fos_user_registration_register',
            'label'=>'menu.register',
            ))->setExtra('icon','<i class="fa-icon-file-alt"></i>');
        $menu['Register']->setLinkAttribute('class', 'ajaxy')->setLinkAttribute('title', 'menu.title.register');

        switch($request->get('_route')){
            case 'fos_user_registration_register':
                $menu['Register']->setLinkAttribute('class', 'ajaxy active');
                break;
            case 'fos_user_security_login':
                $menu['Login']->setLinkAttribute('class', 'ajaxy active');
                break;
        }
        return $menu;
    }

    public function createPublicLoggedMenu(Request $request)
    {
        $menu = $this->factory->createItem('public_logged');
        $menu->setChildrenAttribute('class','nav');
        $menu->addChild('DashBoard',array(
            'route'=>'dashboard',
            'label'=>'menu.dashboard'
            ));
        $menu['DashBoard']->setLinkAttribute('class', 'no-ajaxy')->setLinkAttribute('title', 'menu.title.dashboard');;
        return $menu;
    }
}