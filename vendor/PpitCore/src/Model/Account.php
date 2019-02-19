<?php
namespace PpitCore\Model;

use Zend\Db\Sql\Where;
use Zend\Filter\StripTags;
use Zend\Http\Client;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Filter\Encrypt;
use Symfony\Component\Validator\Constraints\IsNull;

class Account
{
	public static $model = array(
		'entities' => array(
			'core_account' => 	['table' => 'core_account'],
			'core_vcard' => 	['table' => 'core_vcard', 'foreign_key' => 'contact_1_id'],
			'contact_2' => 		['table' => 'core_vcard', 'foreign_key' => 'contact_2_id'],
			'contact_3' => 		['table' => 'core_vcard', 'foreign_key' => 'contact_3_id'],
			'contact_4' => 		['table' => 'core_vcard', 'foreign_key' => 'contact_4_id'],
			'contact_5' => 		['table' => 'core_vcard', 'foreign_key' => 'contact_5_id'],
		),
		'properties' => array(
			'status' => 					['entity' => 'core_account', 'column' => 'status'],
			'place_id' => 					['entity' => 'core_account', 'column' => 'place_id'],
			'identifier' => 				['entity' => 'core_account', 'column' => 'identifier'],
			'name' => 						['entity' => 'core_account', 'column' => 'name'],
			'photo_link_id' => 				['entity' => 'core_account', 'column' => 'photo_link_id'],
			'basket' => 					['entity' => 'core_account', 'column' => 'basket'],
			'contact_1_id' => 				['entity' => 'core_account', 'column' => 'contact_1_id'],
			'contact_1_status' =>			['entity' => 'core_account', 'column' => 'contact_1_status'],
			'org' => 						['entity' => 'core_vcard', 'column' => 'org'],
			'n_title' => 					['entity' => 'core_vcard', 'column' => 'n_title'],
			'n_first' => 					['entity' => 'core_vcard', 'column' => 'n_first'],
			'n_last' => 					['entity' => 'core_vcard', 'column' => 'n_last'],
			'n_fn' => 						['entity' => 'core_vcard', 'column' => 'n_fn'],
			'email' => 						['entity' => 'core_vcard', 'column' => 'email'],
			'tel_work' => 					['entity' => 'core_vcard', 'column' => 'tel_work'],
			'tel_cell' => 					['entity' => 'core_vcard', 'column' => 'tel_cell'],
			'adr_street' => 				['entity' => 'core_vcard', 'column' => 'adr_street'],
			'adr_extended' => 				['entity' => 'core_vcard', 'column' => 'adr_extended'],
			'adr_post_office_box' => 		['entity' => 'core_vcard', 'column' => 'adr_post_office_box'],
			'adr_zip' => 					['entity' => 'core_vcard', 'column' => 'adr_zip'],
			'adr_city' => 					['entity' => 'core_vcard', 'column' => 'adr_city'],
			'adr_state' => 					['entity' => 'core_vcard', 'column' => 'adr_state'],
			'adr_country' => 				['entity' => 'core_vcard', 'column' => 'adr_country'],
			'birth_date' => 				['entity' => 'core_vcard', 'column' => 'birth_date'],
			'gender' => 					['entity' => 'core_vcard', 'column' => 'gender'],
			'nationality' => 				['entity' => 'core_vcard', 'column' => 'nationality'],
			'photo_link_id' => 				['entity' => 'core_vcard', 'column' => 'photo_link_id'],
			'profile_tiny_1' => 			['entity' => 'core_vcard', 'column' => 'tiny_1'],
			'profile_tiny_2' => 			['entity' => 'core_vcard', 'column' => 'tiny_2'],
			'profile_tiny_3' => 			['entity' => 'core_vcard', 'column' => 'tiny_3'],
			'profile_tiny_4' => 			['entity' => 'core_vcard', 'column' => 'tiny_4'],
			'profile_tiny_5' => 			['entity' => 'core_vcard', 'column' => 'tiny_5'],
			'profile_tiny_6' => 			['entity' => 'core_vcard', 'column' => 'tiny_6'],
			'profile_tiny_7' => 			['entity' => 'core_vcard', 'column' => 'tiny_7'],
			'profile_tiny_8' => 			['entity' => 'core_vcard', 'column' => 'tiny_8'],
			'profile_tiny_9' => 			['entity' => 'core_vcard', 'column' => 'tiny_9'],
			'profile_tiny_10' => 			['entity' => 'core_vcard', 'column' => 'tiny_10'],
			'locale' => 					['entity' => 'core_vcard', 'column' => 'locale'],
			'contact_2_id' => 				['entity' => 'core_account', 'column' => 'contact_2_id'],
			'contact_2_status' => 			['entity' => 'core_account', 'column' => 'contact_2_status'],
			'n_title_2' => 					['entity' => 'contact_2', 'column' => 'n_title'],
			'n_first_2' => 					['entity' => 'contact_2', 'column' => 'n_first'],
			'n_last_2' => 					['entity' => 'contact_2', 'column' => 'n_last'],
			'n_fn_2' => 					['entity' => 'contact_2', 'column' => 'n_fn'],
			'email_2' => 					['entity' => 'contact_2', 'column' => 'email'],
			'tel_work_2' => 				['entity' => 'contact_2', 'column' => 'tel_work'],
			'tel_cell_2' => 				['entity' => 'contact_2', 'column' => 'tel_cell'],
			'adr_street_2' => 				['entity' => 'contact_2', 'column' => 'adr_street'],
			'adr_extended_2' => 			['entity' => 'contact_2', 'column' => 'adr_extended'],
			'adr_post_office_box_2' => 		['entity' => 'contact_2', 'column' => 'adr_post_office_box'],
			'adr_zip_2' => 					['entity' => 'contact_2', 'column' => 'adr_zip'],
			'adr_city_2' => 				['entity' => 'contact_2', 'column' => 'adr_city'],
			'adr_state_2' => 				['entity' => 'contact_2', 'column' => 'adr_state'],
			'adr_country_2' => 				['entity' => 'contact_2', 'column' => 'adr_country'],
			'contact_3_id' => 				['entity' => 'core_account', 'column' => 'contact_3_id'],
			'contact_3_status' => 			['entity' => 'coe_account', 'column' => 'contact_3_status'],
			'n_title_3' => 					['entity' => 'contact_3', 'column' => 'n_title'],
			'n_first_3' => 					['entity' => 'contact_3', 'column' => 'n_first'],
			'n_last_3' => 					['entity' => 'contact_3', 'column' => 'n_last'],
			'n_fn_3' => 					['entity' => 'contact_3', 'column' => 'n_fn'],
			'email_3' => 					['entity' => 'contact_3', 'column' => 'email'],
			'tel_work_3' => 				['entity' => 'contact_3', 'column' => 'tel_work'],
			'tel_cell_3' => 				['entity' => 'contact_3', 'column' => 'tel_cell'],
			'adr_street_3' => 				['entity' => 'contact_3', 'column' => 'adr_street'],
			'adr_extended_3' => 			['entity' => 'contact_3', 'column' => 'adr_extended'],
			'adr_post_office_box_3' => 		['entity' => 'contact_3', 'column' => 'adr_post_office_box'],
			'adr_zip_3' => 					['entity' => 'contact_3', 'column' => 'adr_zip'],
			'adr_city_3' => 				['entity' => 'contact_3', 'column' => 'adr_city'],
			'adr_state_3' => 				['entity' => 'contact_3', 'column' => 'adr_state'],
			'adr_country_3' => 				['entity' => 'contact_3', 'column' => 'adr_country'],
			'contact_4_id' => 				['entity' => 'core_account', 'column' => 'contact_4_id'],
			'contact_4_status' => 			['entity' => 'core_account', 'column' => 'contact_4_status'],
			'n_title_4' => 					['entity' => 'contact_4', 'column' => 'n_title'],
			'n_first_4' => 					['entity' => 'contact_4', 'column' => 'n_first'],
			'n_last_4' => 					['entity' => 'contact_4', 'column' => 'n_last'],
			'n_fn_4' => 					['entity' => 'contact_4', 'column' => 'n_fn'],
			'email_4' => 					['entity' => 'contact_4', 'column' => 'email'],
			'tel_work_4' => 				['entity' => 'contact_4', 'column' => 'tel_work'],
			'tel_cell_4' => 				['entity' => 'contact_4', 'column' => 'tel_cell'],
			'adr_street_4' => 				['entity' => 'contact_4', 'column' => 'adr_street'],
			'adr_extended_4' => 			['entity' => 'contact_4', 'column' => 'adr_extended'],
			'adr_post_office_box_4' => 		['entity' => 'contact_4', 'column' => 'adr_post_office_box'],
			'adr_zip_4' => 					['entity' => 'contact_4', 'column' => 'adr_zip'],
			'adr_city_4' => 				['entity' => 'contact_4', 'column' => 'adr_city'],
			'adr_state_4' => 				['entity' => 'contact_4', 'column' => 'adr_state'],
			'adr_country_4' => 				['entity' => 'contact_4', 'column' => 'adr_country'],
			'contact_5_id' => 				['entity' => 'core_account', 'column' => 'contact_5_id'],
			'contact_5_status' => 			['entity' => 'core_account', 'column' => 'contact_5_status'],
			'n_title_5' => 					['entity' => 'contact_5', 'column' => 'n_title'],
			'n_first_5' => 					['entity' => 'contact_5', 'column' => 'n_first'],
			'n_last_5' => 					['entity' => 'contact_5', 'column' => 'n_last'],
			'n_fn_5' => 					['entity' => 'contact_5', 'column' => 'n_fn'],
			'email_5' => 					['entity' => 'contact_5', 'column' => 'email'],
			'tel_work_5' => 				['entity' => 'contact_5', 'column' => 'tel_work'],
			'tel_cell_5' => 				['entity' => 'contact_5', 'column' => 'tel_cell'],
			'adr_street_5' => 				['entity' => 'contact_5', 'column' => 'adr_street'],
			'adr_extended_5' => 			['entity' => 'contact_5', 'column' => 'adr_extended'],
			'adr_post_office_box_5' => 		['entity' => 'contact_5', 'column' => 'adr_post_office_box'],
			'adr_zip_5' => 					['entity' => 'contact_5', 'column' => 'adr_zip'],
			'adr_city_5' => 				['entity' => 'contact_5', 'column' => 'adr_city'],
			'adr_state_5' => 				['entity' => 'contact_5', 'column' => 'adr_state'],
			'adr_country_5' => 				['entity' => 'contact_5', 'column' => 'adr_country'],
			'opening_date' => 				['entity' => 'core_account', 'column' => 'opening_date'],
			'closing_date' => 				['entity' => 'core_account', 'column' => 'closing_date'],
			'callback_date' => 				['entity' => 'core_account', 'column' => 'callback_date'],
			'first_activation_date' => 		['entity' => 'core_account', 'column' => 'first_activation_date'],
			'date_1' => 					['entity' => 'core_account', 'column' => 'date_1'],
			'date_2' => 					['entity' => 'core_account', 'column' => 'date_2'],
			'date_3' => 					['entity' => 'core_account', 'column' => 'date_3'],
			'date_4' => 					['entity' => 'core_account', 'column' => 'date_4'],
			'date_5' => 					['entity' => 'core_account', 'column' => 'date_5'],
			'priority' => 					['entity' => 'core_account', 'column' => 'priority'],
			'origine' => 					['entity' => 'core_account', 'column' => 'origine'],
			'next_meeting_date' => 			['entity' => 'core_account', 'column' => 'next_meeting_date'],
			'next_meeting_confirmed' => 	['entity' => 'core_account', 'column' => 'next_meeting_confirmed'],
			'contact_history' => 			['entity' => 'core_account', 'column' => 'contact_history'],
			'terms_of_use_validation_time' => ['entity' => 'core_account', 'column' => 'terms_of_use_validation_time'],
			'charter_validation_time' => 	['entity' => 'core_account', 'column' => 'charter_validation_time'],
			'opt_out_time' => 				['entity' => 'core_account', 'column' => 'opt_out_time'],
			'notification_time' => 			['entity' => 'core_account', 'column' => 'notification_time'],
			'availability_begin' => 		['entity' => 'core_account', 'column' => 'availability_begin'],
			'availability_end' => 			['entity' => 'core_account', 'column' => 'availability_end'],
			'availability_exceptions' =>	['entity' => 'core_account', 'column' => 'availability_exceptions'],
			'availability_constraints' =>	['entity' => 'core_account', 'column' => 'availability_constraints'],
			'credits' =>					['entity' => 'core_account', 'column' => 'credits'],
			'shopping_cart' =>				['entity' => 'core_account', 'column' => 'shopping_cart'],
			'default_means_of_payment' =>	['entity' => 'core_account', 'column' => 'default_means_of_payment'],
			'transfer_order_id' =>			['entity' => 'core_account', 'column' => 'transfer_order_id'],
			'transfer_order_date' =>		['entity' => 'core_account', 'column' => 'transfer_order_date'],
			'bank_identifier' =>			['entity' => 'core_account', 'column' => 'bank_identifier'],
			'property_1' => 				['entity' => 'core_account', 'column' => 'property_1'],
			'property_2' => 				['entity' => 'core_account', 'column' => 'property_2'],
			'property_3' => 				['entity' => 'core_account', 'column' => 'property_3'],
			'property_4' => 				['entity' => 'core_account', 'column' => 'property_4'],
			'property_5' => 				['entity' => 'core_account', 'column' => 'property_5'],
			'property_6' => 				['entity' => 'core_account', 'column' => 'property_6'],
			'property_7' => 				['entity' => 'core_account', 'column' => 'property_7'],
			'property_8' => 				['entity' => 'core_account', 'column' => 'property_8'],
			'property_9' => 				['entity' => 'core_account', 'column' => 'property_9'],
			'property_10' => 				['entity' => 'core_account', 'column' => 'property_10'],
			'property_11' => 				['entity' => 'core_account', 'column' => 'property_11'],
			'property_12' => 				['entity' => 'core_account', 'column' => 'property_12'],
			'property_13' => 				['entity' => 'core_account', 'column' => 'property_13'],
			'property_14' => 				['entity' => 'core_account', 'column' => 'property_14'],
			'property_15' => 				['entity' => 'core_account', 'column' => 'property_15'],
			'property_16' => 				['entity' => 'core_account', 'column' => 'property_16'],
			'json_property_1' => 			['entity' => 'core_account', 'column' => 'json_property_1'],
			'json_property_2' => 			['entity' => 'core_account', 'column' => 'json_property_2'],
			'json_property_3' => 			['entity' => 'core_account', 'column' => 'json_property_3'],
			'json_property_4' => 			['entity' => 'core_account', 'column' => 'json_property_4'],
			'json_property_5' => 			['entity' => 'core_account', 'column' => 'json_property_5'],
			'comment_1' => 					['entity' => 'core_account', 'column' => 'comment_1'],
			'comment_2' => 					['entity' => 'core_account', 'column' => 'comment_2'],
			'comment_3' => 					['entity' => 'core_account', 'column' => 'comment_3'],
			'comment_4' => 					['entity' => 'core_account', 'column' => 'comment_4'],
			'authentication_token' => 		['entity' => 'core_account', 'column' => 'authentication_token'],
		),
	);
	
	public static function getConfig($type) {
		$context = Context::getCurrent();
		$properties = array();
		$description = $context->getConfig('core_account/'.$type);
		if (!$description) $description = $context->getConfig('core_account/generic');
		$description['type'] = $type;
		foreach($description['properties'] as $propertyId) {
			$property = $context->getConfig('core_account/'.$type.'/property/'.$propertyId);
			if (!$property) $property = $context->getConfig('core_account/generic/property/'.$propertyId);
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			if (!array_key_exists('private', $property)) $property['private'] = false;
			if (!$property['private'] || $context->hasRole('dpo')) {
				if ($propertyId == 'place_id') {
					$property['modalities'] = array();
					foreach (Place::getList(array()) as $place) $property['modalities'][$place->id] = $place->caption;
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
		}
		return $properties;
	}

	public static function getConfigSearch($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configSearch = $context->getConfig('core_account/search/'.$type);
		if (!$configSearch) $configSearch = $context->getConfig('core_account/search/generic');
		$properties = array();
		foreach ($configSearch['properties'] as $propertyId => $options) {
			$property = $configProperties[$propertyId];
			$properties[$propertyId] = $property;
			$properties[$propertyId]['options'] = $options;
		}
		$configSearch['properties'] = $properties;
		return $configSearch;
	}

	public static function getConfigList($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configList = $context->getConfig('core_account/list/'.$type);
		if (!$configList) $configList = $context->getConfig('core_account/list/generic');
		$properties = array();
		foreach ($configList['properties'] as $propertyId => $options) {
			$property = $configProperties[$propertyId];
			$properties[$propertyId] = $property;
			$properties[$propertyId]['options'] = $options;
		}
		$configList['properties'] = $properties;
		return $configList;
	}

	public static function getConfigUpdate($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configUpdate = $context->getConfig('core_account/update/'.$type);
		if (!$configUpdate) $configUpdate = $context->getConfig('core_account/update/generic');
		$properties = array();
		foreach ($configUpdate as $propertyId => $options) {
			if (array_key_exists($propertyId, $configProperties)) {
				$property = $configProperties[$propertyId];
				$property['options'] = $options;
				$properties[$propertyId] = $property;
			}
		}
		return $properties;
	}

	public static function getConfigGroupUpdate($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configUpdate = $context->getConfig('core_account/groupUpdate/'.$type);
		if (!$configUpdate) $configUpdate = $context->getConfig('core_account/groupUpdate/generic');
		$properties = array();
		foreach ($configUpdate as $propertyId => $options) {
			if (array_key_exists($propertyId, $configProperties)) {
				$property = $configProperties[$propertyId];
				$property['options'] = $options;
				$properties[$propertyId] = $property;
			}
		}
		return $properties;
	}

	public static function getConfigExport($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configExport = $context->getConfig('core_account/export/'.$type);
		if (!$configExport) $configExport = $context->getConfig('core_account/export/generic');
		$properties = array();
		foreach ($configExport as $propertyId => $options) {
			if (array_key_exists($propertyId, $configProperties)) {
				$property = $configProperties[$propertyId];
				$properties[$propertyId] = $property;
				$properties[$propertyId]['options'] = $options;
			}
		}
		return $properties;
	}

	public static function getDescription($type)
	{
		$context = Context::getCurrent();
		$config = $context->getConfig('core_account/'.$type);
		if (!$config) $config = $context->getConfig('core_account/generic');
	
		$description = array();
		$description['type'] = $type;
		$description['options'] = (array_key_exists('options', $config)) ? $config['options'] : array();
		$description['properties'] = Account::getConfig($type);
		$description['search'] = Account::getConfigSearch($type, $description['properties']);
		$description['list'] = Account::getConfigList($type, $description['properties']);
		$description['update'] = Account::getConfigUpdate($type, $description['properties']);
		$description['groupUpdate'] = Account::getConfigGroupUpdate($type, $description['properties']);
		$description['export'] = Account::getConfigEXport($type, $description['properties']);
		return $description;
	}
	
	public $id;
    public $instance_id;
    public $status;
    public $type;
    public $place_id;
    public $identifier;
    public $name;
    public $basket;
    public $contact_1_id;
    public $contact_1_status;
    public $contact_2_id;
    public $contact_2_status;
    public $contact_3_id;
    public $contact_3_status;
    public $contact_4_id;
    public $contact_4_status;
    public $contact_5_id;
    public $contact_5_status;
    public $opening_date;
    public $closing_date;
    public $callback_date;
    public $first_activation_date;
    public $date_1;
    public $date_2;
    public $date_3;
    public $date_4;
    public $date_5;
    public $priority;
    public $origine;
    public $next_meeting_date;
    public $next_meeting_confirmed;
    public $contact_history;
    public $notification_time;
    public $terms_of_use_validation_time;
    public $charter_validation_time;
    public $opt_out_time;
    public $availability_begin;
    public $availability_end;
    public $availability_exceptions;
    public $availability_constraints;
    public $credits;
    public $shopping_cart;
    public $default_means_of_payment;
    public $transfer_order_id;
    public $transfer_order_date;
    public $bank_identifier;
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
    public $json_property_1;
    public $json_property_2;
    public $json_property_3;
    public $json_property_4;
    public $json_property_5;
    public $comment_1;
    public $comment_2;
    public $comment_3;
    public $comment_4;
    public $audit;
    public $authentication_token;
    public $currently_updated_by;
    public $update_time;
    
    // Joined properties
    public $place_caption;
    public $place_identifier;
    
    public $n_title;
    public $n_first;
    public $n_last;
    public $n_fn;
    public $email;
    public $birth_date;
    public $tel_work;
    public $tel_cell;
    public $adr_street;
    public $adr_extended;
    public $adr_post_office_box;
    public $adr_zip;
    public $adr_city;
    public $adr_state;
    public $adr_country;
    public $gender;
    public $nationality;
    public $photo_link_id;
    public $profile_tiny_1;
    public $profile_tiny_2;
    public $profile_tiny_3;
    public $profile_tiny_4;
    public $profile_tiny_5;
    public $profile_tiny_6;
    public $profile_tiny_7;
    public $profile_tiny_8;
    public $profile_tiny_9;
    public $profile_tiny_10;
    public $locale;
    
    public $n_title_2;
    public $n_first_2;
    public $n_last_2;
    public $n_fn_2;
    public $email_2;
    public $birth_date_2;
    public $tel_work_2;
    public $tel_cell_2;
    public $adr_street_2;
    public $adr_extended_2;
    public $adr_post_office_box_2;
    public $adr_zip_2;
    public $adr_city_2;
    public $adr_state_2;
    public $adr_country_2;
    
    public $n_title_3;
    public $n_first_3;
    public $n_last_3;
    public $n_fn_3;
    public $email_3;
    public $birth_date_3;
    public $tel_work_3;
    public $tel_cell_3;
    public $adr_street_3;
    public $adr_extended_3;
    public $adr_post_office_box_3;
    public $adr_zip_3;
    public $adr_city_3;
    public $adr_state_3;
    public $adr_country_3;
    
    public $n_title_4;
    public $n_first_4;
    public $n_last_4;
    public $n_fn_4;
    public $email_4;
    public $birth_date_4;
    public $tel_work_4;
    public $tel_cell_4;
    public $adr_street_4;
    public $adr_extended_4;
    public $adr_post_office_box_4;
    public $adr_zip_4;
    public $adr_city_4;
    public $adr_state_4;
    public $adr_country_4;
    
    public $n_title_5;
    public $n_first_5;
    public $n_last_5;
    public $n_fn_5;
    public $email_5;
    public $birth_date_5;
    public $tel_work_5;
    public $tel_cell_5;
    public $adr_street_5;
    public $adr_extended_5;
    public $adr_post_office_box_5;
    public $adr_zip_5;
    public $adr_city_5;
    public $adr_state_5;
    public $adr_country_5;
    
    // Transient properties
    public $place;
    public $contact_1;
    public $contact_2;
    public $contact_3;
    public $contact_4;
    public $contact_5;
	public $properties;
    public $files;
	public $comment;
	public $is_notified;
	public $username;
	public $new_password;
	public $user;
	public $userContact;

	public $invoice_n_title;
	public $invoice_n_first;
	public $invoice_n_last;
	public $invoice_n_fn;
	public $invoice_email;
	public $invoice_tel_work;
	public $invoice_tel_cell;
	public $invoice_adr_street;
	public $invoice_adr_extended;
	public $invoice_adr_post_office_box;
	public $invoice_adr_zip;
	public $invoice_adr_city;
	public $invoice_adr_state;
	public $invoice_adr_country;
	
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
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->basket = (isset($data['basket'])) ? $data['basket'] : null;
        $this->contact_1_id = (isset($data['contact_1_id'])) ? $data['contact_1_id'] : null;
        $this->contact_1_status = (isset($data['contact_1_status'])) ? $data['contact_1_status'] : null;
        $this->contact_2_id = (isset($data['contact_2_id'])) ? $data['contact_2_id'] : null;
        $this->contact_2_status = (isset($data['contact_2_status'])) ? $data['contact_2_status'] : null;
        $this->contact_3_id = (isset($data['contact_3_id'])) ? $data['contact_3_id'] : null;
        $this->contact_3_status = (isset($data['contact_3_status'])) ? $data['contact_3_status'] : null;
        $this->contact_4_id = (isset($data['contact_4_id'])) ? $data['contact_4_id'] : null;
        $this->contact_4_status = (isset($data['contact_4_status'])) ? $data['contact_4_status'] : null;
        $this->contact_5_id = (isset($data['contact_5_id'])) ? $data['contact_5_id'] : null;
        $this->contact_5_status = (isset($data['contact_5_status'])) ? $data['contact_5_status'] : null;
        $this->opening_date = (isset($data['opening_date'])) ? $data['opening_date'] : null;
        $this->closing_date = (isset($data['closing_date']) && $data['closing_date'] != '9999-12-31') ? $data['closing_date'] : null;
        $this->callback_date = (isset($data['callback_date']) && $data['callback_date'] != '9999-12-31') ? $data['callback_date'] : null;
        $this->first_activation_date = (isset($data['first_activation_date'])) ? $data['first_activation_date'] : null;
        $this->date_1 = (isset($data['date_1'])) ? $data['date_1'] : null;
        $this->date_2 = (isset($data['date_2'])) ? $data['date_2'] : null;
        $this->date_3 = (isset($data['date_3'])) ? $data['date_3'] : null;
        $this->date_4 = (isset($data['date_4'])) ? $data['date_4'] : null;
        $this->date_5 = (isset($data['date_5'])) ? $data['date_5'] : null;
        $this->priority = (isset($data['priority'])) ? $data['priority'] : null;
        $this->origine = (isset($data['origine'])) ? $data['origine'] : null;
        $this->next_meeting_date = (isset($data['next_meeting_date'])) ? $data['next_meeting_date'] : null;
        $this->next_meeting_confirmed = (isset($data['next_meeting_confirmed'])) ? $data['next_meeting_confirmed'] : null;
        $this->contact_history = (isset($data['contact_history'])) ? json_decode($data['contact_history'], true) : null;
        $this->terms_of_use_validation_time = (isset($data['terms_of_use_validation_time'])) ? $data['terms_of_use_validation_time'] : null;
        $this->charter_validation_time = (isset($data['charter_validation_time'])) ? $data['charter_validation_time'] : null;
        $this->opt_out_time = (isset($data['opt_out_time'])) ? $data['opt_out_time'] : null;
        $this->notification_time = (isset($data['notification_time'])) ? $data['notification_time'] : null;
        $this->availability_begin = (isset($data['availability_begin'])) ? $data['availability_begin'] : null;
        $this->availability_end = (isset($data['availability_end']) && $data['availability_end'] != '9999-12-31') ? $data['availability_end'] : null;
        $this->availability_exceptions = (isset($data['availability_exceptions'])) ? json_decode($data['availability_exceptions'], true) : null;
        $this->availability_constraints = (isset($data['availability_constraints'])) ? json_decode($data['availability_constraints'], true) : null;
        $this->credits = (isset($data['credits'])) ? json_decode($data['credits'], true) : null;
        $this->shopping_cart = (isset($data['shopping_cart'])) ? json_decode($data['shopping_cart'], true) : null;
        $this->default_means_of_payment = (isset($data['default_means_of_payment'])) ? $data['default_means_of_payment'] : null;
        $this->transfer_order_id = (isset($data['transfer_order_id'])) ? $data['transfer_order_id'] : null;
        $this->transfer_order_date = (isset($data['transfer_order_date'])) ? $data['transfer_order_date'] : null;
        $this->bank_identifier = (isset($data['bank_identifier'])) ? $data['bank_identifier'] : null;
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
        $this->json_property_1 = (isset($data['json_property_1'])) ? json_decode($data['json_property_1'], true) : null;
        $this->json_property_2 = (isset($data['json_property_2'])) ? json_decode($data['json_property_2'], true) : null;
        $this->json_property_3 = (isset($data['json_property_3'])) ? json_decode($data['json_property_3'], true) : null;
        $this->json_property_4 = (isset($data['json_property_4'])) ? json_decode($data['json_property_4'], true) : null;
        $this->json_property_5 = (isset($data['json_property_5'])) ? json_decode($data['json_property_5'], true) : null;
        $this->comment_1 = (isset($data['comment_1'])) ? $data['comment_1'] : null;
        $this->comment_2 = (isset($data['comment_2'])) ? $data['comment_2'] : null;
        $this->comment_3 = (isset($data['comment_3'])) ? $data['comment_3'] : null;
        $this->comment_4 = (isset($data['comment_4'])) ? $data['comment_4'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->authentication_token = (isset($data['authentication_token'])) ? $data['authentication_token'] : null;
        $this->currently_updated_by = (isset($data['currently_updated_by'])) ? $data['currently_updated_by'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // Joined properties
        $this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
        $this->place_identifier = (isset($data['place_identifier'])) ? $data['place_identifier'] : null;
        
        $this->n_title = (isset($data['n_title'])) ? $data['n_title'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->birth_date = (isset($data['birth_date'])) ? $data['birth_date'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->adr_street = (isset($data['adr_street'])) ? $data['adr_street'] : null;
        $this->adr_extended = (isset($data['adr_extended'])) ? $data['adr_extended'] : null;
        $this->adr_post_office_box = (isset($data['adr_post_office_box'])) ? $data['adr_post_office_box'] : null;
        $this->adr_zip = (isset($data['adr_zip'])) ? $data['adr_zip'] : null;
        $this->adr_city = (isset($data['adr_city'])) ? $data['adr_city'] : null;
        $this->adr_state = (isset($data['adr_state'])) ? $data['adr_state'] : null;
        $this->adr_country = (isset($data['adr_country'])) ? $data['adr_country'] : null;
        $this->gender = (isset($data['gender'])) ? $data['gender'] : null;
        $this->nationality = (isset($data['nationality'])) ? $data['nationality'] : null;
        $this->photo_link_id = (isset($data['photo_link_id'])) ? $data['photo_link_id'] : null;
        $this->profile_tiny_1 = (isset($data['profile_tiny_1'])) ? $data['profile_tiny_1'] : null;
        $this->profile_tiny_2 = (isset($data['profile_tiny_2'])) ? $data['profile_tiny_2'] : null;
        $this->profile_tiny_3 = (isset($data['profile_tiny_3'])) ? $data['profile_tiny_3'] : null;
        $this->profile_tiny_4 = (isset($data['profile_tiny_4'])) ? $data['profile_tiny_4'] : null;
        $this->profile_tiny_5 = (isset($data['profile_tiny_5'])) ? $data['profile_tiny_5'] : null;
        $this->profile_tiny_6 = (isset($data['profile_tiny_6'])) ? $data['profile_tiny_6'] : null;
        $this->profile_tiny_7 = (isset($data['profile_tiny_7'])) ? $data['profile_tiny_7'] : null;
        $this->profile_tiny_8 = (isset($data['profile_tiny_8'])) ? $data['profile_tiny_8'] : null;
        $this->profile_tiny_9 = (isset($data['profile_tiny_9'])) ? $data['profile_tiny_9'] : null;
        $this->profile_tiny_10 = (isset($data['profile_tiny_10'])) ? $data['profile_tiny_10'] : null;
        $this->locale = (isset($data['locale'])) ? $data['locale'] : null;
        
        $this->n_title_2 = (isset($data['n_title_2'])) ? $data['n_title_2'] : null;
        $this->n_first_2 = (isset($data['n_first_2'])) ? $data['n_first_2'] : null;
        $this->n_last_2 = (isset($data['n_last_2'])) ? $data['n_last_2'] : null;
        $this->n_fn_2 = (isset($data['n_fn_2'])) ? $data['n_fn_2'] : null;
        $this->email_2 = (isset($data['email_2'])) ? $data['email_2'] : null;
        $this->birth_date_2 = (isset($data['birth_date_2'])) ? $data['birth_date_2'] : null;
        $this->tel_work_2 = (isset($data['tel_work_2'])) ? $data['tel_work_2'] : null;
        $this->tel_cell_2 = (isset($data['tel_cell_2'])) ? $data['tel_cell_2'] : null;
        $this->adr_street_2 = (isset($data['adr_street_2'])) ? $data['adr_street_2'] : null;
        $this->adr_extended_2 = (isset($data['adr_extended_2'])) ? $data['adr_extended_2'] : null;
        $this->adr_post_office_box_2 = (isset($data['adr_post_office_box_2'])) ? $data['adr_post_office_box_2'] : null;
        $this->adr_zip_2 = (isset($data['adr_zip_2'])) ? $data['adr_zip_2'] : null;
        $this->adr_city_2 = (isset($data['adr_city_2'])) ? $data['adr_city_2'] : null;
        $this->adr_state_2 = (isset($data['adr_state_2'])) ? $data['adr_state_2'] : null;
        $this->adr_country_2 = (isset($data['adr_country_2'])) ? $data['adr_country_2'] : null;
        
        $this->n_title_3 = (isset($data['n_title_3'])) ? $data['n_title_3'] : null;
        $this->n_first_3 = (isset($data['n_first_3'])) ? $data['n_first_3'] : null;
        $this->n_last_3 = (isset($data['n_last_3'])) ? $data['n_last_3'] : null;
        $this->n_fn_3 = (isset($data['n_fn_3'])) ? $data['n_fn_3'] : null;
        $this->email_3 = (isset($data['email_3'])) ? $data['email_3'] : null;
        $this->birth_date_3 = (isset($data['birth_date_3'])) ? $data['birth_date_3'] : null;
        $this->tel_work_3 = (isset($data['tel_work_3'])) ? $data['tel_work_3'] : null;
        $this->tel_cell_3 = (isset($data['tel_cell_3'])) ? $data['tel_cell_3'] : null;
        $this->adr_street_3 = (isset($data['adr_street_3'])) ? $data['adr_street_3'] : null;
        $this->adr_extended_3 = (isset($data['adr_extended_3'])) ? $data['adr_extended_3'] : null;
        $this->adr_post_office_box_3 = (isset($data['adr_post_office_box_3'])) ? $data['adr_post_office_box_3'] : null;
        $this->adr_zip_3 = (isset($data['adr_zip_3'])) ? $data['adr_zip_3'] : null;
        $this->adr_city_3 = (isset($data['adr_city_3'])) ? $data['adr_city_3'] : null;
        $this->adr_state_3 = (isset($data['adr_state_3'])) ? $data['adr_state_3'] : null;
        $this->adr_country_3 = (isset($data['adr_country_3'])) ? $data['adr_country_3'] : null;
        
        $this->n_title_4 = (isset($data['n_title_4'])) ? $data['n_title_4'] : null;
        $this->n_first_4 = (isset($data['n_first_4'])) ? $data['n_first_4'] : null;
        $this->n_last_4 = (isset($data['n_last_4'])) ? $data['n_last_4'] : null;
        $this->n_fn_4 = (isset($data['n_fn_4'])) ? $data['n_fn_4'] : null;
        $this->email_4 = (isset($data['email_4'])) ? $data['email_4'] : null;
        $this->birth_date_4 = (isset($data['birth_date_4'])) ? $data['birth_date_4'] : null;
        $this->tel_work_4 = (isset($data['tel_work_4'])) ? $data['tel_work_4'] : null;
        $this->tel_cell_4 = (isset($data['tel_cell_4'])) ? $data['tel_cell_4'] : null;
        $this->adr_street_4 = (isset($data['adr_street_4'])) ? $data['adr_street_4'] : null;
        $this->adr_extended_4 = (isset($data['adr_extended_4'])) ? $data['adr_extended_4'] : null;
        $this->adr_post_office_box_4 = (isset($data['adr_post_office_box_4'])) ? $data['adr_post_office_box_4'] : null;
        $this->adr_zip_4 = (isset($data['adr_zip_4'])) ? $data['adr_zip_4'] : null;
        $this->adr_city_4 = (isset($data['adr_city_4'])) ? $data['adr_city_4'] : null;
        $this->adr_state_4 = (isset($data['adr_state_4'])) ? $data['adr_state_4'] : null;
        $this->adr_country_4 = (isset($data['adr_country_4'])) ? $data['adr_country_4'] : null;
        
        $this->n_title_5 = (isset($data['n_title_5'])) ? $data['n_title_5'] : null;
        $this->n_first_5 = (isset($data['n_first_5'])) ? $data['n_first_5'] : null;
        $this->n_last_5 = (isset($data['n_last_5'])) ? $data['n_last_5'] : null;
        $this->n_fn_5 = (isset($data['n_fn_5'])) ? $data['n_fn_5'] : null;
        $this->email_5 = (isset($data['email_5'])) ? $data['email_5'] : null;
        $this->birth_date_5 = (isset($data['birth_date_5'])) ? $data['birth_date_5'] : null;
        $this->tel_work_5 = (isset($data['tel_work_5'])) ? $data['tel_work_5'] : null;
        $this->tel_cell_5 = (isset($data['tel_cell_5'])) ? $data['tel_cell_5'] : null;
        $this->adr_street_5 = (isset($data['adr_street_5'])) ? $data['adr_street_5'] : null;
        $this->adr_extended_5 = (isset($data['adr_extended_5'])) ? $data['adr_extended_5'] : null;
        $this->adr_post_office_box_5 = (isset($data['adr_post_office_box_5'])) ? $data['adr_post_office_box_5'] : null;
        $this->adr_zip_5 = (isset($data['adr_zip_5'])) ? $data['adr_zip_5'] : null;
        $this->adr_city_5 = (isset($data['adr_city_5'])) ? $data['adr_city_5'] : null;
        $this->adr_state_5 = (isset($data['adr_state_5'])) ? $data['adr_state_5'] : null;
        $this->adr_country_5 = (isset($data['adr_country_5'])) ? $data['adr_country_5'] : null;
    }

    public function getProperties($type = null, $description = null)
    {
    	$context = Context::getCurrent();
    	$data = array();    	 
    	$data['id'] = (int) $this->id;
    	$data['status'] = $this->status;
    	$data['type'] =  ($this->type) ? $this->type : null;
    	$data['place_id'] = (int) $this->place_id;
    	$data['identifier'] = $this->identifier;
    	$data['name'] = $this->name;
    	$data['basket'] = $this->basket;
    	$data['opening_date'] =  ($this->opening_date) ? $this->opening_date : null;
    	$data['closing_date'] =  ($this->closing_date) ? $this->closing_date : null;
    	$data['callback_date'] =  ($this->callback_date) ? $this->callback_date : null;
    	$data['first_activation_date'] =  ($this->first_activation_date) ? $this->first_activation_date : null;
    	$data['date_1'] =  ($this->date_1) ? $this->date_1 : null;
    	$data['date_2'] =  ($this->date_2) ? $this->date_2 : null;
    	$data['date_3'] =  ($this->date_3) ? $this->date_3 : null;
    	$data['date_4'] =  ($this->date_4) ? $this->date_4 : null;
    	$data['date_5'] =  ($this->date_5) ? $this->date_5 : null;
    	$data['priority'] =  ($this->priority) ? $this->priority : null;
    	$data['origine'] =  ($this->origine) ? $this->origine : null;
    	$data['next_meeting_date'] =  ($this->next_meeting_date) ? $this->next_meeting_date : null;
    	$data['next_meeting_confirmed'] =  ($this->next_meeting_confirmed) ? $this->next_meeting_confirmed : null;
    	$data['contact_history'] = $this->contact_history;
    	$data['notification_time'] =  $this->notification_time;
    	$data['terms_of_use_validation_time'] =  $this->terms_of_use_validation_time;
    	$data['charter_validation_time'] =  $this->charter_validation_time;
    	$data['availability_begin'] = ($this->availability_begin) ? $this->availability_begin : null;
    	$data['availability_end'] = ($this->availability_end) ? $this->availability_end : null;
    	$data['availability_exceptions'] = $this->availability_exceptions;
    	$data['availability_constraints'] = $this->availability_constraints;
    	$data['credits'] = $this->credits;
    	$data['shopping_cart'] = $this->shopping_cart;
    	$data['default_means_of_payment'] = $this->default_means_of_payment;
    	$data['transfer_order_id'] = $this->transfer_order_id;
    	$data['transfer_order_date'] = ($this->transfer_order_date) ? $this->transfer_order_date : null;
    	$data['bank_identifier'] = $this->bank_identifier;
    	$data['property_1'] =  ($this->property_1) ? $this->property_1 : null;
    	$data['property_2'] =  ($this->property_2) ? $this->property_2 : null;
    	$data['property_3'] =  ($this->property_3) ? $this->property_3 : null;
    	$data['property_4'] =  ($this->property_4) ? $this->property_4 : null;
    	$data['property_5'] =  ($this->property_5) ? $this->property_5 : null;
    	$data['property_6'] =  ($this->property_6) ? $this->property_6 : null;
    	$data['property_7'] =  ($this->property_7) ? $this->property_7 : null;
    	$data['property_8'] =  ($this->property_8) ? $this->property_8 : null;
    	$data['property_9'] =  ($this->property_9) ? $this->property_9 : null;
    	$data['property_10'] =  ($this->property_10) ? $this->property_10 : null;
    	$data['property_11'] =  ($this->property_11) ? $this->property_11 : null;
    	$data['property_12'] =  ($this->property_12) ? $this->property_12 : null;
    	$data['property_13'] =  ($this->property_13) ? $this->property_13 : null;
    	$data['property_14'] =  ($this->property_14) ? $this->property_14 : null;
    	$data['property_15'] =  ($this->property_15) ? $this->property_15 : null;
    	$data['property_16'] =  ($this->property_16) ? $this->property_16 : null;
    	$data['json_property_1'] = $this->json_property_1;
    	$data['json_property_2'] = $this->json_property_2;
    	$data['json_property_3'] = $this->json_property_3;
    	$data['json_property_4'] = $this->json_property_4;
    	$data['json_property_5'] = $this->json_property_5;
    	$data['comment_1'] =  ($this->comment_1) ? $this->comment_1 : null;
    	$data['comment_2'] =  ($this->comment_2) ? $this->comment_2 : null;
    	$data['comment_3'] =  ($this->comment_3) ? $this->comment_3 : null;
    	$data['comment_4'] =  ($this->comment_4) ? $this->comment_4 : null;
    	$data['audit'] = $this->audit;
    	$data['authentication_token'] = $this->authentication_token;
    	$data['currently_updated_by'] = $this->currently_updated_by;

    	// Joined properties
    	$data['place_caption'] = $this->place_caption;

    	$data['contact_1_id'] = $this->contact_1_id;
    	$data['contact_1_status'] = $this->contact_1_status;
    	$data['n_title'] = $this->n_title;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['n_fn'] = $this->n_fn;
    	$data['email'] = $this->email;
    	$data['birth_date'] = $this->birth_date;
    	$data['tel_work'] = $this->tel_work;
    	$data['tel_cell'] = $this->tel_cell;
    	$data['adr_street'] = $this->adr_street;
    	$data['adr_extended'] = $this->adr_extended;
    	$data['adr_post_office_box'] = $this->adr_post_office_box;
    	$data['adr_zip'] = $this->adr_zip;
    	$data['adr_city'] = $this->adr_city;
    	$data['adr_state'] = $this->adr_state;
    	$data['adr_country'] = $this->adr_country;
    	$data['address'] = '';
    	if ($this->adr_street) $data['address'] .= $this->adr_street;
    	if ($this->adr_extended) $data['address'] .= ' '.$this->adr_extended;
    	if ($this->adr_post_office_box) $data['address'] .= ' - '.$this->adr_post_office_box;
    	if ($this->adr_zip) $data['address'] .= ' - '.$this->adr_zip;
    	if ($this->adr_city) $data['address'] .= ' '.$this->adr_city;
    	if ($this->adr_state) $data['address'] .= ' - '.$this->adr_state;
    	if ($this->adr_country) $data['address'] .= ' - '.$this->adr_country;
    	$data['gender'] = $this->gender;
    	$data['nationality'] = $this->nationality;
    	$data['photo_link_id'] = $this->photo_link_id;
    	$data['profile_tiny_1'] = $this->profile_tiny_1;
    	$data['profile_tiny_2'] = $this->profile_tiny_2;
    	$data['profile_tiny_3'] = $this->profile_tiny_3;
    	$data['profile_tiny_4'] = $this->profile_tiny_4;
    	$data['profile_tiny_5'] = $this->profile_tiny_5;
    	$data['profile_tiny_6'] = $this->profile_tiny_6;
    	$data['profile_tiny_7'] = $this->profile_tiny_7;
    	$data['profile_tiny_8'] = $this->profile_tiny_8;
    	$data['profile_tiny_9'] = $this->profile_tiny_9;
    	$data['profile_tiny_10'] = $this->profile_tiny_10;
    	$data['locale'] = $this->locale;
    	 
    	$data['contact_2_id'] = $this->contact_2_id;
    	$data['contact_2_status'] = $this->contact_2_status;
    	$data['n_title_2'] = $this->n_title_2;
    	$data['n_first_2'] = $this->n_first_2;
    	$data['n_last_2'] = $this->n_last_2;
    	$data['n_fn_2'] = $this->n_fn_2;
    	$data['email_2'] = $this->email_2;
    	$data['birth_date_2'] = $this->birth_date_2;
    	$data['tel_work_2'] = $this->tel_work_2;
    	$data['tel_cell_2'] = $this->tel_cell_2;
    	$data['address_2'] = '';
    	if ($this->adr_street_2) $data['address_2'] .= $this->adr_street_2;
    	if ($this->adr_extended_2) $data['address_2'] .= ' '.$this->adr_extended_2;
    	if ($this->adr_post_office_box_2) $data['address_2'] .= ' - '.$this->adr_post_office_box_2;
    	if ($this->adr_zip_2) $data['address_2'] .= ' - '.$this->adr_zip_2;
    	if ($this->adr_city_2) $data['address_2'] .= ' '.$this->adr_city_2;
    	if ($this->adr_state_2) $data['address_2'] .= ' - '.$this->adr_state_2;
    	if ($this->adr_country_2) $data['address_2'] .= ' - '.$this->adr_country_2;
    	 
    	$data['contact_3_id'] = $this->contact_3_id;
    	$data['contact_3_status'] = $this->contact_3_status;
    	$data['n_title_3'] = $this->n_title_3;
    	$data['n_first_3'] = $this->n_first_3;
    	$data['n_last_3'] = $this->n_last_3;
    	$data['n_fn_3'] = $this->n_fn_3;
    	$data['email_3'] = $this->email_3;
    	$data['birth_date_3'] = $this->birth_date_3;
    	$data['tel_work_3'] = $this->tel_work_3;
    	$data['tel_cell_3'] = $this->tel_cell_3;
    	$data['address_3'] = '';
    	if ($this->adr_street_3) $data['address_3'] .= $this->adr_street_3;
    	if ($this->adr_extended_3) $data['address_3'] .= ' '.$this->adr_extended_3;
    	if ($this->adr_post_office_box_3) $data['address_3'] .= ' - '.$this->adr_post_office_box_3;
    	if ($this->adr_zip_3) $data['address_3'] .= ' - '.$this->adr_zip_3;
    	if ($this->adr_city_3) $data['address_3'] .= ' '.$this->adr_city_3;
    	if ($this->adr_state_3) $data['address_3'] .= ' - '.$this->adr_state_3;
    	if ($this->adr_country_3) $data['address_3'] .= ' - '.$this->adr_country_3;
    	 
    	$data['contact_4_id'] = $this->contact_4_id;
    	$data['contact_4_status'] = $this->contact_4_status;
    	$data['n_title_4'] = $this->n_title_4;
    	$data['n_first_4'] = $this->n_first_4;
    	$data['n_last_4'] = $this->n_last_4;
    	$data['n_fn_4'] = $this->n_fn_4;
    	$data['email_4'] = $this->email_4;
    	$data['birth_date_4'] = $this->birth_date_4;
    	$data['tel_work_4'] = $this->tel_work_4;
    	$data['tel_cell_4'] = $this->tel_cell_4;
    	$data['address_4'] = '';
    	if ($this->adr_street_4) $data['address_4'] .= $this->adr_street_4;
    	if ($this->adr_extended_4) $data['address_4'] .= ' '.$this->adr_extended_4;
    	if ($this->adr_post_office_box_4) $data['address_4'] .= ' - '.$this->adr_post_office_box_4;
    	if ($this->adr_zip_4) $data['address_4'] .= ' - '.$this->adr_zip_4;
    	if ($this->adr_city_4) $data['address_4'] .= ' '.$this->adr_city_4;
    	if ($this->adr_state_4) $data['address_4'] .= ' - '.$this->adr_state_4;
    	if ($this->adr_country_4) $data['address_4'] .= ' - '.$this->adr_country_4;
    	 
    	$data['contact_5_id'] = $this->contact_5_id;
    	$data['contact_5_status'] = $this->contact_5_status;
    	$data['n_title_5'] = $this->n_title_5;
    	$data['n_first_5'] = $this->n_first_5;
    	$data['n_last_5'] = $this->n_last_5;
    	$data['n_fn_5'] = $this->n_fn_5;
    	$data['email_5'] = $this->email_5;
    	$data['birth_date_5'] = $this->birth_date_5;
    	$data['tel_work_5'] = $this->tel_work_5;
    	$data['tel_cell_5'] = $this->tel_cell_5;
    	$data['address_5'] = '';
    	if ($this->adr_street_5) $data['address_5'] .= $this->adr_street_5;
    	if ($this->adr_extended_5) $data['address_5'] .= ' '.$this->adr_extended_5;
    	if ($this->adr_post_office_box_5) $data['address_5'] .= ' - '.$this->adr_post_office_box_5;
    	if ($this->adr_zip_5) $data['address_5'] .= ' - '.$this->adr_zip_5;
    	if ($this->adr_city_5) $data['address_5'] .= ' '.$this->adr_city_5;
    	if ($this->adr_state_5) $data['address_5'] .= ' - '.$this->adr_state_5;
    	if ($this->adr_country_5) $data['address_5'] .= ' - '.$this->adr_country_5;

    	if ($this->contact_2_status == 'invoice') {
	    	$data['invoice_n_title'] = $this->n_title_2;
	    	$data['invoice_n_first'] = $this->n_first_2;
	    	$data['invoice_n_last'] = $this->n_last_2;
	    	$data['invoice_n_fn'] = $this->n_fn_2;
	    	$data['invoice_email'] = $this->email_2;
	    	$data['invoice_birth_date'] = $this->birth_date_2;
	    	$data['invoice_tel_work'] = $this->tel_work_2;
	    	$data['invoice_tel_cell'] = $this->tel_cell_2;
	    	$data['invoice_adr_street'] = $this->adr_street_2;
	    	$data['invoice_adr_extended'] = $this->adr_extended_2;
	    	$data['invoice_adr_post_office_box'] = $this->adr_post_office_box_2;
	    	$data['invoice_adr_zip'] = $this->adr_zip_2;
	    	$data['invoice_adr_city'] = $this->adr_city_2;
	    	$data['invoice_adr_state'] = $this->adr_state_2;
	    	$data['invoice_adr_country'] = $this->adr_country_2;
    	}
    	elseif ($this->contact_3_status == 'invoice') {
    		$data['invoice_n_title'] = $this->n_title_3;
    		$data['invoice_n_first'] = $this->n_first_3;
    		$data['invoice_n_last'] = $this->n_last_3;
    		$data['invoice_n_fn'] = $this->n_fn_3;
    		$data['invoice_email'] = $this->email_3;
    		$data['invoice_birth_date'] = $this->birth_date_3;
    		$data['invoice_tel_work'] = $this->tel_work_3;
    		$data['invoice_tel_cell'] = $this->tel_cell_3;
    		$data['invoice_adr_street'] = $this->adr_street_3;
    		$data['invoice_adr_extended'] = $this->adr_extended_3;
    		$data['invoice_adr_post_office_box'] = $this->adr_post_office_box_3;
    		$data['invoice_adr_zip'] = $this->adr_zip_3;
    		$data['invoice_adr_city'] = $this->adr_city_3;
    		$data['invoice_adr_state'] = $this->adr_state_3;
    		$data['invoice_adr_country'] = $this->adr_country_3;
    	}
    	elseif ($this->contact_4_status == 'invoice') {
    		$data['invoice_n_title'] = $this->n_title_4;
    		$data['invoice_n_first'] = $this->n_first_4;
    		$data['invoice_n_last'] = $this->n_last_4;
    		$data['invoice_n_fn'] = $this->n_fn_4;
    		$data['invoice_email'] = $this->email_4;
    		$data['invoice_birth_date'] = $this->birth_date_4;
    		$data['invoice_tel_work'] = $this->tel_work_4;
    		$data['invoice_tel_cell'] = $this->tel_cell_4;
    		$data['invoice_adr_street'] = $this->adr_street_4;
    		$data['invoice_adr_extended'] = $this->adr_extended_4;
    		$data['invoice_adr_post_office_box'] = $this->adr_post_office_box_4;
    		$data['invoice_adr_zip'] = $this->adr_zip_4;
    		$data['invoice_adr_city'] = $this->adr_city_4;
    		$data['invoice_adr_state'] = $this->adr_state_4;
    		$data['invoice_adr_country'] = $this->adr_country_4;
    	}
    	elseif ($this->contact_5_status == 'invoice') {
    		$data['invoice_n_title'] = $this->n_title_5;
    		$data['invoice_n_first'] = $this->n_first_5;
    		$data['invoice_n_last'] = $this->n_last_5;
    		$data['invoice_n_fn'] = $this->n_fn_5;
    		$data['invoice_email'] = $this->email_5;
    		$data['invoice_birth_date'] = $this->birth_date_5;
    		$data['invoice_tel_work'] = $this->tel_work_5;
    		$data['invoice_tel_cell'] = $this->tel_cell_5;
    		$data['invoice_adr_street'] = $this->adr_street_5;
    		$data['invoice_adr_extended'] = $this->adr_extended_5;
    		$data['invoice_adr_post_office_box'] = $this->adr_post_office_box_5;
    		$data['invoice_adr_zip'] = $this->adr_zip_5;
    		$data['invoice_adr_city'] = $this->adr_city_5;
    		$data['invoice_adr_state'] = $this->adr_state_5;
    		$data['invoice_adr_country'] = $this->adr_country_5;
    	}
    	else {
    		$data['invoice_n_title'] = $this->n_title;
    		$data['invoice_n_first'] = $this->n_first;
    		$data['invoice_n_last'] = $this->n_last;
    		$data['invoice_n_fn'] = $this->n_fn;
    		$data['invoice_email'] = $this->email;
    		$data['invoice_birth_date'] = $this->birth_date;
    		$data['invoice_tel_work'] = $this->tel_work;
    		$data['invoice_tel_cell'] = $this->tel_cell;
    		$data['invoice_adr_street'] = $this->adr_street;
    		$data['invoice_adr_extended'] = $this->adr_extended;
    		$data['invoice_adr_post_office_box'] = $this->adr_post_office_box;
    		$data['invoice_adr_zip'] = $this->adr_zip;
    		$data['invoice_adr_city'] = $this->adr_city;
    		$data['invoice_adr_state'] = $this->adr_state;
    		$data['invoice_adr_country'] = $this->adr_country;
    	}
    	 
    	$data['update_time'] = $this->update_time;

    	if ($description) {
	    	$computedProperties = array();
	    	foreach ($description['properties'] as $propertyId => $property) if ($property['type'] == 'computed') $computedProperties[$propertyId] = $property;
    				
			foreach ($computedProperties as $propertyId => $property) {
				if (array_key_exists('function', $property)) $data[$propertyId] = call_user_func($property['function'], $this);
				elseif (array_key_exists('rules', $property)) {
					$data[$propertyId] = null;
					foreach ($property['rules'] as $ruleId => $rule) {
						$matched = true;
						foreach ($rule as $predicateId => $unused) if (!$data[$predicateId]) $matched = false;
						if ($matched) {
							$data[$propertyId] = $ruleId;
							break;
						}
					}
				}
			}
    	}
    	 
    	if ($description) {
	    	foreach ($description['properties'] as $propertyId => $property) {
	    		if ($property['private'] && $data[$propertyId]) {
	    			$value = $context->getSecurityAgent()->unprotectPrivateData($data[$propertyId]);
	    			if ($value) $data[$propertyId] = $value;
	    			else unset($data[$propertyId]);
	    		}
	    	}
    	}

    	return $data;
    }
    
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['closing_date'] =  ($this->closing_date) ? $this->closing_date : '9999-12-31';
    	$data['contact_history'] = json_encode($this->contact_history);
    	$data['availability_end'] =  ($this->availability_end) ? $this->availability_end : '9999-12-31';
    	$data['availability_exceptions'] = json_encode($this->availability_exceptions);
    	$data['availability_constraints'] = json_encode($this->availability_constraints);
    	$data['credits'] = json_encode($this->credits, JSON_PRETTY_PRINT);
    	$data['json_property_1'] = json_encode($this->json_property_1);
    	$data['json_property_2'] = json_encode($this->json_property_2);
    	$data['json_property_3'] = json_encode($this->json_property_3);
    	$data['json_property_4'] = json_encode($this->json_property_4);
    	$data['json_property_5'] = json_encode($this->json_property_5);
    	$data['audit'] = json_encode($this->audit);

    	unset($data['place_caption']);

    	unset($data['n_title']);
    	unset($data['n_first']);
    	unset($data['n_last']);
    	unset($data['n_fn']);
    	unset($data['email']);
    	unset($data['birth_date']);
    	unset($data['tel_work']);
    	unset($data['tel_cell']);
    	unset($data['adr_street']);
    	unset($data['adr_extended']);
    	unset($data['adr_post_office_box']);
    	unset($data['adr_zip']);
    	unset($data['adr_city']);
    	unset($data['adr_state']);
    	unset($data['adr_country']);
    	unset($data['address']);
    	unset($data['gender']);
    	unset($data['nationality']);
    	unset($data['photo_link_id']);
    	unset($data['profile_tiny_1']);
    	unset($data['profile_tiny_2']);
    	unset($data['profile_tiny_3']);
    	unset($data['profile_tiny_4']);
    	unset($data['profile_tiny_5']);
    	unset($data['profile_tiny_6']);
    	unset($data['profile_tiny_7']);
    	unset($data['profile_tiny_8']);
    	unset($data['profile_tiny_9']);
    	unset($data['profile_tiny_10']);
    	unset($data['locale']);
    	 
    	unset($data['n_title_2']);
    	unset($data['n_first_2']);
    	unset($data['n_last_2']);
    	unset($data['n_fn_2']);
    	unset($data['email_2']);
    	unset($data['birth_date_2']);
    	unset($data['tel_work_2']);
    	unset($data['tel_cell_2']);
    	unset($data['address_2']);
    	 
    	unset($data['n_title_3']);
    	unset($data['n_first_3']);
    	unset($data['n_last_3']);
    	unset($data['n_fn_3']);
    	unset($data['email_3']);
    	unset($data['birth_date_3']);
    	unset($data['tel_work_3']);
    	unset($data['tel_cell_3']);
    	unset($data['address_3']);
    	 
    	unset($data['n_title_4']);
    	unset($data['n_first_4']);
    	unset($data['n_last_4']);
    	unset($data['n_fn_4']);
    	unset($data['email_4']);
    	unset($data['birth_date_4']);
    	unset($data['tel_work_4']);
    	unset($data['tel_cell_4']);
    	unset($data['address_4']);
    	 
    	unset($data['n_title_5']);
    	unset($data['n_first_5']);
    	unset($data['n_last_5']);
    	unset($data['n_fn_5']);
    	unset($data['email_5']);
    	unset($data['birth_date_5']);
    	unset($data['tel_work_5']);
    	unset($data['tel_cell_5']);
    	unset($data['address_5']);
    	
    	unset($data['invoice_n_title']);
    	unset($data['invoice_n_first']);
    	unset($data['invoice_n_last']);
    	unset($data['invoice_n_fn']);
    	unset($data['invoice_email']);
    	unset($data['invoice_birth_date']);
    	unset($data['invoice_tel_work']);
    	unset($data['invoice_tel_cell']);
    	unset($data['invoice_adr_street']);
    	unset($data['invoice_adr_extended']);
    	unset($data['invoice_adr_post_office_box']);
    	unset($data['invoice_adr_zip']);
    	unset($data['invoice_adr_city']);
    	unset($data['invoice_adr_state']);
    	unset($data['invoice_adr_country']);
    	 
    	return $data;
    }
    
    public static function getList($type, $params, $order = '+name', $limit = 50, $columns = null, $pageNumber = false, $itemCountPerPage = false)
    {
    	// Retrieve the context and the account description for the given type
    	$context = Context::getCurrent();
    	if ($type) $description = Account::getDescription($type);
    	else $description = null;
    	
    	$order = explode(',', $order);
    	foreach ($order as &$criterion) $criterion = substr($criterion, 1).' '.((substr($criterion, 0, 1) == '-') ? 'DESC' : 'ASC');
    	$select = Account::getTable()->getSelect()
			->join('core_place', 'core_account.place_id = core_place.id', array('place_caption' => 'caption', 'place_identifier' => 'identifier'), 'left')
			->join('core_vcard', 'core_account.contact_1_id = core_vcard.id', array('n_title', 'n_first', 'n_last', 'n_fn', 'email', 'birth_date', 'tel_work', 'tel_cell', 'photo_link_id', 'adr_street', 'adr_extended', 'adr_post_office_box', 'adr_zip', 'adr_city', 'adr_state', 'adr_country', 'gender', 'nationality', 'profile_tiny_1' => 'tiny_1', 'profile_tiny_2' => 'tiny_2', 'profile_tiny_3' => 'tiny_3', 'profile_tiny_4' => 'tiny_4', 'profile_tiny_5' => 'tiny_5', 'profile_tiny_6' => 'tiny_6', 'profile_tiny_7' => 'tiny_7', 'profile_tiny_8' => 'tiny_8', 'profile_tiny_9' => 'tiny_9', 'profile_tiny_10' => 'tiny_10', 'locale'), 'left')
			->join(array('contact_2' => 'core_vcard'), 'core_account.contact_2_id = contact_2.id', array('n_title_2' =>'n_title', 'n_first_2' => 'n_first', 'n_last_2' => 'n_last', 'n_fn_2' => 'n_fn', 'email_2' => 'email', 'birth_date_2' => 'birth_date', 'tel_work_2' => 'tel_work', 'tel_cell_2' => 'tel_cell', 'adr_street_2' => 'adr_street', 'adr_extended_2' => 'adr_extended', 'adr_post_office_box_2' => 'adr_post_office_box', 'adr_zip_2' => 'adr_zip', 'adr_city_2' => 'adr_city', 'adr_state_2' => 'adr_state', 'adr_country_2' => 'adr_country'), 'left')
			->join(array('contact_3' => 'core_vcard'), 'core_account.contact_3_id = contact_3.id', array('n_title_3' =>'n_title', 'n_first_3' => 'n_first', 'n_last_3' => 'n_last', 'n_fn_3' => 'n_fn', 'email_3' => 'email', 'birth_date_3' => 'birth_date', 'tel_work_3' => 'tel_work', 'tel_cell_3' => 'tel_cell', 'adr_street_3' => 'adr_street', 'adr_extended_3' => 'adr_extended', 'adr_post_office_box_3' => 'adr_post_office_box', 'adr_zip_3' => 'adr_zip', 'adr_city_3' => 'adr_city', 'adr_state_3' => 'adr_state', 'adr_country_3' => 'adr_country'), 'left')
			->join(array('contact_4' => 'core_vcard'), 'core_account.contact_4_id = contact_4.id', array('n_title_4' =>'n_title', 'n_first_4' => 'n_first', 'n_last_4' => 'n_last', 'n_fn_4' => 'n_fn', 'email_4' => 'email', 'birth_date_4' => 'birth_date', 'tel_work_4' => 'tel_work', 'tel_cell_4' => 'tel_cell', 'adr_street_4' => 'adr_street', 'adr_extended_4' => 'adr_extended', 'adr_post_office_box_4' => 'adr_post_office_box', 'adr_zip_4' => 'adr_zip', 'adr_city_4' => 'adr_city', 'adr_state_4' => 'adr_state', 'adr_country_4' => 'adr_country'), 'left')
			->join(array('contact_5' => 'core_vcard'), 'core_account.contact_5_id = contact_5.id', array('n_title_5' =>'n_title', 'n_first_5' => 'n_first', 'n_last_5' => 'n_last', 'n_fn_5' => 'n_fn', 'email_5' => 'email', 'birth_date_5' => 'birth_date', 'tel_work_5' => 'tel_work', 'tel_cell_5' => 'tel_cell', 'adr_street_5' => 'adr_street', 'adr_extended_5' => 'adr_extended', 'adr_post_office_box_5' => 'adr_post_office_box', 'adr_zip_5' => 'adr_zip', 'adr_city_5' => 'adr_city', 'adr_state_5' => 'adr_state', 'adr_country_5' => 'adr_country'), 'left')
			->order($order);

		if ($columns) $select->columns($columns);

		$where = new Where;
		if ($type) $where->equalTo('type', $type);
		$where->notEqualTo('core_account.status', 'deleted');

		// Set the filters
		foreach ($params as $propertyId => $value) {
			if (in_array(substr($propertyId, 0, 4), array('min_', 'max_'))) $propertyKey = substr($propertyId, 4);
			else $propertyKey = $propertyId;
			$property = Account::getConfig($type)[$propertyKey];
			$entity = Account::$model['properties'][$propertyKey]['entity'];
			$column = Account::$model['properties'][$propertyKey]['column'];
				
			if ($propertyId == 'gender') $where->equalTo('core_vcard.gender', $value);
			elseif ($propertyId == 'adr_zip') $where->like('core_vcard.adr_zip', '%'.$value.'%');
			elseif ($propertyId == 'locale') $where->like('core_vcard.locale', '%'.$value.'%');
			elseif ($propertyId == 'min_availability') $where->greaterThanOrEqualTo('availability_end', $value);
			elseif ($propertyId == 'max_availability') $where->lessThanOrEqualTo('availability_begin', $value);
			elseif ($propertyId == 'availability') $where->like('availability_constraints', '%'.$value.'%');
			elseif ($propertyId == 'next_meeting_confirmed') {
				$where->isNotNull('next_meeting_date');
				if ($value) $where->isNotNull('next_meeting_confirmed');
				else {
					$where->isNull('next_meeting_confirmed');
				}
			}
			elseif (substr($propertyId, 0, 4) == 'min_') {
				if (in_array($property['type'], ['date', 'datetime']) && !$value) $where->isNull($entity.'.'.substr($propertyId, 4));
				else $where->greaterThanOrEqualTo($entity.'.'.substr($propertyId, 4), $value);
			}
			elseif (substr($propertyId, 0, 4) == 'max_') {
				if (in_array($property['type'], ['date', 'datetime']) && !$value) $where->isNull($entity.'.'.substr($propertyId, 4));
				else $where->lessThanOrEqualTo($entity.'.'.substr($propertyId, 4), $value);
			}
			elseif (strpos($value, ',')) $where->in($entity.'.'.$column, array_map('trim', explode(',', $value)));
			elseif ($value == '*') $where->notEqualTo($entity.'.'.$column, '');
			elseif ($property['type'] == 'select') {
				if (array_key_exists('multiple', $property) && $property['multiple']) $where->like($entity.'.'.$column, '%'.$value.'%');
				else $where->equalTo($entity.'.'.$column, $value);
			}
			elseif ($property['type'] == 'multiselect') $where->like($entity.'.'.$column, '%'.$value.'%');
			elseif ($property['type'] == 'table') $where->equalTo($entity.'.'.$column, $value);
			elseif ($value === null) $where->isNull($column);
			else $where->like($entity.'.'.$column, '%'.$value.'%');
		}

    	$select->where($where);
		$cursor = Account::getTable()->selectWith($select, ($pageNumber) ? new Account : null);
		if ($pageNumber) {
			$cursor->setCurrentPageNumber($pageNumber);
			$cursor->setItemCountPerPage($itemCountPerPage);
		}

		$accounts = array();
		$i = 0;
		foreach ($cursor as $account) {
			$account->properties = $account->getProperties($account->type, $description);

			// Filter on authorized perimeter
			$keep = true;
			if (array_key_exists('p-pit-admin', $context->getPerimeters())) {
				foreach ($context->getPerimeters()['p-pit-admin'] as $key => $values) {
					$keep2 = false;
					foreach ($values as $value) {
						if ($account->properties[$key] == $value) $keep2 = true;
					}
					if (!$keep2) $keep = false;
				}
			}
			if (array_key_exists($type, $context->getPerimeters())) {
				foreach ($context->getPerimeters()[$type] as $key => $values) {
					if (array_key_exists($key, $account->properties) && $account->properties[$key]) {
						$keep2 = false;
						foreach ($values as $value) {
							if (!array_key_exists($key, $account->properties)) $keep2 = true;
							elseif ($account->properties[$key] == $value) $keep2 = true;
						}
						if (!$keep2) $keep = false;
					}
				}
			}
			if ($keep) {
				$i++;
				if ($limit && $i > $limit) break;
				$accounts[$account->id] = $account;
			}
		}
		return $accounts;
    }

    public function denormalize() {

    	$this->n_title = $this->contact_1->n_title;
    	$this->n_first = $this->contact_1->n_first;
    	$this->n_last = $this->contact_1->n_last;
    	$this->n_fn = $this->contact_1->n_fn;
    	$this->email = $this->contact_1->email;
    	$this->birth_date = $this->contact_1->birth_date;
    	$this->tel_work = $this->contact_1->tel_work;
    	$this->tel_cell = $this->contact_1->tel_cell;
    	$this->adr_street = $this->contact_1->adr_street;
    	$this->adr_extended = $this->contact_1->adr_extended;
    	$this->adr_post_office_box = $this->contact_1->adr_post_office_box;
    	$this->adr_zip = $this->contact_1->adr_zip;
    	$this->adr_city = $this->contact_1->adr_city;
    	$this->adr_state = $this->contact_1->adr_state;
    	$this->adr_country = $this->contact_1->adr_country;
    	$this->gender = $this->contact_1->gender;
    	$this->nationality = $this->contact_1->nationality;
    	$this->is_notified = $this->contact_1->is_notified;
    	$this->locale = $this->contact_1->locale;
    	$this->photo_link_id = $this->contact_1->photo_link_id;
    	$this->profile_tiny_1 = $this->contact_1->tiny_1;
    	$this->profile_tiny_2 = $this->contact_1->tiny_2;
    	$this->profile_tiny_3 = $this->contact_1->tiny_3;
    	$this->profile_tiny_4 = $this->contact_1->tiny_4;
    	$this->profile_tiny_5 = $this->contact_1->tiny_5;
    	$this->profile_tiny_6 = $this->contact_1->tiny_6;
    	$this->profile_tiny_7 = $this->contact_1->tiny_7;
    	$this->profile_tiny_8 = $this->contact_1->tiny_8;
    	$this->profile_tiny_9 = $this->contact_1->tiny_9;
    	$this->profile_tiny_10 = $this->contact_1->tiny_10;
    	 
    	if ($this->contact_2) {
    		$this->contact_2 = Vcard::get($this->contact_2_id);
    		$this->n_title_2 = $this->contact_2->n_title;
    		$this->n_first_2 = $this->contact_2->n_first;
    		$this->n_last_2 = $this->contact_2->n_last;
    		$this->email_2 = $this->contact_2->email;
    		$this->birth_date_2 = $this->contact_2->birth_date;
    		$this->tel_work_2 = $this->contact_2->tel_work;
    		$this->tel_cell_2 = $this->contact_2->tel_cell;
    		$this->adr_street_2 = $this->contact_2->adr_street;
    		$this->adr_extended_2 = $this->contact_2->adr_extended;
    		$this->adr_post_office_box_2 = $this->contact_2->adr_post_office_box;
    		$this->adr_zip_2 = $this->contact_2->adr_zip;
    		$this->adr_city_2 = $this->contact_2->adr_city;
    		$this->adr_state_2 = $this->contact_2->adr_state;
    		$this->adr_country_2 = $this->contact_2->adr_country;
    	}
    	if ($this->contact_3) {
    		$this->contact_3 = Vcard::get($this->contact_3_id);
    		$this->n_title_3 = $this->contact_3->n_title;
    		$this->n_first_3 = $this->contact_3->n_first;
    		$this->n_last_3 = $this->contact_3->n_last;
    		$this->email_3 = $this->contact_3->email;
    		$this->birth_date_3 = $this->contact_3->birth_date;
    		$this->tel_work_3 = $this->contact_3->tel_work;
    		$this->tel_cell_3 = $this->contact_3->tel_cell;
    		$this->adr_street_3 = $this->contact_3->adr_street;
    		$this->adr_extended_3 = $this->contact_3->adr_extended;
    		$this->adr_post_office_box_3 = $this->contact_3->adr_post_office_box;
    		$this->adr_zip_3 = $this->contact_3->adr_zip;
    		$this->adr_city_3 = $this->contact_3->adr_city;
    		$this->adr_state_3 = $this->contact_3->adr_state;
    		$this->adr_country_3 = $this->contact_3->adr_country;
    	}
    	if ($this->contact_4) {
    		$this->contact_4 = Vcard::get($this->contact_4_id);
    		$this->n_title_4 = $this->contact_4->n_title;
    		$this->n_first_4 = $this->contact_4->n_first;
    		$this->n_last_4 = $this->contact_4->n_last;
    		$this->email_4 = $this->contact_4->email;
    		$this->birth_date_4 = $this->contact_4->birth_date;
    		$this->tel_work_4 = $this->contact_4->tel_work;
    		$this->tel_cell_4 = $this->contact_4->tel_cell;
    		$this->adr_street_4 = $this->contact_4->adr_street;
    		$this->adr_extended_4 = $this->contact_4->adr_extended;
    		$this->adr_post_office_box_4 = $this->contact_4->adr_post_office_box;
    		$this->adr_zip_4 = $this->contact_4->adr_zip;
    		$this->adr_city_4 = $this->contact_4->adr_city;
    		$this->adr_state_4 = $this->contact_4->adr_state;
    		$this->adr_country_4 = $this->contact_4->adr_country;
    	}
    	if ($this->contact_5) {
    		$this->contact_5 = Vcard::get($this->contact_5_id);
    		$this->n_title_5 = $this->contact_5->n_title;
    		$this->n_first_5 = $this->contact_5->n_first;
    		$this->n_last_5 = $this->contact_5->n_last;
    		$this->email_5 = $this->contact_5->email;
    		$this->birth_date_5 = $this->contact_5->birth_date;
    		$this->tel_work_5 = $this->contact_5->tel_work;
    		$this->tel_cell_5 = $this->contact_5->tel_cell;
    		$this->adr_street_5 = $this->contact_5->adr_street;
    		$this->adr_extended_5 = $this->contact_5->adr_extended;
    		$this->adr_post_office_box_5 = $this->contact_5->adr_post_office_box;
    		$this->adr_zip_5 = $this->contact_5->adr_zip;
    		$this->adr_city_5 = $this->contact_5->adr_city;
    		$this->adr_state_5 = $this->contact_5->adr_state;
    		$this->adr_country_5 = $this->contact_5->adr_country;
    	}
    }
    
    public static function get($id, $column = 'id')
    {
    	$account = Account::getTable()->get($id, $column);
    	 
    	if (!$account) return null;
    	$description = Account::getDescription($account->type);
    	$account->place = Place::getTable()->get($account->place_id);
    	if ($account->place) $account->place_caption = $account->place->caption;
    	if ($account->contact_1_id && !$account->contact_1) {
	    	$account->contact_1 = Vcard::get($account->contact_1_id);
		    	
		    $userContact = UserContact::get($account->contact_1_id, 'vcard_id');
		    if ($userContact) {
		    	$account->userContact = $userContact;

		    	$user = User::get($userContact->user_id);
		    	$account->user = $user;
		    }
		    if (!$account->user) $account->user = User::instanciate();
		    $account->username = $account->user->username;
	    }
	    else $account->contact_1 = Vcard::instanciate();
        	
    	$account->contact_2 = ($account->contact_2_id) ? Vcard::get($account->contact_2_id) : null;
    	$account->contact_3 = ($account->contact_3_id) ? Vcard::get($account->contact_3_id) : null;
    	$account->contact_4 = ($account->contact_4_id) ? Vcard::get($account->contact_4_id) : null;
    	$account->contact_5 = ($account->contact_5_id) ? Vcard::get($account->contact_5_id) : null;
		$account->denormalize();	    
	    $account->properties = $account->getProperties($account->type, $description);

	    return $account;
	}
   
    public static function instanciate($type = null)
    {
    	$context = Context::getCurrent();
    	
		$account = new Account;
		$account->status = 'new';
		$account->type = $type;
		$account->opening_date = date('Y-m-d');
		$account->contact_history = array();
		$account->audit = array();
		$account->contact_1 = Vcard::instanciate();
		$account->availability_exceptions = array();
		$account->availability_constraints = array();
		$account->credits = array();
		$creditDescription = $context->getConfig('core_account/'.$type.'/property/credits');
		if (!$creditDescription) $creditDescription = $context->getConfig('core_account/generic/property/credits');
		if (array_key_exists('default', $creditDescription)) $account->credits = $creditDescription['default'];
		$account->json_property_1 = array();
		$account->json_property_2 = array();
		$account->json_property_3 = array();
		$account->json_property_4 = array();
		$account->json_property_5 = array();
		$account->is_notified = 1;
		$account->locale = $context->getInstance()->getDefaultLocale();
		return $account;
    }

    public static function checktime($time) {
    	if ((int)substr($time, 0, 2) < 0) return false;
    	if ((int)substr($time, 0, 2) > 23) return false;
    	if (substr($time, 2, 1) != ':') return false;
    	if ((int)substr($time, 3, 2) < 0) return false;
    	if ((int)substr($time, 3, 2) > 59) return false;
    	if (substr($time, 5, 1) != ':') return false;
    	if ((int)substr($time, 6, 2) < 0) return false;
    	if ((int)substr($time, 6, 2) > 59) return false;
    	return true;
    }

	public function loadData($type, $data)
	{
		$context = Context::getCurrent();
		$errors = array();

       	$auditRow = array(
    			'time' => Date('Y-m-d G:i:s'),
    			'n_fn' => $context->getFormatedName(),
    	);
		$configProperties = Account::getConfig($type);
			
		// Automatic values
		if (!$this->date_1 && array_key_exists('date_1', $configProperties)) {
			$date1Description = $configProperties['date_1'];
			if (	$date1Description
				&& 	array_key_exists('dependency', $date1Description)
				&& 	array_key_exists($date1Description['dependency']['property'], $data)
				&&	in_array($data[$date1Description['dependency']['property']], $date1Description['dependency']['values'])) {
					$data['date_1'] = date('Y-m-d');
			}
		}

		if (!$this->date_2 && array_key_exists('date_2', $configProperties)) {
			$date2Description = $configProperties['date_2'];
			if (	$date2Description
				&& 	array_key_exists('dependency', $date2Description)
				&& 	array_key_exists($date2Description['dependency']['property'], $data)
				&&	in_array($data[$date2Description['dependency']['property']], $date2Description['dependency']['values'])) {
					$data['date_2'] = date('Y-m-d');
			}
		}

		if (!$this->date_3 && array_key_exists('date_3', $configProperties)) {
			$date3Description = $configProperties['date_3'];
			if (	$date3Description
				&& 	array_key_exists('dependency', $date3Description)
				&& 	array_key_exists($date3Description['dependency']['property'], $data)
				&&	in_array($data[$date3Description['dependency']['property']], $date3Description['dependency']['values'])) {
					$data['date_3'] = date('Y-m-d');
			}
		}

		if (!$this->date_4 && array_key_exists('date_4', $configProperties)) {
			$date4Description = $configProperties['date_4'];
			if (	$date4Description
				&& 	array_key_exists('dependency', $date4Description)
				&& 	array_key_exists($date4Description['dependency']['property'], $data)
				&&	in_array($data[$date4Description['dependency']['property']], $date4Description['dependency']['values'])) {
					$data['date_4'] = date('Y-m-d');
			}
		}

		if (!$this->date_5 && array_key_exists('date_5', $configProperties)) {
			$date5Description = $configProperties['date_5'];
			if (	$date5Description
				&& 	array_key_exists('dependency', $date5Description)
				&& 	array_key_exists($date5Description['dependency']['property'], $data)
				&&	in_array($data[$date5Description['dependency']['property']], $date5Description['dependency']['values'])) {
					$data['date_5'] = date('Y-m-d');
			}
		}

		foreach ($data as $propertyId => $value) {
			if (!array_key_exists($propertyId, $configProperties)) $errors[$propertyId] = "The accounts of type $type does not manage the property $propertyId";
			else {
				$property = $configProperties[$propertyId];
				if (in_array($property['type'], ['array', 'key_value', 'structure'])) $value = $data[$propertyId];
				else $value = trim(strip_tags($data[$propertyId]));
				
				if (in_array($property['type'], ['input', 'select', 'multiselect'])) {
					if (strlen($value) > 255) $errors[$propertyId] = "$propertyId should not be longer than 255 characters";
				}
				elseif (in_array($property['type'], ['textarea', 'log'])) {
					$maxLength = (array_key_exists('max_length', $property)) ? $property['max_length'] : 2047;
					if (strlen($value) > $maxLength) $errors[$propertyId] = "$propertyId should not be longer than $maxLength characters";
				}
				elseif (in_array($property['type'], ['date'])) {
			    	if ($value && (strlen($value) < 10 || !checkdate(substr($value, 5, 2), substr($value, 8, 2), substr($value, 0, 4)))) $errors[$propertyId] = "$propertyId should be a valid date according to the format yyyy-mm-dd";
				}
				elseif (in_array($property['type'], ['time'])) {
			    	if ($value && !Account::checktime($value)) $errors[$propertyId] = "$propertyId should be a valid time according to the format hh:mm:ss";
				}
				elseif (in_array($property['type'], ['datetime'])) {
			    	if ($value && (!checkdate(substr($value, 5, 2), substr($value, 8, 2), substr($value, 0, 4)) || !Account::checktime(substr($value, 11, 8)))) $errors[$propertyId] = "$propertyId should be a valid date & time according to the format yyyy-mm-dd hh:mm:ss";
				}
				elseif ($property['type'] == 'id') $value = (int) $value;
				elseif ($property['type'] == 'number') {
					if (array_key_exists('precision', $property) && $property['precision'] > 0) $value = (float) $value;
					else $value = (int) $value;
				}

				// Private data protection
				if ($property['private'] && $value) {
					$value = $context->getSecurityAgent()->protectPrivateData($value);
				}

				if ($propertyId == 'status') $this->status = $value;
	    		elseif ($propertyId == 'type') $this->type = $value;
	    		elseif ($propertyId == 'place_id') $this->place_id = $value;
	        	elseif ($propertyId == 'identifier') $this->identifier = $value;
	    		elseif ($propertyId == 'name') $this->name = $value;
	    		elseif ($propertyId == 'basket') $this->basket = $value;
	    		elseif ($propertyId == 'contact_1_id') $this->contact_1_id = $value;
	    		elseif ($propertyId == 'contact_1_status') $this->contact_1_status = $value;
	        	elseif ($propertyId == 'contact_2_id') $this->contact_2_id = $value;
	    		elseif ($propertyId == 'contact_2_status') $this->contact_2_status = $value;
	        	elseif ($propertyId == 'contact_3_id') $this->contact_3_id = $value;
	    		elseif ($propertyId == 'contact_3_status') $this->contact_3_status = $value;
	        	elseif ($propertyId == 'contact_4_id') $this->contact_4_id = $value;
	    		elseif ($propertyId == 'contact_4_status') $this->contact_4_status = $value;
	        	elseif ($propertyId == 'contact_5_id') $this->contact_5_id = $value;
	    		elseif ($propertyId == 'contact_5_status') $this->contact_5_status = $value;
				elseif ($propertyId == 'opening_date') $this->opening_date = $value;
				elseif ($propertyId == 'closing_date') $this->closing_date = $value;
	    		elseif ($propertyId == 'callback_date') $this->callback_date = $value;
	    		elseif ($propertyId == 'first_activation_date') $this->first_activation_date = $value;
	    		elseif ($propertyId == 'date_1') $this->date_1 = $value;
	    		elseif ($propertyId == 'date_2') $this->date_2 = $value;
	    		elseif ($propertyId == 'date_3') $this->date_3 = $value;
	    		elseif ($propertyId == 'date_4') $this->date_4 = $value;
	    		elseif ($propertyId == 'date_5') $this->date_5 = $value;
	    		elseif ($propertyId == 'priority') $this->priority = $value;
				elseif ($propertyId == 'origine') $this->origine = $value;
	    		elseif ($propertyId == 'next_meeting_date') $this->next_meeting_date = $value;
				elseif ($propertyId == 'next_meeting_confirmed') $this->next_meeting_confirmed = $value;
				elseif ($propertyId == 'contact_history' && $data['contact_history']) {
							$this->contact_history[] = array(
							'time' => Date('Y-m-d G:i:s'),
							'n_fn' => $context->getFormatedName(),
							'comment' => $value,
					);
				}
	        	elseif ($propertyId == 'notification_time') $this->notification_time = $value;
	        	elseif ($propertyId == 'terms_of_use_validation_time') $this->terms_of_use_validation_time = $value;
	        	elseif ($propertyId == 'charter_validation_time') $this->charter_validation_time = $value;
	        	elseif ($propertyId == 'opt_out_time') $this->opt_out_time = $value;
	        	elseif ($propertyId == 'availability_begin') $this->availability_begin = $value;
				elseif ($propertyId == 'availability_end') $this->availability_end = $value;
				elseif ($propertyId == 'availability_exceptions') $this->availability_exceptions = $value;
				elseif ($propertyId == 'availability_constraints') $this->availability_constraints = $value;
				elseif ($propertyId == 'credits') $this->credits = $value;
				elseif ($propertyId == 'shopping_cart') $this->shopping_cart = $value;
				elseif ($propertyId == 'default_means_of_payment') $this->default_means_of_payment = $value;
				elseif ($propertyId == 'transfer_order_id') $this->transfer_order_id = $value;
				elseif ($propertyId == 'transfer_order_date') $this->transfer_order_date = $value;
				elseif ($propertyId == 'bank_identifier') $this->bank_identifier = $value;
				elseif ($propertyId == 'property_1') $this->property_1 = $value;
				elseif ($propertyId == 'property_2') $this->property_2 = $value;
				elseif ($propertyId == 'property_3') $this->property_3 = $value;
				elseif ($propertyId == 'property_4') $this->property_4 = $value;
				elseif ($propertyId == 'property_5') $this->property_5 = $value;
				elseif ($propertyId == 'property_6') $this->property_6 = $value;
				elseif ($propertyId == 'property_7') $this->property_7 = $value;
				elseif ($propertyId == 'property_8') $this->property_8 = $value;
				elseif ($propertyId == 'property_9') $this->property_9 = $value;
				elseif ($propertyId == 'property_10') $this->property_10 = $value;
	    		elseif ($propertyId == 'property_11') $this->property_11 = $value;
	    		elseif ($propertyId == 'property_12') $this->property_12 = $value;
	    		elseif ($propertyId == 'property_13') $this->property_13 = $value;
	    		elseif ($propertyId == 'property_14') $this->property_14 = $value;
	    		elseif ($propertyId == 'property_15') $this->property_15 = $value;
	    		elseif ($propertyId == 'property_16') $this->property_16 = $value;
	    		elseif ($propertyId == 'json_property_1') $this->json_property_1 = $value;
	        	elseif ($propertyId == 'json_property_2') $this->json_property_2 = $value;
	            elseif ($propertyId == 'json_property_3') $this->json_property_3 = $value;
	            elseif ($propertyId == 'json_property_4') $this->json_property_4 = $value;
	            elseif ($propertyId == 'json_property_5') $this->json_property_5 = $value;
				elseif ($propertyId == 'comment_1') $this->comment_1 = $value;
	        	elseif ($propertyId == 'comment_2') $this->comment_2 = $value;
	        	elseif ($propertyId == 'comment_3') $this->comment_3 = $value;
	        	elseif ($propertyId == 'comment_4') $this->comment_4 = $value;

				if ($propertyId && $propertyId != 'contact_history' && $this->properties[$propertyId] != $value) $auditRow[$propertyId] = $value;
			}
		}
		if ($errors) return 'Integrity';
		$this->audit[] = $auditRow;
    	return 'OK';
    }

    public function add()
    {
    	$context = Context::getCurrent();
    	$this->id = null;
    	Account::getTable()->save($this);
    	if (!$this->identifier) {
    		$this->identifier = $this->id;
    		Account::getTable()->save($this);
    	}
    	return ('OK');
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();
    	$account = Account::get($this->id);

    	// Isolation check
    	if ($update_time && $account->update_time > $update_time) return 'Isolation';
    	
    	if ($this->contact_1) $this->contact_1->update($this->contact_1->update_time);
    	Account::getTable()->save($this);
    	return 'OK';
    }

    public function isUsed($object)
    {
    	// Allow or not deleting a place
    	if (get_class($object) == 'Model\PLace') {
    		if (Generic::getTable()->cardinality('core_account', array('place_id' => $object->id)) > 0) return true;
    	}
    	return false;
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
		foreach($context->getConfig('core_account/'.$type)['properties'] as $propertyId) {
    		if (array_key_exists($propertyId, $data)) {
    			$value = $data[$propertyId];
				$property = $context->getConfig('core_account/'.$type.'/property/'.$propertyId);
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if ($property['type'] == 'select') {
					$valueKey = null;
					foreach ($property['modalities'] as $modalityId => $modality) if ($context->localize($modality) == $value) $valueKey = $modalityId;
					if ($valueKey) $data[$propertyId] = $valueKey;
				}
				elseif ($property['type'] == 'date' && $value) {
					$data[$propertyId] = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($value));
				}
    		}
		}

		$account = Account::instanciate($type);
		$rc = $account->loadAndAdd($data, Account::getConfig($type));
		return $rc;
    }

	public function notify($admins, $url)
	{
		$context = Context::getCurrent();
		$settings = $context->getConfig();
    	if ($settings['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // instance 0 is for demo
			$notification = $context->getConfig('core_account/notification');
			if ($notification['definition'] != 'inline') $notification = $context->getConfig($notification['definition']);
			$template = $notification['template'];
			if ($template['definition'] != 'inline') $template = $context->getConfig($template['definition']);
			$text = sprintf($template['body'][$context->getLocale()], $context->getInstance()->fqdn, $url);
	    	$signature = $context->getConfig('core_account/sendMessage')['signature'];
	    	if ($signature['definition'] != 'inline') $signature = $context->getConfig($signature['definition']);
			$text .= $signature['body'][$context->getLocale()];
			$part = new MimePart($text);
    		$part->type = "text/html";

/*	    	$img = new MimePart($context->getConfig('customisation/esi/send-message/logo')['content']);
    		$img->type = "image/gif";
    		$img->encoding = Mime::ENCODING_BASE64;
    		$img->disposition = Mime::DISPOSITION_INLINE;*/
    		
    		$body = new MimeMessage();
    		$body->setParts(array($part/*, $img*/));
    		 
    		$mail = new Mail\Message();
    		$mail->setEncoding("UTF-8");
    		$mail->setBody($body);
    		$from_mail = $context->getConfig('core_account/notification')['from_mail'];
    		$from_name = $context->getConfig('core_account/notification')['from_name'];
    		$mail->setFrom($from_mail, $from_name);
    		$mail->setSubject($template['subject'][$context->getLocale()]);

    		$mail->addTo($this->email, $this->n_fn);
    		$mail->addBcc('support@p-pit.fr', 'support@p-pit.fr');
    		foreach ($admins as $email => $contact) $mail->addBcc($email, $email);
    		if ($settings['mailProtocol'] == 'Smtp') {
    			$transport = new Mail\Transport\Smtp();
    		}
    		elseif ($settings['mailProtocol'] == 'Sendmail') {
    			$transport = new Mail\Transport\SendMail();
    		}
    		if ($settings['mailProtocol']) $transport->send($mail);

    		if ($settings['isTraceActive']) {
    			$writer = new Writer\Stream('data/log/mailing.txt');
    			$logger = new Logger();
    			$logger->addWriter($writer);
    			$logger->info('to: '.$this->email.' - subject: '.$template['subject'][$context->getLocale()].' - body: '.$text);
    		}
    	}
    }

    public function isDeletable()
    {
    	$context = Context::getCurrent();
    
    	// Check dependencies
    	$config = $context->getConfig();
    	foreach($config['ppitCoreDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}

    	return true;
    }
    
    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$account = Account::getTable()->get($this->id);
    
    	// Isolation check
    	if ($update_time && $account->update_time > $update_time) return 'Isolation';
/*    	$user = User::get($this->contact_1->id, 'vcard_id');
    	if ($user) $user->delete($user->update_time);*/
    	 
    	$this->status = 'deleted';
    	Account::getTable()->save($this);
    	 
    	return 'OK';
    }

    // Unused in 2pit
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }
	
	/**
	* Restfull implementation
	*/
    public function loadAndAdd($data, $config = null /* Deprecated */, $logDuplicate = true)
    {
    	$context = Context::getCurrent();
    	$type = $this->type;
    	$description = Account::getDescription('core_account/'.$type);
    	
    	if (!array_key_exists('status', $data)) $data['status'] = 'new';
/*    	foreach ($description['properties'] as $propertyId) {
			$property = $context->getConfig('core_account/'.$type.'/property/'.$propertyId);
    		if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
    		if (array_key_exists($propertyId, $data)) {
	    		if ($property['type'] == 'select' && $propertyId != 'place_id' && !array_key_exists($data[$propertyId], $property['modalities'])) {
	    			return ['400', 'The modality '.$data[$propertyId].' does not exist in '.$propertyId];
	    		}
	    		elseif ($property['type'] == 'date' && (strlen($data[$propertyId] < 10) || !checkdate(substr($data[$propertyId], 5, 2), substr($data[$propertyId], 8, 2), substr($data[$propertyId], 0, 4)))) {
	    			return ['400', $data[$propertyId].' is not a valid date for '.$propertyId];
	    		}
    		}
    	}*/

    	$accountData = array();
    	$vcardData = array();
    	$contact2Data = array();
    	$contact3Data = array();
    	$contact4Data = array();
    	$contact5Data = array();
    	$entities = Account::$model['entities'];
    	$properties = Account::$model['properties'];
    	foreach($data as $key => $value) {
			if (array_key_exists($key, $description['properties'])) {
    			$value = $data[$key];
    			$keep = true;
	    		if ($key == 'result') $keep = false;
	
				$property = $description['properties'][$key];
	
				if ($keep = true) {
					if ($properties[$key]['entity'] == 'core_account') $accountData[$properties[$key]['column']] = $value;
					elseif ($properties[$key]['entity'] == 'core_vcard') $vcardData[$properties[$key]['column']] = $value;
					elseif ($properties[$key]['entity'] == 'contact_2') $contact2Data[$properties[$key]['column']] = $value;
					elseif ($properties[$key]['entity'] == 'contact_3') $contact3Data[$properties[$key]['column']] = $value;
					elseif ($properties[$key]['entity'] == 'contact_4') $contact4Data[$properties[$key]['column']] = $value;
					elseif ($properties[$key]['entity'] == 'contact_5') $contact5Data[$properties[$key]['column']] = $value;
				}
    		}
    	}

    	// Determine the place to link the account with
    	$place = null;
    	if (array_key_exists('place_identifier', $data)) $place = Place::get($data['place_identifier'], 'identifier');
    	elseif (array_key_exists('place_id', $accountData)) $place = Place::get($accountData['place_id']);
    	if (!$place) $place = Place::get($context->getPlaceId());
    	$accountData['place_id'] = $place->id;
    	if (array_key_exists('options', $description) && array_key_exists('internal_identifier', $description['options'])) $internalIdentifier = $description['options']['internal_identifier'];
    	else $internalIdentifier = false;
    	if ($internalIdentifier && (!array_key_exists('identifier', $accountData) || !$accountData['identifier'])) {
    		$accountData['identifier'] = $place->next_account_identifier;
    		$place->next_account_identifier++;
    		$placeUpdated = true;
    	}
    	else $placeUpdated = false;

    	// Check for duplicate
    	   
    	$account = null;
//		if (array_key_exists('identifier', $accountData) && $accountData['identifier']) $account = Account::get($accountData['identifier'], 'identifier');

    	$vcard = null;
    	if (array_key_exists('email', $vcardData) && $vcardData['email']) {
	    	$vcard = Vcard::get($vcardData['email'], 'email');
    	}
    	if (!$vcard) $vcard = Vcard::instanciate();
		if (!$account) $account = $vcard->id ? current(Account::getList($type, ['place_id' => $place->id, 'contact_1_id' => $vcard->id])) : null;
    	if ($account) {
			if ($logDuplicate && array_key_exists('contact_history', $accountData) && $accountData['contact_history']) {
				$minimumData = ['callback_date' => date('Y-m-d'), 'contact_history' => $accountData['contact_history'].' (ignored)'];
				if ($account->status == 'gone') $minimumData['status'] = 'new';
	    		$rc = $account->loadData($type, $minimumData);
		    	if ($rc != 'OK') return ['500', $rc];
	    		$account->update(null);
				if ($rc != 'OK') return ['500', 'account->update: '.$rc];
	    		return ['206', $account->id];
			}
	    	return ['206', $account->id];
    	}
		$account = $this;

		// Load the data

		$account->contact_1 = $vcard;

		$rc = $vcard->loadData($vcardData);
		if ($rc != 'OK') return ['500', $rc];
    	$rc = $account->loadData($type, $accountData);
    	if ($rc != 'OK') return ['500', $rc];

    	if (!$account->name) $account->name = $account->contact_1->n_fn;

    	if ($contact2Data) {
			$account->contact_2 = Vcard::instanciate();
	    	$rc = $account->contact_2->loadData($contact2Data);
	    	if ($rc != 'OK') return ['500', $rc];
    	}

    	if ($contact3Data) {
			$account->contact_3 = Vcard::instanciate();
	    	$rc = $account->contact_3->loadData($contact3Data);
	    	if ($rc != 'OK') return ['500', $rc];
    	}

    	if ($contact4Data) {
    		$account->contact_4 = Vcard::instanciate();
	    	$rc = $account->contact_4->loadData($contact4Data);
	    	if ($rc != 'OK') return ['500', $rc];
    	}

    	if ($contact5Data) {
    		$account->contact_5 = Vcard::instanciate();
	    	$rc = $account->contact_5->loadData($contact5Data);
	    	if ($rc != 'OK') return ['500', $rc];
    	}

    	// Save the data
    	
		$rc = $vcard->add();
		if ($rc != 'OK') return ['500', 'vcard->add: '.$rc];
		$account->contact_1_id = $vcard->id;
		$account->contact_1_status = 'main';

		if ($contact2Data) {
			$rc = $account->contact_2->add();
			if ($rc != 'OK') return ['500', 'contact_2->add: '.$rc];
			$account->contact_2_id = $account->contact_2->id;
		}

		if ($contact3Data) {
			$rc = $account->contact_3->add();
			if ($rc != 'OK') return ['500', 'contact_3->add: '.$rc];
			$account->contact_3_id = $account->contact_3->id;
		}

		if ($contact4Data) {
			$rc = $account->contact_4->add();
			if ($rc != 'OK') return ['500', 'contact_4->add: '.$rc];
			$account->contact_4_id = $account->contact_4->id;
		}

		if ($contact5Data) {
			$rc = $account->contact_5->add();
			if ($rc != 'OK') return ['500', 'contact_5->add: '.$rc];
			$account->contact_5_id = $account->contact_5->id;
		}
		$account->add();
		if ($rc != 'OK') return ['500', 'account->add: '.$rc];
		if ($placeUpdated) $place->update(null);
		
		$account->denormalize();
		$account->properties = $account->getProperties($account->type, Account::getDescription($type));
		return ['200', $account->id];
    }

    public function loadAndUpdate($data, $config /* deprecated */, $update_time = null)
    {
		$context = Context::getCurrent();
		$type = $this->type;

    	$accountData = array();
    	$vcardData = array();
    	$contact2Data = array();
    	$contact3Data = array();
    	$contact4Data = array();
    	$contact5Data = array();
    	$entities = Account::$model['entities'];
    	$properties = Account::$model['properties'];
    	foreach ($data as $key => $value) {
    		if (array_key_exists($key, $properties)) {
				$propertyId = $properties[$key]['column'];
    			if ($properties[$key]['entity'] == 'core_account') $accountData[$propertyId] = $value;
    			elseif ($properties[$key]['entity'] == 'core_vcard') $vcardData[$propertyId] = $value;
    			elseif ($properties[$key]['entity'] == 'contact_2') $contact2Data[$propertyId] = $value;
    			elseif ($properties[$key]['entity'] == 'contact_3') $contact3Data[$propertyId] = $value;
    			elseif ($properties[$key]['entity'] == 'contact_4') $contact4Data[$propertyId] = $value;
    			elseif ($properties[$key]['entity'] == 'contact_5') $contact5Data[$propertyId] = $value;
    		}
    	}

		// Load the data
		$this->contact_1 = Vcard::get($this->contact_1_id);
		$rc = $this->contact_1->loadData($vcardData);
		if ($rc != 'OK') return ['500', $rc];

    	$rc = $this->loadData($this->type, $accountData);
    	if ($rc != 'OK') return ['500', $rc];
    	if (!$this->name) $this->name = $this->contact_1->n_fn;

    	if ($contact2Data) {
    		if ($this->contact_2_id) $this->contact_2 = Vcard::get($this->contact_2_id);
    		else $this->contact_2 = Vcard::instanciate();
	    	$rc = $this->contact_2->loadData($contact2Data);
	    	if ($rc != 'OK') return ['500', $rc];
    	}
    	
    	if ($contact3Data) {
    		if ($this->contact_3_id) $this->contact_3 = Vcard::get($this->contact_3_id);
    		else $this->contact_3 = Vcard::instanciate();
	    	$rc = $this->contact_3->loadData($contact3Data);
	    	if ($rc != 'OK') return ['500', $rc];
    	}

    	if ($contact4Data) {
    		if ($this->contact_4_id) $this->contact_4 = Vcard::get($this->contact_4_id);
    		else $this->contact_4 = Vcard::instanciate();
	    	$rc = $this->contact_4->loadData($contact4Data);
	    	if ($rc != 'OK') return ['500', $rc];
    	}
    	
    	if ($contact5Data) {
    		if ($this->contact_5_id) $this->contact_5 = Vcard::get($this->contact_5_id);
    		else $this->contact_5 = Vcard::instanciate();
	    	$rc = $this->contact_5->loadData($contact5Data);
	    	if ($rc != 'OK') return ['500', $rc];
    	}

    	// Save the data
		$rc = $this->contact_1->update(null);
		if ($rc != 'OK') return ['500', 'vcard->update: '.$rc];

		if ($contact2Data) {
			if ($this->contact_2_id) {
				$rc = $this->contact_2->update(null);
				if ($rc != 'OK') return ['500', 'contact_2->update: '.$rc];
			}
			else {
				$rc = $this->contact_2->add();
				if ($rc != 'OK') return ['500', 'contact_2->add: '.$rc];
				$this->contact_2_id = $this->contact_2->id;
			}
		}

		if ($contact3Data) {
			if ($this->contact_3_id) {
				$rc = $this->contact_3->update(null);
				if ($rc != 'OK') return ['500', 'contact_3->update: '.$rc];
			}
			else {
				$rc = $this->contact_3->add();
				if ($rc != 'OK') return ['500', 'contact_3->add: '.$rc];
				$this->contact_3_id = $this->contact_3->id;
			}
		}

		if ($contact4Data) {
			if ($this->contact_4_id) {
				$rc = $this->contact_4->update(null);
				if ($rc != 'OK') return ['500', 'contact_4->update: '.$rc];
			}
			else {
				$rc = $this->contact_4->add();
				if ($rc != 'OK') return ['500', 'contact_4->add: '.$rc];
				$this->contact_4_id = $this->contact_4->id;
			}
		}

		if ($contact5Data) {
			if ($this->contact_5_id) {
				$rc = $this->contact_5->update(null);
				if ($rc != 'OK') return ['500', 'contact_5->update: '.$rc];
			}
			else {
				$rc = $this->contact_5->add();
				if ($rc != 'OK') return ['500', 'contact_5->add: '.$rc];
				$this->contact_5_id = $this->contact_5->id;
			}
		}
		$this->update($update_time);
		if ($rc != 'OK') return ['500', 'account->update: '.$rc];

		$this->denormalize();
		return ['200', $this->id];
    }
    
    public function getCharterStatus() {
    	$context = Context::getCurrent();
    	if (!$this->charter_validation_time) return 'to_sign';
    	if ($this->charter_validation_time < $context->getConfig('instance/last_charter_time')) return 'to_resign';
    	return 'OK';
    }
    
    public function getGtouStatus() {
    	$context = Context::getCurrent();
    	if (!$this->terms_of_use_validation_time) return 'to_sign';
    	if ($this->terms_of_use_validation_time < $context->getConfig('instance/last_terms_of_use_time')) return 'to_resign';
    	return 'OK';
    }

    public static function getTable()
    {
    	if (!Account::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Account::$table = $sm->get(AccountTable::class);
    	}
    	return Account::$table;
    }
}