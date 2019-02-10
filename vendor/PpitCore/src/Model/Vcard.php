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
 * The Vcard class manages the data related to a contact.
 * A Vcard stores for a given contact:
 * - Identification properties: title, name, birthdate, nationality
 * - Contact properties: email, phone numbers and ost address
 * - Habilitation properties: roles and context-specific perimeters
 * - Preference properties: locale, demo mode and notifications acceptation
 * A vcard can be global to a 2pit logical instance (\Model\Instance) or belong to a community (\Model\Community)
 */
class Vcard
{
	public static function getConfig() { // Deprecated => To be removed from updateContact
		$context = Context::getCurrent();
		$properties = array();
		foreach($context->getConfig('vcard/properties') as $propertyId => $property) {
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			$properties[$propertyId] = $property;
		}
		return $properties;
	}

	/** @var int */ public $id;
	/** @var int */ public $instance_id;
	/** @var int */ public $status;
	/** @var array */ public $applications;
	/** @var array */ public $communities;
	/** @var string */ public $n_title;
    /** @var string */ public $n_first;
    /** @var string */ public $n_last;
    /** @var string */ public $n_fn;
    /** @var string */ public $org;
    /** @var string */ public $tel_work;
    /** @var string */ public $tel_cell;
    /** @var string */ public $email;
    /** @var string */ public $adr_street;
    /** @var string */ public $adr_extended;
    /** @var string */ public $adr_post_office_box;
    /** @var string */ public $adr_zip;
    /** @var string */ public $adr_city;
    /** @var string */ public $adr_state;
    /** @var string */ public $adr_country;
    /** @var string */ public $gender;
    /** @var string */ public $birth_date;
    /** @var string */ public $place_of_birth;
    /** @var string */ public $nationality;
    /** @var string */ public $origine;
    /** @var string */ public $tiny_1;
    /** @var string */ public $tiny_2;
	/** @var string */ public $tiny_3;
    /** @var string */ public $tiny_4;
    /** @var string */ public $tiny_5;
    /** @var string */ public $tiny_6;
    /** @var string */ public $tiny_7;
    /** @var string */ public $tiny_8;
    /** @var string */ public $tiny_9;
    /** @var string */ public $tiny_10;
	/** @var int */ public $photo_link_id;
    /** @var array */ public $roles = array();
    /** @var array */ public $perimeters;
    /** @var string */ public $locale;
    /** @var boolean */ public $is_demo_mode_active;
    /** @var boolean */ public $is_notified;
    /** @var string */ public $specifications;
	/** @var string */ public $update_time;

    // Additional properties from joined tables
    /** @var string */ public $community_name;
    
    // Transient properties
    /** @var string */ public $previous_n_last;
    /** @var string */ public $previous_n_first;
    /** @var string */ public $previous_email;
    /** @var string */ public $previous_tel_cell;
    /** @var array */ public $properties;
    
	// Deprecated
	/** @var int */ public $community_id;
	
    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

    // Static fields
    /** @var \Model\Vcard */ private static $table;
    /** @var string */ public static $emailRegex = "/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/";
    /** @var string */ public static $telRegex = "/^\+?([0-9\. ]*)$/";

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
        $this->applications = (isset($data['applications'])) ? json_decode($data['applications'], true) : null;
        $this->communities = (isset($data['communities'])) ? json_decode($data['communities'], true) : null;
        $this->n_title = (isset($data['n_title'])) ? $data['n_title'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->org = (isset($data['org'])) ? $data['org'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->adr_street = (isset($data['adr_street'])) ? $data['adr_street'] : null;
        $this->adr_extended = (isset($data['adr_extended'])) ? $data['adr_extended'] : null;
        $this->adr_post_office_box = (isset($data['adr_post_office_box'])) ? $data['adr_post_office_box'] : null;
        $this->adr_zip = (isset($data['adr_zip'])) ? $data['adr_zip'] : null;
        $this->adr_city = (isset($data['adr_city'])) ? $data['adr_city'] : null;
        $this->adr_state = (isset($data['adr_state'])) ? $data['adr_state'] : null;
        $this->adr_country = (isset($data['adr_country'])) ? $data['adr_country'] : null;
        $this->gender = (isset($data['gender'])) ? $data['gender'] : null;
        $this->birth_date = (isset($data['birth_date'])) ? $data['birth_date'] : null;
        $this->place_of_birth = (isset($data['place_of_birth'])) ? $data['place_of_birth'] : null;
        $this->nationality = (isset($data['nationality'])) ? $data['nationality'] : null;
        $this->origine = (isset($data['origine'])) ? $data['origine'] : null;
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
        $this->roles = (isset($data['roles'])) ? json_decode($data['roles'], true) : null;
        $this->perimeters = (isset($data['perimeters'])) ? json_decode($data['perimeters'], true) : null;
        $this->locale = (isset($data['locale'])) ? $data['locale'] : null;
        $this->is_notified = (isset($data['is_notified'])) ? $data['is_notified'] : null;
        $this->is_demo_mode_active = (isset($data['is_demo_mode_active'])) ? $data['is_demo_mode_active'] : null;
        $this->photo_link_id = (isset($data['photo_link_id'])) ? $data['photo_link_id'] : null;
        $this->specifications = (isset($data['specifications'])) ? json_decode($data['specifications'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

    	// Additional properties from joined tables
	    $this->community_name = (isset($data['community_name'])) ? $data['community_name'] : null;
	    
	    // Deprecated
        $this->community_id = (isset($data['community_id'])) ? $data['community_id'] : null;
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
    	$data['applications'] = $this->applications;
    	$data['communities'] = $this->communities;
    	$data['n_title'] = $this->n_title;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['n_fn'] = $this->n_fn;
    	$data['org'] = $this->org;
    	$data['tel_work'] = $this->tel_work;
    	$data['tel_cell'] = $this->tel_cell;
    	$data['email'] = $this->email;
    	$data['adr_street'] = $this->adr_street;
    	$data['adr_extended'] = $this->adr_extended;
    	$data['adr_post_office_box'] = $this->adr_post_office_box;
    	$data['adr_zip'] = $this->adr_zip;
    	$data['adr_city'] = $this->adr_city;
    	$data['adr_state'] = $this->adr_state;
    	$data['adr_country'] = $this->adr_country;
    	$data['gender'] = $this->gender;
    	$data['birth_date'] = ($this->birth_date) ? $this->birth_date : null;
    	$data['place_of_birth'] = $this->place_of_birth;
    	$data['nationality'] = $this->nationality;
    	$data['origine'] = $this->origine;
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
    	$data['roles'] = $this->roles;
    	$data['perimeters'] = $this->perimeters;
    	$data['locale'] = $this->locale;
    	$data['is_notified'] = $this->is_notified;
    	$data['is_demo_mode_active'] = (int) $this->is_demo_mode_active;
    	$data['photo_link_id'] = $this->photo_link_id;
    	$data['specifications'] = $this->specifications;
    	
    	// Deprecated
    	$data['community_id'] = (int) $this->community_id;

    	return $data;
    }

    /**
     * Used for object (php) to relational (database) mapping.
     * @return array
     */
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['applications'] = json_encode($this->applications);
    	$data['communities'] = json_encode($this->communities);
    	$data['roles'] = json_encode($this->roles);
    	$data['perimeters'] = json_encode($this->perimeters);
    	$data['specifications'] = json_encode($this->specifications);
    	return $data;
    }

    /**
     * Returns an array of Vcard instances filtered on the optional given community (no filtering on community if not provided):
     * - without further filtering the list if $mode == 'todo'
     * - matching (with the 'like' sql comparator) the key-value pairs provided in the params argument
     * - matching (with ths '<' sql comparator) the key_value pairs provided in the params argument, where the key has the form 'min_<property_name>'
     * - matching (with ths '>' sql comparator) the key_value pairs provided in the params argument, where the key has the form 'max_<property_name>'
     * The result is primarily ordered according to the value of $major with the direction (ASC or DESC) specified by $dir, and secondarily by 'n_last'
     * @param int $community_id
     * @param array $params
     * @param string $major
     * @param string $dir
     * @param string $mode
     * @return Community[]
     */
	public static function getList($community_id, $params, $major = 'n_last', $dir = 'ASC', $mode = '')
    {
    	$context = Context::getCurrent();
    
    	// Prepare the SQL request
    	$select = Vcard::getTable()->getSelect();
    	
    	$where = new Where();

    	// Access control
    	$community = Community::get($community_id);
    	if ($community) $community_id = $community->id; else $community_id = 0;
    	$where->equalTo('community_id', $community_id);

    	if ($mode == 'todo') {
    	
    		// Todo : Limit the list size
    	}
    	else {
    			
    		// Set the filters
    		if (isset($params['n_fn'])) $where->like('n_fn', '%'.$params['n_fn'].'%');
    		if (isset($params['email'])) $where->like('email', '%'.$params['email'].'%');
    		if (isset($params['tel_cell'])) $where->like('tel_cell', '%'.$params['tel_cell'].'%');
    		if (isset($params['tel_work'])) $where->like('tel_work', '%'.$params['tel_work'].'%');
    		if (isset($params['adr_city'])) $where->like('adr_city', '%'.$params['adr_city'].'%');
    		if (isset($params['adr_state'])) $where->like('adr_state', '%'.$params['adr_state'].'%');
    		if (isset($params['adr_country'])) $where->like('adr_country', '%'.$params['adr_country'].'%');
    		if (isset($params['gender'])) $where->equalTo('gender', $params['gender']);
    		if (isset($params['min_birth_date'])) $where->greaterThanOrEqualTo('birth_date', $params['min_birth_date']);
			if (isset($params['max_birth_date'])) $where->lessThanOrEqualTo('birth_date', $params['max_birth_date']);
    		if (isset($params['place_of_birth'])) $where->like('place_of_birth', '%'.$params['place_of_birth'].'%');
    		if (isset($params['nationality'])) $where->like('nationality', '%'.$params['nationality'].'%');
    		if (isset($params['is_notified'])) $where->like('is_notified', '%'.$params['is_notified'].'%');
    		if (isset($params['is_demo_mode_active'])) $where->like('is_demo_mode_active', '%'.$params['is_demo_mode_active'].'%');
    	}

    	$select->where($where)->order(array($major.' '.$dir, 'n_fn'));
    	$cursor = Vcard::getTable()->selectWith($select);
    
    	// Execute the request
    	$vcards = array();
    	foreach ($cursor as $vcard) {
    		$vcards[$vcard->id] = $vcard;
    		if ($community) $vcard->community_name = $community->name;
    	}
    	return $vcards;
    }

    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();
    	$vcard = Vcard::getTable()->get($id, $column);
    	if ($vcard) {
	    	$instance_id = $vcard->instance_id;
/*	    	$community_id = $vcard->community_id;
	    	if ($community_id) {
	    		$community = Community::getTable()->get($community_id);
	    		if (!$community) return null;
	    	}
	    
	    	if ($community_id) $vcard->community_name = $community->name;*/

	    	$roles = array();
	    	foreach ($vcard->roles as $role) $roles[$role] = $role;
	    	$vcard->roles = $roles;
	
			$vcard->properties = $vcard->toArray();
    	}
    	return $vcard;
    }

    public static function instanciate($community_id = 0)
    {
    	$vcard = new Vcard;
    	$vcard->status = 'new';
    	$vcard->community_id = $community_id; // Deprecated
    	$vcard->applications = array();
    	$vcard->communities = array();
    	$vcard->roles = array();
    	$vcard->perimeters = array();
    	$vcard->specifications = array();
    	$vcard->locale = 'fr_FR';
    	$vcard->photo_link_id = 'no-photo.png';
    	$vcard->properties = $vcard->toArray();
    	return $vcard;
    }
    
    public function loadData($data)
    {
    	$context = Context::getCurrent();
    	$auditRow = array(
    			'time' => Date('Y-m-d G:i:s'),
    			'n_fn' => $context->getFormatedName(),
    	);

    	// Save the identifying previous data
    	$this->previous_n_last = $this->n_last;
    	$this->previous_n_first = $this->n_first;
    	$this->previous_email = $this->email;
    	$this->previous_tel_cell = $this->tel_cell;

        if (array_key_exists('instance_id',$data)) {
	    	$instance_id = (int) $data['instance_id'];
    		if ($this->instance_id != $instance_id) $auditRow['instance_id'] = $this->instance_id = $instance_id;
		}
    	if (array_key_exists('applications', $data)) {
    		$applications = $data['applications'];
    		if ($this->applications != $applications) $auditRow['applications'] = $this->applications = $applications;
    	}    	
        if (array_key_exists('communities', $data)) {
    		$communities = $data['communities'];
    		if ($this->communities != $communities) $auditRow['communities'] = $this->communities = $communities;
    	}    	
    	if (array_key_exists('community_id', $data)) { // Deprecated
    		$community_id = (int) $data['community_id'];
    		if ($this->community_id != $community_id) $auditRow['community_id'] = $this->community_id = $community_id;
    	}
        if (array_key_exists('status', $data)) {
    		$status = trim(strip_tags($data['status']));
    		if (strlen($status) > 255) return 'Integrity';
    		if ($this->status != $status) $auditRow['status'] = $this->status = $status;
    	}
    	if (array_key_exists('n_title', $data)) {
    		$n_title =  trim(strip_tags($data['n_title']));
    		if (strlen($n_title) > 255) return 'Integrity';
    		if ($this->n_title != $n_title) $auditRow['n_title'] = $this->n_title = $n_title;
    	}
    	if (array_key_exists('n_last', $data)) {
    		$n_last =  trim(strip_tags($data['n_last']));
    		if (strlen($n_last) > 255) return 'Integrity';
    		if ($this->n_last != $n_last) $auditRow['n_last'] = $this->n_last = $n_last;
    	}
    	if (array_key_exists('n_first', $data)) {
    		$n_first =  trim(strip_tags($data['n_first']));
    		if (strlen($n_first) > 255) return 'Integrity';
    		if ($this->n_first != $n_first) $auditRow['n_first'] = $this->n_first = $n_first;
    	}
    	if (array_key_exists('email', $data)) {
    		$email =  trim(strip_tags($data['email']));
    		if (strlen($email) > 255) return 'Integrity';
    		if ($email && !preg_match(Vcard::$emailRegex, $email)) return 'Integrity';
    		if ($this->email != $email) $auditRow['email'] = $this->email = $email;
    	}
    	if (array_key_exists('tel_work', $data)) {
    		$tel_work =  trim(strip_tags($data['tel_work']));
	    	if (strlen($tel_work) > 255) return 'Integrity';
//	    	if ($tel_work && !preg_match(Vcard::$telRegex, $tel_work)) return 'Integrity';
    		if ($this->tel_work != $tel_work) $auditRow['tel_work'] = $this->tel_work = $tel_work;
    	}
    	if (array_key_exists('tel_cell', $data)) {
    		$tel_cell =  trim(strip_tags($data['tel_cell']));
	    	if (strlen($tel_cell) > 255) return 'Integrity';
//	    	if ($tel_cell && !preg_match(Vcard::$telRegex, $tel_cell)) return 'Integrity';
    		if ($this->tel_cell != $tel_cell) $auditRow['tel_cell'] = $this->tel_cell = $tel_cell;
    	}
    	if (array_key_exists('adr_street', $data)) {
    		$adr_street = trim(strip_tags($data['adr_street']));
    		if (strlen($adr_street) > 255) return 'Integrity';
    		if ($this->adr_street != $adr_street) $auditRow['adr_street'] = $this->adr_street = $adr_street;
    	}
    	if (array_key_exists('adr_extended', $data)) {
    		$adr_extended = trim(strip_tags($data['adr_extended']));
    		if (strlen($adr_extended) > 255) return 'Integrity';
    		if ($this->adr_extended != $adr_extended) $auditRow['adr_extended'] = $this->adr_extended = $adr_extended;
    	}
    	if (array_key_exists('adr_post_office_box', $data)) {
    		$adr_post_office_box = trim(strip_tags($data['adr_post_office_box']));
    		if (strlen($adr_post_office_box) > 255) return 'Integrity';
    		if ($this->adr_post_office_box != $adr_post_office_box) $auditRow['adr_post_office_box'] = $this->adr_post_office_box = $adr_post_office_box;
    	}
    	if (array_key_exists('adr_zip', $data)) {
    		$adr_zip = trim(strip_tags($data['adr_zip']));
    		if (strlen($adr_zip) > 255) return 'Integrity';
    		if ($this->adr_zip != $adr_zip) $auditRow['adr_zip'] = $this->adr_zip = $adr_zip;
    	}
    	if (array_key_exists('adr_city', $data)) {
    		$adr_city = trim(strip_tags($data['adr_city']));
    		if (strlen($adr_city) > 255) return 'Integrity';
    		if ($this->adr_city != $adr_city) $auditRow['adr_city'] = $this->adr_city = $adr_city;
    	}
    	if (array_key_exists('adr_state', $data)) {
    		$adr_state = trim(strip_tags($data['adr_state']));
    		if (strlen($adr_state) > 255) return 'Integrity';
    		if ($this->adr_state != $adr_state) $auditRow['adr_state'] = $this->adr_state = $adr_state;
    	}
    	if (array_key_exists('adr_country', $data)) {
    		$adr_country = trim(strip_tags($data['adr_country']));
    		if (strlen($adr_country) > 255) return 'Integrity';
    		if ($this->adr_country != $adr_country) $auditRow['adr_country'] = $this->adr_country = $adr_country;
    	}
    	if (array_key_exists('gender', $data)) {
    		$gender = trim(strip_tags($data['gender']));
    		if (strlen($gender) > 255) return 'Integrity';
    		if ($this->gender != $gender) $auditRow['gender'] = $this->gender = $gender;
    	}
    	if (array_key_exists('birth_date', $data)) {
    		$birth_date = trim(strip_tags($data['birth_date']));
			if ($birth_date && !checkdate(substr($birth_date, 5, 2), substr($birth_date, 8, 2), substr($birth_date, 0, 4))) return 'Integrity';
    		if ($this->birth_date != $birth_date) $auditRow['birth_date'] = $this->birth_date = $birth_date;
    	}
    	if (array_key_exists('place_of_birth', $data)) {
    		$place_of_birth = trim(strip_tags($data['place_of_birth']));
    		if (strlen($place_of_birth) > 255) return 'Integrity';
    		if ($this->place_of_birtgh != $place_of_birth) $auditRow['place_of_birth'] = $this->place_of_birth = $place_of_birth;
    	}
    	if (array_key_exists('nationality', $data)) {
    		$nationality = trim(strip_tags($data['nationality']));
    		if (strlen($nationality) > 255) return 'Integrity';
    		if ($this->nationality != $nationality) $auditRow['nationality'] = $this->nationality = $nationality;
    	}
        if (array_key_exists('tiny_1', $data)) {
    		$tiny_1 = trim(strip_tags($data['tiny_1']));
    		if (strlen($tiny_1) > 255) return 'Integrity';
    		if ($this->tiny_1 != $tiny_1) $auditRow['tiny_1'] = $this->tiny_1 = $tiny_1;
    	}
        if (array_key_exists('tiny_2', $data)) {
    		$tiny_2 = trim(strip_tags($data['tiny_2']));
    		if (strlen($tiny_2) > 255) return 'Integrity';
    		if ($this->tiny_2 != $tiny_2) $auditRow['tiny_2'] = $this->tiny_2 = $tiny_2;
    	}
        if (array_key_exists('tiny_3', $data)) {
    		$tiny_3 = trim(strip_tags($data['tiny_3']));
    		if (strlen($tiny_3) > 255) return 'Integrity';
    		if ($this->tiny_3 != $tiny_3) $auditRow['tiny_3'] = $this->tiny_3 = $tiny_3;
    	}
        if (array_key_exists('tiny_4', $data)) {
    		$tiny_4 = trim(strip_tags($data['tiny_4']));
    		if (strlen($tiny_4) > 255) return 'Integrity';
    		if ($this->tiny_4 != $tiny_4) $auditRow['tiny_4'] = $this->tiny_4 = $tiny_4;
    	}
        if (array_key_exists('tiny_5', $data)) {
    		$tiny_5 = trim(strip_tags($data['tiny_5']));
    		if (strlen($tiny_5) > 255) return 'Integrity';
    		if ($this->tiny_5 != $tiny_5) $auditRow['tiny_5'] = $this->tiny_5 = $tiny_5;
    	}
        if (array_key_exists('tiny_6', $data)) {
    		$tiny_6 = trim(strip_tags($data['tiny_6']));
    		if (strlen($tiny_6) > 255) return 'Integrity';
    		if ($this->tiny_6 != $tiny_6) $auditRow['tiny_6'] = $this->tiny_6 = $tiny_6;
    	}
        if (array_key_exists('tiny_7', $data)) {
    		$tiny_7 = trim(strip_tags($data['tiny_7']));
    		if (strlen($tiny_7) > 255) return 'Integrity';
    		if ($this->tiny_7 != $tiny_7) $auditRow['tiny_7'] = $this->tiny_7 = $tiny_7;
    	}
        if (array_key_exists('tiny_8', $data)) {
    		$tiny_8 = trim(strip_tags($data['tiny_8']));
    		if (strlen($tiny_8) > 255) return 'Integrity';
    		if ($this->tiny_8 != $tiny_8) $auditRow['tiny_8'] = $this->tiny_8 = $tiny_8;
    	}
        if (array_key_exists('tiny_9', $data)) {
    		$tiny_9 = trim(strip_tags($data['tiny_9']));
    		if (strlen($tiny_9) > 255) return 'Integrity';
    		if ($this->tiny_9 != $tiny_9) $auditRow['tiny_9'] = $this->tiny_9 = $tiny_9;
    	}
        if (array_key_exists('tiny_10', $data)) {
    		$tiny_10 = trim(strip_tags($data['tiny_10']));
    		if (strlen($tiny_10) > 255) return 'Integrity';
    		if ($this->tiny_10 != $tiny_10) $auditRow['tiny_10'] = $this->tiny_10 = $tiny_10;
    	}
    	if (array_key_exists('locale', $data)) {
    		$locale = trim(strip_tags($data['locale']));
    		if (strlen($locale) > 255) return 'Integrity';
    		if ($this->locale != $locale) $auditRow['locale'] = $this->locale = $locale;
    	}
    	if (array_key_exists('is_notified', $data)) {
    		$is_notified = (int) $data['is_notified'];
    		if ($this->is_notified != $is_notified) $auditRow['is_notified'] = $this->is_notified = $is_notified;
    	}
    	if (array_key_exists('is_demo_mode_active', $data)) {
    		$is_demo_mode_active = (int) $data['is_demo_mode_active'];
    		if ($this->is_demo_mode_active != $is_demo_mode_active) $auditRow['is_demo_mode_active'] = $this->is_demo_mode_active = $is_demo_mode_active;
    	}
    	if (array_key_exists('roles', $data)) {
    		$roles = $data['roles'];
    		if ($this->roles != $roles) $auditRow['roles'] = $this->roles = $roles;
    	}
    	if (array_key_exists('perimeters', $data)) {
    		$perimeters = $data['perimeters'];
    		if ($this->perimeters != $perimeters) $auditRow['perimeters'] = $this->perimeters = $perimeters;
    	}
    	if (array_key_exists('specifications', $data)) {
    		$specifications = $data['specifications'];
    		if ($this->specifications != $specifications) $auditRow['specifications'] = $this->specifications = $specifications;
    	}
    	 
    	$this->n_fn = $this->n_last.', '.$this->n_first;

		// Update the audit
		$this->audit[] = $auditRow;
    	
		return 'OK';
    }
    
    public function add($crossInstance = false)
    {
    	$context = Context::getCurrent();
    	$this->status = 'new';
       	if ($crossInstance) {
			if (!$context->hasRole('super_admin')) return 'Cross instance not authorized';
			Vcard::getTable()->transSave($this);
		}
		else Vcard::getTable()->save($this);
    	return 'OK';
    }

    /**
     * Restfull implementation
     */
    public function loadAndAdd($data)
    {
    	$context = Context::getCurrent();
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', 'vcard->loadData: '.$rc];
    
    	$rc = $this->add((array_key_exists('instance_id', $data)) ? true : false);
    	if ($rc == 'Authorization') return ['401', 'vcard->add: '.$rc];
    	if ($rc != 'OK') return ['500', 'vcard->add: '.$rc];
    
    	$this->properties = $this->getProperties();
    	return ['200', $this->id];
    }
    
    public function update($update_time, $crossInstance = false)
    {
    	$context = Context::getCurrent();

    	// Check isolation
		$vcard = Vcard::getTable()->get($this->id);
		if ($update_time && $vcard->update_time > $update_time) return 'Isolation';
		if ($crossInstance) {
			if (!$context->hasRole('super_admin')) return 'Cross instance not authorized';
			Vcard::getTable()->transSave($this);
		}
		else Vcard::getTable()->save($this);
	    return 'OK';
    }

    public function loadAndUpdate($data, $update_time = null)
    {
    	$context = Context::getCurrent();
    
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', 'vcard->loadData: '.$rc];
    
    	// Save the data
    	$this->update($update_time, (array_key_exists('instance_id', $data)) ? true : false);
    	if ($rc == 'Authorization') return ['401', 'vcard->update: '.$rc];
    	if ($rc != 'OK') return ['500', 'vcard->update: '.$rc];
    
    	$this->properties = $this->getProperties();
    	return ['200', $this->id];
    }
    
    public function savePhoto($file) {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) {
    		$context = Context::getCurrent();
    		if ($file['size'] > $context->getConfig()['maxUploadSize']) $error = 'Size';
    		else {
    			$name = $file['name'];
    			$type = $file['type'];
    			$src = $this->id;
    			$dest = $this->id.'.jpg';
    			$path = 'data/photos/';
    			$publicPath = 'public/photos/';

    			// Create the file on the file system with $id as a name
    			if (file_exists($path.$src)) unlink($path.$src);
		    	move_uploaded_file ($file['tmp_name'], $path.$src);
    			$info = getimagesize($path.$src);
		    	if (file_exists($path.$dest)) unlink($path.$dest);
		    	if ($info['mime'] == 'image/gif' || $info['mime'] == 'image/png') {
    				// Compress the image
		    		if ($info['mime'] == 'image/gif') $image = imagecreatefromgif($path.$src);
		    		elseif ($info['mime'] == 'image/png') $image = imageCreateFromPng($path.$src);
		    		//compress and save file to jpg
		    		imagejpeg($image, $path.$dest, 75);
    			}
    			else rename($path.$src, $path.$dest);

    			if (file_exists($publicPath.$dest)) unlink($publicPath.$dest);
		    	copy($path.$dest, $publicPath.$dest);
    		}
    	}
    }
    
    public function isUsed($object)
    {
    	// Allow or not deleting an instance
    	if (get_class($object) == 'Model\Instance') {
    		if (Generic::getTable()->cardinality('core_vcard', array('instance_id' => $object->id)) > 0) return true;
    	}
    	// Allow or not deleting an community
    	if (get_class($object) == 'Model\Community') {
    		if (Generic::getTable()->cardinality('core_vcard', array('community_id' => $object->id)) > 0) return true;
    	}
    	return false;
    }
    
    public function isDeletable()
    {
    	if (Generic::getTable()->cardinality('core_event', array('status != ?' => 'deleted', 'place_id' => $this->id)) > 0) return false;
    	
    	$config = Context::getCurrent()->getConfig();
    	foreach($config['ppitContactDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}
    	return true;
    }

    public function delete($update_time)
    {
    	// Check isolation and save
    	$vcard = Vcard::getTable()->get($this->id);
    	if ($update_time && $vcard->update_time != $update_time) return 'Isolation';
    	Vcard::getTable()->delete($this->id);
    	return 'OK';
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    public static function getTable()
    {
    	if (!Vcard::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Vcard::$table = $sm->get(VcardTable::class);
    	}
    	return Vcard::$table;
    }
}