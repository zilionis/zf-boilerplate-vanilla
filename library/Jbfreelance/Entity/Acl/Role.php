<?php

/**
 * Jbfreelance\Entity\Acl\Role
 *
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */
class Jbfreelance_Entity_Acl_Role extends Jbfreelance_Entity_Abstract
{
    protected $label;
    protected $allow;
    protected $deny;
    protected $inherits;
    
    public function __construct($label, $allow = null, $deny = null)
    {
        $this->label = $label;
        $this->allow = $allow;
        $this->deny = $deny;
    }
}