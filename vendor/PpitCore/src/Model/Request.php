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
  * Request is the class supporting user requests lifecycle
  */
class Request
{
	public $id;
	public $instance_id;
	public $status;
	public $type;
	public $place_id;
	public $vcard_id;
	public $category;
	public $subcategory;
	public $locale_1;
	public $locale_2;
	public $identifier;
	public $caption;
	public $caption_locale_1;
	public $caption_locale_2;
	public $description;
	public $description_locale_1;
	public $description_locale_2;
	public $begin_date;
	public $end_date;
	public $day_of_week;
	public $day_of_month;
	public $begin_time;
	public $end_time;
	public $time_zone;
	public $location;
	public $latitude;
	public $longitude;
	public $value;
	public $comments;
	public $property_1;
	public $property_2;
	public $property_3;
	public $property_4;
	public $property_5;
	public $property_6;
	public $property_7;
	public $property_8;
	public $property_9;
	public $property_10;
	public $property_11;
	public $property_12;
	public $property_13;
	public $property_14;
	public $property_15;
	public $property_16;
	public $property_17;
	public $property_18;
	public $property_19;
	public $property_20;
	public $property_21;
	public $property_22;
	public $property_23;
	public $property_24;
	public $property_25;
	public $property_26;
	public $property_27;
	public $property_28;
	public $property_29;
	public $property_30;
    public $audit;
	public $update_time;

	// Joined properties
	public $place_identifier;
	public $place_caption;
	public $n_fn;
	
	// Transient properties
	public $properties;
	
	protected $inputFilter;

    // Static fields
    private static $table;
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->vcard_id = (isset($data['vcard_id'])) ? $data['vcard_id'] : null;
        $this->category = (isset($data['category'])) ? $data['category'] : null;
        $this->subcategory = (isset($data['subcategory'])) ? $data['subcategory'] : null;
        $this->locale_1 = (isset($data['locale_1'])) ? $data['locale_1'] : null;
        $this->locale_2 = (isset($data['locale_2'])) ? $data['locale_2'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->caption_locale_1 = (isset($data['caption_locale_1'])) ? $data['caption_locale_1'] : null;
        $this->caption_locale_2 = (isset($data['caption_locale_2'])) ? $data['caption_locale_2'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->description_locale_1 = (isset($data['description_locale_1'])) ? $data['description_locale_1'] : null;
        $this->description_locale_2 = (isset($data['description_locale_2'])) ? $data['description_locale_2'] : null;
        $this->begin_date = (isset($data['begin_date'])) ? $data['begin_date'] : null;
        $this->end_date = (isset($data['end_date']) && $data['end_date'] != '9999-12-31') ? $data['end_date'] : null;
        $this->day_of_week = (isset($data['day_of_week'])) ? $data['day_of_week'] : null;
        $this->day_of_month = (isset($data['day_of_month'])) ? $data['day_of_month'] : null;
        $this->begin_time = (isset($data['begin_time'])) ? $data['begin_time'] : null;
        $this->end_time = (isset($data['end_time'])) ? $data['end_time'] : null;
        $this->time_zone = (isset($data['time_zone'])) ? $data['time_zone'] : null;
        $this->location = (isset($data['location'])) ? $data['location'] : null;
        $this->latitude = (isset($data['latitude'])) ? $data['latitude'] : null;
        $this->longitude = (isset($data['longitude'])) ? $data['longitude'] : null;
        $this->value = (isset($data['value'])) ? $data['value'] : null;
        $this->comments = (isset($data['comments'])) ? json_decode($data['comments'], true) : array();
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
        $this->place_identifier = (isset($data['place_identifier'])) ? $data['place_identifier'] : null;
        $this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
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
    	$data['place_id'] = (int) $this->place_id;
    	$data['vcard_id'] = (int) $this->vcard_id;
    	$data['category'] = $this->category;
    	$data['subcategory'] = $this->subcategory;
    	$data['locale_1'] = $this->locale_1;
    	$data['locale_2'] = $this->locale_2;
    	$data['identifier'] = $this->identifier;
    	$data['caption'] = $this->caption;
    	$data['caption_locale_1'] = $this->caption_locale_1;
    	$data['caption_locale_2'] = $this->caption_locale_2;
    	$data['description'] = $this->description;
    	$data['description_locale_1'] = $this->description_locale_1;
    	$data['description_locale_2'] = $this->description_locale_2;
    	$data['begin_date'] = ($this->begin_date) ? $this->begin_date : null;
    	$data['end_date'] = ($this->end_date) ? $this->end_date : null;
    	$data['day_of_week'] = (int) $this->day_of_week;
    	$data['day_of_month'] = (int) $this->day_of_month;
    	$data['begin_time'] = $this->begin_time;
    	$data['end_time'] = $this->end_time;
    	$data['time_zone'] = (float) $this->time_zone;
    	$data['location'] = $this->location;
    	$data['latitude'] = (float) $this->latitude;
    	$data['longitude'] = (float) $this->longitude;
    	$data['value'] = (float) $this->value;
    	$data['comments'] = $this->comments;
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
    	$data['place_identifier'] = $this->place_identifier;
    	$data['place_caption'] = $this->place_caption;
    	$data['n_fn'] = $this->n_fn;
    	 
    	return $data;
    }

	/**
	 * Used for object (php) to relational (database) mapping.
	 * The difference between getProperties() and toArray() is that:
	 * - getProperties() does not transform data while toArray do sometime. For example, toArray converts array to JSON.
	 * - getProperties() retrieves in the target array the place caption which is a joined properties from place (table 'core_place'), and toArray() do not retrieve joined properties.
	 * @return array
	 */
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['comments'] = json_encode($this->comments);
    	$data['audit'] = json_encode($this->audit);
    	$data['end_date'] = ($this->end_date) ? $this->end_date : '9999-12-31';
    	unset($data['place_identifier']);
    	unset($data['place_caption']);
    	unset($data['n_fn']);
    	unset($data['update_time']);
    	return $data;
    }
    
    /**
     * Returns an array of rows indexed by the primary key in the database :
     * If $mode == 'todo' the list is restricted on new and current requests only.
     * Otherwise, the list is filtered on interactions matching the key-value pairs provided in the $params argument:
     * - if the key has the form 'min_<property_name>', only the rows having a value for this property greater than the argument are kept
     * - if the key has the form 'max_<property_name>', only the rows having a value for this property less than the argument are kept
     * - else only the row which are 'like' (in SQL sense) the argument are kept in the list.
     * The result is primarily ordered according to the value of $major with the direction (ASC or DESC) specified by $dir, and secondarily in descending chronological order ('update_time DESC')
     * For each retrieved request:
     * - A left SQL join is made with the place (core_place table) as to provide the place identifier and name as joined properties.
     * - A left SQL join is made with the contact (core_vcard table) as to provide the contact name as a joined property.
     * - For array based algorythms needs, a $property array is provided using getProperties() on Event properties.
     */
    public static function getList($type, $params, $major = 'begin_date', $dir = 'DESC', $mode = 'search')
    {
    	$context = Context::getCurrent();

    	$select = Event::getTable()->getSelect()
    		->join('core_vcard', 'core_event.vcard_id = core_vcard.id', array('n_fn'), 'left')
    		->join('core_place', 'core_event.place_id = core_place.id', array('place_identifier' => 'identifier', 'place_caption' => 'caption'), 'left')
			->order(array($major.' '.$dir, 'core_event.begin_date DESC'))
    		->limit(200);

    	$where = new Where;
    	$where->notEqualTo('core_event.status', 'deleted');
    	if ($type) $where->equalTo('core_event.type', $type);

    	// Todo list vs search modes
    	if ($mode == 'todo') {
	    	$where->in('core_event.status', array('to-execute', 'executed'));
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $tuplet) {
    			$value = $tuplet['value'];
    			$property = $tuplet['definition'];
    			if ($property['type'] == 'specific') $property = $context->getConfig($property['definition']);
    			if ($propertyId == 'place_identifier') {
    				if ($value == '*') $where->notEqualTo('core_place.identifier', '');
    				else $where->like('core_place.identifier', '%'.$value.'%');
    			}
    		   	elseif ($propertyId == 'n_fn') {
    				if ($value == '*') $where->notEqualTo('core_vcard.id', '');
    				else $where->like('core_vcard.n_fn', '%'.$value.'%');
    			}
    			elseif (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('core_event.'.substr($propertyId, 4), $value);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('core_event.'.substr($propertyId, 4), $value);
    			elseif (strpos($value, ',')) $where->in('core_event.'.$propertyId, array_map('trim', explode(', ', $value)));
    			elseif ($value == '*') $where->notEqualTo('core_event.'.$propertyId, '');
    			elseif ($property['type'] == 'select') $where->equalTo('core_event.'.$propertyId, $value);
    			else $where->like('core_event.'.$propertyId, '%'.$value.'%');
    		}
    		
			// Filter on authorized perimeter
    		if (array_key_exists('p-pit-admin', $context->getPerimeters())) {
    			$where->in('core_event.place_id', $context->getPerimeters()['p-pit-admin']['place_id']);
			}
    	}
    	$select->where($where);

    	$cursor = Request::getTable()->selectWith($select);
    	$requests = array();
    	foreach ($cursor as $request) {
    		$request->properties = $request->getProperties();
    		$requests[$request->id] = $request;
    	}
    	return $requests;
    }
    
    /**
     * Retrieve from the database the request having the giving value as the given specified column ('id' as a default).
     * The place identifier and caption are retrieved from the place in database ('core_place' table) using the foreign key 'place_id'.
     * The contact name is retrieved from the contact in database ('core_vcard' table) using the foreign key 'vcard_id'.
     * For array based algorythms needs, a $property array is provided using getProperties() on Event properties.
     */
    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();
    	$request = Request::getTable()->get($id, $column);
		
        $place = Place::get($request->place_id);
    	if ($place) {
    		$request->place_caption = $place->caption;
    		$request->place_identifier = $place->identifier;
    	}

    	$vcard = Vcard::get($request->vcard_id);
    	if ($vcard) $request->n_fn = $vcard->n_fn;

    	if ($request) $request->properties = $request->getProperties();
    	 
    	return $request;
    }
    
    /**
     * Returns a new Request.
     * The status is set to 'new'.
     */
    public static function instanciate($type)
    {
    	$context = Context::getCurrent();
    	$request = new Request;
    	$request->status = 'new';
    	$request->type = $type;
    	$request->comments = array();
    	return $request;
	}

    /**
     * Loads the data into the Request object depending of an array, typically constructed by the controller with value extracted from an HTTP request.
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
		if (array_key_exists('place_id', $data)) {
			$place_id = (int) $data['place_id'];
			if ($this->place_id != $place_id) $auditRow['place_id'] = $this->place_id = $place_id;
		}
		if (array_key_exists('vcard_id', $data)) {
			$vcard_id = (int) $data['vcard_id'];
			if ($this->vcard_id != $vcard_id) $auditRow['vcard_id'] = $this->vcard_id = $vcard_id;
		}
		if (array_key_exists('category', $data)) {
	    	$category = trim(strip_tags($data['category']));
			if ($category == '' || strlen($category) > 255) return 'Integrity';
			if ($this->category != $category) $auditRow['category'] = $this->category = $category;
		}
		if (array_key_exists('subcategory', $data)) {
	    	$subcategory = trim(strip_tags($data['subcategory']));
			if (strlen($subcategory) > 255) return 'Integrity';
			if ($this->subcategory != $subcategory) $auditRow['subcategory'] = $this->subcategory = $subcategory;
		}
		if (array_key_exists('locale_1', $data)) {
	    	$locale_1 = trim(strip_tags($data['locale_1']));
			if (strlen($locale_1) > 255) return 'Integrity';
			if ($this->locale_1 != $locale_1) $auditRow['locale_1'] = $this->locale_1 = $locale_1;
		}
		if (array_key_exists('locale_2', $data)) {
	    	$locale_2 = trim(strip_tags($data['locale_2']));
			if (strlen($locale_2) > 255) return 'Integrity';
			if ($this->locale_2 != $locale_2) $auditRow['locale_2'] = $this->locale_2 = $locale_2;
		}
		if (array_key_exists('identifier', $data)) {
	    	$identifier = trim(strip_tags($data['identifier']));
			if (strlen($identifier) > 255) return 'Integrity';
			if ($this->identifier != $identifier) $auditRow['identifier'] = $this->identifier = $identifier;
		}
		if (array_key_exists('caption', $data)) {
	    	$caption = trim(strip_tags($data['caption']));
			if (strlen($caption) > 255) return 'Integrity';
			if ($this->caption != $caption) $auditRow['caption'] = $this->caption = $caption;
		}
		if (array_key_exists('caption_locale_1', $data)) {
	    	$caption_locale_1 = trim(strip_tags($data['caption_locale_1']));
			if (strlen($caption_locale_1) > 255) return 'Integrity';
			if ($this->caption_locale_1 != $caption_locale_1) $auditRow['caption_locale_1'] = $this->caption_locale_1 = $caption_locale_1;
		}
		if (array_key_exists('caption_locale_2', $data)) {
	    	$caption_locale_2 = trim(strip_tags($data['caption_locale_2']));
			if (strlen($caption_locale_2) > 255) return 'Integrity';
			if ($this->caption_locale_2 != $caption_locale_2) $auditRow['caption_locale_2'] = $this->caption_locale_2 = $caption_locale_2;
		}
		if (array_key_exists('description', $data)) {
	    	$description = trim(strip_tags($data['description']));
			if (strlen($description) > 2047) return 'Integrity';
			if ($this->description != $description) $auditRow['description'] = $this->description = $description;
		}
		if (array_key_exists('description_locale_1', $data)) {
	    	$description_locale_1 = trim(strip_tags($data['description_locale_1']));
			if (strlen($description_locale_1) > 65535) return 'Integrity';
			if ($this->description_locale_1 != $description_locale_1) $auditRow['description_locale_1'] = $this->description_locale_1 = $description_locale_1;
		}
		if (array_key_exists('description_locale_2', $data)) {
	    	$description_locale_2 = trim(strip_tags($data['description_locale_2']));
			if (strlen($description_locale_2) > 65535) return 'Integrity';
			if ($this->description_locale_2 != $description_locale_2) $auditRow['description_locale_2'] = $this->description_locale_2 = $description_locale_2;
		}
		if (array_key_exists('begin_date', $data)) {
	    	$begin_date = $data['begin_date'];
			if ($begin_date && !checkdate(substr($begin_date, 5, 2), substr($begin_date, 8, 2), substr($begin_date, 0, 4))) return 'Integrity';
	    	if ($this->begin_date != $begin_date) $auditRow['begin_date'] = $this->begin_date = $begin_date;
		}
		if (array_key_exists('end_date', $data)) {
	    	$end_date = $data['end_date'];
			if ($end_date && !checkdate(substr($end_date, 5, 2), substr($end_date, 8, 2), substr($end_date, 0, 4))) return 'Integrity';
	    	if ($this->end_date != $end_date) $auditRow['end_date'] = $this->end_date = $end_date;
		}
		if (array_key_exists('day_of_week', $data)) {
	    	$day_of_week = (int) $data['day_of_week'];
			if ($this->day_of_week != $day_of_week) $auditRow['day_of_week'] = $this->day_of_week = $day_of_week;
		}
		if (array_key_exists('day_of_month', $data)) {
	    	$day_of_month = (int) $data['day_of_month'];
			if ($this->day_of_month != $day_of_month) $auditRow['day_of_month'] = $this->day_of_month = $day_of_month;
		}
		if (array_key_exists('begin_time', $data)) {
	    	$begin_time = $data['begin_time'];
			if ($begin_time && ((int) substr($begin_time, 0, 2) < 0 || (int) substr($begin_time, 0, 2) > 23 || (int) substr($begin_time, 2, 2) < 0 || (int) substr($begin_time, 0, 2) > 59 || (int) substr($begin_time, 4, 2) < 0 || (int) substr($begin_time, 4, 2) > 59)) return 'Integrity';
	    	if ($this->begin_time != $begin_time) $auditRow['begin_time'] = $this->begin_time = $begin_time;
		}
		if (array_key_exists('end_time', $data)) {
	    	$end_time = $data['end_time'];
			if ($end_time && ((int) substr($end_time, 0, 2) < 0 || (int) substr($end_time, 0, 2) > 23 || (int) substr($end_time, 2, 2) < 0 || (int) substr($end_time, 0, 2) > 59 || (int) substr($end_time, 4, 2) < 0 || (int) substr($end_time, 4, 2) > 59)) return 'Integrity';
	    	if ($this->end_time != $end_time) $auditRow['end_time'] = $this->end_time = $end_time;
		}
		if (array_key_exists('time_zone', $data)) {
	    	$time_zone = (float) $data['time_zone'];
			if ($this->time_zone != $time_zone) $auditRow['time_zone'] = $this->time_zone = $time_zone;
		}
		if (array_key_exists('location', $data)) {
			$location = trim(strip_tags($data['location']));
			if (strlen($location) > 255) return 'Integrity';
			if ($this->location != $location) $auditRow['location'] = $this->location = $location;
		}
		if (array_key_exists('latitude', $data)) {
	    	$latitude = (float) $data['latitude'];
			if ($this->latitude != $latitude) $auditRow['latitude'] = $this->latitude = $latitude;
		}
		if (array_key_exists('longitude', $data)) {
	    	$longitude = (float) $data['longitude'];
			if ($this->longitude != $longitude) $auditRow['longitude'] = $this->longitude = $longitude;
		}
		if (array_key_exists('value', $data)) {
	    	$value = (float) $data['value'];
			if ($this->value != $value) $auditRow['value'] = $this->value = $value;
		}
		if (array_key_exists('comments', $data)) {
	    	$comment = trim(strip_tags($data['comments']));
			if (strlen($comment) > 65535) return 'Integrity';
		}
		else $comment = '';
		$this->comments[] = array(
				'time' => Date('Y-m-d G:i:s'),
				'n_fn' => $context->getFormatedName(),
				'status' => $this->status,
				'comment' => $comment,
		);

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
			if ($this->property_3 != $property_3) $auditRow['property_3'] = $this->property_3 = $property_3;
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
    	Request::getTable()->save($this);
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
    	$request = Request::get($this->id);
    	if ($update_time && $request->update_time > $update_time) return 'Isolation';
    	Request::getTable()->save($this);
		return ('OK');
    }
    
    /**
     * Checks if this request can de deleted. 
     * An request is not deletable if the result of calling the isUsed() method for each class which has registered itself in the 'ppitCoreDependencies' list returns true.
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
    	$request = Request::get($this->id);
    	if ($update_time && $request->update_time > $update_time) return 'Isolation';
    	$this->status = 'deleted';
    	Request::getTable()->save($this);
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
     * Returns the object to relational manager for the Event class
     */
    public static function getTable()
    {
    	if (!Request::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Request::$table = $sm->get(RequestTable::class);
    	}
    	return Request::$table;
    }
}
