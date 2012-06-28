<?php

/**
 * Jbfreelance\Auth\User
 * 
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */

class Jbfreelance_Auth_User
{
    protected $id;
    protected $username;
    protected $role;
    
    public function __construct($id, $username, $role)
    {
        $this->id = $id;
        $this->username = $username;
        $this->role = $role;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function getRole()
    {
        return $this->role;
    }
}
?>
