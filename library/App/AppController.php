<?php
namespace App;

/*
 * Default application controller
 * Init common functionality for each controller
 * e.g. Cache, Entity Manager etc.
 */

class AppController extends \Zend_Controller_Action
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
        $this->view->title = "TwEvents";
    }
}

?>
