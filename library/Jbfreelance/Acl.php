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
    
    public function __construct($configType = null, \Zend_Config $config = null)
    {
        $this->_configType = $configType;
        $this->_config = $config;
        
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
        if (!isset($this->_config->resources->resource)) {
            throw new \Zend_Acl_Exception('No resources have been defined.');
        }
        
        // Check theres more than one resource available
        foreach ($this->_config->resources->resource as $resource) {
            if (!$this->has($resource)) {
                $this->addResource(new \Zend_Acl_Resource($resource));
            }
        }
        
        // Get the user's current role
        $role = $this->getCurrentRole();
        
        // Check this role has been defined in config
        if (!isset($this->_config->roles->$role)) {
            throw new \Zend_Acl_Exception("The role '" . $role . "' has not been defined.");
        } else {
            // Check role hasn't previously been defined
            if (!$this->hasRole($role))
            {
                // Add role to ACL
                $this->addRole($role);
            }

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

                foreach ($resources as $resource) {

                    if ($resource === '*') {

                        $this->allow($role); // global allow
                    } else if ($resource && $this->has($resource)) {
                        $this->allow($role, $resource);
                    }
                }
            }
        }
    }
    
    public function __buildAclFromDb()
    {
        
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
