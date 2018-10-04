<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Model;

use PpitCore\Model\Account;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
  * DataPrivacy is the class supporting vectors of encrypted data relative to a data type
  *
  * At each encrypted vector is associated an owner ID depending on the type. For example a vcard ID if the type is profile. 
  * The owner of the encrypted vectore has read and write access on it.
  * The instance config defines the other roles having read and/or write access on envrypted data for a given type
  */
class DataPrivacy {
	
	private static $cipher  = MCRYPT_RIJNDAEL_128;
	private static $mode    = 'cbc';
	
	public static $model = array(
		'entities' => array(
			'core_data_privacy' => 	['table' => 'core_data_privacy'],
		),
		'properties' => array(
			'id' => 				['entity' => 'core_data_privacy', 'column' => 'id', 'type' => 'int'],
			'status' => 			['entity' => 'core_data_privacy', 'column' => 'status', 'type' => 'varchar'],
			'type' => 				['entity' => 'core_data_privacy', 'column' => 'type', 'type' => 'varchar'],
			'owner_id' =>	 		['entity' => 'core_data_privacy', 'column' => 'owner_id', 'type' => 'int'],
			'tiny_1' =>	 			['entity' => 'core_data_privacy', 'column' => 'tiny_1', 'type' => 'varchar'],
			'tiny_2' =>	 			['entity' => 'core_data_privacy', 'column' => 'tiny_2', 'type' => 'varchar'],
			'tiny_3' =>	 			['entity' => 'core_data_privacy', 'column' => 'tiny_3', 'type' => 'varchar'],
			'tiny_4' =>	 			['entity' => 'core_data_privacy', 'column' => 'tiny_4', 'type' => 'varchar'],
			'tiny_5' =>	 			['entity' => 'core_data_privacy', 'column' => 'tiny_5', 'type' => 'varchar'],
			'tiny_6' =>	 			['entity' => 'core_data_privacy', 'column' => 'tiny_6', 'type' => 'varchar'],
			'tiny_7' =>	 			['entity' => 'core_data_privacy', 'column' => 'tiny_7', 'type' => 'varchar'],
			'tiny_8' =>	 			['entity' => 'core_data_privacy', 'column' => 'tiny_8', 'type' => 'varchar'],
			'tiny_9' =>	 			['entity' => 'core_data_privacy', 'column' => 'tiny_9', 'type' => 'varchar'],
			'tiny_10' =>	 		['entity' => 'core_data_privacy', 'column' => 'tiny_10', 'type' => 'varchar'],
			'text_1' =>	 			['entity' => 'core_data_privacy', 'column' => 'text_1', 'type' => 'text'],
			'text_2' =>	 			['entity' => 'core_data_privacy', 'column' => 'text_2', 'type' => 'text'],
			'text_3' =>	 			['entity' => 'core_data_privacy', 'column' => 'text_3', 'type' => 'text'],
			'text_4' =>	 			['entity' => 'core_data_privacy', 'column' => 'text_4', 'type' => 'text'],
			'text_5' =>	 			['entity' => 'core_data_privacy', 'column' => 'text_5', 'type' => 'text'],
			'text_6' =>	 			['entity' => 'core_data_privacy', 'column' => 'text_6', 'type' => 'text'],
			'text_7' =>	 			['entity' => 'core_data_privacy', 'column' => 'text_7', 'type' => 'text'],
			'text_8' =>	 			['entity' => 'core_data_privacy', 'column' => 'text_8', 'type' => 'text'],
			'text_9' =>	 			['entity' => 'core_data_privacy', 'column' => 'text_9', 'type' => 'text'],
			'text_10' =>	 		['entity' => 'core_data_privacy', 'column' => 'text_10', 'type' => 'text'],
			'medium_1' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_1', 'type' => 'mediumtext'],
			'medium_2' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_2', 'type' => 'mediumtext'],
			'medium_3' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_3', 'type' => 'mediumtext'],
			'medium_4' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_4', 'type' => 'mediumtext'],
			'medium_5' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_5', 'type' => 'mediumtext'],
			'medium_6' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_6', 'type' => 'mediumtext'],
			'medium_7' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_7', 'type' => 'mediumtext'],
			'medium_8' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_8', 'type' => 'mediumtext'],
			'medium_9' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_9', 'type' => 'mediumtext'],
			'medium_10' =>	 		['entity' => 'core_data_privacy', 'column' => 'medium_10', 'type' => 'mediumtext'],
		),
		'authorizations' => array(
			'core_data_privacy.type', 'core_data_privacy.status',
		),
	);
	
	public $id;
    public $instance_id;
    public $status;
    public $type;
    public $owner_id;
    public $tiny_1;
    public $tiny_2;
    public $tiny_3;
    public $tiny_4;
    public $tiny_5;
    public $tiny_6;
    public $tiny_7;
    public $tiny_8;
    public $tiny_9;
    public $tiny_10;
    public $text_1;
    public $text_2;
    public $text_3;
    public $text_4;
    public $text_5;
    public $text_6;
    public $text_7;
    public $text_8;
    public $text_9;
    public $text_10;
    public $medium_1;
    public $medium_2;
    public $medium_3;
    public $medium_4;
    public $medium_5;
    public $medium_6;
    public $medium_7;
    public $medium_8;
    public $medium_9;
    public $medium_10;
    public $audit;
    public $update_time;
    
    // Transient properties
    public $properties;
    
    protected $inputFilter;

    // Static fields
    private static $table;

    /**
     * Used for relational (database) to object (php) mapping.
     * @param array data
     */
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
        $this->owner_id = (isset($data['owner_id'])) ? $data['owner_id'] : null;
        $this->tiny_1 = (isset($data['tiny_1'])) ? $data['tiny_1'] : null;
        $this->tiny_2 = (isset($data['tiny_2'])) ? $data['tiny_2'] : null;
        $this->tiny_3 = (isset($data['tiny_3'])) ? $data['tiny_3'] : null;
        $this->tiny_4 = (isset($data['tiny_4'])) ? $data['tiny_4'] : null;
        $this->tiny_5 = (isset($data['tiny_5'])) ? $data['tiny_5'] : null;
        $this->tiny_6 = (isset($data['tiny_6'])) ? $data['tiny_6'] : null;
        $this->tiny_7 = (isset($data['tiny_7'])) ? $data['tiny_7'] : null;
        $this->tiny_8 = (isset($data['tiny_8'])) ? $data['tiny_8'] : null;
        $this->tiny_9 = (isset($data['tiny_9'])) ? $data['tiny_9'] : null;
        $this->tiny_10 = (isset($data['tiny_10'])) ? $data['tiny_10'] : null;
        $this->text_1 = (isset($data['text_1'])) ? $data['text_1'] : null;
        $this->text_2 = (isset($data['text_2'])) ? $data['text_2'] : null;
        $this->text_3 = (isset($data['text_3'])) ? $data['text_3'] : null;
        $this->text_4 = (isset($data['text_4'])) ? $data['text_4'] : null;
        $this->text_5 = (isset($data['text_5'])) ? $data['text_5'] : null;
        $this->text_6 = (isset($data['text_6'])) ? $data['text_6'] : null;
        $this->text_7 = (isset($data['text_7'])) ? $data['text_7'] : null;
        $this->text_8 = (isset($data['text_8'])) ? $data['text_8'] : null;
        $this->text_9 = (isset($data['text_9'])) ? $data['text_9'] : null;
        $this->text_10 = (isset($data['text_10'])) ? $data['text_10'] : null;
        $this->medium_1 = (isset($data['medium_1'])) ? $data['medium_1'] : null;
        $this->medium_2 = (isset($data['medium_2'])) ? $data['medium_2'] : null;
        $this->medium_3 = (isset($data['medium_3'])) ? $data['medium_3'] : null;
        $this->medium_4 = (isset($data['medium_4'])) ? $data['medium_4'] : null;
        $this->medium_5 = (isset($data['medium_5'])) ? $data['medium_5'] : null;
        $this->medium_6 = (isset($data['medium_6'])) ? $data['medium_6'] : null;
        $this->medium_7 = (isset($data['medium_7'])) ? $data['medium_7'] : null;
        $this->medium_8 = (isset($data['medium_8'])) ? $data['medium_8'] : null;
        $this->medium_9 = (isset($data['medium_9'])) ? $data['medium_9'] : null;
        $this->medium_10 = (isset($data['medium_10'])) ? $data['medium_10'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : [];
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
    	$data['owner_id'] = (int) $this->owner_id;
    	$data['tiny_1'] = $this->tiny_1;
    	$data['tiny_2'] = $this->tiny_2;
    	$data['tiny_3'] = $this->tiny_3;
    	$data['tiny_4'] = $this->tiny_4;
    	$data['tiny_5'] = $this->tiny_5;
    	$data['tiny_6'] = $this->tiny_6;
    	$data['tiny_7'] = $this->tiny_7;
    	$data['tiny_8'] = $this->tiny_8;
    	$data['tiny_9'] = $this->tiny_9;
    	$data['tiny_10'] = $this->tiny_10;
    	$data['text_1'] = $this->text_1;
    	$data['text_2'] = $this->text_2;
    	$data['text_3'] = $this->text_3;
    	$data['text_4'] = $this->text_4;
    	$data['text_5'] = $this->text_5;
    	$data['text_6'] = $this->text_6;
    	$data['text_7'] = $this->text_7;
    	$data['text_8'] = $this->text_8;
    	$data['text_9'] = $this->text_9;
    	$data['text_10'] = $this->text_10;
    	$data['medium_1'] = $this->medium_1;
    	$data['medium_2'] = $this->medium_2;
    	$data['medium_3'] = $this->medium_3;
    	$data['medium_4'] = $this->medium_4;
    	$data['medium_5'] = $this->medium_5;
    	$data['medium_6'] = $this->medium_6;
    	$data['medium_7'] = $this->medium_7;
    	$data['medium_8'] = $this->medium_8;
    	$data['medium_9'] = $this->medium_9;
    	$data['medium_10'] = $this->medium_10;
    	$data['audit'] = $this->audit;
    	return $data;
    }

    /**
     * Used for object (php) to relational (database) serialization.
     * The difference between getProperties() and toArray() is that:
     * - getProperties() does not transform data while toArray do sometime. For example, toArray converts array properties into JSON format.
     * - getProperties() retrieves in the target array the properties from joined tables while toArray() do not retrieve joined properties.
     * @return array
     */
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['audit'] = json_encode($this->audit);
    	return $data;
    }

    /**
     * Normalize and returns, for a given type of DataPrivacy, the description for all the properties
     */
    public static function getConfigProperties($type) {
    	$context = Context::getCurrent();
    	$properties = array();
    	foreach(DataPrivacy::$model['properties'] as $propertyId => $unused) {
    		$property = $context->getConfig('data_privacy/'.$type.'/property/'.$propertyId);
    		if (!$property) $property = $context->getConfig('data_privacy/generic/property/'.$propertyId);
    		if ($property) {
    			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
    			$properties[$propertyId] = $property;
    			$properties[$propertyId]['entity'] = DataPrivacy::$model['properties'][$propertyId]['entity'];
    			$properties[$propertyId]['column'] = DataPrivacy::$model['properties'][$propertyId]['column'];
    		}
    	}
    	return $properties;
    }
    
    /**
     * Normalize and returns the view options defined for a given type of DataPrivacy
     */
    public static function getConfigView($type)
    {
    	$context = Context::getCurrent();
    	$configView = $context->getConfig('data_privacy/view/'.$type);
    	return $configView;
    }
    
    /**
     * Consolidates returns the whole description defined for a given type of DataPrivacy:
     * - The list of the managed properties along with their definition
     * - The options for the view part
     */
    public static function getDescription($type)
    {
    	$context = Context::getCurrent();
    	$description = array();
    	$description['authorizations'] = DataPrivacy::$model['authorizations'];
    	$description['type'] = $type;
    	$description['properties'] = DataPrivacy::getConfigProperties($type);
    	$description['view'] = DataPrivacy::getConfigView($type);
    	return $description;
    }
    
    /**
     * Retrieve from the database the DataPrivacy matching the given type and owner_id.
     * An array of all the properties, owned bt GroupAccount or joined from the related entities is provided using getProperties().
     * @param string $type
     * @param string $owner_id
     * @return DataPrivacy
     */
    public static function get($id, $column = 'id', $id2 = false, $column2 = false, $id3 = false, $column3 = false, $id4 = false, $column4 = false)
    {
		$dataPrivacy = DataPrivacy::getTable()->get($id, $column, $id2, $column2, $id3, $column3, $id4, $column4);
		if ($dataPrivacy) {
			$this->getDecryptedVector();
			$dataPrivacy->properties = $dataPrivacy->getProperties();
		}
		return $dataPrivacy;
    }

    /**
     * Returns a new instance of DataPrivacy<type, owner_id>.
     * The status is set to 'new'.
     * @return DataPrivacy
     */
    public static function instanciate($type , $owner_id)
    {
    	$context = Context::getCurrent();
    	$dataPrivacy = new DataPrivacy;
    	$dataPrivacy->status = 'new';
    	$dataPrivacy->type = $type;
    	$dataPrivacy->owner_id = $owner_id;
    	$dataPrivacy->audit = array();
    	return $dataPrivacy;
    }

    /**
     * Loads the array argument $data into the DataPrivacy object.
     * Only the properties present as a key in $data are updated in the target object.
     * Each properties are trimed and cleaned of unsupported tags. The accepted html tags in strings is defined by the configuration parameter 'supportedTagsInDatabase'.
     * Integrity controls are operated according to the DataPrivacy<type> description provided by the $description argument:
     * - The length of the data is checked against the database type associated with the property in DataPrivacy::$model['properties']
     * - The value in 'select' or 'multiselect' properties should belong to the modalities defined in the description
     * - The value should not be null or empty for mandatory properties
     * - The validity of the value format is checked for properties of type 'date', 'time', 'datetime', 'int' and 'float'
     * Returns 'OK' or a list of errors in the form of an array ok key-pairs associating the property id and the error caption
     * - 'Integrity' if the protection check failed,
     * - 'OK' otherwise
     * The audit database property is augmented with a row storing the current date and time, the user doing the update, and the list of updated properties along with their previous and new values (support for future undo facility).
     * @param $description[]
     * @param $data[]
     * @return array
     */
    public function loadData($description, $data)
	{
		$context = Context::getCurrent();
		$errors = array();
		$auditRow = array(
			'time' => Date('Y-m-d G:i:s'),
			'n_fn' => $context->getFormatedName(),
		);
		foreach ($data as $propertyId => $property) {
			if (array_key_exists($propertyId, $description['properties'])) {
				$property = $description['properties'][$propertyId];
				$value = ($property['type'] == 'html') ? trim(strip_tags($data[$propertyId]), $context->getConfig('accepted_tags_in_database')) : $data[$propertyId];

				if (array_key_exists('mandatory', $property) && $property['mandatory'] && !$this->properties[$propertyId] && !$value) $errors[$propertyId] = "$propertyId is mandatory";

				if (DataPrivacy::$model['properties'][$propertyId]['type'] == 'varchar') {
					if (strlen($value) > 255) $errors[$propertyId] = "$propertyId should not be longer than 255 characters";
				}
				elseif (DataPrivacy::$model['properties'][$propertyId]['type'] == 'text') {
					if (strlen($value) > 65535) $errors[$propertyId] = "$propertyId should not be longer than 65535 characters";
				}
				elseif (DataPrivacy::$model['properties'][$propertyId]['type'] == 'date') {
					if ($value && (strlen($value) < 10 || !checkdate(substr($value, 5, 2), substr($value, 8, 2), substr($value, 0, 4)))) $errors[$propertyId] = "$propertyId should be a valid date according to the format yyyy-mm-dd";
				}
				elseif (DataPrivacy::$model['properties'][$propertyId]['type'] == 'time') {
					if ($value && !Account::checktime($value)) $errors[$propertyId] = "$propertyId should be a valid time according to the format hh:mm:ss";
				}
				elseif (DataPrivacy::$model['properties'][$propertyId]['type'] == 'datetime') {
					if ($value && (!checkdate(substr($value, 5, 2), substr($value, 8, 2), substr($value, 0, 4)) || !Account::checktime(substr($value, 11, 8)))) $errors[$propertyId] = "$propertyId should be a valid date & time according to the format yyyy-mm-dd hh:mm:ss";
				}
				elseif (DataPrivacy::$model['properties'][$propertyId]['type'] == 'int') $value = (int) $value;
				elseif (DataPrivacy::$model['properties'][$propertyId]['type'] == 'float') $value = (float) $value;
				
				if ($property['type'] == 'select') {
					foreach (explode(',', $value) as $modalityId) {
						if (!array_key_exists($modalityId, $property['modalities'])) {
							$errors[$propertyId] = 'The modality '.$modalityId.' does not exist in '.$propertyId;
						}
					}
				}
				
				if (!$errors) {
					if ($propertyId == 'status') $this->status = $value;
					elseif ($propertyId == 'type') $this->type = $value;
					elseif ($propertyId == 'owner_id') $this->owner_id = $value;
					elseif ($propertyId == 'tiny_1') $this->tiny_1 = $value;
					elseif ($propertyId == 'tiny_2') $this->tiny_2 = $value;
					elseif ($propertyId == 'tiny_3') $this->tiny_3 = $value;
					elseif ($propertyId == 'tiny_4') $this->tiny_4 = $value;
					elseif ($propertyId == 'tiny_5') $this->tiny_5 = $value;
					elseif ($propertyId == 'tiny_6') $this->tiny_6 = $value;
					elseif ($propertyId == 'tiny_7') $this->tiny_7 = $value;
					elseif ($propertyId == 'tiny_8') $this->tiny_8 = $value;
					elseif ($propertyId == 'tiny_9') $this->tiny_9 = $value;
					elseif ($propertyId == 'tiny_10') $this->tiny_10 = $value;
					elseif ($propertyId == 'text_1') $this->text_1 = $value;
					elseif ($propertyId == 'text_2') $this->text_2 = $value;
					elseif ($propertyId == 'text_3') $this->text_3 = $value;
					elseif ($propertyId == 'text_4') $this->text_4 = $value;
					elseif ($propertyId == 'text_5') $this->text_5 = $value;
					elseif ($propertyId == 'text_6') $this->text_6 = $value;
					elseif ($propertyId == 'text_7') $this->text_7 = $value;
					elseif ($propertyId == 'text_8') $this->text_8 = $value;
					elseif ($propertyId == 'text_9') $this->text_9 = $value;
					elseif ($propertyId == 'text_10') $this->text_10 = $value;
					elseif ($propertyId == 'medium_1') $this->medium_1 = $value;
					elseif ($propertyId == 'medium_2') $this->medium_2 = $value;
					elseif ($propertyId == 'medium_3') $this->medium_3 = $value;
					elseif ($propertyId == 'medium_4') $this->medium_4 = $value;
					elseif ($propertyId == 'medium_5') $this->medium_5 = $value;
					elseif ($propertyId == 'medium_6') $this->medium_6 = $value;
					elseif ($propertyId == 'medium_7') $this->medium_7 = $value;
					elseif ($propertyId == 'medium_8') $this->medium_8 = $value;
					elseif ($propertyId == 'medium_9') $this->medium_9 = $value;
					elseif ($propertyId == 'medium_10') $this->medium_10 = $value;
					$this->getEncryptedVector();

					if ($this->properties[$propertyId] != $value) $auditRow[$propertyId] = ['previous' => $value, 'current' => $this->properties[$propertyId]];
				}
			}	
			if ($errors) return $errors;
		}

		$this->audit[] = $auditRow;
		$this->properties = $this->getProperties();
		return 'OK';
	}

   /**
     * Adds a new row in the database. 
     * A consistency check is operated avoiding duplicate rows for the same triplet of type, group_id and account_id
     * @return string
     */
	public function add()
	{
		$context = Context::getCurrent();

		// Consistency check
		if (Generic::getTable()->cardinality('core_data_privacy', array('type' => $this->type, 'owner_id' => $this->owner_id)) != 0) {
			return 'Consistency';
		}
		
		$this->id = null;
		DataPrivacy::getTable()->save($this);
		return ('OK');
	}

	/**
	 * Packaging of load and add for a Restfull implementation.
	 * Manages the atomicity of the transaction. Should several entities be updated, the method warrant that all the updates are 
	 * done or the database stays in the initial state.
	 * The errors received from the load method are added as an 'errors' subarray to the updatable $content argument.
	 * Returns a tuplet of an HTTP compliant return codes (200 if OK, 500 if not) and a complement as a string 
     * @param $description[]
     * @param &$content[]
     * @return string[]
	 */
	public function loadAndAdd($description, &$content)
	{
		$context = Context::getCurrent();

		// Atomicity
		$connection = GroupAccount::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {

			// Load the data
			$rc = $this->loadData($description, $content['data']);
			if ($rc != 'OK') {
				$content['errors'] = $rc;
				return ['500', 'core_data_privacy->load: see errors'];
			}

			// Save the data
			$this->add();
			if ($rc != 'OK') return ['500', 'core_data_privacy->add: '.$rc];
	
    		$connection->commit();
			return ['200'];
		}
		catch (\Exception $e) {
			$connection->rollback();
			throw $e;
		}
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
		$dataPrivacy = DataPrivacy::get($this->id);

		// Isolation check
		if ($dataPrivacy->update_time > $update_time) return 'Isolation';

		DataPrivacy::getTable()->save($this);

		return 'OK';
	}

	/**
	 * Packaging of load and update for a Restfull implementation.
	 * Manages the atomicity of the transaction. Should several entities be updated, the method warrant that all the updates are
	 * done or the database stays in the initial state.
	 * The errors received from the load method are added as an 'errors' subarray to the updatable $content argument.
	 * Returns a tuplet of an HTTP compliant return codes (200 if OK, 500 if not) and a complement as a string
	 * @param $description[]
	 * @param &$content[]
	 * @return string[]
	 */
	public function loadAndUpdate($description, &$content, $update_time = null)
	{
		$context = Context::getCurrent();
	
		// Load the data
		$rc = $this->loadData($description, $content['data']);
		if ($rc != 'OK') {
			$content['errors'] = $rc;
			return ['500', 'core_data_privacy->load: see errors'];
		}
	
		// Save the data
		$this->update($update_time);
		if ($rc == 'Isolation') return ['409', 'core_data_privacy->update: '.$rc];
		if ($rc != 'OK') return ['500', 'core_data_privacy->update: '.$rc];
		return ['200'];
	}

    /**
     * Delete the row in the database
     */
	public function delete($update_time)
	{
		$context = Context::getCurrent();
		$dataPrivacy = DataPrivacy::get($this->id);

		// Isolation check
		if ($groupAccount->update_time > $update_time) return 'Isolation';

		GroupAccount::getTable()->delete($this->id);

		return 'OK';
	}

	public static function crypt($data, $passphrase){
		$keyHash = md5($passphrase);
		$key = substr($keyHash, 0, mcrypt_get_key_size(self::$cipher, self::$mode));
		$iv  = substr($keyHash, 0, mcrypt_get_block_size(self::$cipher, self::$mode));
	
		$encrypted = mcrypt_encrypt(self::$cipher, $key, $data, self::$mode, $iv);
		 
		return base64_encode($encrypted);
	}

	public function getEncryptedVector() {
		$key = $context->getConfig()['ppitUserSettings']['safe'][$context->getInstance()->caption]['privacy_key'];
		$this->tiny_1 = DataPrivacy::crypt($this->tiny_1, $key);
		$this->tiny_2 = DataPrivacy::crypt($this->tiny_2, $key);
		$this->tiny_3 = DataPrivacy::crypt($this->tiny_3, $key);
		$this->tiny_4 = DataPrivacy::crypt($this->tiny_4, $key);
		$this->tiny_5 = DataPrivacy::crypt($this->tiny_5, $key);
		$this->tiny_6 = DataPrivacy::crypt($this->tiny_6, $key);
		$this->tiny_7 = DataPrivacy::crypt($this->tiny_7, $key);
		$this->tiny_8 = DataPrivacy::crypt($this->tiny_8, $key);
		$this->tiny_9 = DataPrivacy::crypt($this->tiny_9, $key);
		$this->tiny_10 = DataPrivacy::crypt($this->tiny_10, $key);
		$this->text_1 = DataPrivacy::crypt($this->text_1, $key);
		$this->text_2 = DataPrivacy::crypt($this->text_2, $key);
		$this->text_3 = DataPrivacy::crypt($this->text_3, $key);
		$this->text_4 = DataPrivacy::crypt($this->text_4, $key);
		$this->text_5 = DataPrivacy::crypt($this->text_5, $key);
		$this->text_6 = DataPrivacy::crypt($this->text_6, $key);
		$this->text_7 = DataPrivacy::crypt($this->text_7, $key);
		$this->text_8 = DataPrivacy::crypt($this->text_8, $key);
		$this->text_9 = DataPrivacy::crypt($this->text_9, $key);
		$this->text_10 = DataPrivacy::crypt($this->text_10, $key);
		$this->medium_1 = DataPrivacy::crypt($this->medium_1, $key);
		$this->medium_2 = DataPrivacy::crypt($this->medium_2, $key);
		$this->medium_3 = DataPrivacy::crypt($this->medium_3, $key);
		$this->medium_4 = DataPrivacy::crypt($this->medium_4, $key);
		$this->medium_5 = DataPrivacy::crypt($this->medium_5, $key);
		$this->medium_6 = DataPrivacy::crypt($this->medium_6, $key);
		$this->medium_7 = DataPrivacy::crypt($this->medium_7, $key);
		$this->medium_8 = DataPrivacy::crypt($this->medium_8, $key);
		$this->medium_9 = DataPrivacy::crypt($this->medium_9, $key);
		$this->medium_10 = DataPrivacy::crypt($this->medium_10, $key);
	}
	
	public static function decrypt($data, $passphrase){
		$keyHash = md5($passphrase);
		$key = substr($keyHash, 0, mcrypt_get_key_size(self::$cipher, self::$mode) );
		$iv  = substr($keyHash, 0, mcrypt_get_block_size(self::$cipher, self::$mode) );
	
		$decoded = base64_decode($data);
		$initial = rtrim(mcrypt_decrypt(self::$cipher, $key, $decoded, self::$mode, $iv));
		// Check that the passphrase is the right one
		$encrypted = DataPrivacy::crypt($initial, $passphrase);
	
		if ($encrypted == $data) return $initial;
		else return null;
	}
	
	public function getDecryptedVector() {
		$key = $context->getConfig()['ppitUserSettings']['safe'][$context->getInstance()->caption]['privacy_key'];
		$this->tiny_1 = DataPrivacy::decrypt($this->tiny_1, $key);
		$this->tiny_2 = DataPrivacy::decrypt($this->tiny_2, $key);
		$this->tiny_3 = DataPrivacy::decrypt($this->tiny_3, $key);
		$this->tiny_4 = DataPrivacy::decrypt($this->tiny_4, $key);
		$this->tiny_5 = DataPrivacy::decrypt($this->tiny_5, $key);
		$this->tiny_6 = DataPrivacy::decrypt($this->tiny_6, $key);
		$this->tiny_7 = DataPrivacy::decrypt($this->tiny_7, $key);
		$this->tiny_8 = DataPrivacy::decrypt($this->tiny_8, $key);
		$this->tiny_9 = DataPrivacy::decrypt($this->tiny_9, $key);
		$this->tiny_10 = DataPrivacy::decrypt($this->tiny_10, $key);
		$this->text_1 = DataPrivacy::decrypt($this->text_1, $key);
		$this->text_2 = DataPrivacy::decrypt($this->text_2, $key);
		$this->text_3 = DataPrivacy::decrypt($this->text_3, $key);
		$this->text_4 = DataPrivacy::decrypt($this->text_4, $key);
		$this->text_5 = DataPrivacy::decrypt($this->text_5, $key);
		$this->text_6 = DataPrivacy::decrypt($this->text_6, $key);
		$this->text_7 = DataPrivacy::decrypt($this->text_7, $key);
		$this->text_8 = DataPrivacy::decrypt($this->text_8, $key);
		$this->text_9 = DataPrivacy::decrypt($this->text_9, $key);
		$this->text_10 = DataPrivacy::decrypt($this->text_10, $key);
		$this->medium_1 = DataPrivacy::decrypt($this->medium_1, $key);
		$this->medium_2 = DataPrivacy::decrypt($this->medium_2, $key);
		$this->medium_3 = DataPrivacy::decrypt($this->medium_3, $key);
		$this->medium_4 = DataPrivacy::decrypt($this->medium_4, $key);
		$this->medium_5 = DataPrivacy::decrypt($this->medium_5, $key);
		$this->medium_6 = DataPrivacy::decrypt($this->medium_6, $key);
		$this->medium_7 = DataPrivacy::decrypt($this->medium_7, $key);
		$this->medium_8 = DataPrivacy::decrypt($this->medium_8, $key);
		$this->medium_9 = DataPrivacy::decrypt($this->medium_9, $key);
		$this->medium_10 = DataPrivacy::decrypt($this->medium_10, $key);
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
     * Returns the object to relational manager for the Event class
     */
	public static function getTable()
    {
    	if (!DataPrivacy::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		DataPrivacy::$table = $sm->get('PpitCore\Model\DataPrivacyTable');
    	}
    	return DataPrivacy::$table;
    }
}

?>