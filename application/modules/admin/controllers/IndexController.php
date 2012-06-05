<?php

use App\AppController as AppController;
use App\Service\Twitter as Twitter;
use App\Service\Tweets as Tweets;

class Admin_IndexController extends AppController
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $id = $this->getRequest()->getParam('stream');
        $stream = $this->_em->getRepository("App\Entity\Tweet")->findOrderLastFirst($id);
        $this->view->tweets = $stream;
    }

    public function headerAction()
    {
        // action body
    }

    public function footerAction()
    {
        // action body
    }

    public function feedAction()
    {
        // Disable layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    
        $type = $this->getRequest()->getParam('type');
        switch($type)
        {
            case "twitter":
                $id = $this->getRequest()->getParam('id');
                $tweets = $this->_em->getRepository("App\Entity\Tweet")->findSinceId($id);
                
                $json_tweets = array();
                
                foreach($tweets as $tweet)
                {
                    $json_tweets[$tweet->getId()]['id'] = $tweet->getId();
                    $json_tweets[$tweet->getId()]['twitter_id'] = $tweet->twitterId;
                    $json_tweets[$tweet->getId()]['content'] = $tweet->content;
                    $json_tweets[$tweet->getId()]['source'] = $tweet->source;
                    $json_tweets[$tweet->getId()]['profile_img'] = $tweet->profileImg;
                    $json_tweets[$tweet->getId()]['name'] = $tweet->name;
                    $json_tweets[$tweet->getId()]['screen_name'] = $tweet->screenName;
                }
                
                echo json_encode($json_tweets);
                break;
        }
    }
    
    public function streamAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $command = $this->getRequest()->getParam('command');

        switch($command)
        {
            case "start": 
                break;
            case "stop":
                // Get instance of stream manager
                $sm = new App\Cron\Twitter\StreamManager();
                // Get Stream entity
                $id = $this->getRequest()->getParam('id');
                $stream = $this->_em->find("App\Entity\Stream", $id);
                
                $sm->stop($stream);
                break;
        }
    }
}





