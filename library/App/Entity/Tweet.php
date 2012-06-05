<?php
namespace App\Entity;
use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @Entity(repositoryClass="App\Repository\Tweet")
 * @Table(name="tweets")
 */
class Tweet
{
    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $_id;
    
    
    /**
     * @ManyToOne(targetEntity="App\Entity\Stream")
     * @JoinColumns({
     *  @JoinColumn(name="stream_id", referencedColumnName="id")
     * })
     */
    
    private $_stream;
    
    /** @Column(type="string", name="twitter_id") */
    public $twitterId;
    /** @Column(type="string", name="content") */
    public $content;
    /** @Column(type="string", name="source") */
    public $source;
    /** @Column(type="string", name="profile_img") */
    public $profileImg;
    /** @Column(type="string", name="name") */
    public $name;
    /** @Column(type="string", name="screen_name") */
    public $screenName;
    /** @Column(type="datetime", name="created") */
    protected $_created;
    /** @Column(type="boolean", name="approved") */
    protected $_approved = false;
    
    public function __construct($stream, $twitterId, $content, $source, $profileImg, $name, $screenName, $created)
    {
        $this->_stream = $stream;
        $this->twitterId = $twitterId;
        $this->content = $content;
        $this->source = $source;
        $this->profileImg = $profileImg;
        $this->name = $name;
        $this->screenName = $screenName;
        $this->_created = new \DateTime($created);
    }
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function getCreated($format = \DateTime::RFC2822)
    {
        return $this->_created->format($format);
    }
    
    public function isApproved()
    {
        return $this->_approved;
    }
    
    public function setApproved($approve)
    {
        $this->_approved = $approve;
        return $this;
    }
    
    public function __toString()
    {
        return $this->content;
    }
}