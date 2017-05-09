<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Model;

use PpitCore\Model\Context;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\TableGateway\TableGateway;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Session\Container;

class UserTable
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
    	$context = Context::getCurrent();
    	$table = $this->tableGateway->getTable();
		$select->where(array($table.'.instance_id' => $context->getInstanceId()));
//		echo $select->getSqlString($this->getAdapter()->getPlatform()).'<br>';
    	return $this->tableGateway->selectWith($select);
    }

    // To use with caution !
    public function transSelectWith($select)
    {
    	$context = Context::getCurrent();
    	$table = $this->tableGateway->getTable();
    	//		echo $select->getSqlString($this->getAdapter()->getPlatform()).'<br>';
    	return $this->tableGateway->selectWith($select);
    }

    public function fetchDistinct($column)
    {
		$select = new \Zend\Db\Sql\Select();
    	$select->from($this->tableGateway->getTable())
			   ->columns(array($column))
			   ->where(array('instance_id' => $context->getInstanceId()))
			   ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT);
		return $this->tableGateway->selectWith($select);
    }
    
    public function get($id, $column = 'user_id')
    {
    	$context = Context::getCurrent();
    	$where = array($column => $id);
       	$where['instance_id'] = $context->getInstanceId();
    	$rowset = $this->tableGateway->select($where);
    	$row = $rowset->current();
    	return $row;
    }

    public function transGet($id, $column = 'user_id')
    {
    	$where = array($column => $id);
    	$rowset = $this->tableGateway->select($where);
    	$row = $rowset->current();
    	return $row;
    }
    
    public function getCurrent($id)
    {
    	$where = array('user_id' => $id);
    	$rowset = $this->tableGateway->select($where);
    	$row = $rowset->current();
    	return $row;
    }
    
    public function save($entity)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
		if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // Instance 0 is for demo => Not updatable
   			$data = $entity->toArray();
			$data['instance_id'] = $entity->instance_id;
			$data['update_time'] = date("Y-m-d H:i:s");
			$data['update_user'] = $context->getUserId();
	        $id = $data['user_id'];
	        if ($id == 0) {
	        	$data['creation_time'] = date("Y-m-d H:i:s");
	        	$data['creation_user'] = $context->getUserId();
	        	$this->tableGateway->insert($data);

		        // Write to the log
/*	    		$writer = new Writer\Stream('data/log/user.txt');
		        $logger = new Logger();
		        $logger->addWriter($writer);
		        $content = '';
		        foreach ($data as $cell) $content .= ';'.$cell;
		        $logger->info('add;'.$context->getUserId().$content);*/

	            $entity->user_id = $this->getAdapter()->getDriver()->getLastGeneratedValue();
	            return $entity->user_id;
	        } else {
	            if ($this->transGet($id)) {

					$where = array('user_id' => $id);
//					$where['instance_id'] = $context->getInstanceId();
	            	$this->tableGateway->update($data, $where);
	            	                    	
	        		// Write to the log
	    			$writer = new Writer\Stream('data/log/user.txt');
	            	$logger = new Logger();
			        $logger->addWriter($writer);
			        $content = '';
			        foreach ($data as $cell) $content .= ';'.$cell;
			        $logger->info('update;'.$context->getUserId().$content);
			        return $entity->user_id;
	            } else {
	                throw new \Exception('Form id does not exist');
	            }
	        }
		}
    }
    
    public function changePassword($entity, $new_password)
    {

    	$context = Context::getCurrent();
    	$config = $context->getCOnfig();
		if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // Instance 0 is for demo => Not updatable

       		$data = $entity->toArray();
       		$bcrypt = new Bcrypt();
    		$bcrypt->setCost(14); // Needs to match password cost in ZfcUser options
    		$data['password'] = $bcrypt->create($new_password);

    		$data['password_init_token'] = null;
    		$data['password_init_validity'] = null;
    		
//	    	$data['instance_id'] = $context->getInstanceId();
    		$data['update_time'] = date("Y-m-d H:i:s");
    		$data['update_user'] = $context->getUserId();
	        $id = $data['user_id'];
    		if ($this->transGet($id)) {
    			$where = array('user_id' => $id);
//				$where['instance_id'] = $context->getInstanceId();
    			$this->tableGateway->update($data, $where);

		        // Write to the log
	    		$writer = new Writer\Stream('data/log/user.txt');
		        $logger = new Logger();
		        $logger->addWriter($writer);
		        $content = '';
		        foreach ($data as $cell) $content .= ';'.$cell;
		        $logger->info('add;'.$context->getUserId().$content);

    		} else {
    			throw new \Exception('Form id does not exist');
    		}
    	}
    }
    
    public function authenticate($identity, $credential)
    {
    	$auth = new AuthenticationService();
    	$dbAdapter = $this->getAdapter();
 
    	$credentialValidationCallback = function($dbCredential, $requestCredential) {
    		$bcrypt = new Bcrypt();
    		$bcrypt->setCost(14);
    		return $bcrypt->verify($requestCredential, $dbCredential);
    	};
    	$authAdapter = new AuthAdapter($dbAdapter, 'user', 'username', 'password', $credentialValidationCallback);

    	$authAdapter
	    	->setIdentity($identity)
    		->setCredential($credential);
    	 
		return $auth->authenticate($authAdapter);
    }

    public function delete($id, $column = 'user_id')
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
		if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) // Instance 0 is for demo => Not updatable
		{
  	    	$where = array($column => $id);
			$where['instance_id'] = $context->getInstanceId();
			$this->tableGateway->delete($where);
	
			// Write to the log
		  	$writer = new Writer\Stream('data/log/user.txt');
		    $logger = new Logger();
		    $logger->addWriter($writer);
		    $logger->info('delete;'.$context->getUserId().$id);
		}
    }

    public function multipleDelete($where)
    {
    	$context = Context::getCurrent();
		if ($context->getInstanceId() != 0)
		{
			$where['instance_id'] = $context->getInstanceId();
	    	$this->tableGateway->delete($where);

	    	// Write to the log
	    	$writer = new Writer\Stream('data/log/user.txt');
	        $logger = new Logger();
	        $logger->addWriter($writer);
	        $content = '';
	        foreach ($where as $column => $value) $content .= ';'.$column.'='.$value;
	        $logger->info('multiple_delete;'.$context->getUserId().$content);
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
	    		$writer = new Writer\Stream('data/log/user.txt');
	            $logger = new Logger();
	            $logger->addWriter($writer);
	            $content = '';
	            foreach ($where as $column => $value) $content .= ';'.$column.'='.$value;
	            $logger->info('multiple_delete;'.$context->getUserId().$content);
	        }
		}
    }
}
