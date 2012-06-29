<?php

class Site_IndexController extends Jbfreelance_Controller_Action
{

    public function init()
    {
        parent::init();
    }
    
    public function indexAction()
    {
        
    }
    
    public function headerAction()
    {
        $container = new Zend_Navigation(
            array(
                array(
                    'action'     => 'index',
                    'controller' => 'index',
                    'module'     => 'site',
                    'label'      => 'Home'
                ),
                array(
                    'uri'        => 'http://zf-boilerplate.com/documentation/',
                    'label'      => 'Documentation'
                )
            )
        );

        $this->view->navigation($container);
    }

    public function footerAction()
    { 
        
    }
}