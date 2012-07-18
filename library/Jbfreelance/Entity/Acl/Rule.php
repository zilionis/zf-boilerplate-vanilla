<?php

/**
 * Jbfreelance\Entity\Acl\Rule
 *
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */

use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;


/**
 * @Entity(repositoryClass="\Jbfreelance\Repository\Acl\Rule")
 * @Table(name="acl_rules")
 */
class Jbfreelance_Entity_Acl_Rule extends Jbfreelance_Entity_Abstract
{
    
     /**
     * @ManyToOne(targetEntity="\Jbfreelance\Entity\Acl\Role")
     * @JoinColumns({
     *  @JoinColumn(name="role_id", referencedColumnName="id")
     * })
     */
    protected $role;
    
    /**
     * @ManyToOne(targetEntity="\Jbfreelance\Entity\Acl\Resource")
     * @JoinColumns({
     *  @JoinColumn(name="resource_id", referencedColumnName="id")
     * })
     */
    protected $resource;
    
    /** @Column(type="array", name="permissions") */
    protected $permissions;
    
    /**
     * Creates a rule for a role
     * @param \Jbfreelance_Entity_Acl_Role $role
     * @param \Jbfreelance_Entity_Acl_Resource $resource
     * @param array $permissions 
     */
    public function __construct(\Jbfreelance_Entity_Acl_Role $role, \Jbfreelance_Entity_Acl_Resource $resource, $permissions = array())
    {
        $this->role = $role;
        $this->resource = $resource;
        $this->permissions = $permissions;
    }
    
    /**
     * Gets the role that this rule applies to
     * @return \Jbfreelance_Entity_Acl_role 
     */
    public function getRole()
    {
        return $this->role;
    }
    
    /**
     * Sets a role for this rule
     * @param \Jbfreelance_Entity_Acl_Role $role
     * @return \Jbfreelance_Entity_Acl_Rule 
     */
    public function setRole(\Jbfreelance_Entity_Acl_Role $role)
    {
        $this->role = $role;
        
        return $this;
    }
    
    /**
     * Gets the resource this rule applies to for a role
     * @return \Jbfreelance_Entity_Acl_Resource 
     */
    public function getResource()
    {
        return $this->resource;
    }
    
    public function setResource(\Jbfreelance_Entity_Acl_Resource $resource)
    {
        $this->resource = $resource;
        
        return $this;
    }
    
    /**
     * Adds a permission for this rule
     * @param string $permission
     * @return \Jbfreelance_Entity_Acl_Rule 
     */
    public function addPermission($permission)
    {
        // Add permission to existing permissions
        array_push($this->permissions, $permission);
        
        return $this;
    }
    
    /**
     * Removes a permission from a rule
     * @param string $permission
     * @return \Jbfreelance_Entity_Acl_Rule 
     */
    public function removePermission($permission)
    {
        // Find array key for permission
        $key = array_search($permission, $this->permissions);
        
        // Remove permission from array using found key
        unset($this->permissions[$key]);
        
        // Reset indexes
        array_values($this->permissions);
        
        return $this;
    }
    
    /**
     * Get all permissions for this rule
     * @return array 
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}