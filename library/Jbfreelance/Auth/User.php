<?php

class Jbfreelance_Auth_User
{
    private $_id;
    private $_username;
    private $_roleId;
    
    public function __construct($id, $username, $roleId)
    {
        $this->_id = $id;
        $this->_username = $username;
        $this->_roleId = $roleId;
    }
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function getUsername()
    {
        return $this->_username;
    }
    
    public function getRoleId()
    {
        return $this->_roleId;
    }
}
?>
