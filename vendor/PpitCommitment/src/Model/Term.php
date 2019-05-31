<?php
namespace PpitCommitment\Model;

use PpitCore\Model\Account;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use PpitCore\Model\Place;
use Zend\Db\Sql\Where;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Term
{
	public static $model = array(
		'entities' => array(
			'term' => 				['table' => 'commitment_term'],
			'commitment' => 		['table' => 'commitment'],
			'core_account' => 		['table' => 'core_account'],
			'core_place' => 		['table' => 'core_place'],
			'invoice_account' => 	['table' => 'core_account', 'foreign_key' => 'invoice_account_id'],
		),
		'properties' => array(
			'status' => 					['entity' => 'commitment_term', 'column' => 'status'],
			'type' => 						['entity' => 'commitment_term', 'column' => 'type'],
			'commitment_id' => 				['entity' => 'commitment_term', 'column' => 'commitment_id'],
			'account_status' => 			['entity' => 'core_account', 'column' => 'status'],
			'name' => 						['entity' => 'core_account', 'column' => 'name'],
			'account_identifier' => 		['entity' => 'core_account', 'column' => 'identifier'],
			'commitment_caption' => 		['entity' => 'commitment', 'column' => 'caption'],
			'place_id' => 					['entity' => 'commitment', 'column' => 'place_id'],
			'place_identifier' => 			['entity' => 'core_place', 'column' => 'place_identifier'],
			'place_caption' => 				['entity' => 'core_place', 'column' => 'place_caption'],
			'invoice_account_id' => 		['entity' => 'commitment_term', 'column' => 'invoice_account_id'],
			'invoice_account_name' => 		['entity' => 'invoice_account', 'column' => 'name'],
			'caption' => 					['entity' => 'commitment_term', 'column' => 'caption'],
			'due_date' => 					['entity' => 'commitment_term', 'column' => 'due_date'],
			'settlement_date' => 			['entity' => 'commitment_term', 'column' => 'settlement_date'],
			'collection_date' => 			['entity' => 'commitment_term', 'column' => 'collection_date'],
			'quantity' => 					['entity' => 'commitment_term', 'column' => 'quantity'],
			'unit_price' => 				['entity' => 'commitment_term', 'column' => 'unit_price'],
			'amount' => 					['entity' => 'commitment_term', 'column' => 'amount'],
			'means_of_payment' => 			['entity' => 'commitment_term', 'column' => 'means_of_payment'],
			'default_means_of_payment' => 	['entity' => 'core_account', 'column' => 'default_means_of_payment'],
			'transfer_order_id' => 			['entity' => 'core_account', 'column' => 'transfer_order_id'],
			'transfer_order_date' => 		['entity' => 'core_account', 'column' => 'transfer_order_date'],
			'bank_identifier' => 			['entity' => 'core_account', 'column' => 'bank_identifier'],
			'bank_name' => 					['entity' => 'commitment_term', 'column' => 'bank_name'],
			'reference' => 					['entity' => 'commitment_term', 'column' => 'reference'],
			'comment' => 					['entity' => 'commitment_term', 'column' => 'comment'],
			'document' => 					['entity' => 'commitment_term', 'column' => 'document'],
			'invoice_id' => 				['entity' => 'commitment_term', 'column' => 'invoice_id'],
			'invoice_identifier' => 		['entity' => 'commitment_term', 'column' => 'invoice_identifier'],
			'tiny_1' => 					['entity' => 'commitment_term', 'column' => 'tiny_1'],
			'tiny_2' => 					['entity' => 'commitment_term', 'column' => 'tiny_2'],
			'tiny_3' => 					['entity' => 'commitment_term', 'column' => 'tiny_3'],
			'tiny_4' => 					['entity' => 'commitment_term', 'column' => 'tiny_4'],
			'tiny_5' => 					['entity' => 'commitment_term', 'column' => 'tiny_5'],
			'update_time' => 				['entity' => 'commitment_term', 'column' => 'update_time'],
			'commitment_property_1' => 		['entity' => 'commitment', 'column' => 'property_1'],
			'commitment_property_2' => 		['entity' => 'commitment', 'column' => 'property_2'],
			'commitment_property_3' => 		['entity' => 'commitment', 'column' => 'property_3'],
			'commitment_property_4' => 		['entity' => 'commitment', 'column' => 'property_4'],
			'commitment_property_5' => 		['entity' => 'commitment', 'column' => 'property_5'],
			'commitment_property_6' => 		['entity' => 'commitment', 'column' => 'property_6'],
			'commitment_property_7' => 		['entity' => 'commitment', 'column' => 'property_7'],
			'commitment_property_8' => 		['entity' => 'commitment', 'column' => 'property_8'],
			'commitment_property_9' => 		['entity' => 'commitment', 'column' => 'property_9'],
			'commitment_property_10' => 	['entity' => 'commitment', 'column' => 'property_10'],
			'commitment_property_11' => 	['entity' => 'commitment', 'column' => 'property_11'],
			'commitment_property_12' => 	['entity' => 'commitment', 'column' => 'property_12'],
			'commitment_property_13' => 	['entity' => 'commitment', 'column' => 'property_13'],
			'commitment_property_14' => 	['entity' => 'commitment', 'column' => 'property_14'],
			'commitment_property_15' => 	['entity' => 'commitment', 'column' => 'property_15'],
			'commitment_property_16' => 	['entity' => 'commitment', 'column' => 'property_16'],
			'commitment_property_17' => 	['entity' => 'commitment', 'column' => 'property_17'],
			'commitment_property_18' => 	['entity' => 'commitment', 'column' => 'property_18'],
			'commitment_property_19' => 	['entity' => 'commitment', 'column' => 'property_19'],
			'commitment_property_20' => 	['entity' => 'commitment', 'column' => 'property_20'],
			'commitment_property_21' => 	['entity' => 'commitment', 'column' => 'property_21'],
			'commitment_property_22' => 	['entity' => 'commitment', 'column' => 'property_22'],
			'commitment_property_23' => 	['entity' => 'commitment', 'column' => 'property_23'],
			'commitment_property_24' => 	['entity' => 'commitment', 'column' => 'property_24'],
			'commitment_property_25' => 	['entity' => 'commitment', 'column' => 'property_25'],
			'commitment_property_26' => 	['entity' => 'commitment', 'column' => 'property_26'],
			'commitment_property_27' => 	['entity' => 'commitment', 'column' => 'property_27'],
			'commitment_property_28' => 	['entity' => 'commitment', 'column' => 'property_28'],
			'commitment_property_29' => 	['entity' => 'commitment', 'column' => 'property_29'],
			'commitment_property_30' => 	['entity' => 'commitment', 'column' => 'property_30'],

			'account_date_1' => 			['entity' => 'core_account', 'column' => 'date_1'],
			'account_date_2' => 			['entity' => 'core_account', 'column' => 'date_2'],
			'account_date_3' => 			['entity' => 'core_account', 'column' => 'date_3'],
			'account_date_4' => 			['entity' => 'core_account', 'column' => 'date_4'],
			'account_date_5' => 			['entity' => 'core_account', 'column' => 'date_5'],
			'account_property_1' => 		['entity' => 'core_account', 'column' => 'property_1'],
			'account_property_2' => 		['entity' => 'core_account', 'column' => 'property_2'],
			'account_property_3' => 		['entity' => 'core_account', 'column' => 'property_3'],
			'account_property_4' => 		['entity' => 'core_account', 'column' => 'property_4'],
			'account_property_5' => 		['entity' => 'core_account', 'column' => 'property_5'],
			'account_property_6' => 		['entity' => 'core_account', 'column' => 'property_6'],
			'account_property_7' => 		['entity' => 'core_account', 'column' => 'property_7'],
			'account_property_8' => 		['entity' => 'core_account', 'column' => 'property_8'],
			'account_property_9' => 		['entity' => 'core_account', 'column' => 'property_9'],
			'account_property_10' => 		['entity' => 'core_account', 'column' => 'property_10'],
			'account_property_11' => 		['entity' => 'core_account', 'column' => 'property_11'],
			'account_property_12' => 		['entity' => 'core_account', 'column' => 'property_12'],
			'account_property_13' => 		['entity' => 'core_account', 'column' => 'property_13'],
			'account_property_14' => 		['entity' => 'core_account', 'column' => 'property_14'],
			'account_property_15' => 		['entity' => 'core_account', 'column' => 'property_15'],
			'account_property_16' => 		['entity' => 'core_account', 'column' => 'property_16'],
		),
	);

	public static function getConfig($type) {
		$context = Context::getCurrent();
		$properties = array();
		$description = $context->getConfig('commitmentTerm/'.$type);
		if (!$description) $description = $context->getConfig('commitmentTerm/generic');
		foreach($description['properties'] as $propertyId) {
			$property = $context->getConfig('commitmentTerm/'.$type.'/property/'.$propertyId);
			if (!$property) $property = $context->getConfig('commitmentTerm/generic/property/'.$propertyId);
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			if ($property) {
				if (!array_key_exists('private', $property)) $property['private'] = false;
				if (!$property['private'] || $context->hasRole('dpo')) {
					if ($propertyId == 'place_id') {
						$property['modalities'] = array();
						foreach (Place::getList(array()) as $place) $property['modalities'][$place->id] = $place->caption;
					}
					elseif ($propertyId == 'invoice_account_id') {
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
		}
		return $properties;
	}
	
	public static function getConfigSearch($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configSearch = $context->getConfig('commitmentTerm/search/'.$type);
		if (!$configSearch) $configSearch = $context->getConfig('commitmentTerm/search/generic');
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
		$configList = $context->getConfig('commitmentTerm/list/'.$type);
		if (!$configList) $configList = $context->getConfig('commitmentTerm/list/generic');
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
		$configUpdate = $context->getConfig('commitmentTerm/update/'.$type);
		if (!$configUpdate) $configUpdate = $context->getConfig('commitmentTerm/update/generic');
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

	public static function getConfigGenerate($type, $configProperties)
	{
		$context = Context::getCurrent();
		$configGenerate = $context->getConfig('commitmentTerm/generate/'.$type);
		if (!$configGenerate) $configGenerate = $context->getConfig('commitmentTerm/generate/generic');
		$properties = array();
		foreach ($configGenerate as $propertyId => $options) {
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
		$configUpdate = $context->getConfig('commitmentTerm/groupUpdate/'.$type);
		if (!$configUpdate) $configUpdate = $context->getConfig('commitmentTerm/groupUpdate/generic');
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
		$configExport = $context->getConfig('commitmentTerm/export/'.$type);
		if (!$configExport) $configExport = $context->getConfig('commitmentTerm/export/generic');
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
		$config = $context->getConfig('commitmentTerm/'.$type);
		if (!$config) $config = $context->getConfig('commitmentTerm/generic');
	
		$description = array();
		$description['type'] = $type;
		$description['options'] = (array_key_exists('options', $config)) ? $config['options'] : array();
		$description['properties'] = Term::getConfig($type);
		$description['search'] = Term::getConfigSearch($type, $description['properties']);
		$description['list'] = Term::getConfigList($type, $description['properties']);
		$description['update'] = Term::getConfigUpdate($type, $description['properties']);
		$description['generate'] = Term::getConfigGenerate($type, $description['properties']);
		$description['groupUpdate'] = Term::getConfigGroupUpdate($type, $description['properties']);
		$description['export'] = Term::getConfigEXport($type, $description['properties']);
		return $description;
	}
	
	public $id;
	public $instance_id;
	public $status;
	public $type;
	public $commitment_id;
	public $invoice_account_id;
	public $caption;
	public $due_date;
	public $settlement_date;
	public $collection_date;
	public $quantity;
	public $unit_price;
	public $amount;
	public $means_of_payment;
	public $bank_name;
	public $reference;
	public $comment;
	public $document;
	public $invoice_id;
	public $invoice_identifier;
	public $tiny_1;
	public $tiny_2;
	public $tiny_3;
	public $tiny_4;
	public $tiny_5;
	public $audit;
	public $update_time;
	
	//Deprecated
	public $invoice_n_last;
	
	// Joined properties
	public $name;
	public $account_identifier;
	public $commitment_caption;
	public $place_id;
	public $place_caption;
	public $place_identifier;
	public $invoice_account_name;
	
	public $default_means_of_payment;
	public $transfer_order_id;
	public $transfer_order_date;
	public $bank_identifier;
	
	public $commitment_property_1;
	public $commitment_property_2;
	public $commitment_property_3;
	public $commitment_property_4;
	public $commitment_property_5;
	public $commitment_property_6;
	public $commitment_property_7;
	public $commitment_property_8;
	public $commitment_property_9;
	public $commitment_property_10;
	public $commitment_property_11;
	public $commitment_property_12;
	public $commitment_property_13;
	public $commitment_property_14;
	public $commitment_property_15;
	public $commitment_property_16;
	public $commitment_property_17;
	public $commitment_property_18;
	public $commitment_property_19;
	public $commitment_property_20;
	public $commitment_property_21;
	public $commitment_property_22;
	public $commitment_property_23;
	public $commitment_property_24;
	public $commitment_property_25;
	public $commitment_property_26;
	public $commitment_property_27;
	public $commitment_property_28;
	public $commitment_property_29;
	public $commitment_property_30;
	
	public $account_status;
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
		$this->commitment_id = (isset($data['commitment_id'])) ? $data['commitment_id'] : null;
		$this->invoice_account_id = (isset($data['invoice_account_id'])) ? $data['invoice_account_id'] : null;
		$this->caption = (isset($data['caption'])) ? $data['caption'] : null;
		$this->due_date = (isset($data['due_date'])) ? $data['due_date'] : null;
		$this->settlement_date = (isset($data['settlement_date'])) ? (($data['settlement_date'] == '9999-12-31') ? null : $data['settlement_date']) : null;
		$this->collection_date = (isset($data['collection_date'])) ? (($data['collection_date'] == '9999-12-31') ? null : $data['collection_date']) : null;
		$this->quantity = (isset($data['quantity'])) ? $data['quantity'] : null;
		$this->unit_price = (isset($data['unit_price'])) ? $data['unit_price'] : null;
		$this->amount = (isset($data['amount'])) ? $data['amount'] : null;
		$this->means_of_payment = (isset($data['means_of_payment'])) ? $data['means_of_payment'] : null;
		$this->bank_name = (isset($data['bank_name'])) ? $data['bank_name'] : null;
		$this->reference = (isset($data['reference'])) ? $data['reference'] : null;
		$this->comment = (isset($data['comment'])) ? $data['comment'] : null;
		$this->document = (isset($data['document'])) ? $data['document'] : null;
		$this->invoice_id = (isset($data['invoice_id'])) ? $data['invoice_id'] : null;
		$this->invoice_identifier = (isset($data['invoice_identifier'])) ? $data['invoice_identifier'] : null;
		$this->tiny_1 = (isset($data['tiny_1'])) ? $data['tiny_1'] : null;
		$this->tiny_2 = (isset($data['tiny_2'])) ? $data['tiny_2'] : null;
		$this->tiny_3 = (isset($data['tiny_3'])) ? $data['tiny_3'] : null;
		$this->tiny_4 = (isset($data['tiny_4'])) ? $data['tiny_4'] : null;
		$this->tiny_5 = (isset($data['tiny_5'])) ? $data['tiny_5'] : null;
		$this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
		$this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

		// Deprecated
		$this->invoice_n_last = (isset($data['invoice_n_last'])) ? $data['invoice_n_last'] : null;
		
		// Joined properties
		$this->name = (isset($data['name'])) ? $data['name'] : null;
		$this->account_identifier = (isset($data['account_identifier'])) ? $data['account_identifier'] : null;
		$this->commitment_caption = (isset($data['commitment_caption'])) ? $data['commitment_caption'] : null;
		$this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
		$this->place_caption = (isset($data['place_caption'])) ? $data['place_caption'] : null;
		$this->place_identifier = (isset($data['place_identifier'])) ? $data['place_identifier'] : null;
		$this->invoice_account_name = (isset($data['invoice_account_name'])) ? $data['invoice_account_name'] : null;

		$this->default_means_of_payment = (isset($data['default_means_of_payment'])) ? $data['default_means_of_payment'] : null;
		$this->transfer_order_id = (isset($data['transfer_order_id'])) ? $data['transfer_order_id'] : null;
		$this->transfer_order_date = (isset($data['transfer_order_date'])) ? $data['transfer_order_date'] : null;
		$this->bank_identifier = (isset($data['bank_identifier'])) ? $data['bank_identifier'] : null;

		$this->commitment_property_1 = (isset($data['commitment_property_1'])) ? $data['commitment_property_1'] : null;
		$this->commitment_property_2 = (isset($data['commitment_property_2'])) ? $data['commitment_property_2'] : null;
		$this->commitment_property_3 = (isset($data['commitment_property_3'])) ? $data['commitment_property_3'] : null;
		$this->commitment_property_4 = (isset($data['commitment_property_4'])) ? $data['commitment_property_4'] : null;
		$this->commitment_property_5 = (isset($data['commitment_property_5'])) ? $data['commitment_property_5'] : null;
		$this->commitment_property_6 = (isset($data['commitment_property_6'])) ? $data['commitment_property_6'] : null;
		$this->commitment_property_7 = (isset($data['commitment_property_7'])) ? $data['commitment_property_7'] : null;
		$this->commitment_property_8 = (isset($data['commitment_property_8'])) ? $data['commitment_property_8'] : null;
		$this->commitment_property_9 = (isset($data['commitment_property_9'])) ? $data['commitment_property_9'] : null;
		$this->commitment_property_10 = (isset($data['commitment_property_10'])) ? $data['commitment_property_10'] : null;
		$this->commitment_property_11 = (isset($data['commitment_property_11'])) ? $data['commitment_property_11'] : null;
		$this->commitment_property_12 = (isset($data['commitment_property_12'])) ? $data['commitment_property_12'] : null;
		$this->commitment_property_13 = (isset($data['commitment_property_13'])) ? $data['commitment_property_13'] : null;
		$this->commitment_property_14 = (isset($data['commitment_property_14'])) ? $data['commitment_property_14'] : null;
		$this->commitment_property_15 = (isset($data['commitment_property_15'])) ? $data['commitment_property_15'] : null;
		$this->commitment_property_16 = (isset($data['commitment_property_16'])) ? $data['commitment_property_16'] : null;
		$this->commitment_property_17 = (isset($data['commitment_property_17'])) ? $data['commitment_property_17'] : null;
		$this->commitment_property_18 = (isset($data['commitment_property_18'])) ? $data['commitment_property_18'] : null;
		$this->commitment_property_19 = (isset($data['commitment_property_19'])) ? $data['commitment_property_19'] : null;
		$this->commitment_property_20 = (isset($data['commitment_property_20'])) ? $data['commitment_property_20'] : null;
		$this->commitment_property_21 = (isset($data['commitment_property_21'])) ? $data['commitment_property_21'] : null;
		$this->commitment_property_22 = (isset($data['commitment_property_22'])) ? $data['commitment_property_22'] : null;
		$this->commitment_property_23 = (isset($data['commitment_property_23'])) ? $data['commitment_property_23'] : null;
		$this->commitment_property_24 = (isset($data['commitment_property_24'])) ? $data['commitment_property_24'] : null;
		$this->commitment_property_25 = (isset($data['commitment_property_25'])) ? $data['commitment_property_25'] : null;
		$this->commitment_property_26 = (isset($data['commitment_property_26'])) ? $data['commitment_property_26'] : null;
		$this->commitment_property_27 = (isset($data['commitment_property_27'])) ? $data['commitment_property_27'] : null;
		$this->commitment_property_28 = (isset($data['commitment_property_28'])) ? $data['commitment_property_28'] : null;
		$this->commitment_property_29 = (isset($data['commitment_property_29'])) ? $data['commitment_property_29'] : null;
		$this->commitment_property_30 = (isset($data['commitment_property_30'])) ? $data['commitment_property_30'] : null;

		$this->account_status = (isset($data['account_status'])) ? $data['account_status'] : null;
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
	}
	
	public function getProperties($description = null)
	{
		$context = Context::getCurrent();
		$data = array();
		$data['id'] = (int) $this->id;
		$data['status'] = $this->status;
		$data['type'] = $this->type;
		$data['commitment_id'] = (int) $this->commitment_id;
		$data['invoice_account_id'] = (int) $this->invoice_account_id;
		$data['caption'] = $this->caption;
		$data['due_date'] =  ($this->due_date) ? $this->due_date : null;
		$data['settlement_date'] = ($this->settlement_date) ? $this->settlement_date : null;
		$data['collection_date'] = ($this->collection_date) ? $this->collection_date : null;
		$data['quantity'] = ($this->quantity) ? $this->quantity : null;
		$data['unit_price'] = ($this->unit_price) ? $this->unit_price : null;
		$data['amount'] = ($this->amount) ? $this->amount : null;
		$data['means_of_payment'] = $this->means_of_payment;
		$data['bank_name'] = $this->bank_name;
		$data['reference'] = $this->reference;
		$data['comment'] = $this->comment;
		$data['document'] = $this->document;
		$data['invoice_id'] = (int) $this->invoice_id;
		$data['invoice_identifier'] = $this->invoice_identifier;
		$data['tiny_1'] = $this->tiny_1;
		$data['tiny_2'] = $this->tiny_2;
		$data['tiny_3'] = $this->tiny_3;
		$data['tiny_4'] = $this->tiny_4;
		$data['tiny_5'] = $this->tiny_5;
		$data['audit'] = $this->audit;
		$data['update_time'] = $this->update_time;
		
		// Deprecated
		$data['invoice_n_last'] = $this->invoice_n_last;
		
		$data['name'] = $this->name;
		$data['account_identifier'] = $this->account_identifier;
		$data['commitment_caption'] = $this->commitment_caption;
		$data['place_caption'] = $this->place_caption;
		$data['place_identifier'] = $this->place_identifier;
		$data['place_id'] = $this->place_id;
		$data['invoice_account_name'] = $this->invoice_account_name;
		$data['default_means_of_payment'] = $this->default_means_of_payment;
		$data['transfer_order_id'] = $this->transfer_order_id;
		$data['transfer_order_date'] = $this->transfer_order_date;
		$data['transfer_order_id'] = $this->transfer_order_id;

		if ($context->hasRole('dpo')) {
			$value = $context->getSecurityAgent()->unprotectPrivateDataV2($this->bank_identifier);
			if ($value) $data['bank_identifier'] = $value;
		}

		$data['commitment_property_1'] = $this->commitment_property_1;
		$data['commitment_property_2'] = $this->commitment_property_2;
		$data['commitment_property_3'] = $this->commitment_property_3;
		$data['commitment_property_4'] = $this->commitment_property_4;
		$data['commitment_property_5'] = $this->commitment_property_5;
		$data['commitment_property_6'] = $this->commitment_property_6;
		$data['commitment_property_7'] = $this->commitment_property_7;
		$data['commitment_property_8'] = $this->commitment_property_8;
		$data['commitment_property_9'] = $this->commitment_property_9;
		$data['commitment_property_10'] = $this->commitment_property_10;
		$data['commitment_property_11'] = $this->commitment_property_11;
		$data['commitment_property_12'] = $this->commitment_property_12;
		$data['commitment_property_13'] = $this->commitment_property_13;
		$data['commitment_property_14'] = $this->commitment_property_14;
		$data['commitment_property_15'] = $this->commitment_property_15;
		$data['commitment_property_16'] = $this->commitment_property_16;
		$data['commitment_property_17'] = $this->commitment_property_17;
		$data['commitment_property_18'] = $this->commitment_property_18;
		$data['commitment_property_19'] = $this->commitment_property_19;
		$data['commitment_property_20'] = $this->commitment_property_20;
		$data['commitment_property_21'] = $this->commitment_property_21;
		$data['commitment_property_22'] = $this->commitment_property_22;
		$data['commitment_property_23'] = $this->commitment_property_23;
		$data['commitment_property_24'] = $this->commitment_property_24;
		$data['commitment_property_25'] = $this->commitment_property_25;
		$data['commitment_property_26'] = $this->commitment_property_26;
		$data['commitment_property_27'] = $this->commitment_property_27;
		$data['commitment_property_28'] = $this->commitment_property_28;
		$data['commitment_property_29'] = $this->commitment_property_29;
		$data['commitment_property_30'] = $this->commitment_property_30;
		 
		$data['account_status'] = $this->account_status;
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

		if ($description) {
			foreach ($description['properties'] as $propertyId => $property) {
				if ($property['private'] && $data[$propertyId]) {
					$value = $context->getSecurityAgent()->unprotectPrivateDataV2($data[$propertyId]);
					if ($value) $data[$propertyId] = $value;
				}
			}
		}
		
		return $data;
	}

	public function toArray()
	{
		$data = $this->getProperties();
		if (!$data['settlement_date']) $data['settlement_date'] = '9999-12-31';
		if (!$data['collection_date']) $data['collection_date'] = '9999-12-31';
		$data['audit'] = json_encode($data['audit']);
		unset($data['name']);
		unset($data['account_identifier']);
		unset($data['commitment_caption']);
		unset($data['place_caption']);
		unset($data['place_identifier']);
		unset($data['place_id']);
		unset($data['invoice_account_name']);
		unset($data['default_means_of_payment']);
		unset($data['transfer_order_id']);
		unset($data['transfer_order_date']);
		unset($data['bank_identifier']);
 
		unset($data['commitment_property_1']);
		unset($data['commitment_property_2']);
		unset($data['commitment_property_3']);
		unset($data['commitment_property_4']);
		unset($data['commitment_property_5']);
		unset($data['commitment_property_6']);
		unset($data['commitment_property_7']);
		unset($data['commitment_property_8']);
		unset($data['commitment_property_9']);
		unset($data['commitment_property_10']);
		unset($data['commitment_property_11']);
		unset($data['commitment_property_12']);
		unset($data['commitment_property_13']);
		unset($data['commitment_property_14']);
		unset($data['commitment_property_15']);
		unset($data['commitment_property_16']);
		unset($data['commitment_property_17']);
		unset($data['commitment_property_18']);
		unset($data['commitment_property_19']);
		unset($data['commitment_property_20']);
		unset($data['commitment_property_21']);
		unset($data['commitment_property_22']);
		unset($data['commitment_property_23']);
		unset($data['commitment_property_24']);
		unset($data['commitment_property_25']);
		unset($data['commitment_property_26']);
		unset($data['commitment_property_27']);
		unset($data['commitment_property_28']);
		unset($data['commitment_property_29']);
		unset($data['commitment_property_30']);

		unset($data['account_status']);
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
		return $data;
	}

	public static function getList($type, $params, $order = '+due_date', $limit = null, $columns = null, $pageNumber = false, $itemCountPerPage = false)
	{
		$context = Context::getCurrent();
		if ($type) $description = Term::getDescription($type);
		else $description = null;
		
		$order = explode(',', $order);
    	foreach ($order as &$criterion) $criterion = substr($criterion, 1).' '.((substr($criterion, 0, 1) == '-') ? 'DESC' : 'ASC');
		$select = Term::getTable()->getSelect()
			->join('commitment', 'commitment.id = commitment_term.commitment_id', ['commitment_caption' => 'caption', 'commitment_property_1' => 'property_1', 'commitment_property_2' => 'property_2', 'commitment_property_3' => 'property_3', 'commitment_property_4' => 'property_4', 'commitment_property_5' => 'property_5', 'commitment_property_6' => 'property_6', 'commitment_property_7' => 'property_7', 'commitment_property_8' => 'property_8', 'commitment_property_9' => 'property_9', 'commitment_property_10' => 'property_10', 'commitment_property_11' => 'property_11', 'commitment_property_12' => 'property_12', 'commitment_property_13' => 'property_13', 'commitment_property_14' => 'property_14', 'commitment_property_15' => 'property_15', 'commitment_property_16' => 'property_16', 'commitment_property_17' => 'property_17', 'commitment_property_18' => 'property_18', 'commitment_property_19' => 'property_19', 'commitment_property_20' => 'property_20', 'commitment_property_21' => 'property_21', 'commitment_property_22' => 'property_22', 'commitment_property_23' => 'property_23', 'commitment_property_24' => 'property_24', 'commitment_property_25' => 'property_25', 'commitment_property_26' => 'property_26', 'commitment_property_27' => 'property_27', 'commitment_property_28' => 'property_28', 'commitment_property_29' => 'property_29', 'commitment_property_30' => 'property_30'], 'left')
			->join('core_account', 'core_account.id = commitment.account_id', ['account_status' => 'status', 'place_id', 'name', 'account_identifier' => 'identifier', 'default_means_of_payment', 'transfer_order_id', 'transfer_order_date', 'bank_identifier', 'account_date_1' => 'date_1', 'account_date_2' => 'date_2', 'account_date_3' => 'date_3', 'account_date_4' => 'date_4', 'account_date_5' => 'date_5', 'account_property_1' => 'property_1', 'account_property_2' => 'property_2', 'account_property_3' => 'property_3', 'account_property_4' => 'property_4', 'account_property_5' => 'property_5', 'account_property_6' => 'property_6', 'account_property_7' => 'property_7', 'account_property_8' => 'property_8', 'account_property_9' => 'property_9', 'account_property_10' => 'property_10', 'account_property_11' => 'property_11', 'account_property_12' => 'property_12', 'account_property_13' => 'property_13', 'account_property_14' => 'property_14', 'account_property_15' => 'property_15', 'account_property_16' => 'property_16'], 'left')
			->join('core_place', 'core_account.place_id = core_place.id', ['place_caption' => 'caption', 'place_identifier' => 'identifier'], 'left')
			->join(['invoice_account' => 'core_account'], 'invoice_account.id = commitment_term.invoice_account_id', ['invoice_account_name' => 'name'], 'left')
			->order($order);

		if ($columns) $select->columns($columns);

		$where = new Where;
		if ($type) $where->equalTo('commitment_term.type', $type);
		$where->notEqualTo('commitment_term.status', 'deleted');

		// Filter on place
		$keep = true;
		if (array_key_exists('p-pit-admin', $context->getPerimeters()) && array_key_exists('place_id', $context->getPerimeters()['p-pit-admin'])) {
			$where->in('core_account.place_id', $context->getPerimeters()['p-pit-admin']['place_id']);
		}

		// Todo list vs search modes
		if (count($params) == 0) {
			$where->notEqualTo('commitment_term.status', 'collected');
			$where->lessThanOrEqualTo('collection_date', date('Y-m-d'));
		}
		else {
			// Set the filters
			foreach ($params as $propertyId => $value) {
				if (in_array(substr($propertyId, 0, 4), array('min_', 'max_'))) $propertyKey = substr($propertyId, 4);
				else $propertyKey = $propertyId;
				$property = Term::getConfig($type)[$propertyKey];
				$entity = Term::$model['properties'][$propertyKey]['entity'];
				$column = Term::$model['properties'][$propertyKey]['column'];

				if ($propertyId == 'place_id') {
					$where->in('core_account.'.$propertyId, array_map('trim', explode(',', $value)));
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
		}
		$select->where($where);
		$cursor = Term::getTable()->selectWith($select);
		$terms = array();

		foreach ($cursor as $term) {
			$term->properties = $term->getProperties();
			$terms[] = $term;
		}
		return $terms;
	}

	public static function get($id, $column = 'id')
	{
		$term = Term::getTable()->get($id, $column);
		if (!$term) return null;
		$commitment = Commitment::get($term->commitment_id);
		if ($commitment) {
			$term->commitment_caption = $commitment->caption;
			$account = Account::get($commitment->account_id);
			if ($account) {
				$term->name = $account->name;
				
				if (!$term->invoice_identifier) $term->invoice_identifier = $commitment->invoice_identifier;
				$term->commitment_property_1 = $commitment->property_1;
				$term->commitment_property_2 = $commitment->property_2;
				$term->commitment_property_3 = $commitment->property_3;
				$term->commitment_property_4 = $commitment->property_4;
				$term->commitment_property_5 = $commitment->property_5;
				$term->commitment_property_6 = $commitment->property_6;
				$term->commitment_property_7 = $commitment->property_7;
				$term->commitment_property_8 = $commitment->property_8;
				$term->commitment_property_9 = $commitment->property_9;
				$term->commitment_property_10 = $commitment->property_10;
				$term->commitment_property_11 = $commitment->property_11;
				$term->commitment_property_12 = $commitment->property_12;
				$term->commitment_property_13 = $commitment->property_13;
				$term->commitment_property_14 = $commitment->property_14;
				$term->commitment_property_15 = $commitment->property_15;
				$term->commitment_property_16 = $commitment->property_16;
				$term->commitment_property_17 = $commitment->property_17;
				$term->commitment_property_18 = $commitment->property_18;
				$term->commitment_property_19 = $commitment->property_19;
				$term->commitment_property_20 = $commitment->property_20;
				$term->commitment_property_21 = $commitment->property_21;
				$term->commitment_property_22 = $commitment->property_22;
				$term->commitment_property_23 = $commitment->property_23;
				$term->commitment_property_24 = $commitment->property_24;
				$term->commitment_property_25 = $commitment->property_25;
				$term->commitment_property_26 = $commitment->property_26;
				$term->commitment_property_27 = $commitment->property_27;
				$term->commitment_property_28 = $commitment->property_28;
				$term->commitment_property_29 = $commitment->property_29;
				$term->commitment_property_30 = $commitment->property_30;
				
				$term->account_status = $account->status;
	    		$term->account_identifier = $account->identifier;
				$term->default_means_of_payment = $account->default_means_of_payment;
				$term->transfer_order_id = $account->transfer_order_id;
				$term->transfer_order_date = $account->transfer_order_date;
				$term->bank_identifier = $account->bank_identifier;

				$term->account_date_1 = $account->date_1;
				$term->account_date_2 = $account->date_2;
				$term->account_date_3 = $account->date_3;
				$term->account_date_4 = $account->date_4;
				$term->account_date_5 = $account->date_5;
				$term->account_property_1 = $account->property_1;
		    	$term->account_property_2 = $account->property_2;
		    	$term->account_property_3 = $account->property_3;
		    	$term->account_property_4 = $account->property_4;
		    	$term->account_property_5 = $account->property_5;
		    	$term->account_property_6 = $account->property_6;
		    	$term->account_property_7 = $account->property_7;
		    	$term->account_property_8 = $account->property_8;
		    	$term->account_property_9 = $account->property_9;
		    	$term->account_property_10 = $account->property_10;
		    	$term->account_property_11 = $account->property_11;
		    	$term->account_property_12 = $account->property_12;
		    	$term->account_property_13 = $account->property_13;
		    	$term->account_property_14 = $account->property_14;
		    	$term->account_property_15 = $account->property_15;
		    	$term->account_property_16 = $account->property_16;
			}
			if ($term->invoice_account_id) {
				$invoiceAccount = Account::get($term->invoice_account_id);
				if ($invoiceAccount) {
					$term->invoice_account_name = $invoiceAccount->name;
				}
			}
		}
		$term->properties = $term->getProperties();
		return $term;
	}

	public static function instanciate($type = null, $commitment_id = null)
	{
		$term = new Term;
		$term->type = $type;
		$term->status = 'expected';
		$term->commitment_id = $commitment_id;
		$term->audit = array();
		$term->properties = $term->getProperties();
		return $term;
	}

	public function loadData($data, $files = array()) {

		$context = Context::getCurrent();
		$errors = array();
		$auditRow = array(
			'time' => Date('Y-m-d G:i:s'),
			'n_fn' => $context->getFormatedName(),
		);
		$configProperties = Term::getConfig($this->type);
		foreach ($data as $propertyId => $value) {
			if (!array_key_exists($propertyId, $configProperties)) $errors[$propertyId] = "The terms of type $this->type does not manage the property $propertyId";
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
					/*if (array_key_exists('precision', $property) && $property['precision'] > 0) */$value = (float) $value;
//					else $value = (int) $value;
				}

				// Private data protection
				if ($property['private'] && $value) {
					$value = $context->getSecurityAgent()->protectPrivateDataV2($value);
				}
    				 
				if ($propertyId == 'status') $this->status = $value;
				elseif ($propertyId == 'commitent_id') $this->commitent_id = $value;
				elseif ($propertyId == 'invoice_account_id') $this->invoice_account_id = $value;
				elseif ($propertyId == 'caption') $this->caption = $value;
				elseif ($propertyId == 'due_date') $this->due_date = $value;
				elseif ($propertyId == 'settlement_date') $this->settlement_date = $value;
				elseif ($propertyId == 'collection_date') $this->collection_date = $value;
				elseif ($propertyId == 'quantity') $this->quantity = $value;
				elseif ($propertyId == 'unit_price') $this->unit_price = $value;
				elseif ($propertyId == 'amount') $this->amount = $value;
				elseif ($propertyId == 'means_of_payment') $this->means_of_payment = $value;
				elseif ($propertyId == 'bank_name') $this->bank_name = $value;
				elseif ($propertyId == 'reference') $this->reference = $value;
				elseif ($propertyId == 'comment') $this->comment = $value;
				elseif ($propertyId == 'document') $this->document = $value;
				elseif ($propertyId == 'invoice_id') $this->invoice_id = $value;
				elseif ($propertyId == 'invoice_identifier') $this->invoice_identifier = $value;
				elseif ($propertyId == 'tiny_1') $this->tiny_1 = $value;
				elseif ($propertyId == 'tiny_2') $this->tiny_2 = $value;
				elseif ($propertyId == 'tiny_3') $this->tiny_3 = $value;
				elseif ($propertyId == 'tiny_4') $this->tiny_4 = $value;
				elseif ($propertyId == 'tiny_5') $this->tiny_5 = $value;
				elseif ($propertyId == 'update_time') $this->update_time = $value;

	    		// Deprecated
				elseif ($propertyId == 'invoice_n_last') $this->invoice_n_last = $value;

				if ($propertyId && $this->properties[$propertyId] != $value) {
					$auditRow[$propertyId] = ['old' => $this->properties[$propertyId], 'new' => $value];
				}
			}
		}

		if ($this->quantity) $this->amount = round($this->quantity * $this->unit_price, 2) * 1.2; // Amount is tax inclusive historically (and it's bad)
			
		$this->properties = $this->getProperties();
		$this->files = $files;
		if ($errors) return 'Integrity';
		$this->audit[] = $auditRow;
		return 'OK';
	}

    public function add()
    {
    	$context = Context::getCurrent();
    	$this->id = null;
    	Term::getTable()->save($this);    
    	return ('OK');
    }

    public function loadAndAdd($data)
    {
    	$context = Context::getCurrent();
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', 'term->loadData: '.$rc];
    
    	$rc = $this->add();
    	if ($rc != 'OK') return ['500', 'term->add: '.$rc];
    
    	$this->properties = $this->getProperties();
    	return ['200', $this->id];
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();
    	$term = Term::get($this->id);

    	// Isolation check
    	if ($update_time && $term->update_time > $update_time) return 'Isolation';
/*    	if (isset($this->files)) {
			$root_id = Document::getTable()->get(0, 'parent_id')->id; 
    		$document = Document::instanciate($root_id);
    		$document->files = $this->files;
    		$document->saveFile();
    		$this->document_id = $document->save();
    	}*/
    	Term::getTable()->save($this);
    	return 'OK';
    }

    public function isDeletable()
    {
    	return true;
    }
    
    public function delete($update_time)
    {
		$context = Context::getCurrent();
    	$term = Term::get($this->id);
    
    	// Isolation check
    	if ($update_time && $term->update_time > $update_time) return 'Isolation';    	 
    	$this->status = 'deleted';
    	Term::getTable()->save($this);
    	 
    	return 'OK';
    }

    // Add content to this method:
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
    	if (!Term::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Term::$table = $sm->get(TermTable::class);
    	}
    	return Term::$table;
    }
}