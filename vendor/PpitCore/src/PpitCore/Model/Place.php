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
 * The Place class implements the first subdivision of the customer organization. It can typically be mapped on legal geographical entities that constitutes a firm.
 *
 * A place has an identifier (for example a legal one), an opening date ans possibly a closing date.
 * It can be given:
 * - For accounting applications: a tax regime and a legal footer string,
 * - For operational services: a reception and delivery contact id (foreign keys on Vcard),
 * - A logo and a banner for communication purposes
 * - And number of domain  specific properties for search and restitution purposes
 */
class Place implements InputFilterAwareInterface
{
	/** @var int */ public $id;
	/** @var int */ public $instance_id;
	/** @var string */ public $status;
	/** @var string */ public $identifier;
	/** @var string */ public $caption;
	/** @var string */ public $opening_date;
	/** @var string */ public $closing_date;
	/** @var string */ public $tax_regime;
	/** @var int */ public $reception_vcard_id;
	/** @var int */ public $delivery_vcard_id;
	/** @var string */ public $logo_src;
	/** @var int */ public $logo_width;
	/** @var int */ public $logo_height;
	/** @var string */ public $logo_href;
	/** @var string */ public $banner_src;
	/** @var string */ public $banner_href;
	/** @var string */ public $legal_footer;
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
    /** @var array */ public $audit;
	/** @var string */ public $update_time;

	// Joined properties
	/** @var string */ public $reception_n_fn;
	/** @var string */ public $reception_email;
	/** @var string */ public $reception_tel_work;
	/** @var string */ public $reception_tel_cell;
	/** @var string */ public $delivery_n_fn;
	/** @var string */ public $delivery_email;
	/** @var string */ public $delivery_tel_work;
	/** @var string */ public $delivery_tel_cell;
	
	// Transient properties
	/** @var array */ public $properties;
	/** @var \PpitCore\Model\Vcard */ public $reception_contact;
	/** @var \PpitCoreModel\Model\Vcard */ public $delivery_contact;
	/** @var boolean */ public $is_open;
	
    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

    // Static fields
    /** @var \PpitCore\Model\Place */ private static $table;
    
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
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->opening_date = (isset($data['opening_date'])) ? $data['opening_date'] : null;
        $this->closing_date = (isset($data['closing_date'])) ? (($data['closing_date'] == '9999-12-31') ? null : $data['closing_date']) : null;
        $this->tax_regime = (isset($data['tax_regime'])) ? $data['tax_regime'] : null;
        $this->reception_vcard_id = (isset($data['reception_vcard_id'])) ? $data['reception_vcard_id'] : null;
        $this->delivery_vcard_id = (isset($data['delivery_vcard_id'])) ? $data['delivery_vcard_id'] : null;
        $this->logo_src = (isset($data['logo_src'])) ? $data['logo_src'] : null;
        $this->logo_width = (isset($data['logo_width'])) ? $data['logo_width'] : null;
        $this->logo_height = (isset($data['logo_height'])) ? $data['logo_height'] : null;
        $this->logo_href = (isset($data['logo_href'])) ? $data['logo_href'] : null;
        $this->banner_src = (isset($data['banner_src'])) ? $data['banner_src'] : null;
        $this->banner_href = (isset($data['banner_href'])) ? $data['banner_href'] : null;
        $this->legal_footer = (isset($data['legal_footer'])) ? $data['legal_footer'] : null;
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
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
        
        // Joined properties
        $this->reception_n_fn = (isset($data['reception_n_fn'])) ? $data['reception_n_fn'] : null;
        $this->reception_email = (isset($data['reception_email'])) ? $data['reception_email'] : null;
        $this->reception_tel_work = (isset($data['reception_tel_work'])) ? $data['reception_tel_work'] : null;
        $this->reception_tel_cell = (isset($data['reception_tel_cell'])) ? $data['reception_tel_cell'] : null;
        $this->delivery_n_fn = (isset($data['delivery_n_fn'])) ? $data['delivery_n_fn'] : null;
        $this->delivery_email = (isset($data['delivery_email'])) ? $data['delivery_email'] : null;
        $this->delivery_tel_work = (isset($data['delivery_tel_work'])) ? $data['delivery_tel_work'] : null;
        $this->delivery_tel_cell = (isset($data['delivery_tel_cell'])) ? $data['delivery_tel_cell'] : null;
    }

	/**
	 * Used for object (php) to relational (database) mapping.
	 * @return array
	 */
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['status'] = $this->status;
    	$data['identifier'] = $this->identifier;
    	$data['caption'] = $this->caption;
    	$data['opening_date'] = ($this->opening_date) ? $this->opening_date : null;
    	$data['closing_date'] = ($this->closing_date) ? $this->closing_date : '9999-12-31';
    	$data['tax_regime'] = $this->tax_regime;
    	$data['reception_vcard_id'] = (int) $this->reception_vcard_id;
    	$data['delivery_vcard_id'] = (int) $this->delivery_vcard_id;
    	$data['logo_src'] = $this->logo_src;
    	$data['logo_width'] = $this->logo_width;
    	$data['logo_height'] = $this->logo_height;
    	$data['logo_href'] = $this->logo_href;
    	$data['banner_src'] = $this->banner_src;
    	$data['banner_href'] = $this->banner_href;
    	$data['legal_footer'] = $this->legal_footer;
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
    	$data['audit'] = json_encode($this->audit);
    	 
    	return $data;
    }

    /**
     * Returns an array of Place instances:
     * The transient property 'is_open' is computed, based on the opening and the closing date: A place is open if the opening and closing dates are framing the current date.
     * If $mode == 'todo', the list is filtered on only open places.
     * Otherwise, the list is filtered on places matching the key-value pairs provided in the $params argument:
     * - if the key has the form 'min_<property_name>', only the rows having a value for this property greater than the argument are kept
     * - if the key has the form 'max_<property_name>', only the rows having a value for this property less than the argument are kept
     * - else only the row which are 'like' (in SQL sense) the argument are kept in the list.
     * Deleted places remains in the database with a 'deleted' status, so in any case, the getList methods filters places with the 'deleted' status. 
     * The result is primarily ordered according to the value of $major with the direction (ASC or DESC) specified by $dir, and secondarily by descending 'opening_date'
     * @param array $params
     * @param string $major
     * @param string $dir
     * @param string $mode
     * @return Instance[]
     */
    public static function getList($params, $major = 'caption', $dir = 'ASC', $mode = 'todo')
    {
    	$select = Place::getTable()->getSelect()
    		->join(array('reception_contact' => 'core_vcard'), 'core_place.reception_vcard_id = reception_contact.id', array('reception_n_fn' => 'n_fn', 'reception_email' => 'email', 'reception_tel_work' => 'tel_work', 'reception_tel_cell' => 'tel_cell'), 'left')
    		->join(array('delivery_contact' => 'core_vcard'), 'core_place.delivery_vcard_id = delivery_contact.id', array('delivery_n_fn' => 'n_fn', 'delivery_email' => 'email', 'delivery_tel_work' => 'tel_work', 'delivery_tel_cell' => 'tel_cell'), 'left')
    		->order(array($major.' '.$dir, 'caption', 'opening_date DESC'));
    	$where = new Where;
    	$where->notEqualTo('core_place.status', 'deleted');

    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->lessThanOrEqualTo('opening_date', date('Y-m-d'));
    		$where->greaterThanOrEqualTo('closing_date', date('Y-m-d'));
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('core_place.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('core_place.'.substr($propertyId, 4), $params[$propertyId]);
    			else $where->like('core_place.'.$propertyId, '%'.$params[$propertyId].'%');
    		}
    	}
    	$select->where($where);

    	$cursor = Place::getTable()->selectWith($select);
    	$places = array();
    	foreach ($cursor as $place) {
    		$place->properties = $place->toArray();
    		
    		// The toArray() method, as the ORM for place replaces null values of closing_date by '9999-12-31' for filtering and ordering reason.
    		// In the present context of using the toArray() method, we shouldn't have this transformation so we cancel it. 
    		if (!$place->closing_date) $place->properties['closing_date'] == null;

    		if ((!$place->opening_date || $place->opening_date <= Date('Y-m-d')) && (!$place->closing_date || $place->closing_date >= Date('Y-m-d'))) $place->is_open = true;
    		else $place->is_open = false;
    		$places[$place->id] = $place;
    	}
    	return $places;
    }
    
    /**
     * Retrieve from the database the place having the giving value as the given specified column ('id' as a default).
     * @param string $id
     * @param string $column
     * @return Place
     */
    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();
    	$place = Place::getTable()->get($id, $column);

    	if ($place) {
	    	$place->properties = $place->toArray();
    		
    		// The toArray() method, as the ORM for place replaces null values of closing_date by '9999-12-31' for filtering and ordering reason.
    		// In the present context of using the toArray() method, we shouldn't have this transformation so we cancel it. 
	    	if (!$place->closing_date) $place->properties['closing_date'] == null;
	    	
	    	// Retrieve the reception contact
	    	if ($place->reception_vcard_id) $place->reception_contact = Vcard::getTable()->get($place->reception_vcard_id);
	    	else $place->reception_contact = new Vcard;
	    	
	    	// Retrieve the delivery contact
	    	if ($place->delivery_vcard_id) $place->delivery_contact = Vcard::getTable()->get($place->delivery_vcard_id);
	    	else $place->delivery_contact = new Vcard;
    	}

    	return $place;
    }

    /**
     * Returns a new instance of Place.
     * The status is set to 'new' and the transient pointers to reception_contact and delivery_contact are set to new instances of Vcard.
     * @return Instance
     */
    public static function instanciate()
    {
    	$context = Context::getCurrent();
    	$place = new Place;
    	$place->status = 'new';
    	$place->reception_contact = new Vcard;
    	$place->delivery_contact = new Vcard;
    	return $place;
	}

    /**
     * Loads the data into the Place object depending of an array, typically constructed by the controller with value extracted from an HTTP request.
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
		if (array_key_exists('caption', $data)) {
			$caption = trim(strip_tags($data['caption']));
			if ($caption == '' || strlen($caption) > 255) return 'Integrity';
    		if ($this->caption != $caption) $auditRow['caption'] = $this->caption = $caption;
		}
		if (array_key_exists('opening_date', $data)) {
			$opening_date = $data['opening_date'];
			if ($opening_date && !checkdate(substr($opening_date, 5, 2), substr($opening_date, 8, 2), substr($opening_date, 0, 4))) return 'Integrity';
    		if ($this->opening_date != $opening_date) $auditRow['opening_date'] = $this->opening_date = $opening_date;
		}
		if (array_key_exists('closing_date', $data)) {
			$closing_date = $data['closing_date'];
			if ($closing_date && !checkdate(substr($closing_date, 5, 2), substr($closing_date, 8, 2), substr($closing_date, 0, 4))) return 'Integrity';
    		if ($this->closing_date != $closing_date) $auditRow['closing_date'] = $this->closing_date = $closing_date;
		}
		if (array_key_exists('tax_regime', $data)) {
			$tax_regime = $data['tax_regime'];
			if (!$tax_regime) return 'Integrity';
    		if ($this->tax_regime != $tax_regime) $auditRow['tax_regime'] = $this->tax_regime = $tax_regime;
		}
		if (array_key_exists('reception_vcard_id', $data)) {
			$reception_vcard_id = (int) $data['reception_vcard_id'];
    		if ($this->reception_vcard_id != $reception_vcard_id) $auditRow['reception_vcard_id'] = $this->reception_vcard_id = $reception_vcard_id;
		}
		if (array_key_exists('delivery_vcard_id', $data)) {
			$delivery_vcard_id = (int) $data['delivery_vcard_id'];
    		if ($this->delivery_vcard_id != $delivery_vcard_id) $auditRow['delivery_vcard_id'] = $this->delivery_vcard_id = $delivery_vcard_id;
		}
		if (array_key_exists('logo_src', $data)) {
			$logo_src = trim(strip_tags($data['logo_src']));
			if (strlen($logo_src) > 255) return 'Integrity';
    		if ($this->logo_src != $logo_src) $auditRow['logo_src'] = $this->logo_src = $logo_src;
		}
		if (array_key_exists('logo_width', $data)) {
			$logo_width = $data['logo_width'];
    		if ($this->logo_width != $logo_width) $auditRow['logo_width'] = $this->logo_width = $logo_width;
		}
		if (array_key_exists('logo_height', $data)) {
			$logo_height = $data['logo_height'];
    		if ($this->logo_height != $logo_height) $auditRow['logo_height'] = $this->logo_height = $logo_height;
		}
		if (array_key_exists('logo_href', $data)) {
			$logo_href = trim(strip_tags($data['logo_href']));
			if (strlen($logo_href) > 255) return 'Integrity';
    		if ($this->logo_href != $logo_href) $auditRow['logo_href'] = $this->logo_href = $logo_href;
		}
		if (array_key_exists('banner_src', $data)) {
			$banner_src = trim(strip_tags($data['banner_src']));
			if (strlen($banner_src) > 255) return 'Integrity';
    		if ($this->banner_src != $banner_src) $auditRow['banner_src'] = $this->banner_src = $banner_src;
		}
		if (array_key_exists('banner_href', $data)) {
			$banner_href = trim(strip_tags($data['banner_href']));
			if (strlen($banner_href) > 255) return 'Integrity';
    		if ($this->banner_href != $banner_href) $auditRow['banner_href'] = $this->banner_href = $banner_href;
		}
		if (array_key_exists('legal_footer', $data)) {
			$legal_footer = trim(strip_tags($data['legal_footer']));
			if (strlen($legal_footer) > 255) return 'Integrity';
    		if ($this->legal_footer != $legal_footer) $auditRow['legal_footer'] = $this->legal_footer = $legal_footer;
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

		// Update the audit
		$this->audit[] = $auditRow;
		
    	return 'OK';
	}
	
    /**
     * Adds a new row in the database after checking that it does not conflict with an existing, not deleted, place with the same 'identifier'. 
     * In such a case the methods does not affect the database and returns 'Duplicate', otherwise it returns 'OK'.
     * The reception and delivery contacts if provided are retrieved from the database or created (optimize method of Vcard) and linked to the new place.
     * @return string
     */
	public function add()
    {
		if (Generic::getTable()->cardinality('core_place', array('status' => 'new', 'identifier' => $this->identifier)) > 0) return 'Duplicate';
    	
    	$this->id = null;
    	if ($this->reception_contact->n_last) $this->reception_vcard_id = Vcard::optimize($this->reception_contact)->id;
    	if ($this->delivery_contact->n_last) $this->delivery_vcard_id = Vcard::optimize($this->delivery_contact)->id;
    	Place::getTable()->save($this);

		return ('OK');
    }

    /**
     * Update an existing row in the database.
     * If $update_time is provided, an isolation check is performed, such that the current update time in the database is not greater than the one given as an argument.
     * In such a case the methods does not affect the database and returns 'Isolation', otherwise it returns 'OK'.
     * The reception and delivery contacts if provided are retrieved from the database or created (optimize method of Vcard) and linked to the updated place.
     * @param string $update_time
     * @return string
     */
    public function update($update_time)
    {
    	$place = Place::get($this->id);
    	if ($update_time && $place->update_time > $update_time) return 'Isolation';
    	if ($this->reception_contact->n_last) $this->reception_vcard_id = Vcard::optimize($this->reception_contact)->id;
    	if ($this->delivery_contact->n_last) $this->delivery_vcard_id = Vcard::optimize($this->delivery_contact)->id;
    	Place::getTable()->save($this);

		return ('OK');
    }
    
    /**
     * Checks if this place can de deleted. 
     * A place is not deletable if a community exists on this place.
     * A place is not deletable if an interaction is owned by this place.
     * A place is not deletable if the result of calling the isUsed() method for each class which has registered itself in the 'ppitCoreDependencies' list returns true.
     * @return boolean
     */
    public function isDeletable() {
    	if (Generic::getTable()->cardinality('core_community', array('status != ?' => 'deleted', 'place_id' => $this->id)) > 0) return false;
    	if (Generic::getTable()->cardinality('core_interaction', array('status != ?' => 'deleted', 'place_id' => $this->id)) > 0) return false;
    	 
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
    	$place = Place::get($this->id);
    	if ($update_time && $place->update_time > $update_time) return 'Isolation';
    	$this->status = 'deleted';
    	Place::getTable()->save($this);

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
    	if (!Place::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Place::$table = $sm->get('PpitCore\Model\PlaceTable');
    	}
    	return Place::$table;
    }
}
