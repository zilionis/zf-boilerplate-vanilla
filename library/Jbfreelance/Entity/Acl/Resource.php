<?php

/**
 * Jbfreelance\Entity\Acl\Resource
 *
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */

/**
 * @Entity(repositoryClass="\Jbfreelance\Repository\Acl\Resource")
 * @Table(name="acl_resources")
 */
class Jbfreelance_Entity_Acl_Resource extends Jbfreelance_Entity_Abstract
{
    
    /** @Column(type="string", name="module") */
    protected $module;
    
    /** @Column(type="string", name="controller") */
    protected $controller;
    
    /** @Column(type="array", name="actions") */
    protected $actions;
    
    /**
     * Creates a new resource
     * @param string $module
     * @param string $controller
     * @param array $actions 
     */
    public function __construct($module, $controller, $actions = array())
    {
        $this->module = $module;
        $this->controller = $controller;
        $this->actions = $actions;
    }
    
    public function getActions()
    {
        return $this->actions;
    }
}