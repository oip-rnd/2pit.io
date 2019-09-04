<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Where;
use Zend\Session\Container;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class GenericTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function getAdapter()
    {
    	return $this->tableGateway->getAdapter();
    }
    
    public function getSelect()
    {
		$select = new \Zend\Db\Sql\Select();
	    $select->from($this->tableGateway->getTable());
    	return $select;
    }
    
    public function selectWith($select, $prototype = false)
    {
    	$context = Context::getCurrent();
    	$table = $this->tableGateway->getTable();
		$select->where(array($table.'.instance_id' => $context->getInstanceId()));
		echo $select->getSqlString($this->getAdapter()->getPlatform()).'<br>';
		if ($prototype) {
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype($prototype);
			$paginatorAdapter = new DbSelect(
					$select,
					$this->tableGateway->getAdapter(),
					$resultSetPrototype
			);
			$paginator = new Paginator($paginatorAdapter);
			return $paginator;
		}
		else return $this->tableGateway->selectWith($select);
    }

	// To use with caution !
    public function transSelectWith($select)
    {
    	$table = $this->tableGateway->getTable();
//		echo $select->getSqlString($this->getAdapter()->getPlatform()).'<br>';
    	return $this->tableGateway->selectWith($select);
    }
    
    public function fetchDistinct($column)
    {
    	$context = Context::getCurrent();
   		$select = new \Zend\Db\Sql\Select();
    	$select->from($this->tableGateway->getTable())
			   ->columns(array($column))
			   ->where(array('instance_id' => $context->getInstanceId()))
    		   ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT);
		return $this->tableGateway->selectWith($select);
    }

    public function cardinality($entity, $where, $group = null)
    {
    	$context = Context::getCurrent();
    	$where['instance_id'] = $context->getInstanceId();
    	$select = new \Zend\Db\Sql\Select();
    	$select->from($entity);
		$select
			->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')))
			->where($where);
//		echo $select->getSqlString($this->getAdapter()->getPlatform()).'<br>';
		return $this->tableGateway->selectWith($select)->current()->count;
    }

    public function distribution($entity, $where, $group)
    {
    	$context = Context::getCurrent();
    	$where['instance_id'] = $context->getInstanceId();
    	$select = new \Zend\Db\Sql\Select();
    	$select->from($entity);
    	$select
	    	->columns(array('group' => $group, 'count' => new \Zend\Db\Sql\Expression('COUNT(*)')))
    		->where($where);
    	$select->group($group);
//		echo $select->getSqlString($this->getAdapter()->getPlatform()).'<br>';
    	$cursor = $this->tableGateway->selectWith($select);
    	$result = array();
    	foreach ($cursor as $row) $result[$row->group] = $row->count;
    	return $result;
    }
    
    public function get($id, $column = 'id', $id2 = false, $column2 = false, $id3 = false, $column3 = false, $id4 = false, $column4 = false, $includeDeleted = false)
    {
    	$context = Context::getCurrent();
    	$where = array($column => $id);
    	if (!$includeDeleted) $where['status != ?'] = 'deleted';
    	$where['instance_id'] = $context->getInstanceId();
    	if ($id2) $where[$column2] = $id2;
    	if ($id3) $where[$column3] = $id3;
    	if ($id4) $where[$column4] = $id4;
    	$rowset = $this->tableGateway->select($where);
    	$row = $rowset->current();
    	return $row;
    }

	// To use with caution !
    public function transGet($id, $column = 'id', $id2 = false, $column2 = false, $id3 = false, $column3 = false, $id4 = false, $column4 = false, $includeDeleted = false)
    {
    	$where = array($column => $id);
    	if (!$includeDeleted) $where['status != ?'] = 'deleted';
    	if ($id2) $where[$column2] = $id2;
    	if ($id3) $where[$column3] = $id3;
    	if ($id4) $where[$column4] = $id4;
    	$rowset = $this->tableGateway->select($where);
    	$row = $rowset->current();
    	return $row;
    }

    public function save($entity, $log = false)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // Instance 0 is for demo => Not updatable
			$data = $entity->toArray();
	    	$data['instance_id'] = $context->getInstanceId();
			$data['update_time'] = date("Y-m-d H:i:s");
	        $entity->update_time = $data['update_time'];
			$data['update_user'] = $context->getUserId();
	        $id = $data['id'];
	        if ($id == 0) {
	        	$data['creation_time'] = date("Y-m-d H:i:s");
	        	$data['creation_user'] = $context->getUserId();
	        	$this->tableGateway->insert($data);
	        	
		        // Write to the log
    			if ($log) {
	    			$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
		            $logger = new Logger();
		            $logger->addWriter($writer);
		            $content = '';
		            foreach ($data as $cell) $content .= ';'.$cell;
		            $logger->info('add;'.$context->getUserId().$content);
	        	}

	            $entity->id = $this->getAdapter()->getDriver()->getLastGeneratedValue();
	            return $entity->id;
	        }
	        else {
	            if ($this->get($id)) {

					$where = array('id' => $id);
					$where['instance_id'] = $context->getInstanceId();
	            	$this->tableGateway->update($data, $where);
	                    	
	        		// Write to the log
			    	if ($log) {
	    				$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
	            		$logger = new Logger();
			            $logger->addWriter($writer);
			            $content = '';
			            foreach ($data as $cell) $content .= ';'.$cell;
			            $logger->info('update;'.$context->getUserId().$content);
		        	}
		        	return $id;
	            }
	            else {
	                throw new \Exception('Form id does not exist: ' . $id);
	            }
	        }
		}
		else return 0;
    }

    public function transSave($entity, $log = false)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // Instance 0 is for demo => Not updatable
    		$data = $entity->toArray();
    		$data['update_time'] = date("Y-m-d H:i:s");
    		$entity->update_time = $data['update_time'];
    		$data['update_user'] = $context->getUserId();
    		$id = $data['id'];
    		if ($id == 0) {
    			$data['creation_time'] = date("Y-m-d H:i:s");
    			$data['creation_user'] = $context->getUserId();
    			$this->tableGateway->insert($data);
    
    			// Write to the log
    			if ($log) {
    				$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
    				$logger = new Logger();
    				$logger->addWriter($writer);
    				$content = '';
    				foreach ($data as $cell) $content .= ';'.$cell;
    				$logger->info('add;'.$context->getUserId().$content);
    			}
    
    			$entity->id = $this->getAdapter()->getDriver()->getLastGeneratedValue();
    			return $entity->id;
    		}
    		else {
    			if ($this->transGet($id)) {
    
    				$where = array('id' => $id);
    				$this->tableGateway->update($data, $where);
    
    				// Write to the log
    				if ($log) {
    					$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
    					$logger = new Logger();
    					$logger->addWriter($writer);
    					$content = '';
    					foreach ($data as $cell) $content .= ';'.$cell;
    					$logger->info('update;'.$context->getUserId().$content);
    				}
    				return $id;
    			}
    			else {
    				throw new \Exception('Form id does not exist');
    			}
    		}
    	}
    	else return 0;
    }
    
    public function delete($id, $column = 'id', $log = false)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // Instance 0 is for demo => Not updatable

			$where = array($column => $id);
			$where['instance_id'] = $context->getInstanceId();
			$this->tableGateway->delete($where);

			// Write to the log
    		if ($log) {
	    		$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
	            $logger = new Logger();
	            $logger->addWriter($writer);
	            $logger->info('delete;'.$context->getUserId().$id);
	        }
		}
    }

    public function multipleDelete($where, $log = false)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // Instance 0 is for demo => Not updatable

			$where['instance_id'] = $context->getInstanceId();
	    	$this->tableGateway->delete($where);

	    	// Write to the log
	    	if ($log) {
	    		$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
	            $logger = new Logger();
	            $logger->addWriter($writer);
	            $content = '';
	            foreach ($where as $column => $value) $content .= ';'.$column.'='.$value;
	            $logger->info('multiple_delete;'.$context->getUserId().$content);
	        }
		}
    }

    public function transMultipleDelete($where, $log = false)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // Instance 0 is for demo => Not updatable

			$this->tableGateway->delete($where);

	    	// Write to the log
	    	if ($log) {
	    		$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
	            $logger = new Logger();
	            $logger->addWriter($writer);
	            $content = '';
	            foreach ($where as $column => $value) $content .= ';'.$column.'='.$value;
	            $logger->info('multiple_delete;'.$context->getUserId().$content);
	        }
		}
    }
}
