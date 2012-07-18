<?php

/**
 * Jbfreelance\Entity\Acl\Role
 *
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */

use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @Entity(repositoryClass="Jbfreelance\Repository\Acl\Role")
 * @Table(name="acl_roles")
 */
class Jbfreelance_Entity_Acl_Role extends Jbfreelance_Entity_Abstract
{
    
    /** @Column(type="string", name="name") */
    protected $name;
    
    /**
     * @OneToOne(targetEntity="\Jbfreelance\Entity\Acl\Role")
     * @JoinColumn(name="id", referencedColumnName="id")
     */
    protected $inherits;
    
    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $rules
     * 
     * @OneToMany(targetEntity="\Jbfreelance\Entity\Acl\rule", mappedBy="role", cascade={"persist", "remove"})
     */
    protected $rules;
    
    /**
     * Creates a new role
     * @param string $name - name of role
     * @param Jbfreelance_Entity_Acl_Role $inherits - role to inherit from
     */
    public function __construct($name, \Jbfreelance_Entity_Acl_Role $inherits = null)
    {
        $this->name = $name;
        $this->inherits = $inherits;
        $this->rules = new ArrayCollection();
    }
}