<?php

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Proxy\Proxy;

/**
 * Jbfreelance\Entity\Abstract
 *
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */
class Jbfreelance_Entity_Abstract
{
    /**
     *
     * @var integer
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * Magic getter for accessing entity properties. Calls the getProperty()
     * method if it exists.
     * @param string $property
     * @return mixed
     * @throws \InvalidArgumentException 
     */
    public function __get($property)
    {
        if (!property_exists($this, $property)) {
            throw new \InvalidArgumentException("Class does not have property {$property}");
        }
        
        $methodName = 'get' . ucfirst($property);
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        } else {
            return $this->{$property};
        }
    }
    
    /**
     * Magic setter for setting entity properties. Calls the setProperty()
     * method if it exists.
     * @param string $property
     * @param mixed $value
     * @throws \InvalidArgumentException 
     */
    public function __set($property, $value)
    {
        if (!property_exists($this, $property)) {
            throw new \InvalidArgumentException("Class does not have property {$property}");
        }
        
        $methodName = 'set' . ucfirst($property);
        if (method_exists($this, $methodName)) {
            $this->{$methodName}($value);
        } else {
            $this->{$property} = $value;
        }
    }

    /**
   	 * Converts the entity into an array.
   	 * @param int $maxdepth max recursion to allow
   	 * @param int $depth current depth recursion
   	 * @return array
   	 */
    public function toArray($maxdepth = 1, $depth = 0)
    {
        $tmp = array();
		$depth++;

        // determine whether or not we're still within the maximum depth
        if ($maxdepth === 0 || $depth <= $maxdepth) {
            // get the list of sleepable fields and run through them individually
            $fields = $this->__sleep();

	        foreach ($fields as $field) {
                // retrieve the value of the field
                $value = $this->__get($field);
                
                // differentiate between different types of value
	            if ($value instanceof Proxy) {
                    // with proxies, force them to load their data
	           	 	$value->__load();
                    
                    $tmp[$field] = $value->toArray($maxdepth, $depth);
	        	} elseif ($value instanceof AbstractEntity) {
                    // entity
	                $tmp[$field] = $value->toArray($maxdepth, $depth);
                } elseif ($value instanceof Collection) {
                    // collection
                    $tmp[$field] = array();
                    
                    foreach ($value as $item) {
                        $tmp[$field][] = $item->toArray($maxdepth, $depth);
                    }
                } elseif ($value instanceof \DateTime) {
                    $tmp[$field] = $value->format(\DateTime::ISO8601);
	            } else {
                    // scalar
	                $tmp[$field] = $value;
	            }
	        }
        } else {
            // we're at maximum traversal depth already, just include the ID
            $tmp['id'] = $this->id;
        }

        return $tmp;
    }
    
    /**
     * Returns the sleepable properties for an entity. Defaults to all properties.
     * @return array
     */
    public function __sleep()
    {
        // get the properties of the class
        $reflClass = new \ReflectionClass(get_class($this));
        $properties = $reflClass->getProperties();
        
        // create an array of property names
        $sleepable = array();
        
        foreach ($properties as $property) {
            // skip static properties
            if ($property->isStatic()) continue;
            
            $propertyName = $property->getName();
            
            // skip underscored properties
            // TODO: implement more concrete fix here
            if (substr($propertyName, 0, 1) == '_') continue;
            
            $sleepable[] = $propertyName;
        }
        
        return $sleepable;
    }
}