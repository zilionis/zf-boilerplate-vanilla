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
    public $_twitterId;
    /** @Column(type="string", name="content") */
    public $_content;
    /** @Column(type="string", name="source") */
    public $_source;
    /** @Column(type="string", name="profile_img") */
    public $_profileImg;
    /** @Column(type="string", name="name") */
    public $_name;
    /** @Column(type="datetime", name="created") */
    protected $_created;
    
    public function __construct($stream, $twitterId, $content, $source, $profileImg, $name, $created)
    {
        $this->_stream = $stream;
        $this->_twitterId = $twitterId;
        $this->_content = $content;
        $this->_source = $source;
        $this->_profileImg = $profileImg;
        $this->_name = $name;
        $this->_created = new \DateTime($created);
    }
    
    public function getCreated($format = \DateTime::RFC2822)
    {
        return $this->_created->format($format);
    }
    
    public function __toString()
    {
        return $this->_content;
    }
}