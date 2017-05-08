<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Model;

use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use PpitCore\Model\Instance;
use PpitCore\Model\Vcard;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
  * Interaction is the class supporting generic interactions between applications
  *
  * An interaction has a type (domain specific), a format (ie JSON, XML...), a direction (reception, emission), a content and an http status. It is timestamped.
  * It can be linked to a place (reflecting customer organization), a domain specific reference, and number of domain specific properties for search purposes.
  */
class Interaction implements InputFilterAwareInterface
{
	/** @var int */ public $id;
	/** @var int */ public $instance_id;
	/** @var string */ public $status;
	/** @var string */ public $type;
	/** @var string */ public $format;
	/** @var string */ public $direction;
	/** @var int */ public $place_id;
	/** @var string */ public $reference;
	/** @var string */ public $content;
	/** @var string */ public $http_status;
	/** @var string */ public $property_1;
	/** @var string */ public $property_2;
	/** @var string */ public $property_3;
	/** @var string */ public $property_4;
	/** @var string */ public $property_5;
	/** @var string */ public $property_6;
	/** @var string */ public $property_7;
	/** @var string */ public $property_8;
	/** @var string */ public $property_9;
	/** @var string */ public $property_10;
	/** @var string */ public $property_11;
	/** @var string */ public $property_12;
	/** @var string */ public $property_13;
	/** @var string */ public $property_14;
	/** @var string */ public $property_15;
	/** @var string */ public $property_16;
	/** @var string */ public $property_17;
	/** @var string */ public $property_18;
	/** @var string */ public $property_19;
	/** @var string */ public $property_20;
	/** @var string */ public $property_21;
	/** @var string */ public $property_22;
	/** @var string */ public $property_23;
	/** @var string */ public $property_24;
	/** @var string */ public $property_25;
	/** @var string */ public $property_26;
	/** @var string */ public $property_27;
	/** @var string */ public $property_28;
	/** @var string */ public $property_29;
	/** @var string */ public $property_30;
    /** @var array */ public $audit;
	/** @var string */ public $update_time;
	
	// Transient properties
	/** @var array */ public $properties;
	/** @var string */ public $place_caption;
	
    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

    // Static fields
    /** @var \PpitCore\Model\Interaction */ private static $table;
    
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
        $this->format = (isset($data['format'])) ? $data['format'] : null;
        $this->direction = (isset($data['direction'])) ? $data['direction'] : null;
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->reference = (isset($data['reference'])) ? $data['reference'] : null;
        $this->content = (isset($data['content'])) ? $data['content'] : null;
        $this->http_status = (isset($data['http_status'])) ? $data['http_status'] : null;
        $this->property_1 = (isset($data['property_1'])) ? $data['property_1'] : null;
        $this->property_2 = (isset($data['property_2'])) ? $data['property_2'] : null;
        $this->property_3 = (isset($data['property_3'])) ? $data['property_3'] : null;
        $this->property_4 = (isset($data['property_4'])) ? $data['property_4'] : null;
        $this->property_5 = (isset($data['property_5'])) ? $data['property_5'] : null;
        $this->property_6 = (isset($data['property_6'])) ? $data['property_6'] : null;
        $this->property_7 = (isset($data['property_7'])) ? $data['property_7'] : null;
        $this->property_8 = (isset($data['property_8'])) ? $data['property_8'] : null;
        $this->property_9 = (isset($data['property_9'])) ? $data['property_9'] : null;
        $this->property_10 = (isset($data['property_10'])) ? $data['property_10'] : null;
        $this->property_11 = (isset($data['property_11'])) ? $data['property_11'] : null;
        $this->property_12 = (isset($data['property_12'])) ? $data['property_12'] : null;
        $this->property_13 = (isset($data['property_13'])) ? $data['property_13'] : null;
        $this->property_14 = (isset($data['property_14'])) ? $data['property_14'] : null;
        $this->property_15 = (isset($data['property_15'])) ? $data['property_15'] : null;
        $this->property_16 = (isset($data['property_16'])) ? $data['property_16'] : null;
        $this->property_17 = (isset($data['property_17'])) ? $data['property_17'] : null;
        $this->property_18 = (isset($data['property_18'])) ? $data['property_18'] : null;
        $this->property_19 = (isset($data['property_19'])) ? $data['property_19'] : null;
        $this->property_20 = (isset($data['property_20'])) ? $data['property_20'] : null;
        $this->property_21 = (isset($data['property_21'])) ? $data['property_21'] : null;
        $this->property_22 = (isset($data['property_22'])) ? $data['property_22'] : null;
        $this->property_23 = (isset($data['property_23'])) ? $data['property_23'] : null;
        $this->property_24 = (isset($data['property_24'])) ? $data['property_24'] : null;
        $this->property_25 = (isset($data['property_25'])) ? $data['property_25'] : null;
        $this->property_26 = (isset($data['property_26'])) ? $data['property_26'] : null;
        $this->property_27 = (isset($data['property_27'])) ? $data['property_27'] : null;
        $this->property_28 = (isset($data['property_28'])) ? $data['property_28'] : null;
        $this->property_29 = (isset($data['property_29'])) ? $data['property_29'] : null;
        $this->property_30 = (isset($data['property_30'])) ? $data['property_30'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
        
        // Joined properties
        $this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
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
    	$data['type'] = $this->type;
    	$data['format'] = $this->format;
    	$data['direction'] = $this->direction;
    	$data['place_id'] = (int) $this->place_id;
    	$data['reference'] = $this->reference;
    	$data['content'] = $this->content;
    	$data['http_status'] = $this->http_status;
    	$data['property_1'] = $this->property_1;
    	$data['property_2'] = $this->property_2;
    	$data['property_3'] = $this->property_3;
    	$data['property_4'] = $this->property_4;
    	$data['property_5'] = $this->property_5;
    	$data['property_6'] = $this->property_6;
    	$data['property_7'] = $this->property_7;
    	$data['property_8'] = $this->property_8;
    	$data['property_9'] = $this->property_9;
    	$data['property_10'] = $this->property_10;
    	$data['property_11'] = $this->property_11;
    	$data['property_12'] = $this->property_12;
    	$data['property_13'] = $this->property_13;
    	$data['property_14'] = $this->property_14;
    	$data['property_15'] = $this->property_15;
    	$data['property_16'] = $this->property_16;
    	$data['property_17'] = $this->property_17;
    	$data['property_18'] = $this->property_18;
    	$data['property_19'] = $this->property_19;
    	$data['property_20'] = $this->property_20;
    	$data['property_21'] = $this->property_21;
    	$data['property_22'] = $this->property_22;
    	$data['property_23'] = $this->property_23;
    	$data['property_24'] = $this->property_24;
    	$data['property_25'] = $this->property_25;
    	$data['property_26'] = $this->property_26;
    	$data['property_27'] = $this->property_27;
    	$data['property_28'] = $this->property_28;
    	$data['property_29'] = $this->property_29;
    	$data['property_30'] = $this->property_30;
    	$data['audit'] = $this->audit;
    	$data['update_time'] = $this->update_time;
    	 
    	// Joined properties
    	$data['place_caption'] = $this->place_caption;
    	
    	return $data;
    }

	/**
	 * Used for object (php) to relational (database) mapping.
	 * The difference between getProperties() and toArray() is that:
	 * - getProperties() does not transform data while toArray do sometime. For example, to Array converts array to JSON.
	 * - getProperties() retrieves in the target array the place caption which is a joined properties from place (table 'core_place'), and toArray() do not retrieve joined properties.
	 * @return array
	 */
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['audit'] = json_encode($this->audit);
    	unset($data['place_caption']);
    	unset($data['update_time']);
    	return $data;
    }
    
    /**
     * Returns an array of Interaction instances indexed by the interaction primary key in the database :
     * If $mode == 'todo' the list is restricted on today's interactions only.
     * Otherwise, the list is filtered on interactions matching the key-value pairs provided in the $params argument:
     * - if the key has the form 'min_<property_name>', only the rows having a value for this property greater than the argument are kept
     * - if the key has the form 'max_<property_name>', only the rows having a value for this property less than the argument are kept
     * - else only the row which are 'like' (in SQL sense) the argument are kept in the list.
     * The result is primarily ordered according to the value of $major with the direction (ASC or DESC) specified by $dir, and secondarily in descending chronological order ('update_time DESC')
     * For each retrieved interaction:
     * - A left SQL join is made with the place (core_place table) as to provide the place name as a joined property.
     * - For array based algorythms needs, a $property array is provided using getProperties() on Interaction properties.
     * @param $params[]
     * @param string $major
     * @param string $dir
     * @param string $mode
     * @return Interaction[]
     */
    public static function getList($params, $major = 'caption', $dir = 'ASC', $mode = 'todo')
    {
    	$select = Interaction::getTable()->getSelect()
    		->join('core_place', 'core_interaction.place_id = core_place.id', array('place_caption' => 'caption'), 'left')
    		->order(array($major.' '.$dir, 'core_interaction.update_time DESC'));
    	$where = new Where;
    	$where->notEqualTo('core_interaction.status', 'deleted');

    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->equalTo('core_interaction.status', 'new');
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('core_interaction.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('core_interaction.'.substr($propertyId, 4), $params[$propertyId]);
    			else $where->like('core_interaction.'.$propertyId, '%'.$params[$propertyId].'%');
    		}
    	}
    	$select->where($where);

    	$cursor = Interaction::getTable()->selectWith($select);
    	$interactions = array();
    	foreach ($cursor as $interaction) {
    		$interaction->properties = $interaction->getProperties();
    		$interactions[$interaction->id] = $interaction;
    	}
    	return $interactions;
    }

    /**
     * Retrieve from the database the interaction having the giving value as the given specified column ('id' as a default).
     * The place caption is retrieved from the place in database ('core_place' table) using the foreign key 'place_id'.
     * For array based algorythms needs, a $property array is provided using getProperties() on Interaction properties.
     * @param string $id
     * @param string $column
     * @return Interaction
     */
    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();
    	$interaction = Interaction::getTable()->get($id, $column);
    	$place = Place::get($interaction->place_id);
    	if ($place) $interaction->place_caption = $place->caption;
    	if ($interaction) $interaction->properties = $interaction->getProperties();
    	return $interaction;
    }

    /**
     * Returns a new instance of Interaction.
     * The status is set to 'new'.
     * @return Interaction
     */
    public static function instanciate()
    {
    	$context = Context::getCurrent();
    	$interaction = new Interaction;
    	$interaction->status = 'new';
    	return $interaction;
	}

    /**
     * Loads the data into the Interaction object depending of an array, typically constructed by the controller with value extracted from an HTTP request.
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
		$context = Context::getCurrent();
		if (array_key_exists('status', $data)) {
	    	$status = trim(strip_tags($data['status']));
			if ($status == '' || strlen($status) > 255) return 'Integrity';
			if ($this->status != $status) $auditRow['status'] = $this->status = $status;
		}
		if (array_key_exists('type', $data)) {
	    	$type = trim(strip_tags($data['type']));
			if ($type == '' || strlen($type) > 255) return 'Integrity';
			if ($this->type != $type) $auditRow['type'] = $this->type = $type;
		}
		if (array_key_exists('format', $data)) {
	    	$format = trim(strip_tags($data['format']));
			if ($format == '' || strlen($format) > 255) return 'Integrity';
			if ($this->format != $format) $auditRow['format'] = $this->format = $format;
		}
		if (array_key_exists('direction', $data)) {
	    	$direction = trim(strip_tags($data['direction']));
			if ($direction == '' || strlen($direction) > 255) return 'Integrity';
			if ($this->direction != $direction) $auditRow['direction'] = $this->direction = $direction;
		}
		if (array_key_exists('place_id', $data)) {
			$place_id = (int) $data['place_id'];
			if ($this->place_id != $place_id) $auditRow['place_id'] = $this->place_id = $place_id;
		}
		if (array_key_exists('reference', $data)) {
	    	$reference = trim(strip_tags($data['reference']));
			if (strlen($reference) > 255) return 'Integrity';
			if ($this->reference != $reference) $auditRow['reference'] = $this->reference = $reference;
		}
		if (array_key_exists('content', $data)) {
	    	$content = $data['content'];
			if ($this->content != $content) $auditRow['content'] = $this->content = $content;
		}
		if (array_key_exists('http_status', $data)) {
			$http_status = trim(strip_tags($data['http_status']));
			if (strlen($http_status) > 255) return 'Integrity';
			if ($this->http_status != $http_status) $auditRow['http_status'] = $this->http_status = $http_status;
		}
		if (array_key_exists('property_1', $data)) {
			$property_1 = trim(strip_tags($data['property_1']));
			if (strlen($property_1) > 255) return 'Integrity';
			if ($this->property_1 != $property_1) $auditRow['property_1'] = $this->property_1 = $property_1;
		}
		if (array_key_exists('property_2', $data)) {
			$property_2 = trim(strip_tags($data['property_2']));
			if (strlen($property_2) > 255) return 'Integrity';
			if ($this->property_2 != $property_2) $auditRow['property_2'] = $this->property_2 = $property_2;
		}
		if (array_key_exists('property_3', $data)) {
			$property_3 = trim(strip_tags($data['property_3']));
			if (strlen($property_3) > 255) return 'Integrity';
			if ($this->property_3 != property_3) $auditRow['property_3'] = $this->property_3 = $property_3;
		}
		if (array_key_exists('property_4', $data)) {
			$property_4 = trim(strip_tags($data['property_4']));
			if (strlen($property_4) > 255) return 'Integrity';
			if ($this->property_4 != $property_4) $auditRow['property_4'] = $this->property_4 = $property_4;
		}
		if (array_key_exists('property_5', $data)) {
			$property_5 = trim(strip_tags($data['property_5']));
			if (strlen($property_5) > 255) return 'Integrity';
			if ($this->property_5 != $property_5) $auditRow['property_5'] = $this->property_5 = $property_5;
		}
		if (array_key_exists('property_6', $data)) {
			$property_6 = trim(strip_tags($data['property_6']));
			if (strlen($property_6) > 255) return 'Integrity';
			if ($this->property_6 != $property_6) $auditRow['property_6'] = $this->property_6 = $property_6;
		}
		if (array_key_exists('property_7', $data)) {
			$property_7 = trim(strip_tags($data['property_7']));
			if (strlen($property_7) > 255) return 'Integrity';
			if ($this->property_7 != $property_7) $auditRow['property_7'] = $this->property_7 = $property_7;
		}
		if (array_key_exists('property_8', $data)) {
			$property_8 = trim(strip_tags($data['property_8']));
			if (strlen($property_8) > 255) return 'Integrity';
			if ($this->property_8 != $property_8) $auditRow['property_8'] = $this->property_8 = $property_8;
		}
		if (array_key_exists('property_9', $data)) {
			$property_9 = trim(strip_tags($data['property_9']));
			if (strlen($property_9) > 255) return 'Integrity';
			if ($this->property_9 != $property_9) $auditRow['property_9'] = $this->property_9 = $property_9;
		}
		if (array_key_exists('property_10', $data)) {
			$property_10 = trim(strip_tags($data['property_10']));
			if (strlen($property_10) > 255) return 'Integrity';
			if ($this->property_10 != $property_10) $auditRow['property_10'] = $this->property_10 = $property_10;
		}
		if (array_key_exists('property_11', $data)) {
			$property_11 = trim(strip_tags($data['property_11']));
			if (strlen($property_11) > 255) return 'Integrity';
			if ($this->property_11 != $property_11) $auditRow['property_11'] = $this->property_11 = $property_11;
		}
		if (array_key_exists('property_12', $data)) {
			$property_12 = trim(strip_tags($data['property_12']));
			if (strlen($property_12) > 255) return 'Integrity';
			if ($this->property_12 != $property_12) $auditRow['property_12'] = $this->property_12 = $property_12;
		}
		if (array_key_exists('property_13', $data)) {
			$property_13 = trim(strip_tags($data['property_13']));
			if (strlen($property_13) > 255) return 'Integrity';
			if ($this->property_13 != $property_13) $auditRow['property_13'] = $this->property_13 = $property_13;
		}
		if (array_key_exists('property_14', $data)) {
			$property_14 = trim(strip_tags($data['property_14']));
			if (strlen($property_14) > 255) return 'Integrity';
			if ($this->property_14 != $property_14) $auditRow['property_14'] = $this->property_14 = $property_14;
		}
		if (array_key_exists('property_15', $data)) {
			$property_15 = trim(strip_tags($data['property_15']));
			if (strlen($property_15) > 255) return 'Integrity';
			if ($this->property_15 != $property_15) $auditRow['property_15'] = $this->property_15 = $property_15;
		}
		if (array_key_exists('property_16', $data)) {
			$property_16 = trim(strip_tags($data['property_16']));
			if (strlen($property_16) > 255) return 'Integrity';
			if ($this->property_16 != $property_16) $auditRow['property_16'] = $this->property_16 = $property_16;
		}
		if (array_key_exists('property_17', $data)) {
			$property_17 = trim(strip_tags($data['property_17']));
			if (strlen($property_17) > 255) return 'Integrity';
			if ($this->property_17 != $property_17) $auditRow['property_17'] = $this->property_17 = $property_17;
		}
		if (array_key_exists('property_18', $data)) {
			$property_18 = trim(strip_tags($data['property_18']));
			if (strlen($property_18) > 255) return 'Integrity';
			if ($this->property_18 != $property_18) $auditRow['property_18'] = $this->property_18 = $property_18;
		}
		if (array_key_exists('property_19', $data)) {
			$property_19 = trim(strip_tags($data['property_19']));
			if (strlen($property_19) > 255) return 'Integrity';
			if ($this->property_19 != $property_19) $auditRow['property_19'] = $this->property_19 = $property_19;
		}
		if (array_key_exists('property_20', $data)) {
			$property_20 = trim(strip_tags($data['property_20']));
			if (strlen($property_20) > 255) return 'Integrity';
			if ($this->property_20 != $property_20) $auditRow['property_20'] = $this->property_20 = $property_20;
		}
		if (array_key_exists('property_21', $data)) {
			$property_21 = trim(strip_tags($data['property_21']));
			if (strlen($property_21) > 255) return 'Integrity';
			if ($this->property_21 != $property_21) $auditRow['property_21'] = $this->property_21 = $property_21;
		}
		if (array_key_exists('property_22', $data)) {
			$property_22 = trim(strip_tags($data['property_22']));
			if (strlen($property_22) > 255) return 'Integrity';
			if ($this->property_22 != $property_22) $auditRow['property_22'] = $this->property_22 = $property_22;
		}
		if (array_key_exists('property_23', $data)) {
			$property_23 = trim(strip_tags($data['property_23']));
			if (strlen($property_23) > 255) return 'Integrity';
			if ($this->property_23 != $property_23) $auditRow['property_23'] = $this->property_23 = $property_23;
		}
		if (array_key_exists('property_24', $data)) {
			$property_24 = trim(strip_tags($data['property_24']));
			if (strlen($property_24) > 255) return 'Integrity';
			if ($this->property_24 != $property_24) $auditRow['property_24'] = $this->property_24 = $property_24;
		}
		if (array_key_exists('property_25', $data)) {
			$property_25 = trim(strip_tags($data['property_25']));
			if (strlen($property_25) > 255) return 'Integrity';
			if ($this->property_25 != $property_25) $auditRow['property_25'] = $this->property_25 = $property_25;
		}
		if (array_key_exists('property_26', $data)) {
			$property_26 = trim(strip_tags($data['property_26']));
			if (strlen($property_26) > 255) return 'Integrity';
			if ($this->property_26 != $property_26) $auditRow['property_26'] = $this->property_26 = $property_26;
		}
		if (array_key_exists('property_27', $data)) {
			$property_27 = trim(strip_tags($data['property_27']));
			if (strlen($property_27) > 255) return 'Integrity';
			if ($this->property_27 != $property_27) $auditRow['property_27'] = $this->property_27 = $property_27;
		}
		if (array_key_exists('property_28', $data)) {
			$property_28 = trim(strip_tags($data['property_28']));
			if (strlen($property_28) > 255) return 'Integrity';
			if ($this->property_28 != $property_28) $auditRow['property_28'] = $this->property_28 = $property_28;
		}
		if (array_key_exists('property_29', $data)) {
			$property_29 = trim(strip_tags($data['property_29']));
			if (strlen($property_29) > 255) return 'Integrity';
			if ($this->property_29 != $property_29) $auditRow['property_29'] = $this->property_29 = $property_29;
		}
		if (array_key_exists('property_30', $data)) {
			$property_30 = trim(strip_tags($data['property_30']));
			if (strlen($property_30) > 255) return 'Integrity';
			if ($this->property_30 != $property_30) $auditRow['property_30'] = $this->property_30 = $property_30;
		}
		$this->audit[] = $auditRow;

    	return 'OK';
	}
	
    /**
     * Adds a new row in the database. 
     * @return string
     */
	public function add()
    {
    	$this->id = null;
    	Interaction::getTable()->save($this);
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
    	$interaction = Interaction::get($this->id);
    	if ($update_time && $interaction->update_time > $update_time) return 'Isolation';
    	Interaction::getTable()->save($this);
		return ('OK');
    }
    
    /**
     * Checks if this interaction can de deleted. 
     * An interaction is not deletable if the result of calling the isUsed() method for each class which has registered itself in the 'ppitCoreDependencies' list returns true.
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
    	$interaction = Interaction::get($this->id);
    	if ($update_time && $interaction->update_time > $update_time) return 'Isolation';
    	$this->status = 'deleted';
    	Interaction::getTable()->save($this);
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
     * Returns the object to relational manager for the Interaction class
     */
    public static function getTable()
    {
    	if (!Interaction::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Interaction::$table = $sm->get('PpitCore\Model\InteractionTable');
    	}
    	return Interaction::$table;
    }
}
