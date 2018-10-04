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
use PpitCore\Model\Generic;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Filter\StripTags;

/**
 * The Credit class implements the P-pit credits log for a P-Pit instance.
 * A credit object encapsulates the type (ex. p-pit-community), the current available credit quantity and the activation date.
 */
class Credit implements InputFilterAwareInterface
{
    /** @var int */ public $id;
    /** @var int */ public $instance_id;
    /** @var string */ public $status;
    /** @var string */ public $type;
    /** @var int */ public $quantity;
    /** @var string */ public $activation_date;
	/** @var array */ public $audit;
    /** @var string */ public $update_time;

    // Transient properties
    /** @var array */ public $properties;
    /** @var array */ public $periods;

    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

    // Static fields
    /** @var \PpitCore\Model\Credit */ private static $table;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Used for relational (database) to object (php) mapping.
     * @param array data
     */
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->quantity = (isset($data['quantity'])) ? $data['quantity'] : null;
        $this->activation_date = (isset($data['activation_date'])) ? $data['activation_date'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
    }
    /**
     * Provides an array-style access to properties for generic and configurable algorythms at the controller and view layers.
     * @return array
     */
    public function getProperties()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['status'] = $this->status;
    	$data['type'] = $this->type;
    	$data['quantity'] = (int) $this->quantity;
    	$data['activation_date'] =  ($this->activation_date) ? $this->activation_date : null;
    	$data['audit'] = $this->audit;
		return $data;
    }
    
    /**
     * Used for object (php) to relational (database) mapping.
     * @return array
     */
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['audit'] = json_encode($this->audit);
    	return $data;
    }

    /**
     * Returns an array of Credit instances:
     * If $mode == 'todo', the list is filtered as to return only credits that are already activated.
     * Otherwise, the list is filtered on credits matching the key-value pairs provided in the $params argument:
     * - if the key has the form 'min_<property_name>', only the rows having a value for this property greater than the argument are kept
     * - if the key has the form 'max_<property_name>', only the rows having a value for this property less than the argument are kept
     * - else only the row which are 'like' (in SQL sense) the argument are kept in the list.
     * The result is primarily ordered according to the value of $major with the direction (ASC or DESC) specified by $dir, and secondarily by 'type'
     * @param array $params
     * @param string $major
     * @param string $dir
     * @param string $mode
     * @return Credit[]
     */
    public static function getList($params, $major, $dir, $mode = 'todo')
    {
    	$select = Credit::getTable()->getSelect()
			->order(array($major.' '.$dir, 'type'));
		$where = new Where;
		
    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->lessThanOrEqualTo('activation_date', date('Y-m-d'));
    	}
    	else {

    		// Set the filters
    		foreach ($params as $propertyId => $property) {
				if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo(substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo(substr($propertyId, 4), $params[$propertyId]);
    			else $where->like($propertyId, '%'.$params[$propertyId].'%');
    		}
    	}

    	$select->where($where);
		$cursor = Credit::getTable()->selectWith($select);
		$credits = array();
		foreach ($cursor as $credit) {
			$credit->properties = $credit->toArray();
			$credits[] = $credit;
		}
		return $credits;
    }

    /**
     * Retrieve from the database the instance having the giving value as the given specified column ('id' as a default).
     * @param string $id
     * @param string $column
     * @return instance of Instance
     */
    public static function get($id, $column = 'id')
    {
    	$credit = Credit::getTable()->get($id, $column);
    	return $credit;
    }

    /**
     * @return Credit
     */
    public static function instanciate()
    {
		$credit = new Credit;
		return $credit;
    }

    /**
     * Adds a new row in the database.
     * @return string
     */
    public function add()
    {
    	$context = Context::getCurrent();

    	$this->id = null;
    	Credit::getTable()->save($this);
    
    	return ('OK');
    }

    /**
     * Update an existing row in the database.
     * If $update_time is provided, an isolation check is performed, such that the current update time in the database is not greater than the one given as an argument.
     * In suche a cas the methods does not affect the database and returns 'Isolation', otherwise it returns 'OK'.
     * @param string $update_time
     * @return string
     */
    public function update($update_time)
    {
    	$context = Context::getCurrent();

    	$credit = Credit::get($this->id);
    	if ($credit->update_time > $update_time) return 'Isolation';
    	Credit::getTable()->save($this);
    
    	return 'OK';
    }

    /**
     * Delete the row in the database
     */
    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$credit = Credit::get($this->id);
    
    	// Isolation check
    	if ($credit->update_time > $update_time) return 'Isolation';
    	 
    	Credit::getTable()->delete($this->id);
    
    	return 'OK';
    }

    /**
     * Not used in P-Pit
     * {@inheritDoc}
     * @see \Zend\InputFilter\InputFilterAwareInterface::setInputFilter()
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Not used in P-Pit
     * {@inheritDoc}
     * @see \Zend\InputFilter\InputFilterAwareInterface::getInputFilter()
     */
    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    /**
     * Returns the object to relational manager for the Instance class
     */
    public static function getTable()
    {
    	if (!Credit::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Credit::$table = $sm->get('PpitCore\Model\CreditTable');
    	}
    	return Credit::$table;
    }
}