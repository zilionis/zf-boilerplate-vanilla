<?php

class Jbfreelance_Acl extends Zend_Acl
{
    /**
     * Define xml const for building Acl
     * @var string 
     */
    const XML = 'XML';
    
    /**
     * Define database const for building Acl
     * @var string 
     */
    const DB = 'DB';
    
    /**
     * Define role constants  
     */
    const GUEST = "Guest";
    const MEMBER = "Member";
    const ADMIN = "Admin";
    
    protected $_configType;
    
    protected $_config;
    
    public function __construct($configType = null)
    {
        $this->_configType = $configType;
        
        switch($this->_configType)
        {
            case self::XML:
                // Build Acl from Xml
                $this->_buildAclFromXml();
                break;
            
            case self::DB:
                // Build Acl from database
                $this->__buildAclFromDb();
                break;
        }
    }
    
    /**
     * Build the ACL using XML config
     *
     * @return void
     */
    protected function _buildAclFromXml()
    {
        // Get config from XML
        $this->_config = new \Zend_Config_Xml(APPLICATION_PATH.'/configs/acl.xml');
        
        // Build the ACL using config
        $this->_buildAcl();
    
    }
    
    protected function _buildAclFromDb()
    {
        
    }
    
    /**
     * Builds ACL using defined config
     * @throws \Zend_Acl_Exception 
     */
    private function _buildAcl()
    {
        // Check resources have been defined
        if (!isset($this->_config->resources->resource)) {
            throw new \Zend_Acl_Exception('No resources have been defined.');
        }
        
        // Check there are resources available
        foreach ($this->_config->resources->resource as $resource) {
            // Check resource hasn't previously been defined
            if (!$this->has($resource)) {
                // Added resource to ACL
                $this->addResource(new \Zend_Acl_Resource($resource));
            }
        }
        
        // Get the user's current role
        $role = $this->getCurrentRole();
        
        // Check this role has been defined in config
        if (!isset($this->_config->roles->$role)) {
            throw new \Zend_Acl_Exception("The role '" . $role . "' has not been defined.");
        } else {
            
            // Check if this role has any inheritance
            if(isset($this->_config->roles->{$role}->inherits))
            {
                // Get all parents for inheritance
                $parents = explode(',', $this->_config->roles->{$role}->inherits);
                
                $parentRoles = array();
                
                // Loop through each parent
                foreach($parents as $parent)
                {
                    // Check parent doesn't already exist
                    if(!$this->hasRole($parent))
                    {
                        // Add role to ACL
                        $this->addRole($parent);
        
                        // Build role access for this parent
                        $this->_defineRules($parent);
                        
                        // Get ACL role instance for this parent
                        $parentRole = $this->getRole($parent);
                        
                        // Add parent to array 
                        $parentRoles[] = $parentRole;
                        
                    }
                }
                
                // Add role with inheritance from parents
                $this->addRole($role, $parentRoles);
                
                // Build role access
                $this->_defineRules($role);
   
            }else{
            
                // Add role to ACL
                $this->addRole($role);
                
                // Build role access
                $this->_defineRules($role);
            }
        }
        
    }
    
    /**
     * Builds rules for given role
     * @param string $role - current Auth Role
     */
    private function _defineRules($role)
    {
        // Set a global deny for this role
        $this->deny($role);

        if (isset($this->_config->roles->{$role}->allow)) {
            $allow = $this->_config->roles->{$role}->allow;
            
            // always use an array of resources, even if there's only 1
            if ($allow->resource instanceof \Zend_Config) {
                $resources = $allow->resource->toArray();
            } else {
                $resources = array($allow->resource);
            }
            
            // If only one resource is defined toArray causes problems
            // Wrap it in another array to fix this
            if(!isset($resources[0]))
            {
                $resources = array($resources);
            }
            
            // Loop through each resource to add to role
            foreach ($resources as $resource) 
            {
               if ($resource === '*')
               {
                    // global allow
                    $this->allow($role); 
                } else if (isset($resource['name']) && $this->has($resource['name'])){
                    if(isset($resource['permissions']))
                    {
                        $this->allow($role, $resource['name'], explode(',', $resource['permissions']));
                    }else{
                        $this->allow($role, $resource['name']);
                    }
                }
            }
        }
    }
    
    public function getCurrentRole()
    {
        // Get Auth Object
        $auth = \Zend_Auth::getInstance();
        
        // Check if there is a logged in user
        if($auth->hasIdentity())
        {
            $user = $auth->getIdentity();
            return $user->getRole();
        }else{
            // No one logged in assume guest
            return self::GUEST;
        }
    }
}
?>
