<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Model;

use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Supports configuration associated to a place
 */
class Config
{
	public $id;
	public $instance_id;
	public $place_id;
	public $status;
	public $identifier;
	public $content;
	public $previous_content;
	public $audit;
	public $update_time;
	
    /** 
     * Ignored 
     */ 
	protected $inputFilter;

    // Static fields
    private static $table;
    
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
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->content = (isset($data['content'])) ? json_decode($data['content'], true) : array();
        $this->previous_content = (isset($data['previous_content'])) ? json_decode($data['previous_content'], true) : array();
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
    }

	/**
	 * Used for object (php) to relational (database) mapping.
	 * @return array
	 */
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['place_id'] = (int) $this->place_id;
    	$data['status'] = $this->status;
    	$data['identifier'] = $this->identifier;
    	$data['content'] = json_encode($this->content, JSON_PRETTY_PRINT);
    	$data['previous_content'] = json_encode($this->previous_content);
    	$data['audit'] = json_encode($this->audit);
    	 
    	return $data;
    }
    
    /**
     * Retrieve from the database the config having the giving value as the given specified column ('id' as a default).
     */
    public static function get($id, $column = 'id', $place_id = null)
    {
    	$context = Context::getCurrent();
    	if (!$place_id) return Config::getTable()->get($id, $column, $place_id, 'place_id'); //$place_id = $context->getPlaceId();
    	return Config::getTable()->get($id, $column, $place_id, 'place_id');
    }
    
    /**
     * Returns a new instance of Config.
     * The status is set to 'new'
     */
    public static function instanciate($place_id, $identifier)
    {
    	$config = new Config;
    	$config->status = 'new';
    	$config->place_id = $place_id;
    	$config->identifier = $identifier;
    	$config->content = array();
    	return $config;
	}

    /**
     * Loads the data into the Config object depending of an array, typically constructed by the controller with value extracted from an HTTP request.
     * Only the properties present as a key in the argument array are updated in the target object.
     * As a protection against bugs or attacks from the view level, every string property are trimed, cleaned of tags and checked against max length.
     * If the protection check failed, the method returns the string 'Integrity' otherwise it returns the string 'OK'.
     * The audit database property is augmented with a row storing the current date and time, the user doing the update, and the list of updated properties along with the new value.
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
	    if (array_key_exists('place_id', $data)) {
	    	$place_id = $data['place_id'];
    		if ($this->place_id != $place_id) $auditRow['place_id'] = $this->place_id = $place_id;
    	}
    	if (array_key_exists('identifier', $data)) {
	    	$identifier = trim(strip_tags($data['identifier']));
			if ($identifier == '' || strlen($identifier) > 255) return 'Integrity';
    		if ($this->identifier != $identifier) $auditRow['identifier'] = $this->identifier = $identifier;
    	}
    	if (array_key_exists('content', $data)) {
			if (!is_array($data['content'])) return 'Integrity';
			foreach ($data['content'] as $key => $value) {
				$path = explode('/', $key);
				$current = &$this->content;
				foreach ($path as $level) {
					$previous = &$current;
		    		$current = &$current[$level];
				}
				if (!$current || $current != $value) {
	    			$previous[$level] = $value;
				}
			}
		}

		// Update the audit
		$this->audit[] = $auditRow;
		
    	return 'OK';
	}
	
    /**
     * Adds a new row in the database after checking that it does not conflict with an existing, not deleted, place with the same 'identifier'. 
     * In such a case the methods does not affect the database and returns 'Duplicate', otherwise it returns 'OK'.
     */
	public function add()
    {
		if (Generic::getTable()->cardinality('core_place', array('status != %' => 'deleted', 'identifier' => $this->identifier)) > 0) return 'Duplicate';
    	$this->id = null;
    	Config::getTable()->save($this);
		return ('OK');
    }

    /**
     * Update an existing row in the database.
     * If $update_time is provided, an isolation check is performed, such that the current update time in the database is not greater than the one given as an argument.
     * In such a case the methods does not affect the database and returns 'Isolation', otherwise it returns 'OK'.
     */
    public function update($update_time)
    {
    	$config = Config::get($this->id);
    	if ($update_time && $config->update_time > $update_time) return 'Isolation';
    	Config::getTable()->save($this);
		return ('OK');
    }

    /**
     * Load data from a structure to this Config and update the database
     */
    public function loadAndUpdate($data)
    {
    	$context = Context::getCurrent();
    	 
    	// Load the data
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', $rc];
print_r($this->content);
    	// Save the data
    	$rc = $this->update(null);
    	if ($rc != 'OK') return ['500', 'config->update: '.$rc];

    	return ['200', $this->identifier];
    }

    public function saveImage($file) {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) {
    		$context = Context::getCurrent();
    		if ($file['size'] > $context->getConfig()['maxUploadSize']) $error = 'Size';
    		else {
				$relative = '/img/'.$context->getInstance()->caption.'/'.$place_identifier.'/'; 
				$path = 'public/img/'.$relative;
    			$name = $file['name'];
    			$type = $file['type'];
    
    			$adapter = new \Zend\File\Transfer\Adapter\Http();
    
    			// Create the file on the file system with $id as a name
    			$adapter->addFilter('Rename', $config->id);
    			if (file_exists($config->id)) unlink($config->id);
    			if ($adapter->receive($file['name'])) {
    				$info = getimagesize($config->id);
    				if (file_exists($path.$name)) unlink($path.$name);
    				if ($info['mime'] == 'image/gif' || $info['mime'] == 'image/png') {
    					// Compress the image
    					if ($info['mime'] == 'image/gif') $image = imagecreatefromgif($path.$name);
    					elseif ($info['mime'] == 'image/png') $image = imageCreateFromPng($path.$name);
    					//compress and save file to jpg
    					imagejpeg($image, $path.$name.'.jpg', 75);
    				}
    				else rename($path.$name, $path.$name.'.jpg');
    			}
    		}
    	}
    }
    
    /**
     * @param Interaction $interaction
     * @return string
     */
    public static function processInteraction($data, $interaction)
    {
    	$context = Context::getCurrent();
    	$identifier = $interaction->category;
    	$config = Config::get($identifier, 'identifier');
		if (!$config) $config = Config::instanciate($context->getPlaceId(), $identifier);
    	$configData = array('content' => $data);
		$rc = $config->loadAndUpdate($configData);
    	return $rc[0].' - '.$rc[1];
    }
    
    /**
     * Checks if this config can de deleted. 
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
     * If $update_time is provided, an isolation check is performed, such that the current update time in the database is not greater than the one given as an argument.
     * In such a case the methods does not affect the database and returns 'Isolation', otherwise it returns 'OK'.
     */
	public function delete($update_time)
    {
    	$place = Place::get($this->id);
    	if ($update_time && $place->update_time > $update_time) return 'Isolation';
    	$this->status = 'deleted';
    	Place::getTable()->save($this);

		return ('OK');
    }
	
	/**
	 * Not used in 2Pit
	 * {@inheritDoc}
	 * @see \Zend\InputFilter\InputFilterAwareInterface::setInputFilter()
	 */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Not used in 2Pit
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
    	if (!Config::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Config::$table = $sm->get(ConfigTable::class);
    	}
    	return Config::$table;
    }
}
