<?php
namespace PpitCommitment\Model;

use PpitAccounting\Model\Journal;
use PpitCore\Model\Account;
use PpitCore\Model\Community;
use PpitCore\Model\Generic;
use PpitCore\Model\Vcard;
use PpitContact\Model\ContactMessage;
use PpitCore\Model\Context;
use PpitCore\Model\Credit;
use PpitCore\Model\Instance;
use PpitCore\Model\Place;
use PpitCore\Model\Document;
use PpitCore\Model\ProductOption;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\db\sql\Where;
use Zend\Log\Logger;
use Zend\Log\Writer;

class Commitment
{
	public static $model = array(
		'entities' => array(
			'commitment' => 		['table' => 'commitment'],
			'core_account' => 		['table' => 'core_account'],
			'core_vcard' => 		['table' => 'core_vcard', 'foreign_key' => 'contact_1_id'],
			'core_contact_2' => 	['table' => 'core_vcard', 'foreign_key' => 'contact_2_id'],
			'core_contact_3' => 	['table' => 'core_vcard', 'foreign_key' => 'contact_3_id'],
			'core_contact_4' => 	['table' => 'core_vcard', 'foreign_key' => 'contact_4_id'],
			'core_contact_5' => 	['table' => 'core_vcard', 'foreign_key' => 'contact_5_id'],
			'core_place' => 		['table' => 'core_place'],
		),
		'properties' => array(
			'status' => 						['entity' => 'commitment', 'column' => 'status'],
			'year' => 							['entity' => 'commitment', 'column' => 'year'],
			
			'account_id' => 					['entity' => 'commitment', 'column' => 'account_id'],
			'place_id' => 						['entity' => 'core_account', 'column' => 'place_id'],
			'place_caption' => 					['entity' => 'core_place', 'column' => 'caption'],
			'place_identifier' => 				['entity' => 'core_place', 'column' => 'identifier'],
			'place_suport_email' => 			['entity' => 'core_place', 'column' => 'support_email'],
			'place_logo_height' => 				['entity' => 'core_place', 'column' => 'logo_height'],
			'place_logo_src' => 				['entity' => 'core_place', 'column' => 'logo_src'],
			'place_config' => 					['entity' => 'core_place', 'column' => 'config'],
			
			'default_means_of_payment' => 		['entity' => 'core_account', 'column' => 'default_means_of_payment'],
			'account_status' => 				['entity' => 'core_account', 'column' => 'status'],
			'account_name' => 					['entity' => 'core_account', 'column' => 'name'],
			'account_identifier' => 			['entity' => 'core_account', 'column' => 'identifier'],
			'account_date_1' => 				['entity' => 'core_account', 'column' => 'date_1'],
			'account_date_2' => 				['entity' => 'core_account', 'column' => 'date_2'],
			'account_date_3' => 				['entity' => 'core_account', 'column' => 'date_3'],
			'account_date_4' => 				['entity' => 'core_account', 'column' => 'date_4'],
			'account_date_5' => 				['entity' => 'core_account', 'column' => 'date_5'],
			'account_property_1' => 			['entity' => 'core_account', 'column' => 'property_1'],
			'account_property_2' => 			['entity' => 'core_account', 'column' => 'property_2'],
			'account_property_3' => 			['entity' => 'core_account', 'column' => 'property_3'],
			'account_property_4' => 			['entity' => 'core_account', 'column' => 'property_4'],
			'account_property_5' => 			['entity' => 'core_account', 'column' => 'property_5'],
			'account_property_6' => 			['entity' => 'core_account', 'column' => 'property_6'],
			'account_property_7' => 			['entity' => 'core_account', 'column' => 'property_7'],
			'account_property_8' => 			['entity' => 'core_account', 'column' => 'property_8'],
			'account_property_9' => 			['entity' => 'core_account', 'column' => 'property_9'],
			'account_property_10' => 			['entity' => 'core_account', 'column' => 'property_10'],
			'account_property_11' => 			['entity' => 'core_account', 'column' => 'property_11'],
			'account_property_12' => 			['entity' => 'core_account', 'column' => 'property_12'],
			'account_property_13' => 			['entity' => 'core_account', 'column' => 'property_13'],
			'account_property_14' => 			['entity' => 'core_account', 'column' => 'property_14'],
			'account_property_15' => 			['entity' => 'core_account', 'column' => 'property_15'],
			'account_property_16' => 			['entity' => 'core_account', 'column' => 'property_16'],
			
			'contact_1_status' => 				['entity' => 'core_account', 'column' => 'contact_1_status'],
			'contact_2_status' => 				['entity' => 'core_account', 'column' => 'contact_2_status'],
			'contact_3_status' => 				['entity' => 'core_account', 'column' => 'contact_3_status'],
			'contact_4_status' => 				['entity' => 'core_account', 'column' => 'contact_4_status'],
			'contact_5_status' => 				['entity' => 'core_account', 'column' => 'contact_5_status'],
			
			'contact_n_title' => 				['entity' => 'core_vcard', 'column' => 'n_title'],
			'contact_n_first' => 				['entity' => 'core_vcard', 'column' => 'n_first'],
			'contact_n_last' => 				['entity' => 'core_vcard', 'column' => 'n_last'],
			'contact_n_fn' => 					['entity' => 'core_vcard', 'column' => 'n_fn'],
			'contact_email' => 					['entity' => 'core_vcard', 'column' => 'email'],
			'contact_tel_work' => 				['entity' => 'core_vcard', 'column' => 'tel_work'],
			'contact_tel_cell' => 				['entity' => 'core_vcard', 'column' => 'tel_cell'],

			'contact_n_title_2' => 				['entity' => 'contact_2', 'column' => 'n_title'],
			'contact_n_first_2' => 				['entity' => 'contact_2', 'column' => 'n_first'],
			'contact_n_last_2' => 				['entity' => 'contact_2', 'column' => 'n_last'],
			'contact_n_fn_2' => 				['entity' => 'contact_2', 'column' => 'n_fn'],
			'contact_email_2' => 				['entity' => 'contact_2', 'column' => 'email'],
			'contact_tel_work_2' => 			['entity' => 'contact_2', 'column' => 'tel_work'],
			'contact_tel_cell_2' => 			['entity' => 'contact_2', 'column' => 'tel_cell'],

			'contact_n_title_3' => 				['entity' => 'contact_3', 'column' => 'n_title'],
			'contact_n_first_3' => 				['entity' => 'contact_3', 'column' => 'n_first'],
			'contact_n_last_3' => 				['entity' => 'contact_3', 'column' => 'n_last'],
			'contact_n_fn_3' => 				['entity' => 'contact_3', 'column' => 'n_fn'],
			'contact_email_3' => 				['entity' => 'contact_3', 'column' => 'email'],
			'contact_tel_work_3' => 			['entity' => 'contact_3', 'column' => 'tel_work'],
			'contact_tel_cell_3' => 			['entity' => 'contact_3', 'column' => 'tel_cell'],

			'contact_n_title_4' => 				['entity' => 'contact_4', 'column' => 'n_title'],
			'contact_n_first_4' => 				['entity' => 'contact_4', 'column' => 'n_first'],
			'contact_n_last_4' => 				['entity' => 'contact_4', 'column' => 'n_last'],
			'contact_n_fn_4' => 				['entity' => 'contact_4', 'column' => 'n_fn'],
			'contact_email_4' => 				['entity' => 'contact_4', 'column' => 'email'],
			'contact_tel_work_4' => 			['entity' => 'contact_4', 'column' => 'tel_work'],
			'contact_tel_cell_4' => 			['entity' => 'contact_4', 'column' => 'tel_cell'],

			'contact_n_title_5' => 				['entity' => 'contact_5', 'column' => 'n_title'],
			'contact_n_first_5' => 				['entity' => 'contact_5', 'column' => 'n_first'],
			'contact_n_last_5' => 				['entity' => 'contact_5', 'column' => 'n_last'],
			'contact_n_fn_5' => 				['entity' => 'contact_5', 'column' => 'n_fn'],
			'contact_email_5' => 				['entity' => 'contact_5', 'column' => 'email'],
			'contact_tel_work_5' => 			['entity' => 'contact_5', 'column' => 'tel_work'],
			'contact_tel_cell_5' => 			['entity' => 'contact_5', 'column' => 'tel_cell'],
				
			'caption' => 						['entity' => 'commitment', 'column' => 'caption'],
			'description' => 					['entity' => 'commitment', 'column' => 'description'],
			'product_identifier' => 			['entity' => 'commitment', 'column' => 'product_identifier'],
			'product_brand' => 					['entity' => 'commitment', 'column' => 'product_brand'],
			'product_caption' => 				['entity' => 'commitment', 'column' => 'product_caption'],
			'quantity' => 						['entity' => 'commitment', 'column' => 'quantity'],
			'unit_price' => 					['entity' => 'commitment', 'column' => 'unit_price'],
			'amount' => 						['entity' => 'commitment', 'column' => 'amount'],
			'taxable_1_amount' => 				['entity' => 'commitment', 'column' => 'taxable_1_amount'],
			'taxable_2_amount' => 				['entity' => 'commitment', 'column' => 'taxable_2_amount'],
			'taxable_3_amount' => 				['entity' => 'commitment', 'column' => 'taxable_3_amount'],
			'options' => 						['entity' => 'commitment', 'column' => 'options'],
			'including_options_amount' => 		['entity' => 'commitment', 'column' => 'including_options_amount'],
			'taxable_1_total' => 				['entity' => 'commitment', 'column' => 'taxable_1_total'],
			'taxable_2_total' => 				['entity' => 'commitment', 'column' => 'taxable_2_total'],
			'taxable_3_total' => 				['entity' => 'commitment', 'column' => 'taxable_3_total'],
			'identifier' => 					['entity' => 'commitment', 'column' => 'identifier'],
			'invoice_identifier' => 			['entity' => 'commitment', 'column' => 'invoice_identifier'],
			'commitment_date' => 				['entity' => 'commitment', 'column' => 'commitment_date'],
			'property_1' => 					['entity' => 'commitment', 'column' => 'property_1'],
			'property_2' => 					['entity' => 'commitment', 'column' => 'property_2'],
			'property_3' => 					['entity' => 'commitment', 'column' => 'property_3'],
			'property_4' => 					['entity' => 'commitment', 'column' => 'property_4'],
			'property_5' => 					['entity' => 'commitment', 'column' => 'property_5'],
			'property_6' => 					['entity' => 'commitment', 'column' => 'property_6'],
			'property_7' => 					['entity' => 'commitment', 'column' => 'property_7'],
			'property_8' => 					['entity' => 'commitment', 'column' => 'property_8'],
			'property_9' => 					['entity' => 'commitment', 'column' => 'property_9'],
			'property_10' => 					['entity' => 'commitment', 'column' => 'property_10'],
			'property_11' => 					['entity' => 'commitment', 'column' => 'property_11'],
			'property_12' => 					['entity' => 'commitment', 'column' => 'property_12'],
			'property_13' => 					['entity' => 'commitment', 'column' => 'property_13'],
			'property_14' => 					['entity' => 'commitment', 'column' => 'property_14'],
			'property_15' => 					['entity' => 'commitment', 'column' => 'property_15'],
			'property_16' => 					['entity' => 'commitment', 'column' => 'property_16'],
			'property_17' => 					['entity' => 'commitment', 'column' => 'property_17'],
			'property_18' => 					['entity' => 'commitment', 'column' => 'property_18'],
			'property_19' => 					['entity' => 'commitment', 'column' => 'property_19'],
			'property_20' => 					['entity' => 'commitment', 'column' => 'property_20'],
			'property_21' => 					['entity' => 'commitment', 'column' => 'property_21'],
			'property_22' => 					['entity' => 'commitment', 'column' => 'property_22'],
			'property_23' => 					['entity' => 'commitment', 'column' => 'property_23'],
			'property_24' => 					['entity' => 'commitment', 'column' => 'property_24'],
			'property_25' => 					['entity' => 'commitment', 'column' => 'property_25'],
			'property_26' => 					['entity' => 'commitment', 'column' => 'property_26'],
			'property_27' => 					['entity' => 'commitment', 'column' => 'property_27'],
			'property_28' => 					['entity' => 'commitment', 'column' => 'property_28'],
			'property_29' => 					['entity' => 'commitment', 'column' => 'property_29'],
			'property_30' => 					['entity' => 'commitment', 'column' => 'property_30'],
			'retraction_limit' => 				['entity' => 'commitment', 'column' => 'retraction_limit'],
			'retraction_date' => 				['entity' => 'commitment', 'column' => 'retraction_date'],
			'expected_shipment_limit' => 		['entity' => 'commitment', 'column' => 'expected_shipment_limit'],
			'shipment_date' => 					['entity' => 'commitment', 'column' => 'shipment_date'],
			'expected_delivery_date' => 		['entity' => 'commitment', 'column' => 'expected_delivery_date'],
			'delivery_date' => 					['entity' => 'commitment', 'column' => 'delivery_date'],
			'expected_commissioning_date' =>	['entity' => 'commitment', 'column' => 'expected_commissioning_date'],
			'commissioning_date' => 			['entity' => 'commitment', 'column' => 'commissioning_date'],
			'due_date' => 						['entity' => 'commitment', 'column' => 'due_date'],
			'invoice_date' => 					['entity' => 'commitment', 'column' => 'invoice_date'],
			'order_form_id' => 					['entity' => 'commitment', 'column' => 'order_form_id'],
			'excluding_tax' => 					['entity' => 'commitment', 'column' => 'excluding_tax'],
			'tax_regime' => 					['entity' => 'commitment', 'column' => 'tax_regime'],
			'tax_1_amount' => 					['entity' => 'commitment', 'column' => 'tax_1_amount'],
			'tax_2_amount' => 					['entity' => 'commitment', 'column' => 'tax_2_amount'],
			'tax_3_amount' => 					['entity' => 'commitment', 'column' => 'tax_3_amount'],
			'tax_amount' => 					['entity' => 'commitment', 'column' => 'tax_amount'],
			'tax_inclusive' => 					['entity' => 'commitment', 'column' => 'tax_inclusive'],
			'means_of_paiement' => 				['entity' => 'commitment', 'column' => 'means_of_paiement'],
			'update_time' => 					['entity' => 'commitment', 'column' => 'update_time'],
		),
	);

	public static function getConfig($type) {
		$context = Context::getCurrent();
		$properties = array();
		$description = $context->getConfig('commitment/'.$type);
		if (!$description) $description = $context->getConfig('commitment/generic');
		$description['type'] = $type;
		foreach($description['properties'] as $propertyId) {
			
			$property = $context->getConfig('commitment/'.$type.'/property/'.$propertyId);
			if (!$property) $property = $context->getConfig('commitment/generic/property/'.$propertyId);
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			if (!array_key_exists('private', $property)) $property['private'] = false;
			if (!$property['private'] || $context->hasRole('dpo')) {
				if ($propertyId == 'place_id') {
					$property['modalities'] = array();
					foreach (Place::getList(array()) as $place) $property['modalities'][$place->id] = $place->caption;
				}
				elseif ($propertyId == 'account_id') {
					$property['modalities'] = array();
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
		$configSearch = $context->getConfig('commitment/search/'.$type);
		if (!$configSearch) $configSearch = $context->getConfig('commitment/search/generic');
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
		$configList = $context->getConfig('commitment/list/'.$type);
		if (!$configList) $configList = $context->getConfig('commitment/list/generic');
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
		$configUpdate = $context->getConfig('commitment/update/'.$type);
		if (!$configUpdate) $configUpdate = $context->getConfig('commitment/update/generic');
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

	public static function getConfigGroup($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configUpdate = $context->getConfig('commitment/group/'.$type);
		if (!$configUpdate) $configUpdate = $context->getConfig('commitment/group/generic');
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
		$configExport = $context->getConfig('commitment/export/'.$type);
		if (!$configExport) $configExport = $context->getConfig('commitment/export/generic');
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
		$config = $context->getConfig('commitment/'.$type);
		if (!$config) $config = $context->getConfig('commitment/generic');
	
		$description = array();
		$description['type'] = $type;
		$description['options'] = (array_key_exists('options', $config)) ? $config['options'] : array();
		$description['properties'] = Commitment::getConfig($type);
		$description['search'] = Commitment::getConfigSearch($type, $description['properties']);
		$description['list'] = Commitment::getConfigList($type, $description['properties']);
		$description['update'] = Commitment::getConfigUpdate($type, $description['properties']);
		$description['group'] = Commitment::getConfigGroup($type, $description['properties']);
		$description['export'] = Commitment::getConfigEXport($type, $description['properties']);
		return $description;
	}
	
	public $id;
    public $instance_id;
    public $year;
    public $type;
	public $account_id;
	public $status;
	public $caption;
	public $description;
	public $product_identifier;
	public $product_brand;
	public $product_caption;
	public $quantity;
	public $unit_price;
	public $amount;
	public $taxable_1_amount;
	public $taxable_2_amount;
	public $taxable_3_amount;
	public $options;
	public $including_options_amount;
	public $taxable_1_total;
	public $taxable_2_total;
	public $taxable_3_total;
	public $identifier;
	public $invoice_identifier;
	public $commitment_date;
	public $retraction_limit;
    public $retraction_date;
	public $expected_shipment_date;
    public $shipment_date;
	public $expected_delivery_date;
	public $delivery_date;
	public $expected_commissioning_date;
	public $commissioning_date;
	public $due_date;
	public $invoice_date;
	public $order_form_id;
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
	public $audit = array();
	public $excluding_tax;
	public $tax_regime;
	public $tax_1_amount;
	public $tax_2_amount;
	public $tax_3_amount;
	public $tax_amount;
	public $tax_inclusive;
	public $means_of_paiement;
	public $commitment_message_id;
	public $change_message_id;
	public $confirmation_message_id;
	public $shipment_message_id;
	public $delivery_message_id;
	public $commissioning_message_id;
	public $invoice_message_id;
	public $notification_time;
	public $update_time;

	// Additional field from joined tables
	public $place_id;
    public $place_caption;
    public $place_identifier;
    public $place_support_email;
	public $place_logo_height;
	public $place_logo_src;
    public $place_config;
    public $properties;

    public $default_means_of_payment;
    public $transfer_order_id;
    public $transfer_order_date;
    public $bank_identifier;
    
    public $account_status;
    public $account_name;
	public $account_identifier;
	public $account_date_1;
	public $account_date_2;
	public $account_date_3;
	public $account_date_4;
	public $account_date_5;
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

	public $contact_1_status;
	public $contact_2_status;
	public $contact_3_status;
	public $contact_4_status;
	public $contact_5_status;
	
	public $n_title;
	public $n_first;
	public $n_last;
	public $n_fn;
	public $email;
	public $tel_work;
	public $tel_cell;
	
	public $n_title_2;
	public $n_first_2;
	public $n_last_2;
	public $n_fn_2;
	public $email_2;
	public $tel_work_2;
	public $tel_cell_2;
	
	public $n_title_3;
	public $n_first_3;
	public $n_last_3;
	public $n_fn_3;
	public $email_3;
	public $tel_work_3;
	public $tel_cell_3;
	
	public $n_title_4;
	public $n_first_4;
	public $n_last_4;
	public $n_fn_4;
	public $email_4;
	public $tel_work_4;
	public $tel_cell_4;
	
	public $n_title_5;
	public $n_first_5;
	public $n_last_5;
	public $n_fn_5;
	public $email_5;
	public $tel_work_5;
	public $tel_cell_5;
	
	// Transient properties
//	public $properties;
	public $account;

	public $invoice_n_title;
	public $invoice_n_first;
	public $invoice_n_last;
	public $invoice_n_fn;
	public $invoice_email;
	public $invoice_tel_work;
	public $invoice_tel_cell;
	
	public $terms;
	public $termSum;
	public $breadcrumb;
    public $vat_rate;
    public $files;
    public $comment;
    
    protected $inputFilter;
    protected $validatePiInputFilter;
    protected $validateInputFilter;

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
        $this->year = (isset($data['year'])) ? $data['year'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->account_id = (isset($data['account_id'])) ? $data['account_id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->product_identifier = (isset($data['product_identifier'])) ? $data['product_identifier'] : null;
        $this->product_brand = (isset($data['product_brand'])) ? $data['product_brand'] : null;
        $this->product_caption = (isset($data['product_caption'])) ? $data['product_caption'] : null;
        $this->quantity = (isset($data['quantity'])) ? $data['quantity'] : null;
        $this->unit_price = (isset($data['unit_price'])) ? $data['unit_price'] : null;
        $this->amount = (isset($data['amount'])) ? $data['amount'] : null;
        $this->taxable_1_amount = (isset($data['taxable_1_amount'])) ? $data['taxable_1_amount'] : null;
        $this->taxable_2_amount = (isset($data['taxable_2_amount'])) ? $data['taxable_2_amount'] : null;
        $this->taxable_3_amount = (isset($data['taxable_3_amount'])) ? $data['taxable_3_amount'] : null;
        $this->options = (isset($data['options'])) ? ((is_array($data['options'])) ? $data['options'] : json_decode($data['options'], true)) : null;
        $this->including_options_amount = (isset($data['including_options_amount'])) ? $data['including_options_amount'] : null;
        $this->taxable_1_total = (isset($data['taxable_1_total'])) ? $data['taxable_1_total'] : null;
        $this->taxable_2_total = (isset($data['taxable_2_total'])) ? $data['taxable_2_total'] : null;
        $this->taxable_3_total = (isset($data['taxable_3_total'])) ? $data['taxable_3_total'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->invoice_identifier = (isset($data['invoice_identifier'])) ? $data['invoice_identifier'] : null;
        $this->commitment_date = (isset($data['commitment_date'])) ? $data['commitment_date'] : null;
        $this->retraction_limit = (isset($data['retraction_limit'])) ? $data['retraction_limit'] : null;
        $this->retraction_date = (isset($data['retraction_date'])) ? $data['retraction_date'] : null;
        $this->expected_shipment_date = (isset($data['expected_shipment_date'])) ? $data['expected_shipment_date'] : null;
        $this->shipment_date = (isset($data['shipment_date'])) ? $data['shipment_date'] : null;
        $this->expected_delivery_date = (isset($data['expected_delivery_date'])) ? $data['expected_delivery_date'] : null;
        $this->delivery_date = (isset($data['delivery_date'])) ? $data['delivery_date'] : null;
        $this->expected_commissioning_date = (isset($data['expected_commissioning_date'])) ? $data['expected_commissioning_date'] : null;
        $this->commissioning_date = (isset($data['commissioning_date'])) ? $data['commissioning_date'] : null;
        $this->due_date = (isset($data['due_date'])) ? $data['due_date'] : null;
        $this->invoice_date = (isset($data['invoice_date'])) ? $data['invoice_date'] : null;
        $this->order_form_id = (isset($data['order_form_id'])) ? $data['order_form_id'] : null;
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
        $this->audit = (isset($data['audit'])) ? ((is_array($data['audit'])) ? $data['audit'] : json_decode($data['audit'], true)) : array();
        $this->excluding_tax = (isset($data['excluding_tax'])) ? $data['excluding_tax'] : null;
        $this->tax_regime = (isset($data['tax_regime'])) ? $data['tax_regime'] : null;
        $this->tax_1_amount = (isset($data['tax_1_amount'])) ? $data['tax_1_amount'] : null;
        $this->tax_2_amount = (isset($data['tax_2_amount'])) ? $data['tax_2_amount'] : null;
        $this->tax_3_amount = (isset($data['tax_3_amount'])) ? $data['tax_3_amount'] : null;
        $this->tax_amount = (isset($data['tax_amount'])) ? $data['tax_amount'] : null;
        $this->tax_inclusive = (isset($data['tax_inclusive'])) ? $data['tax_inclusive'] : null;
        $this->tax_means_of_paiement = (isset($data['tax_means_of_paiement'])) ? $data['tax_means_of_paiement'] : null;
        $this->commitment_message_id = (isset($data['commitment_message_id'])) ? $data['commitment_message_id'] : null;
        $this->change_message_id = (isset($data['change_message_id'])) ? $data['change_message_id'] : null;
        $this->confirmation_message_id = (isset($data['confirmation_message_id'])) ? $data['confirmation_message_id'] : null;
        $this->shipment_message_id = (isset($data['shipment_message_id'])) ? $data['shipment_message_id'] : null;
        $this->delivery_message_id = (isset($data['delivery_message_id'])) ? $data['delivery_message_id'] : null;
        $this->commissioning_message_id = (isset($data['commissioning_message_id'])) ? $data['commissioning_message_id'] : null;
        $this->invoice_message_id = (isset($data['invoice_message_id'])) ? $data['invoice_message_id'] : null;
        $this->notification_time = (isset($data['notification_time'])) ? $data['notification_time'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // Additional properties from joined tables
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
        $this->place_identifier = (isset($data['place_identifier'])) ? $data['place_identifier'] : null;
        $this->place_support_email = (isset($data['place_support_email'])) ? $data['place_support_email'] : null;
        $this->place_logo_height = (isset($data['place_logo_height'])) ? $data['place_logo_height'] : null;
        $this->place_logo_src = (isset($data['place_logo_src'])) ? $data['place_logo_src'] : null;
        $this->place_config = (isset($data['place_config'])) ? json_decode($data['place_config'], true) : [];

        $this->default_means_of_payment = (isset($data['default_means_of_payment'])) ? $data['default_means_of_payment'] : null;
        $this->transfer_order_id = (isset($data['transfer_order_id'])) ? $data['transfer_order_id'] : null;
        $this->transfer_order_date = (isset($data['transfer_order_date'])) ? $data['transfer_order_date'] : null;
        $this->bank_identifier = (isset($data['bank_identifier'])) ? $data['bank_identifier'] : null;
        
        $this->n_title = (isset($data['n_title'])) ? $data['n_title'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->account_status = (isset($data['account_status'])) ? $data['account_status'] : null;
        $this->account_name = (isset($data['account_name'])) ? $data['account_name'] : null;
        $this->account_identifier = (isset($data['account_identifier'])) ? $data['account_identifier'] : null;
        $this->account_date_1 = (isset($data['account_date_1'])) ? $data['account_date_1'] : null;
        $this->account_date_2 = (isset($data['account_date_2'])) ? $data['account_date_2'] : null;
        $this->account_date_3 = (isset($data['account_date_3'])) ? $data['account_date_3'] : null;
        $this->account_date_4 = (isset($data['account_date_4'])) ? $data['account_date_4'] : null;
        $this->account_date_5 = (isset($data['account_date_5'])) ? $data['account_date_5'] : null;
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

        $this->contact_1_status = (isset($data['contact_1_status'])) ? $data['contact_1_status'] : null;
        $this->contact_2_status = (isset($data['contact_2_status'])) ? $data['contact_2_status'] : null;
        $this->contact_3_status = (isset($data['contact_3_status'])) ? $data['contact_3_status'] : null;
        $this->contact_4_status = (isset($data['contact_4_status'])) ? $data['contact_4_status'] : null;
        $this->contact_5_status = (isset($data['contact_5_status'])) ? $data['contact_5_status'] : null;
        
        $this->n_title_2 = (isset($data['n_title_2'])) ? $data['n_title_2'] : null;
        $this->n_first_2 = (isset($data['n_first_2'])) ? $data['n_first_2'] : null;
        $this->n_last_2 = (isset($data['n_last_2'])) ? $data['n_last_2'] : null;
        $this->n_fn_2 = (isset($data['n_fn_2'])) ? $data['n_fn_2'] : null;
        $this->email_2 = (isset($data['email_2'])) ? $data['email_2'] : null;
        $this->tel_work_2 = (isset($data['tel_work_2'])) ? $data['tel_work_2'] : null;
        $this->tel_cell_2 = (isset($data['tel_cell_2'])) ? $data['tel_cell_2'] : null;
        
        $this->n_title_3 = (isset($data['n_title_3'])) ? $data['n_title_3'] : null;
        $this->n_first_3 = (isset($data['n_first_3'])) ? $data['n_first_3'] : null;
        $this->n_last_3 = (isset($data['n_last_3'])) ? $data['n_last_3'] : null;
        $this->n_fn_3 = (isset($data['n_fn_3'])) ? $data['n_fn_3'] : null;
        $this->email_3 = (isset($data['email_3'])) ? $data['email_3'] : null;
        $this->tel_work_3 = (isset($data['tel_work_3'])) ? $data['tel_work_3'] : null;
        $this->tel_cell_3 = (isset($data['tel_cell_3'])) ? $data['tel_cell_3'] : null;
        
        $this->n_title_4 = (isset($data['n_title_4'])) ? $data['n_title_4'] : null;
        $this->n_first_4 = (isset($data['n_first_4'])) ? $data['n_first_4'] : null;
        $this->n_last_4 = (isset($data['n_last_4'])) ? $data['n_last_4'] : null;
        $this->n_fn_4 = (isset($data['n_fn_4'])) ? $data['n_fn_4'] : null;
        $this->email_4 = (isset($data['email_4'])) ? $data['email_4'] : null;
        $this->tel_work_4 = (isset($data['tel_work_4'])) ? $data['tel_work_4'] : null;
        $this->tel_cell_4 = (isset($data['tel_cell_4'])) ? $data['tel_cell_4'] : null;
        
        $this->n_title_5 = (isset($data['n_title_5'])) ? $data['n_title_5'] : null;
        $this->n_first_5 = (isset($data['n_first_5'])) ? $data['n_first_5'] : null;
        $this->n_last_5 = (isset($data['n_last_5'])) ? $data['n_last_5'] : null;
        $this->n_fn_5 = (isset($data['n_fn_5'])) ? $data['n_fn_5'] : null;
        $this->email_5 = (isset($data['email_5'])) ? $data['email_5'] : null;
        $this->tel_work_5 = (isset($data['tel_work_5'])) ? $data['tel_work_5'] : null;
        $this->tel_cell_5 = (isset($data['tel_cell_5'])) ? $data['tel_cell_5'] : null;
        
        // Denormalized properties
        $this->site_id = (isset($data['site_id'])) ? $data['site_id'] : null;
        $this->site_identifier = (isset($data['site_identifier'])) ? $data['site_identifier'] : null;
        $this->site_caption = (isset($data['site_caption'])) ? $data['site_caption'] : null;
        $this->area_caption = (isset($data['area_caption'])) ? $data['area_caption'] : null;
    }

    public function getProperties() {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['year'] = $this->year;
    	$data['type'] = $this->type;
    	$data['account_id'] = $this->account_id;
    	$data['status'] = $this->status;
    	$data['caption'] = $this->caption;
    	$data['description'] = $this->description;
    	$data['product_identifier'] = $this->product_identifier;
    	$data['product_brand'] = $this->product_brand;
    	$data['product_caption'] = $this->product_caption;
    	$data['quantity'] = $this->quantity;
    	$data['unit_price'] = $this->unit_price;
    	$data['amount'] = $this->amount;
    	$data['taxable_1_amount'] = $this->taxable_1_amount;
    	$data['taxable_2_amount'] = $this->taxable_2_amount;
    	$data['taxable_3_amount'] = $this->taxable_3_amount;
    	$data['options'] = json_encode($this->options);
    	$data['including_options_amount'] = $this->including_options_amount;
    	$data['taxable_1_total'] = $this->taxable_1_total;
    	$data['taxable_2_total'] = $this->taxable_2_total;
    	$data['taxable_3_total'] = $this->taxable_3_total;
    	$data['identifier'] = $this->identifier;
    	$data['invoice_identifier'] = $this->invoice_identifier;
    	$data['commitment_date'] = ($this->commitment_date) ? $this->commitment_date : null;
    	$data['retraction_limit'] = ($this->retraction_limit) ? $this->retraction_limit : null;
    	$data['retraction_date'] = ($this->retraction_date) ? $this->retraction_date : null;
    	$data['expected_shipment_date'] = ($this->expected_shipment_date) ? $this->expected_shipment_date: null;
    	$data['shipment_date'] = ($this->shipment_date) ? $this->shipment_date: null;
    	$data['expected_delivery_date'] = ($this->expected_delivery_date) ? $this->expected_delivery_date: null;
    	$data['delivery_date'] = ($this->delivery_date) ? $this->delivery_date: null;
    	$data['expected_commissioning_date'] = ($this->expected_commissioning_date) ? $this->expected_commissioning_date: null;
    	$data['commissioning_date'] = ($this->commissioning_date) ? $this->commissioning_date: null;
    	$data['due_date'] = ($this->due_date) ? $this->due_date: null;
    	$data['invoice_date'] = ($this->invoice_date) ? $this->invoice_date: null;
    	$data['order_form_id'] = $this->order_form_id;
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
    	$data['audit'] = json_encode($this->audit);
    	$data['excluding_tax'] = $this->excluding_tax;
    	$data['tax_regime'] = $this->tax_regime;
    	$data['tax_1_amount'] = $this->tax_1_amount;
    	$data['tax_2_amount'] = $this->tax_2_amount;
    	$data['tax_3_amount'] = $this->tax_3_amount;
    	$data['tax_amount'] = $this->tax_amount;
    	$data['tax_inclusive'] = $this->tax_inclusive;
    	$data['means_of_paiement'] = $this->means_of_paiement;
    	$data['commitment_message_id'] = $this->commitment_message_id;
    	$data['change_message_id'] = $this->change_message_id;
    	$data['confirmation_message_id'] = $this->confirmation_message_id;
    	$data['shipment_message_id'] = $this->shipment_message_id;
    	$data['delivery_message_id'] = $this->delivery_message_id;
    	$data['commissioning_message_id'] = $this->commissioning_message_id;
    	$data['invoice_message_id'] = $this->invoice_message_id;
    	$data['notification_time'] = ($this->notification_time) ? $this->notification_time : null;
    	$data['update_time'] = ($this->update_time) ? $this->update_time : null;
  
    	$data['contact_1_status'] = $this->contact_1_status;
    	$data['contact_2_status'] = $this->contact_2_status;
    	$data['contact_3_status'] = $this->contact_3_status;
    	$data['contact_4_status'] = $this->contact_4_status;
    	$data['contact_5_status'] = $this->contact_5_status;
    	 
    	$data['place_caption'] = $this->place_caption;
    	$data['place_identifier'] = $this->place_identifier;
    	$data['place_support_email'] = $this->place_support_email;
    	$data['place_logo_height'] = $this->place_logo_height;
    	$data['place_logo_src'] = $this->place_logo_src;
    	$data['place_config'] = $this->place_config;
    	$data['place_id'] = $this->place_id;
    	$data['account_name'] = $this->account_name;
 
    	$data['default_means_of_payment'] = $this->default_means_of_payment;
    	$data['transfer_order_id'] = $this->transfer_order_id;
    	$data['transfer_order_date'] = $this->transfer_order_date;
    	$data['bank_identifier'] = $this->bank_identifier;
    	
    	$data['n_title'] = $this->n_title;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['n_fn'] = $this->n_fn;
    	$data['email'] = $this->email;
    	$data['tel_work'] = $this->tel_work;
    	$data['tel_cell'] = $this->tel_cell;
    	$data['account_status'] = $this->account_status;
    	$data['account_identifier'] = $this->account_identifier;
    	$data['account_date_1'] = $this->account_date_1;
    	$data['account_date_2'] = $this->account_date_2;
    	$data['account_date_3'] = $this->account_date_3;
    	$data['account_date_4'] = $this->account_date_4;
    	$data['account_date_5'] = $this->account_date_5;
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
    	
    	$data['n_title_2'] = $this->n_title_2;
    	$data['n_first_2'] = $this->n_first_2;
    	$data['n_last_2'] = $this->n_last_2;
    	$data['n_fn_2'] = $this->n_fn_2;
    	$data['email_2'] = $this->email_2;
    	$data['tel_work_2'] = $this->tel_work_2;
    	$data['tel_cell_2'] = $this->tel_cell_2;
    	 
    	$data['n_title_3'] = $this->n_title_3;
    	$data['n_first_3'] = $this->n_first_3;
    	$data['n_last_3'] = $this->n_last_3;
    	$data['n_fn_3'] = $this->n_fn_3;
    	$data['email_3'] = $this->email_3;
    	$data['tel_work_3'] = $this->tel_work_3;
    	$data['tel_cell_3'] = $this->tel_cell_3;
    	
    	$data['n_title_4'] = $this->n_title_4;
    	$data['n_first_4'] = $this->n_first_4;
    	$data['n_last_4'] = $this->n_last_4;
    	$data['n_fn_4'] = $this->n_fn_4;
    	$data['email_4'] = $this->email_4;
    	$data['tel_work_4'] = $this->tel_work_4;
    	$data['tel_cell_4'] = $this->tel_cell_4;
    	
    	$data['n_title_5'] = $this->n_title_5;
    	$data['n_first_5'] = $this->n_first_5;
    	$data['n_last_5'] = $this->n_last_5;
    	$data['n_fn_5'] = $this->n_fn_5;
    	$data['email_5'] = $this->email_5;
    	$data['tel_work_5'] = $this->tel_work_5;
    	$data['tel_cell_5'] = $this->tel_cell_5;

    	if ($this->contact_2_status == 'invoice') {
    		$data['invoice_n_title'] = $this->n_title_2;
    		$data['invoice_n_first'] = $this->n_first_2;
    		$data['invoice_n_last'] = $this->n_last_2;
    		$data['invoice_n_fn'] = $this->n_fn_2;
    		$data['invoice_email'] = $this->email_2;
    		$data['invoice_tel_work'] = $this->tel_work_2;
    		$data['invoice_tel_cell'] = $this->tel_cell_2;
    	}
    	elseif ($this->contact_3_status == 'invoice') {
    		$data['invoice_n_title'] = $this->n_title_3;
    		$data['invoice_n_first'] = $this->n_first_3;
    		$data['invoice_n_last'] = $this->n_last_3;
    		$data['invoice_n_fn'] = $this->n_fn_3;
    		$data['invoice_email'] = $this->email_3;
    		$data['invoice_tel_work'] = $this->tel_work_3;
    		$data['invoice_tel_cell'] = $this->tel_cell_3;
    	}
    	elseif ($this->contact_4_status == 'invoice') {
    		$data['invoice_n_title'] = $this->n_title_4;
    		$data['invoice_n_first'] = $this->n_first_4;
    		$data['invoice_n_last'] = $this->n_last_4;
    		$data['invoice_n_fn'] = $this->n_fn_4;
    		$data['invoice_email'] = $this->email_4;
    		$data['invoice_tel_work'] = $this->tel_work_4;
    		$data['invoice_tel_cell'] = $this->tel_cell_4;
    	}
    	elseif ($this->contact_5_status == 'invoice') {
    		$data['invoice_n_title'] = $this->n_title_5;
    		$data['invoice_n_first'] = $this->n_first_5;
    		$data['invoice_n_last'] = $this->n_last_5;
    		$data['invoice_n_fn'] = $this->n_fn_5;
    		$data['invoice_email'] = $this->email_5;
    		$data['invoice_tel_work'] = $this->tel_work_5;
    		$data['invoice_tel_cell'] = $this->tel_cell_5;
    	}
    	else {
    		$data['invoice_n_title'] = $this->n_title;
    		$data['invoice_n_first'] = $this->n_first;
    		$data['invoice_n_last'] = $this->n_last;
    		$data['invoice_n_fn'] = $this->n_fn;
    		$data['invoice_email'] = $this->email;
    		$data['invoice_tel_work'] = $this->tel_work;
    		$data['invoice_tel_cell'] = $this->tel_cell;
    	}
 
    	return $data;
    }

    public function toArray() 
    {
    	$data = $this->getProperties();

    	unset($data['contact_1_status']);
    	unset($data['contact_2_status']);
    	unset($data['contact_3_status']);
    	unset($data['contact_4_status']);
    	unset($data['contact_5_status']);
    	 
    	unset($data['n_title']);
    	unset($data['n_first']);
    	unset($data['n_last']);
    	unset($data['n_fn']);
    	unset($data['email']);
    	unset($data['tel_work']);
    	unset($data['tel_cell']);
    	unset($data['place_caption']);
    	unset($data['place_identifier']);
    	unset($data['place_support_email']);
    	unset($data['place_logo_height']);
    	unset($data['place_logo_src']);
    	unset($data['place_config']);
    	unset($data['place_id']);
    	
    	unset($data['default_means_of_payment']);
    	unset($data['transfer_order_id']);
    	unset($data['transfer_order_date']);
    	unset($data['bank_identifier']);
    	
    	unset($data['account_status']);
    	unset($data['account_name']);
    	unset($data['account_identifier']);
    	unset($data['account_date_1']);
    	unset($data['account_date_2']);
    	unset($data['account_date_3']);
    	unset($data['account_date_4']);
    	unset($data['account_date_5']);
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

    	unset($data['n_title_2']);
    	unset($data['n_first_2']);
    	unset($data['n_last_2']);
    	unset($data['n_fn_2']);
    	unset($data['email_2']);
    	unset($data['tel_work_2']);
    	unset($data['tel_cell_2']);
    	 
    	unset($data['n_title_3']);
    	unset($data['n_first_3']);
    	unset($data['n_last_3']);
    	unset($data['n_fn_3']);
    	unset($data['email_3']);
    	unset($data['tel_work_3']);
    	unset($data['tel_cell_3']);
    	
    	unset($data['n_title_4']);
    	unset($data['n_first_4']);
    	unset($data['n_last_4']);
    	unset($data['n_fn_4']);
    	unset($data['email_4']);
    	unset($data['tel_work_4']);
    	unset($data['tel_cell_4']);
    	
    	unset($data['n_title_5']);
    	unset($data['n_first_5']);
    	unset($data['n_last_5']);
    	unset($data['n_fn_5']);
    	unset($data['email_5']);
    	unset($data['tel_work_5']);
    	unset($data['tel_cell_5']);

    	unset($data['invoice_n_title']);
    	unset($data['invoice_n_first']);
    	unset($data['invoice_n_last']);
    	unset($data['invoice_n_fn']);
    	unset($data['invoice_email']);
    	unset($data['invoice_tel_work']);
    	unset($data['invoice_tel_cell']);
    	 
    	return $data;
    }
    
    public static function getList($type, $params, $major = 'update_time', $dir = 'DESC', $mode = 'search')
    {
    	$context = Context::getCurrent();
    	$select = Commitment::getTable()->getSelect()
    		->join('core_account', 'commitment.account_id = core_account.id', array('account_status' => 'status', 'account_name' => 'name', 'account_identifier' => 'identifier', 'place_id', 'contact_1_status', 'contact_2_status', 'contact_3_status', 'contact_4_status', 'contact_5_status', 'default_means_of_payment', 'transfer_order_id', 'transfer_order_date', 'bank_identifier', 'account_date_1' => 'date_1', 'account_date_2' => 'date_2', 'account_date_3' => 'date_3', 'account_date_4' => 'date_4', 'account_date_5' => 'date_5', 'account_property_1' => 'property_1', 'account_property_2' => 'property_2', 'account_property_3' => 'property_3', 'account_property_4' => 'property_4', 'account_property_5' => 'property_5', 'account_property_6' => 'property_6', 'account_property_7' => 'property_7', 'account_property_8' => 'property_8', 'account_property_9' => 'property_9', 'account_property_10' => 'property_10', 'account_property_11' => 'property_11', 'account_property_12' => 'property_12', 'account_property_13' => 'property_13', 'account_property_14' => 'property_14', 'account_property_15' => 'property_15', 'account_property_16' => 'property_16'), 'left')
    		->join('core_vcard', 'core_account.contact_1_id = core_vcard.id', array('n_title', 'n_first', 'n_last', 'n_fn', 'email', 'tel_work', 'tel_cell'), 'left')
			->join(array('contact_2' => 'core_vcard'), 'core_account.contact_2_id = contact_2.id', array('n_title_2' =>'n_title', 'n_first_2' => 'n_first', 'n_last_2' => 'n_last', 'n_fn_2' => 'n_fn', 'email_2' => 'email', 'tel_work_2' => 'tel_work', 'tel_cell_2' => 'tel_cell'), 'left')
			->join(array('contact_3' => 'core_vcard'), 'core_account.contact_3_id = contact_3.id', array('n_title_3' =>'n_title', 'n_first_3' => 'n_first', 'n_last_3' => 'n_last', 'n_fn_3' => 'n_fn', 'email_3' => 'email', 'tel_work_3' => 'tel_work', 'tel_cell_3' => 'tel_cell'), 'left')
			->join(array('contact_4' => 'core_vcard'), 'core_account.contact_4_id = contact_4.id', array('n_title_4' =>'n_title', 'n_first_4' => 'n_first', 'n_last_4' => 'n_last', 'n_fn_4' => 'n_fn', 'email_4' => 'email', 'tel_work_4' => 'tel_work', 'tel_cell_4' => 'tel_cell'), 'left')
			->join(array('contact_5' => 'core_vcard'), 'core_account.contact_5_id = contact_5.id', array('n_title_5' =>'n_title', 'n_first_5' => 'n_first', 'n_last_5' => 'n_last', 'n_fn_5' => 'n_fn', 'email_5' => 'email', 'tel_work_5' => 'tel_work', 'tel_cell_5' => 'tel_cell'), 'left')
    		->join('core_place', 'core_account.place_id = core_place.id', array('place_caption' => 'caption', 'place_identifier' => 'identifier', 'place_support_email' => 'support_email', 'place_logo_height' => 'logo_height', 'place_logo_src' => 'logo_src', 'place_config' => 'config'), 'left');
    	
    	$where = new Where();
    	$where->notEqualTo('commitment.status', 'deleted');

    	// Filter on type
		if ($type) $where->equalTo('commitment.type', $type);

		// Filter on place
		$keep = true;
		if (array_key_exists('p-pit-admin', $context->getPerimeters()) && array_key_exists('place_id', $context->getPerimeters()['p-pit-admin'])) {
			$where->in('core_account.place_id', $context->getPerimeters()['p-pit-admin']['place_id']);
		}
		
		// Todo list vs search modes
		if ($mode == 'todo') {

			$todo = $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['todo'];
			foreach($todo as $role => $properties) {
				if ($context->hasRole($role)) {
					foreach($properties as $property => $predicate) {
						if ($predicate['selector'] == 'equalTo') $where->equalTo('commitment.'.$property, $predicate['value']);
						elseif ($predicate['selector'] == 'in') $where->in('commitment.'.$property, $predicate['value']);
						elseif ($predicate['selector'] == 'deadline') $where->lessThanOrEqualTo('commitment.'.$property, date('Y-m-d', strtotime(date('Y-m-d').'+ '.$predicate['value'].' days')));
					}
				}
			}
		}
		else {

			// Set the filters
			foreach ($params as $propertyId => $value) {
				if ($propertyId == 'place_id') {
					if (strpos($value, ',')) $where->in('core_account.'.$propertyId, array_map('trim', explode(',', $value)));
					else $where->equalTo('core_account.place_id', $params['place_id']);
				}
				elseif ($propertyId == 'account_id') $where->equalTo('account_id', $params['account_id']);
				elseif ($propertyId == 'account_name') $where->like('core_account.name', '%'.$params[$propertyId].'%');
				elseif ($propertyId == 'account_status') $where->in('core_account.status', array_map('trim', explode(',', $value)));
				elseif ($propertyId == 'n_fn') $where->like('core_vcard.n_fn', '%'.$params[$propertyId].'%');
				elseif ($propertyId == 'product_identifier') $where->like('product_identifier', '%'.$params[$propertyId].'%');
				elseif ($propertyId == 'min_account_date_1') $where->greaterThanOrEqualTo('core_account.date_1', $params[$propertyId]);
				elseif ($propertyId == 'min_account_date_2') $where->greaterThanOrEqualTo('core_account.date_2', $params[$propertyId]);
				elseif ($propertyId == 'min_account_date_3') $where->greaterThanOrEqualTo('core_account.date_3', $params[$propertyId]);
				elseif ($propertyId == 'min_account_date_4') $where->greaterThanOrEqualTo('core_account.date_4', $params[$propertyId]);
				elseif ($propertyId == 'min_account_date_5') $where->greaterThanOrEqualTo('core_account.date_5', $params[$propertyId]);
				elseif ($propertyId == 'max_account_date_1') $where->lessThanOrEqualTo('core_account.date_1', $params[$propertyId]);
				elseif ($propertyId == 'max_account_date_2') $where->lessThanOrEqualTo('core_account.date_2', $params[$propertyId]);
				elseif ($propertyId == 'max_account_date_3') $where->lessThanOrEqualTo('core_account.date_3', $params[$propertyId]);
				elseif ($propertyId == 'max_account_date_4') $where->lessThanOrEqualTo('core_account.date_4', $params[$propertyId]);
				elseif ($propertyId == 'max_account_date_5') $where->lessThanOrEqualTo('core_account.date_5', $params[$propertyId]);
				elseif ($propertyId == 'account_property_4') $where->in('core_account.property_4', array_map('trim', explode(',', $value)));
				elseif ($propertyId == 'account_property_6') $where->in('core_account.property_6', array_map('trim', explode(',', $value)));
				elseif ($propertyId == 'account_property_10') $where->equalTo('core_account.property_10', $params[$propertyId]);
				elseif ($propertyId == 'account_property_15') $where->equalTo('core_account.property_15', $params[$propertyId]);
				elseif (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('commitment.'.substr($propertyId, 4), $params[$propertyId]);
				elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('commitment.'.substr($propertyId, 4), $params[$propertyId]);
				elseif (substr($propertyId, 0, 8) == 'account_') {
					if (strpos($value, ',')) $where->in('core_account.'.$propertyId, array_map('trim', explode(',', $value)));
					else $where->like('core_account.'.substr($propertyId, 8), '%'.$params[$propertyId].'%');
				}
				else {
					if (strpos($value, ',')) $where->in('commitment.'.$propertyId, array_map('trim', explode(',', $value)));
					else $where->like('commitment.'.$propertyId, '%'.$params[$propertyId].'%');
				}
			}
		}

    	// Sort the list
    	$select->where($where)->order(array($major.' '.$dir, 'identifier'));

    	$cursor = Commitment::getTable()->selectWith($select);
    	$orders = array();
    	foreach ($cursor as $order) {
    		$order->properties = $order->getProperties();
    		$orders[] = $order;
    	}

    	return $orders;
    }

    public static function get($id, $column = 'id')
    {
    	$commitment = Commitment::getTable()->get($id, $column);
    	if (!$commitment) return null;
        if ($commitment->account_id) {
	    	$commitment->account = Account::get($commitment->account_id);
	    	$commitment->place_id = $commitment->account->place_id;
	    	$commitment->account_status = $commitment->account->status;
	    	$commitment->account_name = $commitment->account->name;
	    	$commitment->account_identifier = $commitment->account->identifier;

			$commitment->default_means_of_payment = $commitment->account->default_means_of_payment;
	    	$commitment->transfer_order_id = $commitment->account->transfer_order_id;
			$commitment->transfer_order_date = $commitment->account->transfer_order_date;
			$commitment->bank_identifier = $commitment->account->bank_identifier;
	    	
			$commitment->account_date_1 = $commitment->account->date_1;
			$commitment->account_date_2 = $commitment->account->date_2;
			$commitment->account_date_3 = $commitment->account->date_3;
			$commitment->account_date_4 = $commitment->account->date_4;
			$commitment->account_date_5 = $commitment->account->date_5;
			$commitment->account_property_1 = $commitment->account->property_1;
	    	$commitment->account_property_2 = $commitment->account->property_2;
	    	$commitment->account_property_3 = $commitment->account->property_3;
	    	$commitment->account_property_4 = $commitment->account->property_4;
	    	$commitment->account_property_5 = $commitment->account->property_5;
	    	$commitment->account_property_6 = $commitment->account->property_6;
	    	$commitment->account_property_7 = $commitment->account->property_7;
	    	$commitment->account_property_8 = $commitment->account->property_8;
	    	$commitment->account_property_9 = $commitment->account->property_9;
	    	$commitment->account_property_10 = $commitment->account->property_10;
	    	$commitment->account_property_11 = $commitment->account->property_11;
	    	$commitment->account_property_12 = $commitment->account->property_12;
	    	$commitment->account_property_13 = $commitment->account->property_13;
	    	$commitment->account_property_14 = $commitment->account->property_14;
	    	$commitment->account_property_15 = $commitment->account->property_15;
	    	$commitment->account_property_16 = $commitment->account->property_16;
	    	$commitment->account->contact_1 = Vcard::get($commitment->account->contact_1_id);
	    	$commitment->n_fn = $commitment->account->contact_1->n_fn;
	    	$commitment->email = $commitment->account->contact_1->email;
	    	$commitment->n_first = $commitment->account->contact_1->n_first;
	    	$commitment->n_last = $commitment->account->contact_1->n_last;
	    	$place = Place::get($commitment->account->place_id);
	    	$commitment->place_caption = $place->caption;
	    	$commitment->place_identifier = $place->identifier;
	    	$commitment->place_support_email = $place->support_email;
	    	$commitment->place_logo_height = $place->logo_height;
	    	$commitment->place_logo_src = $place->logo_src;
	    	$commitment->place_config = $place->config;
        }
    	$commitment->properties = $commitment->getProperties();

    	$commitment->terms = Term::getList($commitment->type, array('commitment_id' => $commitment->id), '+due_date');
    	$commitment->termSum = 0;
    	foreach ($commitment->terms as $term) $commitment->termSum += $term->amount;

    	return $commitment;
    }

    public static function getArray($id, $column = 'id')
    {
    	$commitment = Commitment::getTable()->get($id, $column);
    	$commitment->terms = Term::getList($commitment->type, array('commitment_id' => $commitment->id), '+due_date');
    	$commitment->termSum = 0;
    	foreach ($commitment->terms as $term) $commitment->termSum += $term->amount;
    	$data = $commitment->toarray();
        if ($commitment->account_id) {
	    	$data['account'] = Account::get($commitment->account_id)->getProperties();
	    	$data['account_name'] = $data['account']['name'];
    	}
    	return $data;
    }

    public function computeDeadlines()
    {
    	$context = Context::getCurrent();
		foreach ($context->getConfig('commitment'.(($this->type) ? '/'.$this->type : ''))['deadlines'] as $step => $deadline) {
			if ($this->status == $deadline['status']) {
				
				// Retrieve the start date
				if ($this->status == 'new') $start = $this->commitment_date;
				if ($this->status == 'shipped') $start = $this->shipment_date;
				if ($this->status == 'delivered') $start = $this->delivery_date;
				if ($this->status == 'commissioned') $start = $this->commissioning_date;
				if ($this->status == 'invoiced') $start = $this->invoice_date;

				// Compute the expected target date
				$targetDate = strtotime(($deadline['period']) ? $start.' +'.$deadline['period'].' '.$deadline['unit'] : $start);
				if ($step == 'retraction') $this->retraction_limit = date('Y-m-d', $targetDate);
				if ($step == 'shipment') $this->expected_shipment_date = date('Y-m-d', $targetDate);
				if ($step == 'delivery') $this->expected_delivery_date = date('Y-m-d', $targetDate);
				if ($step == 'commissioning') $this->expected_commissioning_date = date('Y-m-d', $targetDate);
				if ($step == 'invoice') $this->due_date = date('Y-m-d', $targetDate);
			}
		}
    }

    public static function instanciate($type)
    {
    	$commitment = new Commitment;
    	$commitment->type = $type;
    	$commitmentYear = CommitmentYear::getCurrent();
    	if ($commitmentYear) $commitment->year = $commitmentYear->year;
    	$commitment->status = 'new';
    	$commitment->properties = $commitment->toArray();
    	$commitment->options = array();
    	$commitment->terms = array();
    	return $commitment;
    }
    
    public static function instanciateFromJson($type, $content)
    {
		$account = Account::get($content['buyer_party'], 'name');
		if (!$account) return null;

		$commitment = Commitment::instanciate($type);
		$commitment->account_id = $account->id;

    	// Load from a JSON web service
    	$commitment->identifier = $content['message_identifier'];
    	$commitment->commitment_date = $content['issue_date'];
    	$commitment->computeDeadlines();

    	return $commitment;
    }

    public static function instanciateFromXcbl($xmlMessage)
    {
    	$commitment = new Commitment;
    
    	// Load from an XML web service
    	$xmlOrder = new XmlOrder($xmlMessage);
    	$commitment->type = $xmlOrder->getType();
    	$commitment->commitment_date = $xmlOrder->getOrderIssueDate();
    	$commitment->identifier = $xmlOrder->getBuyerOrderNumber();
    	$commitment->expected_delivery_date = $xmlOrder->getRequestedDeliverByDate();
    
    	// Check integrity
    	if ($commitment->type == 'unknown') return null;
    
    	return $commitment;
    }
 /*   
    public function computeHeader($proforma = false)
    {
    	$context = Context::getCurrent();
    	$specsId = 'commitment/invoice';
    	$type = $this->type;
    	if ($context->getConfig($specsId.(($type) ? '/'.$type : ''))) $invoiceSpecs = $context->getConfig($specsId.(($type) ? '/'.$type : ''));
    	else $invoiceSpecs = $context->getConfig($specsId);
    	$first = true;
    	foreach($invoiceSpecs['header'] as $line) {
    		$arguments = array();
    		foreach($line['params'] as $propertyId) {
    			if ($propertyId == 'date') $arguments[] = $context->decodeDate(date('Y-m-d'));
    			else {
    				if (array_key_exists($propertyId, $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'])) {
    					$property = $context->getConfig('commitment'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
    				}
    				else {
    					$property = $context->getConfig('commitment')['properties'][$propertyId];
    				}
    				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
    				if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
    				if ($propertyId == 'account_name') $arguments[] = $this->account_name;
    				elseif ($property['type'] == 'date') $arguments[] = $context->decodeDate($this->properties[$propertyId]);
    				elseif ($property['type'] == 'number') $arguments[] = $context->formatFloat($this->properties[$propertyId], 2);
    				elseif ($property['type'] == 'select') $arguments[] = $property['modalities'][$this->properties[$propertyId]][$context->getLocale()];
    				else $arguments[] = $this->properties[$propertyId];
    			}
    		}
    		if (!$first) $this->customer_invoice_name .= "\n";
    		$first = false;
    		$this->customer_invoice_name .= vsprintf($line['format'][$context->getLocale()], $arguments);
    	}
    	$account = $this->account;
    	$invoicingContact = null;
    	if ($account->contact_1_status == 'invoice') $invoicingContact = $account->contact_1;
    	elseif ($account->contact_2_status == 'invoice') $invoicingContact = $account->contact_2;
    	elseif ($account->contact_3_status == 'invoice') $invoicingContact = $account->contact_3;
    	elseif ($account->contact_4_status == 'invoice') $invoicingContact = $account->contact_4;
    	elseif ($account->contact_5_status == 'invoice') $invoicingContact = $account->contact_5;
    		
    	if (!$invoicingContact) {
    		if ($account->contact_1_status == 'main') $invoicingContact = $account->contact_1;
    		elseif ($account->contact_2_status == 'main') $invoicingContact = $account->contact_2;
    		elseif ($account->contact_3_status == 'main') $invoicingContact = $account->contact_3;
    		elseif ($account->contact_4_status == 'main') $invoicingContact = $account->contact_4;
    		elseif ($account->contact_5_status == 'main') $invoicingContact = $account->contact_5;
    	}
    	if (!$invoicingContact) $invoicingContact = $account->contact_1;
    		 
    	$this->customer_n_fn = '';
    	if ($invoicingContact->n_title || $invoicingContact->n_last || $invoicingContact->n_first) {
    		if ($invoicingContact->n_title) $this->customer_n_fn .= $invoicingContact->n_title.' ';
    		$this->customer_n_fn .= $invoicingContact->n_last.' ';
    		$this->customer_n_fn .= $invoicingContact->n_first;
    	}
    	$this->customer_adr_street = $invoicingContact->adr_street;
    	$this->customer_adr_extended = $invoicingContact->adr_extended;
    	$this->customer_adr_post_office_box = $invoicingContact->adr_post_office_box;
    	$this->customer_adr_zip = $invoicingContact->adr_zip;
    	$this->customer_adr_city = $invoicingContact->adr_city;
    	$this->customer_adr_state = $invoicingContact->adr_state;
    	$this->customer_adr_country = $invoicingContact->adr_country;
    }*/
    
    public function computeFooter() 
    {
    	$context = Context::getCurrent();
    	$this->including_options_amount = $this->amount;
    	$this->taxable_1_total = $this->taxable_1_amount;
    	$this->taxable_2_total = $this->taxable_2_amount;
    	$this->taxable_3_total = $this->taxable_3_amount;
    	foreach($this->options as $option) {
    		$this->including_options_amount += $option['amount'];
    		if ($option['vat_id'] == 1) $this->taxable_1_total += $option['amount'];
    		if ($option['vat_id'] == 2) $this->taxable_2_total += $option['amount'];
    		if ($option['vat_id'] == 3) $this->taxable_3_total += $option['amount'];
    	}
    	if ($context->getConfig('commitment/'.$this->type)['tax'] == 'excluding') {
    		$this->excluding_tax = $this->including_options_amount;
    		$this->tax_1_amount = round($this->taxable_1_total * 0.2, 2);
    		$this->tax_2_amount = round($this->taxable_2_total * 0.1, 2);
    		$this->tax_3_amount = round($this->taxable_3_total * 0.055, 2);
    		$this->tax_amount = $this->tax_1_amount + $this->tax_2_amount + $this->tax_3_amount;
    		$this->tax_inclusive = $this->excluding_tax + $this->tax_amount;
    	}
    	else {
    		$this->tax_inclusive = $this->including_options_amount;
    		$this->tax_1_amount = $this->taxable_1_total - round($this->taxable_1_total / 1.2, 2);
    		$this->tax_2_amount = $this->taxable_2_total - round($this->taxable_2_total / 1.1, 2);
    		$this->tax_3_amount = $this->taxable_3_total - round($this->taxable_3_total / 1.055, 2);
    		$this->tax_amount = $this->tax_1_amount + $this->tax_2_amount + $this->tax_3_amount;
    		$this->excluding_tax = $this->tax_inclusive - $this->tax_amount;
    	}
    }
    
    public function loadData($data, $files = null) 
    {
    	$context = Context::getCurrent();
		$settings = $context->getConfig();

    	// Retrieve the data from the request

		if (array_key_exists('year', $data)) {
			$this->year = trim(strip_tags($data['year']));
			if (strlen($this->year) > 255) return 'Integrity';
		}
		
		if (array_key_exists('status', $data)) {
			$this->status = trim(strip_tags($data['status']));
			if (strlen($this->status) > 255) return 'Integrity';
		}

		if (array_key_exists('type', $data)) {
			$this->type = trim(strip_tags($data['type']));
			if (strlen($this->type) > 255) return 'Integrity';
		}
		
		if (array_key_exists('account_id', $data)) $this->account_id = (int) $data['account_id'];

		if (array_key_exists('caption', $data)) {
		    $this->caption = trim(strip_tags($data['caption']));
		   	if (strlen($this->caption) > 255) return 'Integrity';
		}
	    
		if (array_key_exists('description', $data)) {
			$this->description = trim(strip_tags($data['description']));
		    if (strlen($this->description) > 2047) return 'Integrity';
		}

		if (array_key_exists('account_name', $data)) {
			$this->account_name = trim(strip_tags($data['account_name']));
			if (strlen($this->account_name) > 255) return 'Integrity';
		}

		if (array_key_exists('product_caption', $data)) {
			$this->product_caption = trim(strip_tags($data['product_caption']));
			if (strlen($this->product_caption) > 255) return 'Integrity';
		}

		if (array_key_exists('product_brand', $data)) {
			$this->product_brand = trim(strip_tags($data['product_brand']));
			if (strlen($this->product_brand) > 255) return 'Integrity';
		}
		
		if (array_key_exists('product_identifier', $data)) {
			$this->product_identifier = trim(strip_tags($data['product_identifier']));
			if (strlen($this->product_identifier) > 255) return 'Integrity';
		}

		if (array_key_exists('quantity', $data)) {
			$this->quantity = (float) $data['quantity'];
		}

		if (array_key_exists('unit_price', $data)) {
			$this->unit_price = (float) $data['unit_price'];
		}
		
		if (array_key_exists('amount', $data)) {
			$this->amount = trim(strip_tags($data['amount']));
			if (strlen($this->amount) > 255) return 'Integrity';
		}

		if (array_key_exists('taxable_1_amount', $data)) {
			$this->taxable_1_amount = (float) $data['taxable_1_amount'];
		}

		if (array_key_exists('taxable_2_amount', $data)) {
			$this->taxable_2_amount = (float) $data['taxable_2_amount'];
		}

		if (array_key_exists('taxable_3_amount', $data)) {
			$this->taxable_3_amount = (float) $data['taxable_3_amount'];
		}

    	if (array_key_exists('options', $data)) {
    		$this->options = array();
    		foreach($data['options'] as $entry) {
				$entry['identifier'] = trim(strip_tags($entry['identifier']));
    			$productOption = ProductOption::get($entry['identifier'], 'reference');
    			if ($productOption) {
    				$option = array();
    				$option['identifier'] = $entry['identifier'];
					$option['caption'] = $entry['caption'];
    				$option['unit_price'] = $entry['unit_price'];
    				$option['quantity'] = $entry['quantity'];
    				$option['amount'] = $option['unit_price'] * $option['quantity']; // Redundancy to solve
					$option['vat_id'] = $productOption->vat_id; // Redundancy to solve
					$this->options[] = $option;
    			}
    		}
		}
		
		$this->computeFooter();
		
		if (array_key_exists('identifier', $data)) {
			$this->identifier = $data['identifier'];
			if (strlen($this->identifier) > 255) return 'Integrity';
		}
		
		if (array_key_exists('invoice_identifier', $data)) {
			$this->invoice_identifier = $data['invoice_identifier'];
			if (strlen($this->invoice_identifier) > 255) return 'Integrity';
		}

		if (array_key_exists('commitment_date', $data)) {
			$this->commitment_date = trim(strip_tags($data['commitment_date']));
			if ($this->commitment_date && !checkdate(substr($this->commitment_date, 5, 2), substr($this->commitment_date, 8, 2), substr($this->commitment_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('retraction_limit', $data)) {
			$this->retraction_limit = trim(strip_tags($data['retraction_limit']));
			if ($this->retraction_limit && !checkdate(substr($this->retraction_limit, 5, 2), substr($this->retraction_limit, 8, 2), substr($this->retraction_limit, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('retraction_date', $data)) {
			$this->retraction_date = trim(strip_tags($data['retraction_date']));
			if ($this->retraction_date && !checkdate(substr($this->retraction_date, 5, 2), substr($this->retraction_date, 8, 2), substr($this->retraction_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('expected_shipment_date', $data)) {
			$this->expected_shipment_date = trim(strip_tags($data['expected_shipment_date']));
			if ($this->expected_shipment_date && !checkdate(substr($this->expected_shipment_date, 5, 2), substr($this->expected_shipment_date, 8, 2), substr($this->expected_shipment_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('shipment_date', $data)) {
			$this->shipment_date = trim(strip_tags($data['shipment_date']));
			if ($this->shipment_date && !checkdate(substr($this->shipment_date, 5, 2), substr($this->shipment_date, 8, 2), substr($this->shipment_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('expected_delivery_date', $data)) {
			$this->expected_delivery_date = trim(strip_tags($data['expected_delivery_date']));
			if ($this->expected_delivery_date && !checkdate(substr($this->expected_delivery_date, 5, 2), substr($this->expected_delivery_date, 8, 2), substr($this->expected_delivery_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('delivery_date', $data)) {
			$this->delivery_date = trim(strip_tags($data['delivery_date']));
			if ($this->delivery_date && !checkdate(substr($this->delivery_date, 5, 2), substr($this->delivery_date, 8, 2), substr($this->delivery_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('expected_commissioning_date', $data)) {
			$this->expected_commissioning_date = trim(strip_tags($data['expected_commissioning_date']));
			if ($this->expected_commissioning_date && !checkdate(substr($this->expected_commissioning_date, 5, 2), substr($this->expected_commissioning_date, 8, 2), substr($this->expected_commissioning_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('commissioning_date', $data)) {
			$this->commissioning_date = trim(strip_tags($data['commissioning_date']));
			if ($this->commissioning_date && !checkdate(substr($this->commissioning_date, 5, 2), substr($this->commissioning_date, 8, 2), substr($this->commissioning_date, 0, 4))) return 'Integrity';
		}

		if (array_key_exists('due_date', $data)) {
			$this->due_date = trim(strip_tags($data['due_date']));
			if ($this->due_date && !checkdate(substr($this->due_date, 5, 2), substr($this->due_date, 8, 2), substr($this->due_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('invoice_date', $data)) {
			$this->invoice_date = trim(strip_tags($data['invoice_date']));
			if ($this->invoice_date && !checkdate(substr($this->invoice_date, 5, 2), substr($this->invoice_date, 8, 2), substr($this->invoice_date, 0, 4))) return 'Integrity';
		}
		
		if (array_key_exists('property_1', $data)) {
			$this->property_1 = $data['property_1'];
		    if (strlen($this->property_1) > 255) return 'Integrity';
		}

		if (array_key_exists('property_2', $data)) {
			$this->property_2 = $data['property_2'];
	    	if (strlen($this->property_2) > 255) return 'Integrity';
		}

		if (array_key_exists('property_3', $data)) {
			$this->property_3 = $data['property_3'];
		    if (strlen($this->property_3) > 255) return 'Integrity';
		}

		if (array_key_exists('property_4', $data)) {
			$this->property_4 = $data['property_4'];
		    if (strlen($this->property_4) > 255) return 'Integrity';
		}

		if (array_key_exists('property_5', $data)) {
			$this->property_5 = $data['property_5'];
		    if (strlen($this->property_5) > 255) return 'Integrity';
		}

		if (array_key_exists('property_6', $data)) {
			$this->property_6 = $data['property_6'];
		    if (strlen($this->property_6) > 255) return 'Integrity';
		}

		if (array_key_exists('property_7', $data)) {
			$this->property_7 = $data['property_7'];
		    if (strlen($this->property_7) > 255) return 'Integrity';
		}

		if (array_key_exists('property_8', $data)) {
			$this->property_8 = $data['property_8'];
		    if (strlen($this->property_8) > 255) return 'Integrity';
		}

		if (array_key_exists('property_9', $data)) {
			$this->property_9 = $data['property_9'];
		    if (strlen($this->property_9) > 255) return 'Integrity';
		}

		if (array_key_exists('property_10', $data)) {
			$this->property_10 = $data['property_10'];
		    if (strlen($this->property_10) > 255) return 'Integrity';
		}

		if (array_key_exists('property_11', $data)) {
			$this->property_11 = $data['property_11'];
		    if (strlen($this->property_11) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_12', $data)) {
			$this->property_12 = $data['property_12'];
		    if (strlen($this->property_12) > 255) return 'Integrity';
		}

		if (array_key_exists('property_13', $data)) {
			$this->property_13 = $data['property_13'];
			if (strlen($this->property_13) > 255) return 'Integrity';
		}

		if (array_key_exists('property_14', $data)) {
			$this->property_14 = $data['property_14'];
			if (strlen($this->property_14) > 255) return 'Integrity';
		}

		if (array_key_exists('property_15', $data)) {
			$this->property_15 = $data['property_15'];
			if (strlen($this->property_15) > 255) return 'Integrity';
		}

		if (array_key_exists('property_16', $data)) {
			$this->property_16 = $data['property_16'];
			if (strlen($this->property_16) > 255) return 'Integrity';
		}

		if (array_key_exists('property_17', $data)) {
			$this->property_17 = $data['property_17'];
			if (strlen($this->property_17) > 255) return 'Integrity';
		}

		if (array_key_exists('property_18', $data)) {
			$this->property_18 = $data['property_18'];
			if (strlen($this->property_18) > 255) return 'Integrity';
		}

		if (array_key_exists('property_19', $data)) {
			$this->property_19 = $data['property_19'];
			if (strlen($this->property_19) > 255) return 'Integrity';
		}

		if (array_key_exists('property_20', $data)) {
			$this->property_20 = $data['property_20'];
			if (strlen($this->property_20) > 255) return 'Integrity';
		}

		if (array_key_exists('property_21', $data)) {
			$this->property_11 = $data['property_21'];
			if (strlen($this->property_21) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_22', $data)) {
			$this->property_12 = $data['property_22'];
			if (strlen($this->property_22) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_23', $data)) {
			$this->property_14 = $data['property_23'];
			if (strlen($this->property_23) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_24', $data)) {
			$this->property_14 = $data['property_24'];
			if (strlen($this->property_24) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_25', $data)) {
			$this->property_15 = $data['property_25'];
			if (strlen($this->property_25) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_26', $data)) {
			$this->property_16 = $data['property_26'];
			if (strlen($this->property_26) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_27', $data)) {
			$this->property_17 = $data['property_27'];
			if (strlen($this->property_27) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_28', $data)) {
			$this->property_18 = $data['property_28'];
			if (strlen($this->property_28) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_29', $data)) {
			$this->property_19 = $data['property_29'];
			if (strlen($this->property_29) > 255) return 'Integrity';
		}
		
		if (array_key_exists('property_30', $data)) {
			$this->property_20 = $data['property_30'];
			if (strlen($this->property_30) > 255) return 'Integrity';
		}
		
		if (array_key_exists('comment', $data)) {
			$this->comment = trim(strip_tags($data['comment']));
    		if (strlen($this->comment) > 2047) return 'Integrity';
		}
    	
		$this->files = $files;

		// Update the audit
    	$this->audit[] = array(
				'status' => $this->status,
				'time' => Date('Y-m-d G:i:s'),
				'n_fn' => array_key_exists('n_fn', $data) ? $data['n_fn'] : $context->getFormatedName(),
				'comment' => $this->comment,
		);
	    $this->notification_time = null;
		$this->properties = $this->toArray();
		return 'OK';
    }

    public function add($xcblOrder = null)
    {
		$context = Context::getCurrent();
		$config = $context->getConfig();

    	// Check consistency
    	$commitment = Commitment::getTable()->get($this->identifier, 'identifier');
//    	if ($commitment) return 'Duplicate'; // Already exists

    	if (!$this->commitment_date) $this->commitment_date = date('Y-m-d');
    	$this->id = Commitment::getTable()->save($this);
		if (!$this->identifier) $this->identifier = sprintf('%1$06d', $this->id);
		
    	Commitment::getTable()->save($this);

    	// Send the confirmation message
    	if ($xcblOrder) {
    	
			// To be completed
    	}
    	return 'OK';
    }

    /**
     * Restfull implementation
     */
    public function loadAndAdd($data)
    {
    	$context = Context::getCurrent();
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', 'commitment->loadData: '.$rc];
    
    	$rc = $this->add();
    	if ($rc != 'OK') return ['500', 'commitment->add: '.$rc];
    
    	$this->properties = $this->getProperties();
    	return ['200', $this->id];
    }
    
    public function update($update_time, $xcblOrderResponse = null)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	$commitment = Commitment::get($this->id);

    	// Isolation check
    	if ($update_time && $commitment->update_time > $update_time) return 'Isolation';

    	// Consistency check
	    $select = Commitment::getTable()->getSelect()->columns(array('id'))->where(array('identifier' => $this->identifier, 'id <> ?' => $this->id));
	    $cursor = Commitment::getTable()->selectWith($select);
//	    if (count($cursor) > 0) return 'Duplicate';

    	// Save the order form and the commitment
    	if ($this->files) {
    		$root_id = Document::getTable()->get(0, 'parent_id')->id; 
    		$document = Document::instanciate($root_id);
    		$document->files = $this->files;
    		$document->saveFile();
    		$this->order_form_id = $document->save();
    	}
    	Commitment::getTable()->save($this);

    	// Send the confirmation message
    	if ($xcblOrderResponse) {

    		$orderMessage = Message::get($this->$commitment_message_id);
			$xcblOrder = new XcblOrder($orderMessage);
    		$xcblOrderResponse->setOrderResponseIssueDate(date('Y-m-d').'T'.date('G:i:s'));
    		$xcblOrderResponse->setOrderReference($this->identifier);
    		$xcblOrderResponse->setSellerIdent($xcblOrder->getSellerIdent());
    		$xcblOrderResponse->setBuyerIdent($xcblOrder->getBuyerIdent());
    		$xcblOrderResponse->setHeaderStatusEvent($this->expected_delivery_date);
    	}
    	return 'OK';
    }

    public function record($step)
    {
    	$context = Context::getCurrent();
    	$accountingChart = $context->getConfig('journal/accountingChart/sale')[$this->type];
    	if (array_key_exists($step, $accountingChart)) {
    		$step = $accountingChart[$step];
	    	$journalEntry = Journal::instanciate();
	    	$data = array();
	    	$data['operation_date'] = $this->invoice_date;
	    	$data['reference'] = $this->invoice_identifier;
	    	$data['caption'] = $this->caption;
	    	$data['commitment_id'] = $this->id;
	    	$data['rows'] = array();
	    	foreach ($step as $account => $rule) {
	    		$amount = $this->properties[$rule['source']];
	    		if ($amount > 0) {
	    			$row = array();
	    			$row['account'] = $account;
	    			$row['direction'] = $rule['direction'];
	    			$row['amount'] = $amount;
	    			$data['rows'][] = $row;
	    		}
	    	}
	    	$journalEntry->loadData($data);
	    	$journalEntry->add();
    	}
    }
    
    public function invoice($invoiceProperties, $request)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    
    	// Submit the response message
    	$credentials = $context->getConfig('commitment')['invoiceMessage'];
    
    	$client = new Client(
    			$credentials['url'],
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    	);
    
    	$client->setAuth($credential['core_user'], $credential['password'], Client::AUTH_BASIC);
    	$client->setEncType('text/xml');
    	$client->setMethod('POST');

    	$supplyerSheet = $context->getInstance()->getContactSpecs()['supplyerIdentificationSheet'];
    	$customerSheet = $context->getInstance()->getContactSpecs()['customerIdentificationSheet'];

    	$xmlMessage = Message::get($this->commitment_message_id);
    	$xmlOrder = new XmlOrder(new \SimpleXMLElement($xmlMessage->content));

    	$xmlUblInvoiceResponse = new XmlUblInvoice;
    	$xmlUblInvoiceResponse->setID($invoiceProperties['1-cbc:ID']);
    	$xmlUblInvoiceResponse->setIssueDate($invoiceProperties['4-cbc:IssueDate']);
    	$xmlUblInvoiceResponse->setContractDocumentReference('Bon de commande');
    	$xmlUblInvoiceResponse->setDelivery(null, $xmlOrder->getShipToPartyName(), $xmlOrder->getShipToPartyCity(), $xmlOrder->getShipToPartyPostalCode());
       	$xmlUblInvoiceResponse->setPaymentMeans($invoiceProperties['49-cbc:PaymentDueDate'], null, $supplyerSheet['PayeeFinancialAccount']);
    	$xmlUblInvoiceResponse->setPaymentTerms($supplyerSheet['PaymentTerms']);
    	$xmlUblInvoiceResponse->setTaxTotal($invoiceProperties['57-cbc:TaxAmount'], $invoiceProperties['58-cbc:TaxableAmount'], $invoiceProperties['60-cbc:Percent']);
    	$xmlUblInvoiceResponse->setLegalMonetaryTotal($invoiceProperties['62-cbc:LineExtensionAmount'], $order->excluding_tax, $invoiceProperties['64-cbc:TaxInclusiveAmount'], $invoiceProperties['65-cbc:PayableAmount']);

    	for ($i = 0; $i < $xmlOrder->getNumberOfLines(); $i++) {
    		$xmlUblInvoiceResponse->addInvoiceLine(
    				$i+1,
    				$xmlOrder->getLineTotalQuantity($i),
    				$xmlOrder->getLineItemTotal($i),
    				'EUR',
    				$this->commissioning_date,
    				round($xmlOrder->getLineItemTotal($i) * $invoiceProperties['60-cbc:Percent'] / 100, 2),
    				$xmlOrder->getLineItemTotal($i),
    				'TVA',
    				$xmlOrder->getLineItemTotal($i),
    				$xmlOrder->getLineCalculatedPriceBasisQuantity($i)
    		);
    	}
    	// Save the message
    	$message = Message::instanciate('orderResponse/invoice', $xmlUblInvoiceResponse->asXML());
    	$message->type = 'INVOICE';
    	$message->identifier = $this->identifier;
    	$message->add();
    	
    	// Add the message id to the order
    	$this->invoice_message_id = $message->id;
    	
    	$xmlUblInvoiceResponse->setUUID($message->id);
    	$content = $xmlUblInvoiceResponse->asXML();
    	$message->content = $content;
    	
    	// Post the confirmation message
    	$client->setRawBody($content);
    	$response = $client->send();
    	$message->http_status = $response->renderStatusLine();
    	
    	// Write to the log
    	if ($context->getConfig()['isTraceActive']) {
    		$writer = new \Zend\Log\Writer\Stream('data/log/orderResponse.txt');
    		$logger = new \Zend\Log\Logger();
    		$logger->addWriter($writer);
    		$logger->info('invoice;'.$this->identifier.';'.$response->renderStatusLine());
    	}
    	
    	// Save the message
    	$message->update($message->update_time);
    	 
    	return 'OK';
    }

    public static function notify()
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	$select = Commitment::getTable()->getSelect()->where(array('notification_time' => null));
		$cursor = Commitment::getTable()->selectWith($select);

		$newCommitments = array();
		
		foreach ($context->getConfig('commitment')['types'] as $type => $properties) {
			$newCommitments[$type] = array();
		}
		foreach($cursor as $commiment) {
			if ($commitment->status == 'new') $newCommitments[$commitment->type][] = $commitment->identifier;
			$commitment->notification_time = date('Y-m-d H:i:s');
			Commitment::getTable()->save($commitment);
		}
		
		foreach ($context->getConfig('commitment')['types'] as $type => $properties) {
			if (count($newCommitments[$type]) > 0) {
	    		$select = Vcard::getTable()->getSelect();
	    		$where = new Where;
	    		$where->like('roles', '%sales_manager%');
	    		$select->where($where);
	    		$cursor = Vcard::getTable()->selectWith($select);
	    		$url = $config['ppitCoreSettings']['domainName'];
	    		$title = sprintf($context->getConfig('commitment')['messages']['addTitle']['fr_FR'], $properties['labels'][$context->getLocale()]);
	    		$text = sprintf($context->getConfig('commitment')['messages']['addText']['fr_FR'], $url, $properties['labels'][$context->getLocale()], implode(',', $newCommitments[$type]));
	    		foreach ($cursor as $contact) {
	    			ContactMessage::sendMail($contact->email, $text, $title, null);
	    		}
	    	}
		}
    }
    
    public function isUsed($object)
    {
        // Allow or not deleting an account
    	if (get_class($object) == 'PpitCore\Model\Account') {
    		if (Generic::getTable()->cardinality('commitment', array('account_id' => $object->id)) > 0) return true;
    	}
    	return false;
    }
    
    public function isDeletable() {
    
    	// Check the commitment status
    	if ($this->status != 'new') return false;
    	return true;
    }
    
    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$commitment = Commitment::get($this->id);
    
    	// Isolation check
    	if ($commitment->update_time > $update_time) return 'Isolation';
 
    	Term::getTable()->multipleDelete(array('commitment_id' => $this->id));
    	Commitment::getTable()->delete($this->id);
    
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
    	if (!Commitment::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Commitment::$table = $sm->get(CommitmentTable::class);
    	}
    	return Commitment::$table;
    }
}
