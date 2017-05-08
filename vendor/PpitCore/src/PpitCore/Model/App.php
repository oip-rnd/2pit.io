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

/**
 * The App class hosts the specification of apps.
 *
 * An app has an identifier and a specification.
 */
class App implements InputFilterAwareInterface
{
	/** @var int */ public $id;
	/** @var int */ public $instance_id;
	/** @var string */ public $status;
	/** @var string */ public $identifier;
	/** @var string */ public $specification;
	/** @var array */ public $audit;
	/** @var string */ public $update_time;

    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

    // Static fields
    /** @var \PpitCore\Model\GenericTable */ private static $table;
    
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
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->specification = (isset($data['specification'])) ? json_decode($data['specification'], true) : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['status'] = $this->status;
    	$data['identifier'] = $this->identifier;
    	$data['specification'] = $this->specification;
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
    	$data['specification'] = json_encode($this->specification);
    	$data['audit'] = json_encode($this->audit);
    	return $data;
    }

    /**
     * Returns an array of App instances:
     * The transient property 'is_open' is computed, based on the opening and the closing date: A place is open if the opening and closing dates are framing the current date.
     * If $mode == 'todo', the list is filtered on only active apps.
     * Otherwise, the list is filtered on places matching the key-value pairs provided in the $params argument:
     * - if the key has the form 'min_<property_name>', only the rows having a value for this property greater than the argument are kept
     * - if the key has the form 'max_<property_name>', only the rows having a value for this property less than the argument are kept
     * - else only the row which are 'like' (in SQL sense) the argument are kept in the list.
     * Deleted apps remains in the database with a 'deleted' status, so in any case, the getList methods filters apps with the 'deleted' status. 
     * The result is primarily ordered according to the value of $major with the direction (ASC or DESC) specified by $dir, and secondarily by ascending 'identifier'
     * @param array $params
     * @param string $major
     * @param string $dir
     * @param string $mode
     * @return App[]
     */
    public static function getList($params, $major = 'identifier', $dir = 'ASC', $mode = 'todo')
    {
    	$select = App::getTable()->getSelect()
    		->order(array($major.' '.$dir, 'identifier'));
    	$where = new Where;
    	$where->notEqualTo('core_app.status', 'deleted');

    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->equalTo('core_app.status', 'active');
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('core_app.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('core_app.'.substr($propertyId, 4), $params[$propertyId]);
    			else $where->like('core_app.'.$propertyId, '%'.$params[$propertyId].'%');
    		}
    	}
    	$select->where($where);

    	$cursor = App::getTable()->selectWith($select);
    	$apps = array();
    	foreach ($cursor as $app) {
    		$app->properties = $app->getProperties();
    		$apps[$app->id] = $app;
    	}
    	return $apps;
    }
    
    /**
     * Retrieve from the database the app having the giving value as the given specified column ('id' as a default).
     * @param string $id
     * @param string $column
     * @return Place
     */
    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();
    	$app = App::getTable()->get($id, $column);

    	if ($app) {
	    	$app->properties = $app->getProperties();
    	}

    	return $app;
    }

    /**
     * Returns a new instance of App.
     * The status is set to 'new'.
     * @return App
     */
    public static function instanciate()
    {
    	$context = Context::getCurrent();
    	$app = new App;
    	$app->status = 'new';
    	return $app;
	}

	/**
	 * Either this key belongs to the page specification, in which case the method returns the corresponding value
	 * Or it does not belong to the instance specification, in which case the method delegates the request to the context (see getConfig() in \PpitCore\Model\Context).
	 * @param string $key
	 * @return boolean
	 */
	public function getConfig($key = null) {
		$context = Context::getCurrent();
		if (array_key_exists($key, $this->specifications)) return $this->specifications[$key];
		else return $context->getConfig($key);
	}

    /**
     * Loads the data into the App object depending of an array, typically constructed by the controller with value extracted from an HTTP request.
     * Only the properties present as a key in the argument array are updated in the target object.
     * As a protection against bugs or attacks from the view level, every string property are trimed, cleaned of tags and checked against max length.
     * If the protection check failed, the method returns the string 'Integrity' otherwise it returns the string 'OK'.
     * The audit database property is augmented with a row storing the current date and time, the user doing the update, and the list of updated properties along with the new value.
     * @param array $data
     * @return string
     */
	public function loadData($data)
	{
    	$context = Context::getCurrent();
		$auditRow = array(
				'time' => Date('Y-m-d G:i:s'),
				'n_fn' => $context->getFormatedName(),
		);
    	if (array_key_exists('status', $data)) {
	    	$status = trim(strip_tags($data['status']));
			if ($status == '' || strlen($status) > 255) return 'Integrity';
    		if ($this->status != $status) $auditRow['status'] = $this->status = $status;
    	}
    	if (array_key_exists('identifier', $data)) {
	    	$identifier = trim(strip_tags($data['identifier']));
			if ($identifier == '' || strlen($identifier) > 255) return 'Integrity';
    		if ($this->identifier != $identifier) $auditRow['identifier'] = $this->identifier = $identifier;
    	}
    	if (array_key_exists('specification', $data)) {
    		$specification = $data['specification'];
    		if ($this->specification != $specification) $auditRow['specification'] = $this->specification = $specification;
    	}
    	 
		// Update the audit
		$this->audit[] = $auditRow;
		
    	return 'OK';
	}
	
    /**
     * Adds a new row in the database after checking that it does not conflict with an existing, not deleted, app with the same 'identifier'. 
     * In such a case the methods does not affect the database and returns 'Duplicate', otherwise it returns 'OK'.
     * @return string
     */
	public function add()
    {
		if (Generic::getTable()->cardinality('core_app', array('status != ?' => 'deleted', 'identifier' => $this->identifier)) > 0) return 'Duplicate';
    	$this->id = null;
    	App::getTable()->save($this);
		return ('OK');
    }

    /**
     * Update an existing row in the database.
     * If $update_time is provided, an isolation check is performed, such that the current update time in the database is not greater than the one given as an argument.
     * In such a case the methods does not affect the database and returns 'Isolation', otherwise it returns 'OK'.
     * @param string $update_time
     * @return string
     */
    public function update($update_time)
    {
    	$app = App::get($this->id);
    	if ($update_time && $app->update_time > $update_time) return 'Isolation';
    	App::getTable()->save($this);
		return ('OK');
    }

    /**
     * @param Interaction $interaction
     * @return string
     */
    public static function processInteraction($data)
    {
    	$context = Context::getCurrent();
    	$connection = App::getTable()->getAdapter()->getDriver()->getConnection();
    	$connection->beginTransaction();
    	try {
    		if ($data['action'] == 'update' || $data['action'] == 'delete') $app = App::get($data['identifier'], 'identifier');
    		elseif ($data['action'] == 'add') $app = App::instanciate();
    		if ($data['action'] == 'delete') $rc = $app->delete(null);
    		else {
    			if ($app->loadData($data) != 'OK') throw new \Exception('View error');
    			if (!$app->id) $rc = $app->add();
    			else $rc = $app->update(null);
    			$data['result'] = $rc;
    			if ($rc != 'OK') {
    				$connection->rollback();
			    	return $rc;
    			}
    		}
    		$connection->commit();
    	}
    	catch (\Exception $e) {
    		$connection->rollback();
    		throw $e;
    	}
    	return $rc;
    }
    
    /**
     * Checks if this app can de deleted. 
     * @return boolean
     */
    public function isDeletable() {
    	$config = Context::getCurrent()->getConfig();
    	foreach($config['ppitCoreDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}
		return true;
	}
    
    /**
     * Delete the row in the database
     */
	public function delete($update_time)
    {
    	$app = App::get($this->id);
    	if ($update_time && $app->update_time > $update_time) return 'Isolation';
    	$this->status = 'deleted';
    	App::getTable()->save($this);

		return ('OK');
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
     * Returns the object to relational manager for the Place class
     */
    public static function getTable()
    {
    	if (!App::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		App::$table = $sm->get('PpitCore\Model\AppTable');
    	}
    	return App::$table;
    }
}
