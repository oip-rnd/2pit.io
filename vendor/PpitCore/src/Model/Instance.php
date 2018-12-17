<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\db\sql\Where;

/**
  * The Instance class implements logically separated instances of the 2pit application.
  *
  * An instance encapsulates for each application its fully qualified domain name (fqdn), the default locale, 
  * the sponsor instance (for affiliation), the accepted version of the ethical charter, the default home page,
  * the specifications and the legal notices.
  * The specifications property is the place to store any parameter that is specific to this instance, versus the standard default value.
  */
class Instance
{
    /** @var int */ public $id;
    /** @var string */ public $status;
    /** @var string */ public $fqdn;
    /** @var string */ public $default_locale;
    /** @var string */ public $caption;
    /** @var string */ public $default_place_id;
	/** @var string */ public $sponsor_instance_caption;
    /** @var boolean */ public $is_active;
    /** @var int */ public $validated_ethical_charter_id;
    /** @var string */ public $applications;
	/** @var string */ public $home_page;
    /** @var array */ public $specifications;
    /** @var string */ public $legal_notices;
    /** @var array */ public $audit = array();
    /** @var string */ public $update_time;

    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

    // Transient properties
    /** @var array */ public $administrators;
    
    // Static fields
    /** @var \Model\Instance */ private static $table;

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
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->fqdn = (isset($data['fqdn'])) ? $data['fqdn'] : null;
        $this->default_locale = (isset($data['default_locale'])) ? $data['default_locale'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->default_place_id = (isset($data['default_place_id'])) ? $data['default_place_id'] : null;
        $this->sponsor_instance_caption = (isset($data['sponsor_instance_caption'])) ? $data['sponsor_instance_caption'] : null;
        $this->is_active = (isset($data['is_active'])) ? $data['is_active'] : null;
        $this->validated_ethical_charter_id = (isset($data['validated_ethical_charter_id'])) ? $data['validated_ethical_charter_id'] : null;
        $this->applications = (isset($data['applications'])) ? json_decode($data['applications'], true) : null;
        $this->home_page = (isset($data['home_page'])) ? $data['home_page'] : null;
        $this->specifications = (isset($data['specifications'])) ? json_decode($data['specifications'], true) : null;
        $this->legal_notices = (isset($data['legal_notices'])) ? $data['legal_notices'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : array();
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
    	$data['status'] = $this->status;
    	$data['fqdn'] = $this->fqdn;
    	$data['default_locale'] = $this->default_locale;
    	$data['caption'] = $this->caption;
    	$data['default_place_id'] = (int) $this->default_place_id;
    	$data['sponsor_instance_caption'] = $this->sponsor_instance_caption;
    	$data['is_active'] = $this->is_active;
    	$data['validated_ethical_charter_id'] = $this->validated_ethical_charter_id;
    	$data['applications'] = $this->applications;
    	$data['home_page'] = $this->home_page;
    	$data['specifications'] = $this->specifications;
    	$data['legal_notices'] = $this->legal_notices;
    	$data['audit'] = $this->audit;
    	return $data;
    }
    
	/**
	 * Used for object (php) to relational (database) mapping.
	 * The difference between getProperties() and toArray() is that getProperties does not transform data while toArray do sometime.
	 * For example, toArray() converts arrays to JSON.
	 * @return array
	 */
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['applications'] = json_encode($this->applications);
    	$data['specifications'] = json_encode($this->specifications, JSON_PRETTY_PRINT);
    	$data['audit'] = json_encode($this->audit);
    	return $data;
    }

    /**
     * Returns an array of Instance instances:
     * - without filtering the list if $mode == 'todo'
     * - matching (with the 'like' sql comparator) the key-value pairs provided in the params argument otherwise
     * The result is primarily ordered according to the value of $major with the direction (ASC or DESC) specified by $dir, and secondarily by 'caption'
     * @param array $params
     * @param string $major
     * @param string $dir
     * @param string $mode
     * @return Instance[]
     */
    public static function getList($params, $major = 'caption', $dir = 'ASC', $mode = 'search')
    {
    	$context = Context::getCurrent();
    	$select = Instance::getTable()->getSelect();
    
    	$where = new Where();
    
    	// Todo list vs search modes
    	if ($mode == 'todo') {
    
    	}
    	else {
    		 
    		// Set the filters
    		foreach($params as $key => $value) $where->like($key, '%'.$value.'%');
    	}
    
    	// Sort the list
    	$select->where($where)->order(array($major.' '.$dir, 'caption'));
    
    	$cursor = Instance::getTable()->selectWith($select);
    	$instances = array();
    	foreach ($cursor as $instance) $instances[] = $instance;
    
    	return $instances;
    }
    
    /**
     * Retrieve the instances having the giving value as the given specified column ('id' as a default).
     * In 'config' specification mode, the specifications property is overwritten with the current config value (for testing new specific value of parameters)
     * @param int $id
     * @param string $column
     * @return Instance
     */
    public static function get($id, $column = 'id')
    {
    	$config = Context::getCurrent()->getConfig();
    	
    	$instance = Instance::getTable()->get($id, $column);
		if ($instance && $config['specificationMode'] == 'config') $instance->specifications = $config['specifications'];
		return $instance;
    }

    /**
     * Appropriately initializes the properties.
     * The specifications property has a json format in the database. It is set as an empty php array as a default.
     * @return Instance
     */
    public static function instanciate()
    {
    	$config = Context::getCurrent()->getConfig();
    
    	$instance = new Instance;
    	$instance->specifications = array();
    	$instance->is_active = 1;
    	return $instance;
    }
    
    public function getDefaultLocale() {
    	return ($this->default_locale) ? $this->default_locale : 'fr_FR';
    }
    
    /**
     * Loads the data into the Instance object depending of an array, typically constructed by the controller with value extracted from an HTTP request.
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
    		$status = $data['status'];
    		if (!$status || strlen($status) > 255) return 'Integrity';
    		if ($this->status != $status) $auditRow['status'] = $this->status = $status;
    	}
        if (array_key_exists('fqdn', $data)) {
	    	$fqdn = $data['fqdn'];
    		if (strlen($fqdn) > 255) return 'Integrity';
    		if ($this->fqdn != $fqdn) $auditRow['fqdn'] = $this->fqdn = $fqdn;
    	}
    	if (array_key_exists('default_locale', $data)) {
	    	$default_locale = $data['default_locale'];
    		if (!$default_locale || strlen($default_locale) > 255) return 'Identity';
    		if ($this->default_locale != $default_locale) $auditRow['default_locale'] = $this->default_locale = $default_locale;
    	}
    	if (array_key_exists('caption', $data)) {
	    	$caption = $data['caption'];
    		if (!$caption || strlen($caption) > 255) return 'Integrity';
			$caption = explode('/', $caption);
			$caption = implode('_', $caption);
    		if ($this->caption != $caption) $auditRow['caption'] = $this->caption = $caption;
    	}
    	if (array_key_exists('default_place_id',$data)) {
	    	$default_place_id = (int) $data['default_place_id'];
    		if ($this->default_place_id != $default_place_id) $auditRow['default_place_id'] = $this->default_place_id = $default_place_id;
		}
    	if (array_key_exists('sponsor_instance_caption', $data)) {
	    	$sponsor_instance_caption = $data['sponsor_instance_caption'];
    		if (!$sponsor_instance_caption || strlen($sponsor_instance_caption) > 255) return 'Identity';
    		if ($this->sponsor_instance_caption != $sponsor_instance_caption) $auditRow['sponsor_instance_caption'] = $this->sponsor_instance_caption = $sponsor_instance_caption;
      	}
    	if (array_key_exists('is_active', $data)) {
	    	$is_active = $data['is_active'];
    		if ($this->is_active != $is_active) $auditRow['is_active'] = $this->is_active = $is_active;
    	}
		if (array_key_exists('default_place_id', $data)) {
	    	$default_place_id = $data['default_place_id'];
    		if ($this->default_place_id != $default_place_id) $auditRow['default_place_id'] = $this->default_place_id = $default_place_id;
    	}
    	if (array_key_exists('comment', $data)) { // Deprecated
    		$comment = trim(strip_tags($data['comment']));
    		if (strlen($comment) > 2047) return 'Integrity';
    		if ($this->comment != $comment) $auditRow['comment'] = $this->comment = $comment;
    	}
		if (array_key_exists('validated_ethical_charter_id',$data)) {
	    	$validated_ethical_charter_id = (int) $data['validated_ethical_charter_id'];
    		if ($this->validated_ethical_charter_id != $validated_ethical_charter_id) $auditRow['validated_ethical_charter_id'] = $this->validated_ethical_charter_id = $validated_ethical_charter_id;
		}
        if (array_key_exists('applications', $data)) {
	    	$applications = $data['applications'];
    		if ($this->applications != $applications) $auditRow['applications'] = $this->applications = $applications;
    	}
        if (array_key_exists('home_page', $data)) {
	    	$home_page = $data['home_page'];
    		if (!$home_page || strlen($home_page) > 255) return 'Integrity';
    		if ($this->home_page != $home_page) $auditRow['home_page'] = $this->home_page = $home_page;
    	}
    	if (array_key_exists('legal_notices',$data)) {
			$legal_notices = $data['legal_notices'];
			if (strlen($legal_notices) > 16777215) return 'Integrity';
    		if ($this->legal_notices != $legal_notices) $auditRow['legal_notices'] = $this->legal_notices = $legal_notices;
		}
    	$this->audit[] = $auditRow;
    	return 'OK';
    }

    /**
     * Adds a new row in the database after checking that it does not conflict with an existing instance with the same 'caption'. 
     * In such a case the methods does not affect the database and returns 'Duplicate', otherwise it returns 'OK'.
     * @return string
     */
    public function add()
    {
    	// Creates the instance
    	if (Instance::get($this->caption, 'caption')) return 'Duplicate';
    	$this->status = 'new';
    	$this->id = Instance::getTable()->save($this);
    	return 'OK';
    }

    /**
     * Restfull implementation
     */
    public function loadAndAdd($data)
    {
	   	$context = Context::getCurrent();
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', 'instance->loadData: '.$rc];
	
    	$rc = $this->add();
		if ($rc != 'OK') return ['500', 'instance->add: '.$rc];

		$this->properties = $this->getProperties();
		return ['200', $this->id];
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
    	$context = Context::getCurrent();
    
    	// Check isolation
    	$instance = Instance::getTable()->get($this->id);
    	if ($update_time && $instance->update_time != $update_time) return 'Isolation';

    	// Save and return
    	Instance::getTable()->save($this);
    	return 'OK';
    }

	public function loadAndUpdate($data, $update_time = null)
	{
		$context = Context::getCurrent();
    
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', 'instance->loadData: '.$rc];
    
    	// Save the data
    	$this->update($update_time);
    	if ($rc != 'OK') return ['500', 'instance->update: '.$rc];
    
		$this->properties = $this->getProperties();
    	return ['200', $this->id];
    }
    
    /**
     * Adds an image file in the server's file system.
     * If the file has a 'gif' of 'png' type, it is prealably compressed as a 'jpeg' file.
     * If the path where to store the file is not provided it defaults to './public/img/'. 
     * Relatively to the given or default path the file is stored in the existing subfolder which name is the instance caption, for isolation between instances reason.
     * @param array $file
     * @param string $path
     */
    public static function saveFile($file, $path = './public/img/') {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) {
    		if ($file['size'] > $context->getConfig()['maxUploadSize']) $error = 'Size';
	    	else {
	    		$name = $file['name'];
	    		$type = $file['type'];
	    		$dest = substr($name, 0, strrpos($name, '.')).'.jpg';
	    		$path = $path.$context->getInstance()->caption.'/';
	    		if (file_exists($path.$dest)) unlink($path.$dest);
	    		$adapter = new \Zend\File\Transfer\Adapter\Http();
    			$adapter->addFilter('Rename', $path.$dest);
	    		if ($adapter->receive($file['name'])) {
	    			$info = getimagesize($path.$dest);
	    			if ($info['mime'] == 'image/gif' || $info['mime'] == 'image/png') {
	    				// compress and save file to jpg
	    				if ($info['mime'] == 'image/gif') $image = imagecreatefromgif($path.$dest);
	    				elseif ($info['mime'] == 'image/png') $image = imageCreateFromPng($path.$dest);
	    				unlink($path.$dest);
	    				imagejpeg($image, $path.$dest, 75);
	    			}
	    		}
    		}
    	}
    }
    
    /**
     * Checks if this instance can de deleted by:
     * - Checking there is no active Community on this instance
     * - Calling the isUsed() method for each class which has registered itself in the 'ppitCoreDependencies' list.
     * As soon as an isUsed() method return true, the instance is not deletable and so the method returns false.
     * @return boolean
     */
    public function isDeletable()
    {
    	if (Generic::getTable()->cardinality('core_community', array('status <> ?' => 'deleted', 'instance_id' => $this->id)) > 0) return false;
    	$config = Context::getCurrent()->getConfig();
    	foreach($config['ppitCoreDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}
    	return true;
    }

    /**
     * Delete the row in the database
     */
    public function delete()
    {
    	Instance::getTable()->delete($this->id);
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
    	if (!Instance::$table) {
    		$sm = (new Context)->getServiceManager();
    		Instance::$table = $sm->get(InstanceTable::class);
    	}
    	return Instance::$table;
    }
}
