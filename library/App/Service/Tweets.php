<?php

/**
 * Gets Tweets from Twitter using a specific requirement/query
 * 
 * @author Jason Brown
 */

namespace App\Service;

class Tweets
{
    /**
     * Obtain Tweets that use a specific hash tag
     * @param string $tag - Hash Tag to search for
     * @param string $responseType - Type of response to return. json or atom
     * @return mixed $results - Results of search
     */
    public function ByHashTag($tag, $responseType = 'json')
    {
        $twitterSearch = new \Zend_Service_Twitter_Search($responseType);
        $results = $twitterSearch->search($tag, array('include_entities' => true));
        
        return $results;
    }
    
    /**
     * Obtain Tweets that use a specific hash tag
     * And are after a specific id
     * @param string $tag - Hash Tag to search for
     * @param string $responseType - Type of response to return. json or atom
     * @return mixed $results - Results of search
     */
    public function ByHashTagFromId($tag, $id = null, $responseType = 'json')
    {
        $twitterSearch = new \Zend_Service_Twitter_Search($responseType);
        $results = $twitterSearch->search($tag, array('include_entities' => true, 'since_id' => $id));
        
        return $results;
    }
}

?>
