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
use Zend\Session\Container;
use Zend\Log\Logger;
use Zend\Log\Writer;

class InstanceTable
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

    public function selectWith($select)
    {
//    	throw new \Exception($select->getSqlString($this->getAdapter()->getPlatform()));
    	return $this->tableGateway->selectWith($select);
    }

    public function fetchDistinct($column)
    {
		$select = new \Zend\Db\Sql\Select();
    	$select->from($this->tableGateway->getTable())
			   ->columns(array($column))
    		   ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT);
		return $this->tableGateway->selectWith($select);
    }
    
    public function get($id, $column = 'id')
    {
    	$rowset = $this->tableGateway->select(array($column => $id));
    	$row = $rowset->current();
/*    	if (!$row) {
    		throw new \Exception("Could not find row $id");
    	}*/
    	return $row;
    }

    public function save($entity, $log = false)
    {
		$context = Context::getCurrent();
    	$data = $entity->toArray();
    	$data['update_time'] = date("Y-m-d H:i:s");
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
                $this->tableGateway->update($data, array('id' => $id));
            	                    	
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

    public function delete($id, $column = 'id', $log = false)
    {
    	$context = Context::getCurrent();
        $this->tableGateway->delete(array('id' => $id));
    
		// Write to the log
    	if ($log) {
	    	$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
            $logger = new Logger();
            $logger->addWriter($writer);
            $logger->info('delete;'.$context->getUserId().$id);
        }
    }

    public function multipleDelete($where, $log = false)
    {
    	$context = Context::getCurrent();
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
