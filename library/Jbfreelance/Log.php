<?php

/**
 * Jbfreelance\Log
 * 
 * @author Jason Brown <jason.brown@jbfreelance.co.uk> 
 */
class Jbfreelance_Log
{
    /**
     * Logs messages to file 
     * 
     * @param string $message
     * @param \Zend_Log const $priority
     * @param string $filename
     * @throws Zend_Exception - When a log path is invalid
     */
    public static function Log($message, $priority = Zend_Log::INFO, $filename = 'system.log')
    {
        // Open log file path
        $path = @fopen(APPLICATION_PATH ."/../data/logs/".$filename, 'a', false);
        
        // Check path was opened successfully
        if(!$path)
        {
            throw new Zend_Exception('Log path not found');
        }
        
        // init log writer
        $writer = new Zend_Log_Writer_Stream($path, 'a');
        
        // init Logger
        $logger = new Zend_Log($writer);
        
        // Log message with priority
        $logger->log($message, $priority);
    }
    
}
?>
