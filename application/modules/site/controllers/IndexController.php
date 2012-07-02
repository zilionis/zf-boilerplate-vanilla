<?php

class Site_IndexController extends Jbfreelance_Controller_Action
{

    public function init()
    {
        parent::init();
    }
    
    public function indexAction()
    {
        /*$config = array(
            'Method' => Jbfreelance_Service_Auth::AUTH_TWITTER,
        );*/
        
        //$auth = new Jbfreelance_Service_Auth($config);
        
        /*$auth->login(array(
            'callbackUrl' => 'http://vanilla.site.local.ctidigital.com',
            'siteUrl' => 'https://twitter.com/oauth',
            'consumerKey' => 'QVKKc0qSMFgGcOl4QctEug',
            'consumerSecret' => 'jAJoIqYid8ZUbITJOZBgDH1JmzcO4zl529NjQpE'
        ));*/
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