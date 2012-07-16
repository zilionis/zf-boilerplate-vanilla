<?php

/**
 * Jbfreelance\Service\Auth
 *
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */
class Jbfreelance_Service_Auth extends Jbfreelance_Service_Abstract
{
    
    /**
     * Zend_Auth Instance
     * @var Zend_Auth 
     */
    protected $auth;
    
    const AUTH_DOCTRINE = 1;
    const AUTH_FACEBOOK = 2;
    const AUTH_TWITTER = 3;
    
    /**
     * Method of Authentication
     * @var type 
     */
    protected $method;
    
    /**
     * Identity Field to use for Auth Adapter
     * @var string 
     */
    protected $identityField;
    
    /**
     * Credential Field to use for Auth Adapter
     * @var type 
     */
    protected $credentialField;
    
    /**
     * Instance of Auth Adapter
     * @var Zend_Auth_Adapter 
     */
    protected $adapter;
    
    /**
     * Setup Auth Service
     * 
     * @param type $config 
     */
    public function __construct($config)
    {
        $this->auth = Zend_Auth::getInstance();
        parent::__construct($config);
    }
    
    /**
     * Gets the method of Auth
     * @param type $method 
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Sets the method of Auth
     * @param type $method 
     */
    public function setMethod($method)
    {
        $this->method = $method;
        
        return $this;
    }
        
    public function getIdentityField()
    {
        $this->identityField = $identityField;
    }
    
    public function setIdentityField($identityField)
    {
        $this->identityField = $identityField;
        
        return $this;
    }
    
    public function getCredentialField()
    {
        return $this->credentialField;
    }
    
    public function setCredentialField($credentialField)
    {
        $this->credentialField = $credentialField;
        
        return $this;
    }
    
    /**
     * Authenticates a user using selected method
     * @param mixed $options
     * @return boolean 
     */
    public function login($options = array())
    {
        switch($this->method)
        {
            case self::AUTH_DOCTRINE:
                $result = $this->_doctrineLogin($options);
                break;
            case self::AUTH_FACEBOOK:
                $result = $this->_facebookLogin($options);
                break;
            case self::AUTH_TWITTER:
                $result = $this->_twitterLogin($options);
                break;
        }
        
        
        return $this->_handleAuthResult($result);
    }
    
    /**
     * Clears Auth user session 
     */
    public function logout()
    {
        $this->auth->clearIdentity();
    }
    
    /**
     * Get current Auth user
     * @return Jbfreelance_Auth_User
     */
    public function getUser()
    {
        return $this->auth->getIdentity();
    }
    
    /**
     * Attempts to auth user using doctrine adapter
     * @param mixed $options - array holding additional options for adapter
     */
    protected function _doctrineLogin($options)
    {
        $this->adapter = new Jbfreelance_Auth_Adapter_Doctrine($this->entityManager);
                
        $this->adapter->setEntityName("App\Entity\User")
                    ->setIdentityField($this->identityField)
                    ->setCredentialField($this->credentialField);
        
        // Check username and password options were provided
        if(isset($options['username']) && isset($options['password']))
        {
            $this->adapter->setIdentity($options['username']); 

            // Get application salt from config
            $config = \Zend_Registry::get('config'); 
            $salt = $config['salt'];
            
            // Treat password with salt and pass to Adapter
            $this->adapter->setCredential(SHA1($salt.$options['password']));
            
            return $this->auth->authenticate($this->adapter);
        }else{
            throw new Zend_Auth_Adapter_Exception('Identity and Credential values must be given');
        }
    }
    
    protected function _facebookLogin($options)
    {
        //$this->adapter = new Jbfreelance_Auth_Adapter_Facebook($this->entityManager);
        
        /*$this->adapter->setEntityName("App\Entity\User")
                    ->setIdentityField($this->identityField)
                    ->setCredentialField($this->credentialField);*/
        $frontController = Zend_Controller_Front::getInstance();
    	$request = $frontController->getRequest();
    	
    	// First check to see wether we're processing a redirect response.
    	$code = $request->getParam('code');
        
        $config = array('appId'  => '226380840801531',
                            'secret' => 'b6745fc6bc55920225849f73e84a9cf2');

        $facebook = new Facebook_Api($config);
        
        if(empty($code))
        {
            $uri = $facebook->getLoginUrl(
                                array(
                                    'scope' => 'user_about_me, user_birthday' ,
                                    'redirect_uri' => "http://".$_SERVER['SERVER_NAME']."/"
                                    )
                                );

            header("Location:".$uri);
        }else{
            $facebook->getUser();
        }
    }
    
    /**
     * Authorizes a User, using their Twitter account
     * @param Array $config - Must have the following values:
     * callbackUrl - URI to be returned to after authorization
     * siteUrl - Site Url to Twitter Oauth  @link https://twitter.com/oauth
     * consumerKey - Twitter application consumer key
     * consumerSecret Twitter application secret key
     */
    protected function _twitterLogin($config)
    {
        try{
            // create an Oauth consumer from config
            $consumer = new Zend_Oauth_Consumer($config);

            // Create session namespace
            $session = new Zend_Session_Namespace('twitter_auth');

            // Check if we're being directed back from Twitter
            if(!$session->twitter->redirected || empty($_GET))
            {
                // Get Request token
                $requestToken = $consumer->getRequestToken();

                // Serialize and Store request token in session
                $session->twitter->requestToken = serialize($requestToken);

                // We want to now redirect to Twitter and allow the response to pass through
                $session->twitter->redirected = true;
                $consumer->redirect();
            }else{
                // Get and store access token
                $session->twitter->accessToken = $consumer->getAccessToken($_GET, unserialize($session->twitter->requestToken));

                // Reset redirect flag
                $session->twitter->redirected = false;

                // Check access Token was successful
                return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, "");
            }
        }catch (Exception $e){
            // Catch and Log exception 
            $logger = Zend_Registry::get('logger');
            $logger->log($e->getMessage(), Zend_Log::ERR);
            
            return false;
        }
        
    }
    
    /**
     * Processes Authentication result
     * @param boolean $result - Result of Auth adapter
     * @return boolean 
     */
    protected function _handleAuthResult($result)
    {
        if($result->isValid())
        {
            $user = $this->adapter->getResultRowObject();
            
            $user = new Jbfreelance_Auth_User(
                        $user->getId(),
                        $user->getUsername(),
                        $user->getRole()
                    );
            
            $this->auth->getStorage()->write($user);
            
            return true;
        }
        
        return false;
    }
}

?>
