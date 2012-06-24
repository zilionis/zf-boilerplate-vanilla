<?php
namespace App\Entity;

use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @Entity(repositoryClass="Jbfreelance_Repository_User")
 * @Table(name="users")
 */
class User
{
    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $id;
    /** @Column(type="string", name="username", unique="true") */
    protected $username;
    /** @Column(type="string", name="password") */
    protected $password;
    /** @Column(type="string", name="email_address") */
    protected $emailAddress;
    /** @Column(type="datetime", name="created")*/
    protected $created;
    /** @Column(type="datetime", name="updated")*/
    protected $updated;
    /** @Column(type="boolean", name="active")*/
    protected $active = false;
    /** @Column(type="string", name="activation_code") */
    protected $activationCode;
    /** @Column(type="string", name="role") */
    protected $roleId = "Member";
    
    protected $salt;
    
    public function __construct($username, $password, $emailAddress)
    {
        $this->_username = $username;
        
        // Obtain salt from config
        $config = \Zend_Registry::get('config'); 
        $this->salt = $config['salt'];
        
        // Treat password with salt
        $this->_password = SHA1($this->salt.$password);
        $this->_emailAddress = $emailAddress;
        $this->_created = new \DateTime();
        $this->_updated = new \DateTime();
        $this->_activationCode = $this->_generateActivationCode();
    }
    
    public function getId()
    {
        return $this->id;
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
        $this->password = SHA1($this->salt.$password);
        
        // Update timestamp
        $this->updated = new \DateTime();
        
        return $this;
    }
    
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
    
    public function setEmailAddress($email)
    {
        $this->emailAddress = $email;
        
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
    
    public function getRoleId()
    {
        return $this->roleId;
    }
    
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
        
        // Update timestamp
        $this->updated = new \DateTime();
        
        return $this;
    }
    
    public function getActivationCode()
    {
        return $this->activationCode;
    }
    
    private function _generateActivationCode()
    {
        // Take date object, username and email address and generate a hash
        return md5($this->created->getTimestamp().$this->username.$this->emailAddress);
    }
    
    public function isActive()
    {
        return $this->active;
    }
    
    public function setActiveStatus($active = true)
    {
        $this->active = $active;
        
        // Update timestamp
        $this->updated = new \DateTime();
        
        return $this;
    }
}