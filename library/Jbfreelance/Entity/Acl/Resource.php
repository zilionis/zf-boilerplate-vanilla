<?php

/**
 * Jbfreelance\Entity\Acl\Resource
 *
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */
class Jbfreelance_Entity_Acl_Resource extends Jbfreelance_Entity_Abstract
{
    protected $module;
    protected $controller;
    protected $actions;
    
    /**
     *
     * @param string $module
     * @param string $controller
     * @param array | string $actions 
     */
    public function __construct($module, $controller, $actions = null)
    {
        $this->module = $module;
        $this->controller = $controller;
        $this->actions = $actions;
    }
}