<?php

namespace App\Entity;

/**
 * App\Entity\User
 * 
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */

/**
 * @Entity(repositoryClass="App\Repository\User")
 * @Table(name="users")
 */
class User extends \Jbfreelance_Entity_Abstract
{
    /** @Column(type="string", name="username", unique="true") */
    protected $username;
    
    /** @Column(type="string", name="password") */
    protected $password;
    
    /** @Column(type="string", name="email_address") */
    protected $email;
    
    /** @Column(type="datetime", name="created")*/
    protected $created;
    
    /** @Column(type="datetime", name="updated")*/
    protected $updated;
    
    /** @Column(type="boolean", name="active")*/
    protected $active = false;
    
    /** @Column(type="string", name="activation_code") */
    protected $activationCode;
    
    const ROLE_MEMBER = 'Member';
    const ROLE_ADMIN = 'Admin';
    
    /** @Column(type="string", name="role") */
    protected $role;
    
    protected $salt;
    
    /**
     * Construct User
     * 
     * @param string $username
     * @param string $password
     * @param string $emailAddress 
     */
    public function __construct($username, $password, $email)
    {
        $this->username = $username;
        $this->email = $email;
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->role = self::ROLE_MEMBER;
        
        // Get application salt from config
        $config = \Zend_Registry::get('config'); 
        $this->salt = $config['salt'];
        
        // Treat password with salt
        $this->password = SHA1($this->salt.$password);
        
        // Create activation code
        $this->activationCode = sha1(date('d-m-Y H:i:s').$this->salt);
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
        
        // Update timestamp
        $this->updated = new \DateTime();
        
        return $this;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword($password)
    {
        // Get application salt from config
        $config = \Zend_Registry::get('config'); 
        $this->salt = $config['salt'];
        
        // Treat password with salt
        $this->password = SHA1($this->salt.$password);
        return $this;
    }
    
    public function getEmailAddress()
    {
        return $this->email;
    }
    
    public function setEmailAddress($email)
    {
        $this->email = $email;
        
        // Update timestamp
        $this->updated = new \DateTime();
        
        return $this;
    }
    
    public function getCreated($format = "d-m-Y H:i:s")
    {
        return $this->created->format($format);
    }
    
    public function getUpdated($format = "d-m-Y H:i:s")
    {
        return $this->updated->format($format);
    }
    
    public function getRole()
    {
        return $this->role;
    }
    
    public function setRole($role)
    {
        $this->role = $role;
        
        // Update timestamp
        $this->updated = new \DateTime();
        
        return $this;
    }
    
}

?>
