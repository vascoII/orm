<?php
namespace YYY;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ArraySerializable;

// This class represents a database mapper.
class DbMapper implements AdapterAwareInterface
{
    // Private members.
    private $adapter;
    private $hydrator;
    private $prototype;
    private $sql;
    private $tableName;

    // Initializes a new instance of the database mapper class.
    public function __construct(Adapter $adapter, $prototype, $tableName = null, $hydrator = null) {
        $this->setDbAdapter($adapter);
        $this->hydrator = $hydrator ?: new ArraySerializable;
        $this->prototype = $prototype;
        $this->tableName = $tableName;
    }

    // Gets the SQL abstraction layer.
    private function getSql()
    {
        // Check existing SQL abstraction layer.
        if (isset($this->sql)) {
            return $this->sql;
        }

        // Set the SQL abstraction layer.
        $this->sql = new Sql($this->getDbAdapter());
        return $this->sql;
    }

    // Gets the database adapter.
    public function getDbAdapter()
    {
        return $this->adapter;
    }

    // Sets the database adapter.
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    // Selects data.
    public function select($where, $tableName = null, $join = null, $order = null)
    {
        // Prepare select query.
        $sql = $this->getSql();
        $select = $sql->select();
        $select->from($tableName ?: $this->tableName);
        isset($join) && $select->join($join['table'], $join['on'], $join['columns'], $select::JOIN_LEFT);
        isset($where) && $select->where($where);
        isset($order) && $select->order($order);

        //echo $sql->getSqlStringForSqlObject($select);

        // Execute query.
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($statement->execute());
        return $resultSet;
    }

    // Gets select.
    public function getSelect($tableName = null)
    {
        // Prepare select query.
        $sql = $this->getSql();
        $select = $sql->select();
        $select->from($tableName ?: $this->tableName);
        return $select;
    }

    // Executes select.
    public function executeSelect(Select $select)
    {
        // Execute query.
        $sql = $this->getSql();
        //echo $sql->getSqlStringForSqlObject($select);
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($statement->execute());
        return $resultSet;
    }

    // Executes with pagination.
    public function executePagination(Select $select)
    {
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginatorAdapter = new DbSelect($select, $this->getDbAdapter(), $resultSet);
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    // Inserts the specified object in the current or optional specified table.
    public function insert($values, $tableName = null)
    {
        // Prepare insert query.
        $sql = $this->getSql();
        $insert = $sql->insert();
        $insert->into($tableName ?: $this->tableName);
        $data = is_array($values) ? $values : $this->hydrator->extract($values);
        $insert->values($data);

        // Execute query.
        //echo $sql->getSqlStringForSqlObject($insert);
        $statement = $sql->prepareStatementForSqlObject($insert);
        return $statement->execute();
    }

    // Replaces data with the specified where predicate and data in the current or optional specified table.
    public function update($where, $values, $tableName = null)
    {
        // Prepare insert query.
        $sql = $this->getSql();
        $update = $sql->update();
        $update->table($tableName ?: $this->tableName);
        $data = is_array($values) ? $values : $this->hydrator->extract($values);
        $update->set($data);
        isset($where) && $update->where($where);

        // Execute query.
        $statement = $sql->prepareStatementForSqlObject($update);
        return $statement->execute();
    }

    // Deletes data with the specified where predicate in the current or optional specified table.
    public function delete($where, $tableName = null)
    {
        // Prepare delete query.
        $sql = $this->getSql();
        $delete = $sql->delete();
        $delete->from($tableName ?: $this->tableName);
        isset($where) && $delete->where($where);

        // Execute query.
        $statement = $sql->prepareStatementForSqlObject($delete);
        return $statement->execute();
    }

    function getSqlStringForSqlObject($select)
    {
        $sql = $this->getSql();
        return $sql->getSqlStringForSqlObject($select);
    }
}
