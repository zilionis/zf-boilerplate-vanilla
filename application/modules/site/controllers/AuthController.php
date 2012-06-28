<?php

class Site_AuthController extends Jbfreelance_Controller_Action
{
    
    /**
     * Configuration for Auth Service
     * @var mixed 
     */
    private $config = array();
    
    public function init()
    {
        parent::init();
        
        // Define Auth Service config
        $this->config = array(
                'Method' => Jbfreelance_Service_Auth::AUTH_DOCTRINE,
                'EntityManager' => $this->_em,
                'EntityClass' => 'App\Entity\User',
                'IdentityField' => 'username',
                'CredentialField' => 'password'
                );
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
 
        if ($request->isPost())
        {
            if ($form->isValid($request->getPost()))    
            {
                // Get instance of Auth Service using given config
                $authService = new Jbfreelance_Service_Auth($this->config);
                
                if ($authService->login($form->getValues())) 
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
        // Get Auth Service
        $authService = new Jbfreelance_Service_Auth($this->config);
        
        // Clear Auth session
        $authService->logout();
        
        // Display notification to user
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
}