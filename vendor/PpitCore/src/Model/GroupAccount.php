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
  * GroupAccount is the class supporting relationship between groups and accounts
  *
  * At most 1 link of a given type should exist between an account and a group, ie the cardinality of GroupAccount<type> is (0, n)
  * between a given group and accounts and between a given account and groups.
  */
class GroupAccount
{
	public static $model = array(
		'entities' => array(
			'core_group_account' => ['table' => 'core_group_account'],
			'core_group' => 		['table' => 'core_account', 'foreign_key' => 'group_id'],
			'core_place' => 		['table' => 'core_place', 'foreign_key' => 'core_group.place_id'],
			'core_account' => 		['table' => 'core_account', 'foreign_key' => 'account_id'],
			'core_vcard' => 		['table' => 'core_vcard', 'foreign_key' => 'core_account.vcard_id'],
		),
		'properties' => array(
			'id' => 				['entity' => 'core_group_account', 'column' => 'id', 'type' => 'int'],
			'status' => 			['entity' => 'core_group_account', 'column' => 'status', 'type' => 'varchar'],
			'type' => 				['entity' => 'core_group_account', 'column' => 'type', 'type' => 'varchar'],
			'group_id' =>	 		['entity' => 'core_group', 'column' => 'id', 'type' => 'int'],
			'group_name' => 		['entity' => 'core_group', 'column' => 'name', 'type' => 'varchar'],
			'place_id' =>	 		['entity' => 'core_place', 'column' => 'id', 'type' => 'int'],
			'place_caption' => 		['entity' => 'core_place', 'column' => 'caption', 'type' => 'varchar'],
			'account_id' =>	 		['entity' => 'core_account', 'column' => 'id', 'type' => 'int'],
			'name' =>	 			['entity' => 'core_account', 'column' => 'name', 'type' => 'varchar'],
			'n_first' => 			['entity' => 'core_vcard', 'column' => 'n_first', 'type' => 'varchar'],
			'n_last' => 			['entity' => 'core_vcard', 'column' => 'n_last', 'type' => 'varchar'],
			'n_fn' => 				['entity' => 'core_vcard', 'column' => 'n_fn', 'type' => 'varchar'],
			'email' => 				['entity' => 'core_vcard', 'column' => 'email', 'type' => 'varchar'],
			'tel_work' => 			['entity' => 'core_vcard', 'column' => 'tel_work', 'type' => 'varchar'],
			'tel_cell' => 			['entity' => 'core_vcard', 'column' => 'tel_cell', 'type' => 'varchar'],
			'locale' => 			['entity' => 'core_vcard', 'column' => 'locale', 'type' => 'varchar'],
			'contact_1_id' => 		['entity' => 'core_vcard', 'column' => 'locale', 'type' => 'varchar'], // Deprecated
		),
		'authorizations' => array(
			'core_group_account.type', 'core_group_account.status', 
			'core_group.type', 'core_group.status', 'core_group.name',
			'core_place.caption',
			'core_account.type', 'core_account.status', 
		),
	);
	
	public $id;
    public $instance_id;
    public $status;
    public $type;
    public $group_id;
    public $account_id;
    public $audit;
    public $update_time;

    // Joined properties
    public $place_id;
    public $place_caption;
    public $group_name;
    public $name;
    public $n_first;
    public $n_last;
    public $n_fn;
    public $email;
    public $tel_work;
    public $tel_cell;
    public $locale;
    public $contact_1_id;
    
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
        $this->group_id = (isset($data['group_id'])) ? $data['group_id'] : null;
        $this->account_id = (isset($data['account_id'])) ? $data['account_id'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : [];
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
        
        // Joined properties
        $this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
        $this->group_name = (isset($data['group_name'])) ? $data['group_name'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->locale = (isset($data['locale'])) ? $data['locale'] : null;
        $this->contact_1_id = (isset($data['contact_1_id'])) ? $data['contact_1_id'] : null;
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
    	$data['group_id'] = (int) $this->group_id;
    	$data['account_id'] = (int) $this->account_id;
    	$data['audit'] = $this->audit;
        
        // Joined properties
    	$data['place_caption'] = $this->place_caption;
    	$data['group_name'] = $this->group_name;
    	$data['name'] = $this->name;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['n_fn'] = $this->n_fn;
    	$data['email'] =  $this->email;
    	$data['tel_work'] = $this->tel_work;
    	$data['tel_cell'] = $this->tel_cell;
    	$data['locale'] = $this->locale;
    	$data['contact_1_id'] = $this->contact_1_id;

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
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['status'] = $this->status;
    	$data['type'] = $this->type;
    	$data['group_id'] = $this->group_id;
    	$data['account_id'] = $this->account_id;
    	$data['audit'] = json_encode($this->audit);
    	return $data;
    }

    /**
     * Normalize and returns, for a given type of GroupAccount, the description for all the properties, either owned by the GroupAccount,
     * or joined from linked tables: Group (alias for Account<group>), Place, Account and Vcard
     */
    public static function getConfigProperties($type) {
    	$context = Context::getCurrent();
    	$properties = array();
    	foreach(GroupAccount::$model['properties'] as $propertyId => $unused) {
    		$property = $context->getConfig('group_account/'.$type.'/property/'.$propertyId);
    		if (!$property) $property = $context->getConfig('group_account/generic/property/'.$propertyId);
    		if ($property) {
	    		if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
	    		if ($propertyId == 'place_id') {
	    			$property['modalities'] = array();
	    			foreach (Place::getList(array()) as $place) $property['modalities'][$place->id] = ['default' => $place->caption];
	    		}
	    		$properties[$propertyId] = $property;
	    		$properties[$propertyId]['entity'] = GroupAccount::$model['properties'][$propertyId]['entity'];
	    		$properties[$propertyId]['column'] = GroupAccount::$model['properties'][$propertyId]['column'];
    		}
    	}
    	return $properties;
    }
    
    /**
     * Normalize and returns the view options defined for a given type of GroupAccount
     */
    public static function getConfigView($type)
    {
    	$context = Context::getCurrent();
    	$configView = $context->getConfig('group_account/view/'.$type);
    	if (!$configView) $configView = $context->getConfig('group_account/view/generic');
    	return $configView;
    }
    
    /**
     * Normalize and returns the search options defined for a given type of GroupAccount
     */
    public static function getConfigSearch($type, $configProperties)
    {
    	$context = Context::getCurrent();
    	$configSearch = $context->getConfig('group_account/search/'.$type);
    	if (!$configSearch) $configSearch = $context->getConfig('group_account/search/generic');
    	foreach ($configSearch as $propertyId => $options) {
    		$property = $configProperties[$propertyId];
    		$configSearch[$propertyId] = $property;
    	}
    	return $configSearch;
    }
    
    /**
     * Normalize and returns the list options defined for a given type of GroupAccount
     */
    public static function getConfigList($type, $configProperties)
    {
    	$context = Context::getCurrent();
    	$configList = $context->getConfig('group_account/list/'.$type);
    	if (!$configList) $configList = $context->getConfig('group_account/list/generic');
    	foreach ($configList as $propertyId => $options) {
    		$property = $configProperties[$propertyId];
    		$configList[$propertyId] = $property;
    		$configList[$propertyId]['style'] = (array_key_exists('style', $options)) ? $options['style'] : array();
    	}
    	return $configList;
    }
    
    /**
     * Normalize and returns the update options defined for a given type of GroupAccount
     */
    public static function getConfigUpdate($type, $configProperties)
    {
    	$context = Context::getCurrent();
    	$configUpdate = $context->getConfig('grou_account/update/'.$type);
    	if (!$configUpdate) $configUpdate = $context->getConfig('group_account/update/generic');
    	foreach ($configUpdate as $propertyId => $options) {
    		$property = $configProperties[$propertyId];
    		$property['mandatory'] = (array_key_exists('mandatory', $options)) ? $options['mandatory'] : false;
    		$configUpdate[$propertyId] = $property;
    	}
    	return $configUpdate;
    }

    /**
     * Normalize and returns the export options defined for a given type of GroupAccount
     */
    public static function getConfigExport($type, $configProperties)
    {
    	$context = Context::getCurrent();
    	$configExport = $context->getConfig('group_account/export/'.$type);
    	if (!$configExport) $configExport = $context->getConfig('group_account/export/generic');
    	foreach ($configExport as $propertyId => $column) {
    		$property = $configProperties[$propertyId];
    		$configExport[$propertyId] = $property;
    		$configExport[$propertyId]['column'] = $column;
    	}
    	return $configExport;
    }

    /**
     * Consolidates returns the whole description defined for a given type of GroupAccount:
     * - The list of the managed properties along with their definition
     * - The options for the view part
     * - The sublist of proerties for the search, list, update and export subviews
     */
    public static function getDescription($type = 'generic')
    {
    	$context = Context::getCurrent();
    	$description = array();
    	$description['authorizations'] = GroupAccount::$model['authorizations'];
    	$description['type'] = $type;
    	$description['properties'] = GroupAccount::getConfigProperties($type);
    	$description['view'] = GroupAccount::getConfigView($type);
    	$description['search'] = GroupAccount::getConfigSearch($type, $description['properties']);
    	$description['list'] = GroupAccount::getConfigList($type, $description['properties']);
    	$description['update'] = GroupAccount::getConfigUpdate($type, $description['properties']);
    	$description['export'] = GroupAccount::getConfigEXport($type, $description['properties']);
    	return $description;
    }

    /**
     * Returns an array of GroupAccounts indexed by the primary key and joined with the corresponding Group, Account, Place and Vcard.
     * If $mode == 'todo' the list is restricted on the 'todo' view option for the GroupAccount type
     * Otherwise ($mode == 'search'), the list is filtered on the intersection (conjunction) of rows matching the following rules for the property's value:
     * - if the argument is '*': keep the rows for which the value is non-empty
     * - if the key has the form 'min_<property_name>': keep the rows for which the value is greater than or equal to the argument,
     * - if the key has the form 'max_<property_name>': keep the rows for which the value is less than or equal to the argument,
     * - if the property is of type 'select': keep the rows for which the value equals to the argument,
     * - if the property is of type 'multiselect': keep the rows for which the value (a list of comma-separated values) is 'like' (in SQL sense) the argument,
     * - if the property is of type 'select': keep the rows for which the value is 'in' (in SQL sense) the argument (a list of comma-separated values)
     * - if the property is of type 'date', 'time', 'datetime', 'number': keep the rows for which the value equals to the argument
     * - if any other case: keep the rows for which the value is 'like' (in SQL sense) the argument
	 * - in both 'todo' and 'search' modes, the list is also filtered according to values matching the authorizations given to the current user
	 * If $limit is specified, only the page of size $limit indexed by $page is returned
     * The result is ordered according to the $order argument, a list of comma-separated values each prefixed by '+' (ascending) or '-' (descending)
     * @param $description[]
     * @param $params[]
     * @param string $order
     * @param int $limit
     * @param int $page
     * @param $columns[]
     * @param string $mode
     * @return GroupAccount[]
     */
    public static function getList($description, $params, $order = '-update_time', $limit = null, $offset = 0, $columns = null, $mode = 'search')
    {
    	$context = Context::getCurrent();
    	$type = $description['type'];
    	$order = explode(',', $order);
    	foreach ($order as &$criterion) $criterion = substr($criterion, 1).' '.((substr($criterion, 0, 1) == '-') ? 'DESC' : 'ASC');
    	 
    	$select = GroupAccount::getTable()->getSelect()
    		->join(array('core_group' => 'core_account'), 'core_group_account.group_id = core_group.id', array('group_name' => 'name'), 'left')
    		->join('core_place', 'core_group.place_id = core_place.id', array('place_caption' => 'caption'), 'left')
	    	->join(array('core_account' => 'core_account'), 'core_group_account.account_id = core_account.id', array('name'), 'left')
	    	->join('core_vcard', 'core_account.contact_1_id = core_vcard.id', array('n_first', 'n_last', 'n_fn', 'email', 'tel_work', 'tel_cell', 'locale', 'contact_1_id' => 'id'), 'left')
	    	->order($order);

		if ($columns) $select->columns($columns);
    		
    	$where = new Where;
	    $where->notEqualTo('core_group_account.status', 'deleted');
    	if ($type) $where->equalTo('core_group_account.type', $type);

    	// Todo list vs search modes
    	if ($mode == 'todo' && array_key_exists('todo', $description['view'])) {
    		foreach ($description['view']['todo'] as $propertyId => $filter) {
    			if ($filter['operator'] == 'equalTo') $where->equalTo($propertyId, $filter[$value]);
    		}
    	}
    	else {
    		// Set the requested filters
    		foreach ($params as $propertyId => $value) {
    			if (in_array(substr($propertyId, 0, 4), array('min_', 'max_'))) $propertyKey = substr($propertyId, 4);
    			else $propertyKey = $propertyId;

    			$property = $description['properties'][$propertyKey];
    			
    			if ($value == '*') $where->notEqualTo($property['model'].'.'.$property['entity'], $value);
    			elseif ($property['type'] == 'multiselect') $where->like($property['model'].'.'.$property['entity'], $value);
    			elseif ($property['type'] == 'select') $where->in($property['model'].'.'.$property['entity'], $value);
    			elseif (in_array($property['type'], ['date', 'time', 'datetime', 'number'])) $where->equalTo($property['model'].'.'.$property['entity'], $value);
    			elseif (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo($property['model'].'.'.$property['entity'], $value);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo($property['model'].'.'.$property['entity'], $value);
    			else $where->like($property['entity'].'.'.$property['column'], '%'.$value.'%');
    		}
    	}

    	// Set the authorized perimeter filters
    	foreach ($description['authorizations'] as $key) {
    		$authorizationDefinition = $context->getConfig($key);
    		if ($authorizationDefinition) {
    			if ($authorizationDefinition['operator'] == 'equalTo') $where->equalTo($key, $filter[$value]);
    		}
    	}

    	if ($limit) $select->limit($limit);
    	if ($offset) $select->offset($offset);
    	 
    	$select->where($where);
	    $cursor = GroupAccount::getTable()->selectWith($select);
    	$groupAccounts = array();
    	foreach ($cursor as $groupAccount) {
    		$groupAccount->properties = $groupAccount->getProperties();
    		$groupAccounts[$groupAccount->id] = $groupAccount;
    	}
    	return $groupAccounts;
    }

    /**
     * Retrieve from the database the GroupAccount matching the up to 4 column-value pairs given as arguments ('id' as a default).
     * The joind properties from Group, Place, Account and Vcard are retrieved from the database.
     * An array of all the properties, owned bt GroupAccount or joined from the related entities is provided using getProperties().
     * @param string $id defaulting to 'id'
     * @param string $column
     * @param string $id2
     * @param string $column2
     * @param string $id3
     * @param string $column3
     * @param string $id4
     * @param string $column4
     * @return GroupAccount
     */
    public static function get($id, $column = 'id', $id2 = false, $column2 = false, $id3 = false, $column3 = false, $id4 = false, $column4 = false)
    {
		$groupAccount = GroupAccount::getTable()->get($id, $column, $id2, $column2, $id3, $column3, $id4, $column4);
		if ($groupAccount) {
			$group = Account::get($groupAccount->group_id);
			if (!group) $groupAccount->group_name = $group->name;
			$account = Account::get($groupAccount->account_id);
			if ($account) {
				$groupAccount->name = $account->name;
				$place = Place::get($account->place_id);
				if ($place) {
					$groupAccount->place_id = $place->id;
					$groupAccount->place_caption = $place->caption;
				}
				$vcard = Vcard::get($account->vcard_id);
				if ($vcard) {
					$groupAccount->n_first = $vcard->n_first;
					$groupAccount->n_last = $vcard->n_last;
					$groupAccount->n_fn = $vcard->n_fn;
					$groupAccount->email = $vcard->email;
					$groupAccount->tel_work = $vcard->tel_work;
					$groupAccount->tel_cell = $vcard->tel_cell;
					$groupAccount->locale = $vcard->locale;
					$groupAccount->contact_1_id = $vcard->contact_1_id;
				}
			}
			$groupAccount->properties = $groupAccount->getProperties();
		}
		return $groupAccount;
    }

    /**
     * Returns a new instance of GroupAccount<type>.
     * The status is set to 'new'.
     * @return GroupAccount
     */
    public static function instanciate($type = 'generic', $group_id = null, $account_id = null)
    {
    	$context = Context::getCurrent();
    	$groupAccount = new GroupAccount;
    	$groupAccount->status = 'new';
    	$groupAccount->type = $type;
    	$groupAccount->audit = array();
    	return $groupAccount;
    }

    /**
     * Loads the array argument $data into the GroupAccount object.
     * Only the properties present as a key in $data are updated in the target object.
     * Each properties are trimed and cleaned of unsupported tags. The accepted html tags in strings is defined by the configuration parameter 'supportedTagsInDatabase'.
     * Integrity controls are operated according to th GroupAccount<type> description provided by the $description argument:
     * - The length of the data is checked against the database type associated with the property in GroupAccount::$model['properties']
     * - The value in 'select' or 'multiselect' properties should belong to the modalities defined in the description
     * - The value should not be null or empty for mandatory properties
     * - The validity of the value format is checked for properties of type 'date', 'time', 'datetime', 'int' and 'float'
     * Returns 'OK' or a list of errors in the form of an array ok key-pairs associating the property id ans the error caption
     * - 'Integrity' if the protection check failed,
     * - 'OK' otherwise
     * The audit database property is augmented with a row storing the current date and time, the user doing the update, and the list of updated properties along with their previous values (support for future undo facility).
     * @param $description[]
     * @param $data[]
     * @return string
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

				if (GroupAccount::$model['properties'][$propertyId]['type'] == 'varchar') {
					if (strlen($value) > 255) $errors[$propertyId] = "$propertyId should not be longer than 255 characters";
				}
				elseif (GroupAccount::$model['properties'][$propertyId]['type'] == 'text') {
					if (strlen($value) > 65535) $errors[$propertyId] = "$propertyId should not be longer than 65535 characters";
				}
				elseif (GroupAccount::$model['properties'][$propertyId]['type'] == 'date') {
					if ($value && (strlen($value) < 10 || !checkdate(substr($value, 5, 2), substr($value, 8, 2), substr($value, 0, 4)))) $errors[$propertyId] = "$propertyId should be a valid date according to the format yyyy-mm-dd";
				}
				elseif (GroupAccount::$model['properties'][$propertyId]['type'] == 'time') {
					if ($value && !Account::checktime($value)) $errors[$propertyId] = "$propertyId should be a valid time according to the format hh:mm:ss";
				}
				elseif (GroupAccount::$model['properties'][$propertyId]['type'] == 'datetime') {
					if ($value && (!checkdate(substr($value, 5, 2), substr($value, 8, 2), substr($value, 0, 4)) || !Account::checktime(substr($value, 11, 8)))) $errors[$propertyId] = "$propertyId should be a valid date & time according to the format yyyy-mm-dd hh:mm:ss";
				}
				elseif (GroupAccount::$model['properties'][$propertyId]['type'] == 'int') $value = (int) $value;
				elseif (GroupAccount::$model['properties'][$propertyId]['type'] == 'float') $value = (float) $value;
				
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
					elseif ($propertyId == 'group_id') $this->group_id = $value;
					elseif ($propertyId == 'group_name') $this->group_name = $value;
					elseif ($propertyId == 'place_id') $this->place_id = $value;
					elseif ($propertyId == 'place_caption') $this->place_caption = $value;
					elseif ($propertyId == 'account_id') $this->account_id = $value;
					elseif ($propertyId == 'n_fn') $this->n_fn = $value;
					elseif ($propertyId == 'email') $this->email = $value;
					elseif ($propertyId == 'tel_work') $this->tel_work = $value;
					elseif ($propertyId == 'tel_cell') $this->tel_cell = $value;
	
					if ($this->properties[$propertyId] != $value) $auditRow[$propertyId] = $value;
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
		if (Generic::getTable()->cardinality('core_group_account', array('type' => $this->type, 'group_id' => $this->group_id, 'account_id' => $this->account_id)) == 0) {
			$this->id = null;
			GroupAccount::getTable()->save($this);
		}
		
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
				return ['500', 'core_group_account->load: see errors'];
			}

			// Save the data
			$this->add();
			if ($rc != 'OK') return ['500', 'core_group_account->add: '.$rc];
	
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
		$groupAccount = GroupAccount::get($this->id);

		// Isolation check
		if ($groupContact->update_time > $update_time) return 'Isolation';

		GroupContact::getTable()->save($this);

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
			return ['500', 'core_group_account->load: see errors'];
		}
	
		// Save the data
		$this->update($update_time);
		if ($rc == 'Isolation') return ['409', 'core_group_account->update: '.$rc];
		if ($rc != 'OK') return ['500', 'core_group_account->update: '.$rc];
		return ['200'];
	}
	
   /**
     * Checks if this event can de deleted. 
     * An event is not deletable if the result of calling the isUsed() method for each class which has registered itself in the 'ppitCoreDependencies' list returns true.
     * @return boolean
     */
	public function isDeletable() 
	{
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
		$context = Context::getCurrent();
		$groupAccount = GroupAccount::get($this->id);

		// Isolation check
		if ($groupAccount->update_time > $update_time) return 'Isolation';

		GroupAccount::getTable()->delete($this->id);

		return 'OK';
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
    	if (!GroupAccount::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		GroupAccount::$table = $sm->get(GroupAccountTable::class);
    	}
    	return GroupAccount::$table;
    }
}