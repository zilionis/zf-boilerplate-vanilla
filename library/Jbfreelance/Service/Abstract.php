<?php

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Query,
    Doctrine\ORM\QueryBuilder,
    Doctrine\ORM\Query\Expr;

/**
 * Jbfreelance\Service\Abstract
 *
 * @author Jason Brown <jason.brown@jbfreelance.co.uk>
 */
class Jbfreelance_Service_Abstract
{
    /**
     * Instance of the Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    
    /**
     * Fully qualified entity name.
     * @var string 
     */
    protected $entityClass;
    
    /**
     * Creates an instance of the service, taking an optional configuration.
     * @param array $config 
     */
    public function __construct($config)
    {
        // set any configuration if provided
        if (is_array($config)) {
			foreach ($config as $key => $value) {
				$method = 'set' . ucfirst($key);
                
				if (method_exists($this, $method)) {
					$this->{$method}($value);
				}
			}
    	}
    }
    
    /**
     * Sets the entity manager for the service.
     * @param Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
    	$this->entityManager = $entityManager;
    }
    
    /**
     * Returns the entity manager in use by the service.
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
    	return $this->entityManager;
    }
    
    /**
     * Returns the full index of entities managed by the service.
     * @param integer $limit
     * @param integer $offset
     * @param boolean $indexById
     * @return array 
     */
    public function index($limit = null, $offset = null, $indexById = false, $includeDeleted = false)
    {
        return $this->indexWithQueryBuilder($limit, $offset, $indexById, Query::HYDRATE_OBJECT, $includeDeleted);
    }
    
    /**
     * Returns an index of entities managed by the service, each entity in array
     * format rather than object format.
     * @param integer $limit
     * @param integer $offset
     * @param boolean $indexById
     * @return array 
     */
    public function indexAsArray($limit = null, $offset = null, $indexById = false, $includeDeleted = false)
    {
        return $this->indexWithQueryBuilder($limit, $offset, $indexById, Query::HYDRATE_ARRAY, $includeDeleted);
    }
    
    /**
     * Performs the actual index process.
     * @param integer $limit
     * @param integer $offset
     * @param boolean $indexById
     * @param integer $hydrateMode
     * @return array 
     */
    protected function indexWithQueryBuilder($limit = null, $offset = null, $indexById = false, $hydrateMode = Query::HYDRATE_OBJECT, $includeDeleted = false)
    {
        // create a query builder
        $qb = $this->entityManager->createQueryBuilder();

        // set up the basic query
        $qb->select('m')
           ->from($this->entityClass, 'm');
        
        // hide deleted items
        if (!$includeDeleted && property_exists($this->entityClass, 'deleted')) {
            $qb->where('m.deleted IS NULL');
        }
        
        // add in the pagination options
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        
        // perform the query
        $q = $qb->getQuery();
        
        $result = $q->getResult($hydrateMode);
        
        if ($indexById) {
            $output = array();
            
            foreach ($result as $row) {
                $output[$row->id] = $row;
            }
            
            return $output;
        } else {
            return $result;
        }
    }

    /**
     * Returns the full count of entities managed by the service.
     * @return integer
     */
    public function count()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select($qb->expr()->count('m.id'))
           ->from($this->entityClass, 'm');

        $q = $qb->getQuery();

        $result = $q->getSingleScalarResult();
        
        return (int)$result;
    }
    
    /**
     * Searches according to the supplied filters and ordering criteria.
     * @param array $filters
     * @param array $order
     * @param integer $limit
     * @param integer $offset
     * @return array
     * @throws \RuntimeException 
     */
    public function search(array $filters = null, array $order = null, $limit = null, $offset = null)
    {
        // set up base query
        $qb = $this->entityManager->createQueryBuilder();
        
        $qb->select('m')
           ->from($this->entityClass, 'm');
        
        // compose the WHERE query
        $whereExp = $qb->expr()->andx();

        $filters = $this->processFilters($filters, $qb, $whereExp);
        $filters = $this->processGenericFilters($filters, $qb, $whereExp);
        
        // add where expression composed through previous steps
        if ($whereExp->count()) {
            $qb->where($whereExp);
        }
        
        // add order clauses
        if (!empty($order)) {
            $firstOrderClause = true;
            foreach ($order as $field => $direction) {
                if ($firstOrderClause) {
                    $qb->orderBy("m.{$field}", $direction);
                } else {
                    $qb->addOrderBy("m.{$field}", $direction);
                }

                $firstOrderClause = false;
            }
        }
        
        // set pagination options
        if (!empty($limit)) {
            $qb->setMaxResults($limit);
            
            if (!empty($offset)) {
                $qb->setFirstResult($offset);
            }
        }
        
        // execute
        if (method_exists($this, 'customSearch')) {
            return $this->customSearch($qb);
        }
        
        $query = $qb->getQuery();
        
        return $query->execute();
    }
    
    /**
     * Counts search results according to the supplied filters.
     * @param array $filters
     * @return integer 
     */
    public function countSearch(array $filters = null)
    {
        // set up base query
        $qb = $this->entityManager->createQueryBuilder();
        
        $qb->select($qb->expr()->count('m.id'))
           ->from($this->entityClass, 'm');
        
        // compose the WHERE query
        $whereExp = $qb->expr()->andx();

        $filters = $this->processFilters($filters, $qb, $whereExp);
        $filters = $this->processGenericFilters($filters, $qb, $whereExp);
        
        // add where expression composed through previous steps
        if ($whereExp->count()) {
            $qb->where($whereExp);
        }
        
        // execute
        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();

        return (int)$result;
    }
    
    /**
     * Placeholder method to be overridden in specific services. Allows custom 
     * parsing of filters, called before processGenericFilters.
     * @param array $filters
     * @param QueryBuilder $qb
     * @param Expr\Andx $where 
     */
    protected function processFilters(array $filters, QueryBuilder $qb, Expr\Andx $where)
    {
        return $filters;
    }
    
    /**
     * Processes a set of filters, which are presumed to be against existing properties.
     * @param array $filters
     * @param QueryBuilder $qb
     * @param Expr\Andx $where
     * @return array 
     */
    protected function processGenericFilters(array $filters, QueryBuilder $qb, Expr\Andx $where)
    {
        $params = array();
        
        $i = 0;
        
        foreach ($filters as $property => $expression) {
            $i++;
            
            // if just a value supplied, convert it to an array with the default 'equals' operator
            if (!is_array($expression)) {
                $expression = array('=', $expression);
            }
            
            // get the operator
            $operator = strtolower(array_shift($expression));
            
            // check operator
            if ($operator == 'between') {
                // BETWEEN a AND b
                $value = array_shift($expression);
                
                // create expression
                $expr = new Expr();
                $stmt = $expr->between("m.{$property}", ":betweenFrom{$i}", ":betweenTo{$i}");
                
                // add parameters
                $params["betweenFrom{$i}"] = $value[0];
                $params["betweenTo{$i}"] = $value[1];
            } else {
                // =, !=, <, <=, >, >=
                $value = array_shift($expression);

                // special behaviour for NULLs
                if ($value === null && $operator == '=') {
                    $stmt = new Expr\Comparison("m.{$property}", 'IS', 'NULL');
                } elseif ($value === null && $operator == '!=') {
                    $stmt = new Expr\Comparison("m.{$property}", 'IS NOT', 'NULL');
                } else {
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    
                    $stmt = new Expr\Orx();
                    
                    foreach ($value as $individualValue) {
                        // create expression
                        $stmt->add(new Expr\Comparison("m.{$property}", $operator, ":compare{$i}"));
                        
                        // add parameter
                        $params["compare{$i}"] = $individualValue;
                        
                        $i++;
                    }
                }
            }
            
            // add where to the query
            $where->add($stmt);
            
            // remove the criteria from the stack
            unset($filters[$property]);
        }
        
        // add in parameters
        foreach ($params as $param => $expression) {
            $qb->setParameter($param, $expression);
        }
        
        return $filters;
    }
    
    /**
     * Retrieves a single entity.
     * @param integer $id 
     * @return Jbfreelance\Entity\Abstract
     */
    public function get($id)
    {
        return $this->entityManager->find($this->entityClass, $id);
    }
    
    /**
     * Saves a single entity.
     * @param Jbfreelance\Entity\Abstract $entity
     * @return integer 
     */
    public function save(Jbfreelance_Entity_Abstract $entity)
    {
        $this->persistAndFlush($entity);
        
        return $entity->id;
    }
    
    /**
     * Persists an entity but does not flush it to the DB.
     * @param Abstract $entity 
     */
    public function deferSave(Jbfreelance_Entity_Abstract $entity) {
        $this->entityManager->persist($entity);
    }
    
    /**
     * Flushes all persisted entities to the DB. 
     */
    public function deferFlush() {
        $this->entityManager->flush();
    }
    
    /**
     * Saves a batch of entities with a single flush to the database. More optimal
     * than calling save() many times.
     * @param array $entities
     */
    public function batchSave(array $entities)
    {
        foreach ($entities as $entity) {
            if (!$entity instanceof Jbfreelance_Entity_Abstract) {
                throw new \RuntimeException('Invalid entity included in batch save operation');
            }
            
            $this->entityManager->persist($entity);
        }
        
        $this->entityManager->flush();
    }
    
    /**
     * Deletes a single entity.
     * @param Jbfreelance\Entity\Abstract $entity
     * @param boolean $hardDelete Whether to fully delete the entity.
     */
    public function delete(Jbfreelance_Entity_Abstract $entity, $hardDelete = false)
    {
	    // check if we should be performing a soft delete here
        if (property_exists($entity, 'deleted') && !$hardDelete) {
            // set the deleted property to the current time
        	$entity->deleted = new \DateTime();
            $this->persistAndFlush($entity);
        } else {
            // fully remove from the DB
	        $this->removeAndFlush($entity);
        }
    }
    
    /**
     * Persists and flushes an entity using the entity manager.
     * @param Jbfreelance\Entity\Abstract $entity
     */
    protected function persistAndFlush(Jbfreelance_Entity_Abstract $entity)
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (PDOException $e) {
        	throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Removes and flushes an entity using the entity manager.
     * @param Jbfreelance\Entity\Abstract $entity
     */
    protected function removeAndFlush(Jbfreelance_Entity_Abstract $entity)
    {
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch (PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Returns the repository for the entity type handled by this service.
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->entityManager->getRepository($this->entityClass);
    }
}