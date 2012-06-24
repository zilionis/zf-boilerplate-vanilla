<?php

/*
 * Default Application Controller
 * Init common functionality for each controller
 * e.g. Cache, Entity Manager etc.
 */

class Jbfreelance_Controller_Application extends Zend_Controller_Action
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em = null;
    
    /**
     * @var Zend_Controller_Action_Helper
     */
    protected $_flashMessenger = null;
    
    /**
     * @var Zend_Auth 
     */
    protected $_auth = null;
    
    /**
     * @var Cache 
     */
    protected $_cache = null;
    
    /* Initialize action controller here */
    public function init()
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_auth = \Zend_Auth::getInstance();
        $this->_cache = \Zend_Registry::get('cache');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->messages = $this->_flashMessenger->getMessages();
        $this->view->title = "Vanilla";
        
        //$this->view->addHelperPath(APPLICATION_PATH."/../library/Jbfreelance/View/Helper/");
        //Zend_Debug::dump($this->view);
    }
}

?>
