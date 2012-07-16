<?php

class Jbfreelance_Plugin_UserActivity extends \Zend_Controller_Plugin_Abstract
{
    /**
     * @var \Zend_Auth 
     */
    private $_auth;
    
    /**
     * Class path for user entity
     * @var string 
     */
    private $_entityClassPath;
    
    public function __construct($entityClassPath = '\App\Entity\User')
    {
        // Get instance of auth object
        $this->_auth = \Zend_Auth::getInstance();
        
        // Assign class path for user entity
        $this->_entityClassPath = $entityClassPath;
    }
    
    public function isValidRequest(\Zend_Controller_Request_Abstract $request)
    {
        $dispatcher = \Zend_Controller_Front::getInstance()->getDispatcher();
        
        if($dispatcher->isDispatchable($request))
        {
            // Use reflection to check if the action exists
            $className = $dispatcher->getControllerClass($request);
            $fullClassName = $dispatcher->loadClass($className);
            $action = $dispatcher->getActionMethod($request);
            $class = new \Zend_Reflection_Class($fullClassName);
            
            return $class->hasMethod($action);
        }
    }
    
    public function routeShutdown(\Zend_Controller_Request_Abstract $request)
    {   
        // Only perform check against valid requests 
        if($this->isValidRequest($request))
        {
            // Get logged in user object
            $user = $this->_auth->getIdentity();
            // Check object is valid
            if(!is_null($user))
            {
                // Get entity manager
                $em = \Zend_Registry::get('em');
                
                // Get user entity from auth user
                $user = $em->find($this->_entityClassPath, $user->getId());
                
                // Log user activity
                $activity = new Jbfreelance\Entity\Tracking\UserActivity(
                        $user, 
                        $request->getModuleName(),
                        $request->getControllerName(),
                        $request->getActionName(),
                        $request->getUserParams()
                 );

                try{
                    // Save user activity
                    $em->persist($activity);
                    $em->flush();
                }catch (Exception $e){
                    // Log error
                    Jbfreelance_Log::Log($e->getMessage(), Zend_Log::ERR);
                }
            }
        }
    }
}
?>