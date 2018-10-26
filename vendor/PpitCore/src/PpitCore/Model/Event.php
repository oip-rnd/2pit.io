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
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
  * Event is the class supporting generic interactions between applications
  *
  * An event has a type (domain specific), a format (ie JSON, XML...), a direction (reception, emission), a content and an http status. It is timestamped.
  * It can be linked to a place (reflecting customer organization), a domain specific reference, and number of domain specific properties for search purposes.
  */
class Event implements InputFilterAwareInterface
{
	public static $model = array(
		'entities' => array(
			'core_event' => 	['table' => 'core_event'],
			'core_place' => 	['table' => 'core_place', 'foreign_key' => 'core_event.place_id'],
			'core_account' => 	['table' => 'core_account', 'foreign_key' => 'core_event.account_id'],
			'core_vcard' => 	['table' => 'core_vcard', 'foreign_key' => 'core_account.contact_1_id'],
		),
		'properties' => array(
			'status' => 			['entity' => 'core_event', 'column' => 'status'],
			'type' => 				['entity' => 'core_event', 'column' => 'type'],
			'place_id' => 			['entity' => 'core_event', 'column' => 'place_id'],
			'place_caption' => 		['entity' => 'core_place', 'column' => 'caption'],
			'account_id' =>			['entity' => 'core_event', 'column' => 'account_id'],
			'account_status' =>		['entity' => 'core_account', 'column' => 'status'],
			'n_fn' => 				['entity' => 'core_vcard', 'column' => 'n_fn'],
			'n_first' => 			['entity' => 'core_vcard', 'column' => 'n_first'],
			'n_last' => 			['entity' => 'core_vcard', 'column' => 'n_last'],
			'email' => 				['entity' => 'core_vcard', 'column' => 'email'],
			'tel_work' => 			['entity' => 'core_vcard', 'column' => 'tel_work'],
			'tel_cell' => 			['entity' => 'core_vcard', 'column' => 'tel_cell'],
			'category' => 			['entity' => 'core_event', 'column' => 'category'],
			'subcategory' => 		['entity' => 'core_event', 'column' => 'subcategory'],
			'identifier' => 		['entity' => 'core_event', 'column' => 'identifier'],
			'caption' => 			['entity' => 'core_event', 'column' => 'caption'],
			'description' => 		['entity' => 'core_event', 'column' => 'description'],
			'begin_date' => 		['entity' => 'core_event', 'column' => 'begin_date'],
			'end_date' => 			['entity' => 'core_event', 'column' => 'end_date'],
			'day_of_week' => 		['entity' => 'core_event', 'column' => 'day_of_week'],
			'day_of_month' => 		['entity' => 'core_event', 'column' => 'day_of_month'],
			'exception_1' => 		['entity' => 'core_event', 'column' => 'exception_1'],
			'exception_2' => 		['entity' => 'core_event', 'column' => 'exception_2'],
			'exception_3' => 		['entity' => 'core_event', 'column' => 'exception_3'],
			'exception_4' => 		['entity' => 'core_event', 'column' => 'exception_4'],
			'begin_time' => 		['entity' => 'core_event', 'column' => 'begin_time'],
			'end_time' => 			['entity' => 'core_event', 'column' => 'end_time'],
			'time_zone' => 			['entity' => 'core_event', 'column' => 'time_zone'],
			'location' => 			['entity' => 'core_event', 'column' => 'location'],
			'latitude' => 			['entity' => 'core_event', 'column' => 'latitude'],
			'longitude' => 			['entity' => 'core_event', 'column' => 'longitude'], 
			'matched_accounts' => 	['entity' => 'core_event', 'column' => 'matched_accounts'], 
			'matching_log' => 		['entity' => 'core_event', 'column' => 'matching_log'], 
			'rewards' => 			['entity' => 'core_event', 'column' => 'rewards'], 
			'feedbacks' => 			['entity' => 'core_event', 'column' => 'feedbacks'], 
			'value' => 				['entity' => 'core_event', 'column' => 'value'],
			'comments' => 			['entity' => 'core_event', 'column' => 'comments'],
			'property_1' => 		['entity' => 'core_event', 'column' => 'property_1'],
			'property_2' => 		['entity' => 'core_event', 'column' => 'property_2'],
			'property_3' => 		['entity' => 'core_event', 'column' => 'property_3'],
			'property_4' => 		['entity' => 'core_event', 'column' => 'property_4'],
			'property_5' => 		['entity' => 'core_event', 'column' => 'property_5'],
			'property_6' => 		['entity' => 'core_event', 'column' => 'property_6'],
			'property_7' => 		['entity' => 'core_event', 'column' => 'property_7'],
			'property_8' => 		['entity' => 'core_event', 'column' => 'property_8'],
			'property_9' => 		['entity' => 'core_event', 'column' => 'property_9'],
			'property_10' => 		['entity' => 'core_event', 'column' => 'property_10'],
			'property_11' => 		['entity' => 'core_event', 'column' => 'property_11'],
			'property_12' => 		['entity' => 'core_event', 'column' => 'property_12'],
			'property_13' => 		['entity' => 'core_event', 'column' => 'property_13'],
			'property_14' => 		['entity' => 'core_event', 'column' => 'property_14'],
			'property_15' => 		['entity' => 'core_event', 'column' => 'property_15'],
			'property_16' => 		['entity' => 'core_event', 'column' => 'property_16'],
			'property_17' => 		['entity' => 'core_event', 'column' => 'property_17'],
			'property_18' => 		['entity' => 'core_event', 'column' => 'property_18'],
			'property_19' => 		['entity' => 'core_event', 'column' => 'property_19'],
			'property_20' => 		['entity' => 'core_event', 'column' => 'property_20'],
			'property_21' => 		['entity' => 'core_event', 'column' => 'property_21'],
			'property_22' => 		['entity' => 'core_event', 'column' => 'property_22'],
			'property_23' => 		['entity' => 'core_event', 'column' => 'property_23'],
			'property_24' => 		['entity' => 'core_event', 'column' => 'property_24'],
			'property_25' => 		['entity' => 'core_event', 'column' => 'property_25'],
			'property_26' => 		['entity' => 'core_event', 'column' => 'property_26'],
			'property_27' => 		['entity' => 'core_event', 'column' => 'property_27'],
			'property_28' => 		['entity' => 'core_event', 'column' => 'property_28'],
			'property_29' => 		['entity' => 'core_event', 'column' => 'property_29'],
			'property_30' => 		['entity' => 'core_event', 'column' => 'property_30'],
			'update_time' => 		['entity' => 'core_event', 'column' => 'update_time'],
		),
	);
	
	public $id;
	public $instance_id;
	public $status;
	public $type;
	public $place_id;
	public $account_id;
	public $vcard_id; // Deprecated
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
	public $exception_dates;
	public $begin_time;
	public $end_time;
	public $time_zone;
	public $location;
	public $latitude;
	public $longitude;
	public $matched_accounts;
	public $matching_log;
	public $rewards;
	public $feedbacks;
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
    public $authentication_token;
	public $update_time;

	// Joined properties
	public $place_identifier;
	public $place_caption;
	public $n_fn;
	public $n_first;
	public $n_last;
	public $email;
	public $tel_work;
	public $tel_cell;
	public $photo_link_id;
	public $locale;

	public $account_status;
	public $account_property_1;
	public $account_property_2;
	public $account_property_3;
	public $account_property_4;
	public $account_property_5;
	public $account_property_6;
	public $account_property_7;
	public $account_property_8;
	public $account_property_9;
	public $account_property_10;
	public $account_property_11;
	public $account_property_12;
	public $account_property_13;
	public $account_property_14;
	public $account_property_15;
	public $account_property_16;
	
	public $j1_property_1;
	public $j1_property_2;
	public $j1_property_3;
	public $j1_property_4;
	public $j1_property_5;
	public $j1_property_6;
	public $j1_property_7;
	public $j1_property_8;
	public $j1_property_9;
	public $j1_property_10;
	public $j1_property_11;
	public $j1_property_12;
	public $j1_property_13;
	public $j1_property_14;
	public $j1_property_15;
	public $j1_property_16;
	public $j1_property_17;
	public $j1_property_18;
	public $j1_property_19;
	public $j1_property_20;
	public $j1_property_21;
	public $j1_property_22;
	public $j1_property_23;
	public $j1_property_24;
	public $j1_property_25;
	public $j1_property_26;
	public $j1_property_27;
	public $j1_property_28;
	public $j1_property_29;
	public $j1_property_30;

	public $j2_property_1;
	public $j2_property_2;
	public $j2_property_3;
	public $j2_property_4;
	public $j2_property_5;
	public $j2_property_6;
	public $j2_property_7;
	public $j2_property_8;
	public $j2_property_9;
	public $j2_property_10;
	public $j2_property_11;
	public $j2_property_12;
	public $j2_property_13;
	public $j2_property_14;
	public $j2_property_15;
	public $j2_property_16;
	public $j2_property_17;
	public $j2_property_18;
	public $j2_property_19;
	public $j2_property_20;
	public $j2_property_21;
	public $j2_property_22;
	public $j2_property_23;
	public $j2_property_24;
	public $j2_property_25;
	public $j2_property_26;
	public $j2_property_27;
	public $j2_property_28;
	public $j2_property_29;
	public $j2_property_30;

	public static function getConfigProperties($type) {
		$context = Context::getCurrent();
		$properties = array();
		$description = $context->getConfig('event/'.$type);
		if (!$description) $description = $context->getConfig('event/generic');
		foreach($description['properties'] as $propertyId) {
			$property = $context->getConfig('event/'.$type.'/property/'.$propertyId);
			if (!$property) $property = $context->getConfig('event/generic/property/'.$propertyId);
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			if ($propertyId == 'place_id') {
				$property['modalities'] = array();
				foreach (Place::getList(array()) as $place) $property['modalities'][$place->id] = ['default' => $place->caption];
			}
			elseif ($propertyId == 'account_id') {
				$property['modalities'] = array();
				foreach (Account::getList($property['account_type'], [], '+name', null) as $account) $property['modalities'][$account->id] = ['default' => $account->n_fn.' - '.$account->email];
			}
			elseif ($propertyId == 'matched_accounts') {
				$property['modalities'] = array();
				foreach (Account::getList($property['account_type'], [], '+name', null) as $account) $property['modalities'][$account->id] = ['default' => $account->n_fn.' - '.$account->email];
			}
			elseif (in_array($property['type'], ['select', 'multiselect']) && array_key_exists('definition', $property['modalities']) && $property['modalities']['definition'] != 'inline') {
				$definition = $context->getConfig($property['modalities']['definition']);
				$property['modalities'] = array();
				foreach ($definition as $modalityId => $modality) {
					$property['modalities'][$modalityId] = $modality['labels'];
				}
			}
			$properties[$propertyId] = $property;
		}
		return $properties;
	}
	
	public static function getConfigSearch($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configSearch = $context->getConfig('event/search/'.$type);
		if (!$configSearch) $configSearch = $context->getConfig('event/search/generic');
		foreach ($configSearch['properties'] as $propertyId => $options) {
			$property = $configProperties[$propertyId];
			$configSearch['properties'][$propertyId] = $property;
			$configSearch['properties'][$propertyId]['options'] = $options;
		}
		return $configSearch;
	}
	
	public static function getConfigList($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configList = $context->getConfig('event/list/'.$type);
		if (!$configList) $configList = $context->getConfig('event/list/generic');
		foreach ($configList as $propertyId => $options) {
			$property = $configProperties[$propertyId];
			$configList[$propertyId] = $property;
			$configList[$propertyId]['style'] = (array_key_exists('style', $options)) ? $options['style'] : array();
		}
		return $configList;
	}
	
	public static function getConfigUpdate($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configUpdate = $context->getConfig('event/update/'.$type);
		if (!$configUpdate) $configUpdate = $context->getConfig('event/update/generic');
		foreach ($configUpdate as $propertyId => $options) {
			$property = $configProperties[$propertyId];
			$property['mandatory'] = (array_key_exists('mandatory', $options)) ? $options['mandatory'] : false;
			$configUpdate[$propertyId] = $property;
		}
		return $configUpdate;
	}
	
	public static function getConfigExport($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configExport = $context->getConfig('event/export/'.$type);
		if (!$configExport) $configExport = $context->getConfig('event/export/generic');
		foreach ($configExport as $propertyId => $column) {
			$property = $configProperties[$propertyId];
			$configExport[$propertyId] = $property;
			$configExport[$propertyId]['column'] = $column;
		}
		return $configExport;
	}
	
	public static function getDescription($type)
	{
		$context = Context::getCurrent();
		$config = $context->getConfig('event/'.$type);
		if (!$config) $config = $context->getConfig('event/generic');
	
		$description = array();
		$description['type'] = $type;
		$description['options'] = (array_key_exists('options', $config)) ? $config['options'] : array();
		$description['options']['account_type'] = (array_key_exists('account_type', $description['options'])) ? $description['options']['account_type'] : 'generic';
		$description['properties'] = Event::getConfigProperties($type);
		$description['search'] = Event::getConfigSearch($type, $description['properties']);
		$description['list'] = Event::getConfigList($type, $description['properties']);
		$description['update'] = Event::getConfigUpdate($type, $description['properties']);
		$description['export'] = Event::getConfigEXport($type, $description['properties']);
		return $description;
	}
	
	// Transient properties
	/** @var array */ public $properties;
	
    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

    // Static fields
    /** @var \PpitCore\Model\Event */ private static $table;
    
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
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->account_id = (isset($data['account_id'])) ? $data['account_id'] : null;
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
        $this->exception_dates = (isset($data['exception_dates'])) ? json_decode($data['exception_dates'], true) : null;
        $this->begin_time = (isset($data['begin_time'])) ? $data['begin_time'] : null;
        $this->end_time = (isset($data['end_time'])) ? $data['end_time'] : null;
        $this->time_zone = (isset($data['time_zone'])) ? $data['time_zone'] : null;
        $this->location = (isset($data['location'])) ? $data['location'] : null;
        $this->latitude = (isset($data['latitude'])) ? $data['latitude'] : null;
        $this->longitude = (isset($data['longitude'])) ? $data['longitude'] : null;
        $this->matched_accounts = (isset($data['matched_accounts'])) ? $data['matched_accounts'] : null;
        $this->matching_log = (isset($data['matching_log'])) ? json_decode($data['matching_log'], true) : null;
        $this->rewards = (isset($data['rewards'])) ? json_decode($data['rewards'], true) : null;
        $this->feedbacks = (isset($data['feedbacks'])) ? json_decode($data['feedbacks'], true) : null;
        $this->value = (isset($data['value'])) ? $data['value'] : null;
        $this->comments = (isset($data['comments'])) ? json_decode($data['comments'], true) : null;
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
        $this->authentication_token = (isset($data['authentication_token'])) ? $data['authentication_token'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
        
        // Joined properties
        $this->place_identifier = (isset($data['place_identifier'])) ? $data['place_identifier'] : null;
        $this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->photo_link_id = (isset($data['photo_link_id'])) ? $data['photo_link_id'] : null;
        $this->locale = (isset($data['locale'])) ? $data['locale'] : null;
        $this->account_status = (isset($data['account_status'])) ? $data['account_status'] : null;
        $this->account_property_1 = (isset($data['account_property_1'])) ? $data['account_property_1'] : null;
        $this->account_property_2 = (isset($data['account_property_2'])) ? $data['account_property_2'] : null;
        $this->account_property_3 = (isset($data['account_property_3'])) ? $data['account_property_3'] : null;
        $this->account_property_4 = (isset($data['account_property_4'])) ? $data['account_property_4'] : null;
        $this->account_property_5 = (isset($data['account_property_5'])) ? $data['account_property_5'] : null;
        $this->account_property_6 = (isset($data['account_property_6'])) ? $data['account_property_6'] : null;
        $this->account_property_7 = (isset($data['account_property_7'])) ? $data['account_property_7'] : null;
        $this->account_property_8 = (isset($data['account_property_8'])) ? $data['account_property_8'] : null;
        $this->account_property_9 = (isset($data['account_property_9'])) ? $data['account_property_9'] : null;
        $this->account_property_10 = (isset($data['account_property_10'])) ? $data['account_property_10'] : null;
        $this->account_property_11 = (isset($data['account_property_11'])) ? $data['account_property_11'] : null;
        $this->account_property_12 = (isset($data['account_property_12'])) ? $data['account_property_12'] : null;
        $this->account_property_13 = (isset($data['account_property_13'])) ? $data['account_property_13'] : null;
        $this->account_property_14 = (isset($data['account_property_14'])) ? $data['account_property_14'] : null;
        $this->account_property_15 = (isset($data['account_property_15'])) ? $data['account_property_15'] : null;
        $this->account_property_16 = (isset($data['account_property_16'])) ? $data['account_property_16'] : null;
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
    	$data['account_id'] = (int) $this->account_id;
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
    	$data['exception_1'] = (array_key_exists(0, $this->exception_dates)) ? $this->exception_dates[0] : null;
    	$data['exception_2'] = (array_key_exists(1, $this->exception_dates)) ? $this->exception_dates[1] : null;
    	$data['exception_3'] = (array_key_exists(2, $this->exception_dates)) ? $this->exception_dates[2] : null;
    	$data['exception_4'] = (array_key_exists(3, $this->exception_dates)) ? $this->exception_dates[3] : null;
    	$data['exception_dates'] = $this->exception_dates;
    	$data['begin_time'] = $this->begin_time;
    	$data['end_time'] = $this->end_time;
    	$data['time_zone'] = (float) $this->time_zone;
    	$data['location'] = $this->location;
    	$data['latitude'] = (float) $this->latitude;
    	$data['longitude'] = (float) $this->longitude;
    	$data['matched_accounts'] = $this->matched_accounts;
    	$data['matching_log'] = $this->matching_log;
    	$data['rewards'] = $this->rewards;
    	$data['feedbacks'] = $this->feedbacks;
    	$data['value'] = (float) $this->value;
    	$data['comments'] = (float) $this->comments;
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
    	$data['authentication_token'] = $this->authentication_token;
    	$data['audit'] = $this->audit;
    	$data['update_time'] = $this->update_time;
    	 
    	// Joined properties
    	$data['place_identifier'] = $this->place_identifier;
    	$data['place_caption'] = $this->place_caption;
    	$data['n_fn'] = $this->n_fn;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['email'] = $this->email;
    	$data['tel_work'] = $this->tel_work;
    	$data['tel_cell'] = $this->tel_cell;
    	$data['photo_link_id'] = $this->photo_link_id;
    	$data['locale'] = $this->locale;
    	$data['account_status'] = $this->account_status;
    	$data['account_property_1'] = $this->account_property_1;
    	$data['account_property_2'] = $this->account_property_2;
    	$data['account_property_3'] = $this->account_property_3;
    	$data['account_property_4'] = $this->account_property_4;
    	$data['account_property_5'] = $this->account_property_5;
    	$data['account_property_6'] = $this->account_property_6;
    	$data['account_property_7'] = $this->account_property_7;
    	$data['account_property_8'] = $this->account_property_8;
    	$data['account_property_9'] = $this->account_property_9;
    	$data['account_property_10'] = $this->account_property_10;
    	$data['account_property_11'] = $this->account_property_11;
    	$data['account_property_12'] = $this->account_property_12;
    	$data['account_property_13'] = $this->account_property_13;
    	$data['account_property_14'] = $this->account_property_14;
    	$data['account_property_15'] = $this->account_property_15;
    	$data['account_property_16'] = $this->account_property_16;
    	 
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
    	$data['comments'] = json_encode($this->comments);
    	$data['exception_dates'] = json_encode($this->exception_dates);
    	$data['matching_log'] = json_encode($this->matching_log, JSON_PRETTY_PRINT);
    	$data['rewards'] = json_encode($this->rewards, JSON_PRETTY_PRINT);
    	$data['feedbacks'] = json_encode($this->feedbacks, JSON_PRETTY_PRINT);
    	$data['audit'] = json_encode($this->audit);
    	$data['end_date'] = ($this->end_date) ? $this->end_date : '9999-12-31';
    	unset($data['place_identifier']);
    	unset($data['place_caption']);
    	unset($data['n_fn']);
    	unset($data['n_first']);
    	unset($data['n_last']);
    	unset($data['email']);
    	unset($data['tel_work']);
    	unset($data['tel_cell']);
    	unset($data['photo_link_id']);
    	unset($data['locale']);
    	unset($data['account_status']);
    	unset($data['account_property_1']);
    	unset($data['account_property_2']);
    	unset($data['account_property_3']);
    	unset($data['account_property_4']);
    	unset($data['account_property_5']);
    	unset($data['account_property_6']);
    	unset($data['account_property_7']);
    	unset($data['account_property_8']);
    	unset($data['account_property_9']);
    	unset($data['account_property_10']);
    	unset($data['account_property_11']);
    	unset($data['account_property_12']);
    	unset($data['account_property_13']);
    	unset($data['account_property_14']);
    	unset($data['account_property_15']);
    	unset($data['account_property_16']);
    	unset($data['exception_1']);
    	unset($data['exception_2']);
    	unset($data['exception_3']);
    	unset($data['exception_4']);
    	unset($data['update_time']);
    	return $data;
    }
    
    /**
     * Returns an array of Event instances indexed by the event primary key in the database :
     * If $mode == 'todo' the list is restricted on today's interactions only.
     * Otherwise, the list is filtered on interactions matching the key-value pairs provided in the $params argument:
     * - if the key has the form 'min_<property_name>', only the rows having a value for this property greater than the argument are kept
     * - if the key has the form 'max_<property_name>', only the rows having a value for this property less than the argument are kept
     * - else only the row which are 'like' (in SQL sense) the argument are kept in the list.
     * The result is primarily ordered according to the value of $major with the direction (ASC or DESC) specified by $dir, and secondarily in descending chronological order ('update_time DESC')
     * For each retrieved event:
     * - A left SQL join is made with the place (core_place table) as to provide the place name as a joined property.
     * - For array based algorythms needs, a $property array is provided using getProperties() on Event properties.
     * @param $params[]
     * @param string $major
     * @param string $dir
     * @param string $mode
     * @return Event[]
     */
    public static function getList($type, $params, $order = '-update_time', $limit = 200, $columns = null, $mode = 'search', $dimensions = array())
    {
    	$context = Context::getCurrent();
    	$order = explode(',', $order);
    	foreach ($order as &$criterion) $criterion = substr($criterion, 1).' '.((substr($criterion, 0, 1) == '-') ? 'DESC' : 'ASC');

    	$select = Event::getTable()->getSelect()
    		->join('core_account', 'core_event.account_id = core_account.id', array('account_status' => 'status', 'account_property_1' => 'property_1', 'account_property_2' => 'property_2', 'account_property_3' => 'property_3', 'account_property_4' => 'property_4', 'account_property_5' => 'property_5', 'account_property_6' => 'property_6', 'account_property_7' => 'property_7', 'account_property_8' => 'property_8', 'account_property_9' => 'property_9', 'account_property_10' => 'property_10', 'account_property_11' => 'property_11', 'account_property_12' => 'property_12', 'account_property_13' => 'property_13', 'account_property_14' => 'property_14', 'account_property_15' => 'property_15', 'account_property_16' => 'property_16'), 'left')
    		->join('core_vcard', 'core_account.contact_1_id = core_vcard.id', array('n_fn', 'n_first', 'n_last', 'email', 'tel_work', 'tel_cell', 'photo_link_id', 'locale'), 'left')
    		->join('core_place', 'core_event.place_id = core_place.id', array('place_identifier' => 'identifier', 'place_caption' => 'caption'), 'left')
			->order($order);

		if ($columns) $select->columns($columns);
    		
    	$where = new Where;
	    $where->notEqualTo('core_event.status', 'deleted');
    	if ($type) $where->equalTo('core_event.type', $type);

    	// Todo list vs search modes
    	if ($mode == 'todo') {
//	    	$where->equalTo('core_event.status', 'new');
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $value) {
    			if (in_array(substr($propertyId, 0, 4), array('min_', 'max_'))) $propertyKey = substr($propertyId, 4);
    			else $propertyKey = $propertyId;
    			$property = $context->getConfig('event/'.$type.'/property/'.$propertyKey);
    			if (!$property) $property = $context->getConfig('event/generic/property/'.$propertyKey);
    			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
    			
				if ($propertyId == 'status') $where->in('core_event.status', explode(',', $value));
    			elseif ($propertyId == 'place_identifier') {
    				if ($value == '*') $where->notEqualTo('core_place.identifier', '');
    				else $where->like('core_place.identifier', '%'.$value.'%');
    			}
    		    elseif ($propertyId == 'vcard_id') {
    				$where->equalTo('core_vcard.id', $value);
    			}
    		    elseif ($propertyId == 'account_status') {
    				$where->like('core_account.status', '%'.$value.'%');
    			}
    			elseif ($propertyId == 'n_fn') {
    				if ($value == '*') $where->notEqualTo('core_vcard.n_fn', '');
    				else $where->like('core_vcard.n_fn', '%'.$value.'%');
    			}
    		    elseif ($propertyId == 'email') {
    				if ($value == '*') $where->notEqualTo('core_vcard.email', '');
    				else $where->like('core_vcard.email', '%'.$value.'%');
    			}
    			elseif (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('core_event.'.substr($propertyId, 4), $value);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('core_event.'.substr($propertyId, 4), $value);
    			elseif (strpos($value, ',')) $where->in('core_event.'.$propertyId, array_map('trim', explode(', ', $value)));
    			elseif ($value == '*') $where->notEqualTo('core_event.'.$propertyId, '');
				elseif ($property['type'] == 'multiselect') $where->like('core_event.'.$propertyId, '%'.$value.'%');
    			elseif ($property['type'] == 'select') $where->equalTo('core_event.'.$propertyId, $value);
    			else $where->like('core_event.'.$propertyId, '%'.$value.'%');
    		}
    	}
    	$select->where($where);

    	$cursor = Event::getTable()->selectWith($select);
    	$events = array();
    	$i = 0;
    	foreach ($cursor as $event) {
    		$event->properties = $event->getProperties();
    		
			// Filter on authorized perimeter
			$keep = true;
    		if (array_key_exists('p-pit-admin', $context->getPerimeters())) {
					$keep = false;
					foreach ($context->getPerimeters()['p-pit-admin']['place_id'] as $value) {
						if ($event->place_id == $value) $keep = true;
					}
			}
			if ($keep) {
				$i++;
				if ($limit && $i > $limit) break;
				$events['E'.$event->id] = $event;
			}
    	}
    	return $events;
    }

	public static function getListV2($description, $params, $order = '-update_time', $limit = 200, $columns = null)
	{
		$context = Context::getCurrent();
		$order = explode(',', $order);
		foreach ($order as &$criterion) $criterion = substr($criterion, 1).' '.((substr($criterion, 0, 1) == '-') ? 'DESC' : 'ASC');

		$select = Event::getTable()->getSelect()
			->join('core_account', 'core_event.account_id = core_account.id', array('account_status' => 'status', 'account_property_1' => 'property_1', 'account_property_2' => 'property_2', 'account_property_3' => 'property_3', 'account_property_4' => 'property_4', 'account_property_5' => 'property_5', 'account_property_6' => 'property_6', 'account_property_7' => 'property_7', 'account_property_8' => 'property_8', 'account_property_9' => 'property_9', 'account_property_10' => 'property_10', 'account_property_11' => 'property_11', 'account_property_12' => 'property_12', 'account_property_13' => 'property_13', 'account_property_14' => 'property_14', 'account_property_15' => 'property_15', 'account_property_16' => 'property_16'), 'left')
			->join('core_vcard', 'core_account.contact_1_id = core_vcard.id', array('n_fn', 'n_first', 'n_last', 'email', 'tel_work', 'tel_cell', 'photo_link_id', 'locale'), 'left')
			->join('core_place', 'core_event.place_id = core_place.id', array('place_identifier' => 'identifier', 'place_caption' => 'caption'), 'left')
			->order($order);

		if ($columns) $select->columns($columns);

		$where = new Where;
		$where->equalTo('core_event.type', $description['type']);

		// Set the filters
		$where->notEqualTo('core_event.status', 'deleted');
		foreach ($params as $propertyId => $predicate) {
			$property = $description['properties'][$propertyId];
			$entity = Event::$model['properties'][$propertyId]['entity'];
			$column = Event::$model['properties'][$propertyId]['column'];

			if ($propertyId == 'id') $where->equalTo($entity.'.'.$column, $predicate);
			elseif (substr($predicate, 0, 1) == '!') $where->notEqualTo($entity.'.'.$column, substr($predicate, 1));
			elseif (strpos($predicate, ',') > 0) $where->in($entity.'.'.$column, array_map('trim', explode(',', $predicate)));
			elseif (strpos($predicate, '.') > 0) {
				$range = array_map('trim', explode('.', $predicate));
				$where->between($entity.'.'.$column, $range[0], $range[1]);
			}
			elseif (substr($predicate, 0, 1) == '=') $where->equalTo($entity.'.'.$column, substr($predicate, 1));
			elseif (substr($predicate, 0, 1) == '>') $where->greaterThan($entity.'.'.$column, substr($predicate, 1));
    		elseif (substr($predicate, 0, 1) == '<') $where->lessThan($entity.'.'.$column, substr($predicate, 1));
    		elseif (substr($predicate, 0, 2) == '>=') $where->greaterThanOrEqualTo($entity.'.'.$column, substr($predicate, 1));
    		elseif (substr($predicate, 0, 2) == '<=')  $where->lessThanOrEqualTo($entity.'.'.$column, substr($predicate, 1));
    		elseif ($predicate == '*') {
				$where->isNotNull($entity.'.'.$column);
				$where->notEqualTo($entity.'.'.$column, '');
    		}
    		else $where->like($entity.'.'.$column, $predicate);
		}

		$select->where($where);
		$cursor = Event::getTable()->selectWith($select);
		$events = array();
		$i = 0;
		foreach ($cursor as $event) {
			$event->properties = $event->getProperties();

			// Filter on authorized perimeter
			$keep = true;
			if (array_key_exists('p-pit-admin', $context->getPerimeters())) {
				$keep = false;
				foreach ($context->getPerimeters()['p-pit-admin']['place_id'] as $value) {
					if ($event->place_id == $value) $keep = true;
				}
			}
			if ($keep) {
				$i++;
				if ($limit && $i > $limit) break;
				$events['E'.$event->id] = $event;
			}
		}
		return $events;
	}
    
    /**
     * Retrieve from the database the event of a given type and for a given place that occurs in the future.
     * @param string $type
     * @param int $place_id
     * @return Event[]
     */
    public static function retrieveComing($type, $place_id)
    {
		$select = Event::getTable()->getSelect()->order(array('begin_date ASC', 'begin_time ASC'));
    	$where = new Where;
    	$where->notEqualTo('status', 'deleted');
    	$where->equalTo('type', $type);
    	$where->greaterThanOrEqualTo('end_date', date('Y-m-d'));
    	$where->equalTo('place_id', $place_id);
    	$select->where($where);
    	$cursor = Event::getTable()->selectWith($select);
    	$events = array();
    	foreach ($cursor as $event) $events[] = $event;
    	return $events;
    }
    
    /**
     * Retrieve from the database the event having the giving value as the given specified column ('id' as a default).
     * The place caption is retrieved from the place in database ('core_place' table) using the foreign key 'place_id'.
     * For array based algorythms needs, a $property array is provided using getProperties() on Event properties.
     * @param string $id
     * @param string $column
     * @return Event
     */
    public static function get($id, $column = 'id', $id2 = false, $column2 = false, $id3 = false, $column3 = false, $id4 = false, $column4 = false)
    {
    	$context = Context::getCurrent();
    	$event = Event::getTable()->get($id, $column, $id2, $column2, $id3, $column3, $id4, $column4);
		
    	if ($event) {
	        $place = Place::get($event->place_id);
	    	if ($place) {
	    		$event->place_caption = $place->caption;
	    		$event->place_identifier = $place->identifier;
	    	}

	    	$account = Account::get($event->account_id);
	    	if ($account) {
	    		$vcard = Vcard::get($account->contact_1_id);
	    		if ($vcard) {
	    			$event->n_fn = $vcard->n_fn;
	    			$event->n_first = $vcard->n_first;
	    			$event->n_last = $vcard->n_last;
	    			$event->email = $vcard->email;
	    			$event->tel_work = $vcard->tel_work;
	    			$event->tel_cell = $vcard->tel_cell;
	    			$event->photo_link_id = $vcard->photo_link_id;
	    			$event->locale = $vcard->locale;
	    		}
	    		$event->account_property_1 = $account->property_1;
	    		$event->account_property_2 = $account->property_2;
	    		$event->account_property_3 = $account->property_3;
	    		$event->account_property_4 = $account->property_4;
	    		$event->account_property_5 = $account->property_5;
	    		$event->account_property_6 = $account->property_6;
	    		$event->account_property_7 = $account->property_7;
	    		$event->account_property_8 = $account->property_8;
	    		$event->account_property_9 = $account->property_9;
	    		$event->account_property_10 = $account->property_10;
	    		$event->account_property_11 = $account->property_11;
	    		$event->account_property_12 = $account->property_12;
	    		$event->account_property_13 = $account->property_13;
	    		$event->account_property_14 = $account->property_14;
	    		$event->account_property_15 = $account->property_15;
	    		$event->account_property_16 = $account->property_16;
	    	}
			else {	    	
		    	$vcard = Vcard::get($event->vcard_id);
		    	if ($vcard) {
		    		$event->n_fn = $vcard->n_fn;
		    		$event->n_first = $vcard->n_first;
		    		$event->n_last = $vcard->n_last;
	    			$event->email = $vcard->email;
	    			$event->tel_work = $vcard->tel_work;
	    			$event->tel_cell = $vcard->tel_cell;
	    			$event->photo_link_id = $vcard->photo_link_id;
		    		$event->locale = $vcard->locale;
		    	}
			}
	
	    	if ($event) $event->properties = $event->getProperties();
    	}
    	return $event;
    }

    public function join()
    {
    	$context = Context::getCurrent();
    	
    	// Join the dimensions
    	$dimensions = $context->getConfig('event'.(($this->type) ? '/'.$this->type : ''))['dimensions'];
    	foreach ($dimensions as $dimensionId => $dimension) {
    		$rows = Event::getList($dimension['type'], array($dimension['dimension_key'] => $this->properties[$dimension['event_key']]), '+id', null, null, 'search', array());
    		if ($rows) $joined = current($rows);
    		else $joined = null;
    		foreach ($dimension['properties'] as $property => $dimensionProperty) {
    			if ($joined) {
    				$this->properties[$property] = $joined->properties[$dimensionProperty];
    			}
    			else $this->properties[$property] = '';
    		}
    	}
    }
    
    /**
     * Returns a new instance of Event.
     * The status is set to 'new'.
     * @return Event
     */
    public static function instanciate($type = null)
    {
    	$context = Context::getCurrent();
    	$event = new Event;
    	$event->status = 'new';
    	$event->type = $type;
    	$event->exception_dates = [];
    	$event->matching_log = [];
    	$event->rewards = [];
    	$event->feedbacks = [];
    	$event->comments = [];
    	return $event;
	}

    /**
     * Loads the data into the Event object depending of an array, typically constructed by the controller with value extracted from an HTTP request.
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
			if (array_key_exists('place_id', $data)) {
			$place_id = (int) $data['place_id'];
			if ($this->place_id != $place_id) $auditRow['place_id'] = $this->place_id = $place_id;
		}
		if (array_key_exists('account_id', $data)) {
			$account_id = (int) $data['account_id'];
			if ($this->account_id != $account_id) $auditRow['account_id'] = $this->account_id = $account_id;
		}
		if (array_key_exists('vcard_id', $data)) { // Depreciated
			$vcard_id = (int) $data['vcard_id'];
			if ($this->vcard_id != $vcard_id) $auditRow['vcard_id'] = $this->vcard_id = $vcard_id;
		}
		if (array_key_exists('category', $data)) {
	    	$category = trim(strip_tags($data['category']));
			if (strlen($category) > 255) return 'Integrity';
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
	    	$description = $data['description'];
			if ($this->description != $description) $auditRow['description'] = $this->description = $description;
		}
		if (array_key_exists('description_locale_1', $data)) {
	    	$description_locale_1 = trim(strip_tags($data['description_locale_1']));
			if (strlen($description_locale_1) > 2047) return 'Integrity';
			if ($this->description_locale_1 != $description_locale_1) $auditRow['description_locale_1'] = $this->description_locale_1 = $description_locale_1;
		}
		if (array_key_exists('description_locale_2', $data)) {
	    	$description_locale_2 = trim(strip_tags($data['description_locale_2']));
			if (strlen($description_locale_2) > 2047) return 'Integrity';
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
		if (array_key_exists('exception_dates', $data)) {
	    	$exception_dates = $data['exception_dates'];
			if ($this->exception_dates != $exception_dates) $auditRow['exception_dates'] = $this->exception_dates = $exception_dates;
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
		if (array_key_exists('matched_accounts', $data)) {
	    	$matched_accounts = trim(strip_tags($data['matched_accounts']));
			if (strlen($matched_accounts) > 255) return 'Integrity';
			if ($this->matched_accounts != $matched_accounts) $auditRow['matched_accounts'] = $this->matched_accounts = $matched_accounts;
		}
		if (array_key_exists('matching_log', $data)) {
	    	$matching_log = $data['matching_log'];
			if ($this->matching_log != $matching_log) $auditRow['matching_log'] = $this->matching_log = $matching_log;
		}
		if (array_key_exists('rewards', $data)) {
	    	$rewards = $data['rewards'];
			if ($this->rewards != $rewards) $auditRow['rewards'] = $this->rewards = $rewards;
		}
		if (array_key_exists('feedbacks', $data)) {
	    	$feedbacks = $data['feedbacks'];
			if ($this->feedbacks != $feedbacks) $auditRow['feedbacks'] = $this->feedbacks = $feedbacks;
		}
		if (array_key_exists('value', $data)) {
	    	$value = (float) $data['value'];
			if ($this->value != $value) $auditRow['value'] = $this->value = $value;
		}
		if (array_key_exists('comment', $data)) {
	    	$comment = trim(strip_tags($data['comment']));
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
			if (strlen($property_21) > 32767) return 'Integrity';
			if ($this->property_21 != $property_21) $auditRow['property_21'] = $this->property_21 = $property_21;
		}
		if (array_key_exists('property_22', $data)) {
			$property_22 = trim(strip_tags($data['property_22']));
			if (strlen($property_22) > 32767) return 'Integrity';
			if ($this->property_22 != $property_22) $auditRow['property_22'] = $this->property_22 = $property_22;
		}
		if (array_key_exists('property_23', $data)) {
			$property_23 = trim(strip_tags($data['property_23']));
			if (strlen($property_23) > 32767) return 'Integrity';
			if ($this->property_23 != $property_23) $auditRow['property_23'] = $this->property_23 = $property_23;
		}
		if (array_key_exists('property_24', $data)) {
			$property_24 = trim(strip_tags($data['property_24']));
			if (strlen($property_24) > 32767) return 'Integrity';
			if ($this->property_24 != $property_24) $auditRow['property_24'] = $this->property_24 = $property_24;
		}
		if (array_key_exists('property_25', $data)) {
			$property_25 = trim(strip_tags($data['property_25']));
			if (strlen($property_25) > 32767) return 'Integrity';
			if ($this->property_25 != $property_25) $auditRow['property_25'] = $this->property_25 = $property_25;
		}
		if (array_key_exists('property_26', $data)) {
			$property_26 = trim(strip_tags($data['property_26']));
			if (strlen($property_26) > 32767) return 'Integrity';
			if ($this->property_26 != $property_26) $auditRow['property_26'] = $this->property_26 = $property_26;
		}
		if (array_key_exists('property_27', $data)) {
			$property_27 = trim(strip_tags($data['property_27']));
			if (strlen($property_27) > 255) return 'Integrity';
			if ($this->property_27 != $property_27) $auditRow['property_27'] = $this->property_27 = $property_27;
		}
		if (array_key_exists('property_28', $data)) {
			$property_28 = trim(strip_tags($data['property_28']));
			if (strlen($property_28) > 32767) return 'Integrity';
			if ($this->property_28 != $property_28) $auditRow['property_28'] = $this->property_28 = $property_28;
		}
		if (array_key_exists('property_29', $data)) {
			$property_29 = trim(strip_tags($data['property_29']));
			if (strlen($property_29) > 32767) return 'Integrity';
			if ($this->property_29 != $property_29) $auditRow['property_29'] = $this->property_29 = $property_29;
		}
		if (array_key_exists('property_30', $data)) {
			$property_30 = trim(strip_tags($data['property_30']));
			if (strlen($property_30) > 32767) return 'Integrity';
			if ($this->property_30 != $property_30) $auditRow['property_30'] = $this->property_30 = $property_30;
		}
		
		// Set the value property to the total number of scheduled hours for a planning event
		$config = $context->getConfig('event/'.$this->type);
		if (array_key_exists('calendar', $config['options']) && $config['options']['calendar'] && $this->begin_date && $this->end_date) {
			$this->value = 0;

			// Days is a table of all the date of the visible period (ie month, week or single day) associated with the day of week
			$days = array();
			for($date = new \DateTime($this->begin_date); $date <= new \DateTime($this->end_date); $date->modify('+1 day')) {
    			if (!in_array($date->format('Y-m-d'), $this->exception_dates)) {
	    			if ($this->day_of_week == $date->format('w') || $this->day_of_month == $date->format('m')) {
	    				$interval = substr($this->end_time, 0, 2)*60 + substr($this->end_time, 3, 2) - substr($this->begin_time, 0, 2)*60 - substr($this->begin_time, 3, 2);
	    				$this->value += round($interval/60, 2);
	    			}
    			}
			}
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
    	Event::getTable()->save($this);
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
    	$event = Event::get($this->id);
    	if ($update_time && $event->update_time > $update_time) return 'Isolation';
    	Event::getTable()->save($this);
		return ('OK');
    }

    /**
     * Restfull implementation
     */
    public function loadAndAdd($data, $configProperties)
    {
    	$context = Context::getCurrent();

    	if (!array_key_exists('status', $data)) $data['status'] = 'new';
    	foreach ($configProperties as $propertyId => $property) {
	    	if (array_key_exists($propertyId, $data) && $data[$propertyId]) {
		    	if ($property['type'] == 'select') {
		    		foreach (explode(',', $data[$propertyId]) as $modalityId) {
			    		if (!array_key_exists($modalityId, $property['modalities'])) {
					    	return ['400', 'The modality '.$data[$propertyId].' does not exist in '.$propertyId];
			    		}
		    		}
		    	}
	    		elseif ($property['type'] == 'date' && $data[$propertyId] && (strlen($data[$propertyId] < 10) || !checkdate(substr($data[$propertyId], 5, 2), substr($data[$propertyId], 8, 2), substr($data[$propertyId], 0, 4)))) {
	    			return ['400', $data[$propertyId].' is not a valid date for '.$propertyId];
	    		}
	    	}
    	}
    
    	// Load the data
		$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', $rc];
    
    	// Save the data
    	$this->add();
    	if ($rc != 'OK') return ['500', 'event->add: '.$rc];
    	
    	$this->properties = $this->getProperties();
    	return ['200'];
    }
    
    public function loadAndUpdate($data, $configProperties, $update_time = null)
    {
    	$context = Context::getCurrent();

    	foreach ($configProperties as $propertyId => $property) {
    		if (array_key_exists($propertyId, $data)) {
    			if ($property['type'] == 'select' && !array_key_exists($data[$propertyId], $property['modalities'])) {
    				return ['400', 'The modality '.$data[$propertyId].' does not exist in '.$propertyId];
    			}
    			elseif ($property['type'] == 'date' && $data[$propertyId] && (strlen($data[$propertyId] < 10) || !checkdate(substr($data[$propertyId], 5, 2), substr($data[$propertyId], 8, 2), substr($data[$propertyId], 0, 4)))) {
    				return ['400', $data[$propertyId].' is not a valid date for '.$propertyId];
    			}
    		}
    	}

    	// Load the data
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', $rc];
    
    	// Save the data
    	$this->update($update_time);
    	if ($rc != 'OK') return ['500', 'event->update: '.$rc];
    	return ['200'];
    }
    
    /**
     * @param Interaction $interaction
     * @return string
     */
    public static function processInteraction($data, $interaction)
    {
    	require_once 'public/PHPExcel_1/Classes/PHPExcel/Style/Supervisor.php';
    	require_once 'public/PHPExcel_1/Classes/PHPExcel/IComparable.php';
    	require_once 'public/PHPExcel_1/Classes/PHPExcel/Style/NumberFormat.php';
    	require_once 'public/PHPExcel_1/Classes/PHPExcel/Shared/Date.php';
    	$context = Context::getCurrent();
    	$type = $interaction->category;
    
    	// Normalize the data
		$properties = Event::getConfigProperties($type);
		foreach($properties as $propertyId => $property) {
		if (array_key_exists($propertyId, $data)) {
    			$value = $data[$propertyId];
				if ($property['type'] == 'select') {
    				$valueKey = null;
    				foreach ($property['modalities'] as $modalityId => $modality) if ($context->localize($modality) == $value) $valueKey = $modalityId;
    				if ($valueKey) $data[$propertyId] = $valueKey;
    			}
/*    			elseif ($property['type'] == 'date' && $value) {
    				$data[$propertyId] = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($value));
    			}*/
    		}
    	}
    
    	$event = Event::instanciate($type);
    	$rc = $event->loadAndAdd($data, $properties);
    	return ['200', $rc];
    }
    
    /**
     * @param Interaction $interaction
     * @return string
     */
    public static function processInteractionOld($data, $interaction)
    {
    	$rc = 'OK';
    	$context = Context::getCurrent();
    	if ($interaction->format == 'text/csv') {
    		$event = Event::instanciate();
    		$columns = $context->getConfig('event/csv/'.$interaction->category)['columns'];
    		$indicators = array();
    		$attributes = array();
    		foreach ($columns as $columnId => $column) {
    			if ($column['type'] == 'indicator') $indicators[$columnId] = $column;
    			else $attributes[$columnId] = $column;
    		}
    		if (!$indicators) $indicators['count'] = null;
    		
    		foreach ($context->getConfig('event/csv/'.$interaction->category)['targets'] as $targetId => $target) {
    			$targetData = array();
    			$status = 'checked';
    			$targetData['category'] = $interaction->category;
    			$targetData['type'] = $targetId;
		    	foreach($target as $propertyId => $expression) {
		    		$value = '';
		    		foreach ($expression as $component) {
		    			if (array_key_exists($component, $columns)) $value .= (array_key_exists($component, $data)) ? $data[$component] : null;
		    			else $value .= $component;
		    		}
	    			if ($propertyId == 'place_identifier') {
		    			$place = Place::get($value, 'identifier');
		    			if ($place) $targetData['place_id'] = $place->id;
		    			else $status = 'new';
		    		}
		    		elseif ($propertyId == 'n_fn') {
		    			$vcard = Vcard::get($value, 'n_fn');
		    			if ($vcard) $targetData['vcard_id'] = $vcard->id;
		    		}
		    		else $targetData[$propertyId] = $value;
	    		}
	    		// Compute the join keys
	/*    		foreach ($context->getConfig('event/type')['joinKeys'] as $joinKeyId => $joinKey) {
	    			if ($joinKey['type'] == $interaction->category) $targetData[$joinKeyId] = $targetData[$joinKey['property']];
	    		}*/
	    		
	    		// Compute the indicator values
	    		foreach ($context->getConfig('event/'.$interaction->category)['indicators'] as $indicatorId => $indicator) {
	    			$targetData['modality_'.$indicatorId] = $targetData[$indicator['modality']];
	    			$targetData['value_'.$indicatorId] = ($targetData[$indicator['modality']]) ? 1 : 0;
	    		}
	    		$targetData['status'] = $status;

	    		foreach($indicators as $indicatorId => $indicator) {
	    		    if (!$indicator) {
		    			$targetData['caption'] = 'count';
		    			$targetData['value'] = 1;
		    			if ($event->loadData($targetData) != 'OK') throw new \Exception('View error');
		    			$event->id = null;
		    			$rc = $event->add();
		    			if ($rc != 'OK') {
		    				return $rc;
		    			}
		    		}
	    			elseif ($data[$indicatorId]) {
	    				$targetData['caption'] = $indicator['modality'];
	    				$targetData['value'] = $data[$indicatorId];
	    				if ($event->loadData($targetData) != 'OK') throw new \Exception('View error');
	    				$event->id = null;
		    			$rc = $event->add();
		    			if ($rc != 'OK') {
		    				return $rc;
		    			}
		    		}
	    		}
    		}
    	}
    	elseif ($interaction->format == 'application/json') {
	    	if ($data['action'] == 'update' || $data['action'] == 'delete') $event = Event::get($data['identifier'], 'identifier');
	    	elseif ($data['action'] == 'add') $event = Event::instanciate();
	
	    	if ($data['action'] == 'delete') $rc = $event->delete(null);
	    	elseif ($data['action'] == 'add' || $data['action'] == 'update') {
	    		if (array_key_exists('place_identifier', $data)) {
	    			$place = Place::get($data['place_identifier'], 'identifier');
	    			if ($place) $data['place_id'] = $place->id;
	    		}
	    	    if (array_key_exists('n_fn', $data)) {
	    			$vcard = Vcard::get($data['n_fn'], 'n_fn');
	    			if ($vcard) $data['vcard_id'] = $vcard->id;
	    		}
	    		if ($event->loadData($data) != 'OK') throw new \Exception('View error');
	    		if (!$event->id) $rc = $event->add();
	    		else $rc = $event->update(null);
	    		if ($rc != 'OK') {
//	    			$connection->rollback();
			    	return $rc;
	    		}
	    	}
	    	elseif ($data['action'] == 'synchronize') {
	    		$params = $data['params'];
	    		$params['type'] = $data['type'];
	    		foreach ($data['rows'] as $row) {
	    			$event = Event::instanciate();
	    			if ($event->loadData($row) != 'OK') throw new \Exception('Integrity');
	    			$old = Event::get($event->identifier, 'identifier');
	    			$rc = $old->delete(null);
	    		    if ($rc != 'OK') {
//	    				$connection->rollback();
			    		return $rc;
		   			}
	    			$rc = $event->add();
	    		    if ($rc != 'OK') {
//	    				$connection->rollback();
			    		return $rc;
		   			}
	    		}
	    	}
    	}
    	return $rc;
    }
    
    /**
     * Checks if this event can de deleted. 
     * An event is not deletable if the result of calling the isUsed() method for each class which has registered itself in the 'ppitCoreDependencies' list returns true.
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
    	$event = Event::get($this->id);
    	if ($update_time && $event->update_time > $update_time) return 'Isolation';
    	$this->status = 'deleted';
    	Event::getTable()->save($this);
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
     * Returns the object to relational manager for the Event class
     */
    public static function getTable()
    {
    	if (!Event::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Event::$table = $sm->get('PpitCore\Model\EventTable');
    	}
    	return Event::$table;
    }
}
