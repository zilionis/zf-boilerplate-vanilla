<?php

/**
 * Base file for all Crons
 * Loads all necessary classes 
 * @author jasonbrown
 */
namespace App\Cron;

class BaseCron
{
    protected $_em;
    protected $_cache;
    
    public function __construct()
    {
        $this->_em = \Zend_Registry::get('em');
        $this->_cache = \Zend_Registry::get('cache');
    }
}

?>
