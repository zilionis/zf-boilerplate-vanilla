<?php

class Site_AuthController extends Jbfreelance_Controller_Application
{
    public function init()
    {
        parent::init();
    }
    
    public function preDispatch()
    {
        if ($this->_auth->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName() && 'forbidden' != $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index', 'index');
            }
        } else {
            // If they aren't, they can't logout, so that action should 
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }
    }
    
    public function indexAction()
    {
        $this->_helper->redirector('login');
    }
    
    public function loginAction()
    {
        $form = new Jbfreelance_Form_Auth_Login();
        $request = $this->getRequest();
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                if ($this->_processLogin($form->getValues(), 'Doctrine')) 
                {
                    // Get referrer back from session
                    $session = new \Zend_Session_Namespace('tmp');
                    $this->_redirect($session->redirect);
                }else{
                    // Auth failed
                    $this->_flashMessenger->addMessage(array('error' => 'Login failed. Please check your username and password'));
                    $this->_helper->redirector('login');
                }
            }
        }
        
        // Store referrer in session
        $session = new \Zend_Session_Namespace('tmp');
        $session->redirect = $request->getRequestUri();
        $this->view->form = $form;
    }
    
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_flashMessenger->addMessage(array('info' => 'Successfully logged out'));
        $this->_helper->redirector('index'); // back to login page
    }
    
    public function forbiddenAction()
    {
        //@TODO Log unauthorized access attempt
    }
    
    public function registerAction()
    {

    }
    
    protected function _processLogin($values, $adp)
    {
        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter($adp);
        
        // Setup adapter depending on it's type
        switch($adp)
        {
            case 'Doctrine':
                $adapter->setIdentity($values['username']); 
                
                // Get application config
                $config = \Zend_Registry::get('config');
                
                // Salt password using config salt
                $adapter->setCredential(SHA1($config['salt'].$values['password']));
                break;
        }
        
        
        $result = $this->_auth->authenticate($adapter);

        if ($result->isValid()) {
            $user = $adapter->getResultRowObject();
            
            // Remove Array index from query
            if(isset($user[0])){
                $user = $user[0];
            }
            
            // Check user has activated account
            if(!$user->isActive())
            {
                // User isn't activated remove user from auth
                $this->_auth->clearIdentity();
                return false;
            }else{
                // Create Auth User instance for storing to session
                //$authUser = $this->_createAuthUser($user);
                
                // Write Auth User to session
                $this->_auth->getStorage()->write($user);
                
                return true;
            }
        }
        return false;
    }
    
    protected function _getAuthAdapter($adapter)
    {
        switch($adapter)
        {
            case "Doctrine":
                $authAdapter = new Jbfreelance_Auth_Adapter_Doctrine(
                    $this->_em
                );
                
                $authAdapter->setEntityName('App\Entity\User')
                            ->setIdentityField('username')
                            ->setCredentialField('password');
                break;
        }
        
        return $authAdapter;
    }
    
    /**
     * Creates a Session Writeable version of User
     * Due to problem with session when storing User Entity
     * @param type $user The user entity to extract values from
     * @return Jbfreelance_Auth_User 
     */
    protected function _createAuthUser($user)
    {
        return new Jbfreelance_Auth_User($user->getId(), $user->getUsername(), $user->getRoleId());
    }
    
}