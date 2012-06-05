<?php

/**
 * Class to control multiply streams and track their state
 *
 * @author jasonbrown
 */
namespace App\Cron\Twitter;
use App\Cron\BaseCron as BaseCron;
use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use App\Entity\Stream as Stream;
use App\Entity\Tweet as Tweet;

class StreamManager extends BaseCron
{
    /**
     * Currently active Stream Objects
     * @var App\Entity\Stream 
     */
    protected $_streams;
    
    /**
     * Date/Time Streams were last loaded
     * @var DateTime 
     */
    protected $_lastChecked;
    
    public function __construct()
    {
        parent::__construct();
        $this->_streams = $this->_em->getRepository("App\Entity\Stream")->findAllActive();
        $this->_lastChecked = new \DateTime();
    }
    
    public function loadStreams()
    {
        foreach($this->_streams as $stream)
        {
            $this->start($stream);
        }
    }
    
    protected function start(Stream $stream)
    {
        // Save stream active status in cache
        $this->_cache->save('stream.'.$stream->getId(), true);
        
        // Get the tracker value to be used for the stream
        $query_data = array('track' => $stream->getTracker());
        
        $user = 'jbfreelance';	// replace with your account
        $pass = '040rlf09';	// replace with your account
        
        // Open a socket to the stream service
        $fp = fsockopen("ssl://stream.twitter.com", 443, $errno, $errstr, 30);
        
        // Prevent socket stream from blocking on failure or whilst waiting
        stream_set_blocking($fp, 0);
        
        if(!$fp)
        {
            // Log error
            print "$errstr ($errno)\n";
        }else{
            // Setup the request header
            $request = "GET /1/statuses/filter.json?" . http_build_query($query_data) . " HTTP/1.1\r\n";
            $request .= "Host: stream.twitter.com\r\n";
            $request .= "Authorization: Basic " . base64_encode($user . ':' . $pass) . "\r\n\r\n";
            
            // Write to the request
            fwrite($fp, $request);
            
            // Save stream state as running
            $stream->setRunning(true);
            $this->_em->persist($stream);
            $this->_em->flush();
            
            $count = 0;
            
            // Test whether data is being passed and the stream is active in cache
            while(!feof($fp) && $this->_cache->contains('stream.'.$stream->getId())){
                $json = fgets($fp);
                $data = json_decode($json, true);
                if($data){
                    
                    // Create new Tweet object
                    $tweet = new Tweet(
                            $stream, 
                            $data['id_str'], 
                            $data['text'], 
                            $data['source'], 
                            $data['user']['profile_image_url'], 
                            $data['user']['name'],
                            $data['user']['screen_name'],
                            $data['created_at']
                    );
                    
                    // Persist and Save object
                    $this->_em->persist($tweet);
                    $this->_em->flush();
                 
                    // Count number of tweets collected from stream
                    // Store this in cache, merely used for statistics
                    $count = $this->_cache->fetch('stream.'.$stream->getId().'.tweet.count');
                    $count = $count+1;
                    $this->_cache->save('stream.'.$stream->getId().'.tweet.count', $count);
                }
            }
            
            fclose($fp);
            
            // Save stream state as not running
            $stream->setRunning(false);
            $this->_em->persist($stream);
            $this->_em->flush();
        }
    }
    
    public function stop(Stream $stream)
    {
        $this->_cache->delete('stream.'.$stream->getId());
    }
}

?>