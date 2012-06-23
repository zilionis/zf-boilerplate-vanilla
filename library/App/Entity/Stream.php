<?php
namespace App\Entity;
use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Stream")
 * @Table(name="streams")
 */
class Stream
{
    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue
     */
    private $_id;
    /** @Column(type="string", name="tracker") */
    private $_tracker;
    /** @Column(type="boolean", name="active") */
    private $_active = true;
    /** @Column(type="boolean", name="running") */
    private $_running = false;
    
    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $_tweets
     * 
     * @OneToMany(targetEntity="App\Entity\Tweet", mappedBy="_tracker", cascade={"persist", "remove"})
     */
    private $_tweets;
    
    public function __construct($tracker)
    {
        $this->_tracker = $tracker;
        $this->_tweets = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->_id;
    }

    public function getTracker()
    {
        return $this->_tracker;
    }

    public function setTracker($tracker)
    {
        $this->_tracker = $tracker;
        return $this;
    }

    public function isActive()
    {
        return $this->_active;
    }

    public function setActive($active = true)
    {
        $this->_active = $active;
        return $this;
    }
    
    public function isRunning()
    {
        return $this->_running;
    }

    public function setRunning($running = true)
    {
        $this->_running = $running;
        return $this;
    }

    public function __toString()
    {
        return $this->getTracker();
    }



}