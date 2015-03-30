<?php
namespace YYY\Model;

use YYY\DbMapper;
use Zend\Db\Adapter\Adapter;
use YYY\Model\YYY;

// This class represents a database repository to handle yyy.
class DbYYYRepository implements YYYRepositoryInterface
{
	// Private members.
	private $adapter;
	private $mapper;

	// Initializes a new instance of the DbUserRepository class.
    public function __construct(Adapter $adapter)
    {
    	$this->adapter = $adapter;
    	$this->mapper  = new DbMapper($adapter, new YYY, 'yyy', new DbYYYHydrator);
    }

        
    // Gets the yyy with the specified identifier $attributY1.
    public function get($attributY1)
    {
    	$record = $this->mapper->select(array(
    		'id' => $attributY1,
    	))->current();
	if (empty($record))
	{
            throw new \Exception(sprintf(YYY::NO_VALID_USER, $attributY1));			
	}
	return $record;
    }
    
    // Finds the yyy with the specified yyy Entity.
    public function check(YYY $yyy)
    {
        $record = $this->mapper->select(array(
    		'attributY1' => $yyy->attributY1,
                'attributY2' => $yyy->attributY2
    	))->current();
	return $record;
    }

    // Gets all the yyy records.
    public function getAll()
    {
    	// Prepare select.
    	$select = $this->mapper->getSelect();
	$select->columns(array(
            'attributY1'   => 'attributY1',
            'attributY2'   => 'attributY2',
            'create_date'  => 'create_date',
            'update_date'  => 'update_date',
            ));
        
        // Sort by name alphabetically.
    	$select->order('attributY1 ASC');

    	// Get rows.
    	$rows = $this->mapper->executeSelect($select);
	$rows->buffer();
	return $rows;
    }

    // Saves the specified user.
    public function save(YYY $yyy)
    {
        // Update existing record.
	if (!empty($yyy->attributY1))
	{
            // Update record.
            $this->mapper->update(
                array(
                    'attributY1' => $yyy->attributY1, 
                ), $yyy);
            return;
	}

	// Insert record.
	$result = $this->mapper->insert($yyy);
	$yyy->attributY1 = $result->getGeneratedValue();
    }

    // Removes the specified yyy.
    public function remove(YYY $yyy)
    {
        // Remove record.
	$this->mapper->delete(
            array(
                'attributY1' => $yyy->attributY1,
            ));
    }
}