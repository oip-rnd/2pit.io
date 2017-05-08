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
use PpitCore\Model\Credit;
use PpitCore\Model\Generic;
use PpitCore\Model\Instance;
use PpitDocument\Model\Document;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Log\Logger;
use Zend\Log\Writer;

/**
 * The Community class manages a community of users.
 * A community is related to a Place, has a name, is linked with up to 5 contacts (Vcard class) each of whom has a status in this community (ex. invoice contact).
 * A community manages also its own documentary space.
 * Communities are high-level P-Pit objects which are valuable. The Community class implements the P-Pit credit consumption mechanism (1 credit consumed per Community and per month).
 */
class Community implements InputFilterAwareInterface
{
	/** @var int */ public $id;
    /** @var int */ public $instance_id;
    /** @var string */ public $next_credit_consumption_date;
    /** @var string */ public $last_credit_consumption_date;
    /** @var string */ public $status;
    /** @var int */ public $place_id;
    /** @var string */ public $name;
    /** @var int */ public $contact_1_id;
    /** @var string */ public $contact_1_status;
    /** @var int */ public $contact_2_id;
    /** @var string */ public $contact_2_status;
    /** @var int */ public $contact_3_id;
    /** @var string */ public $contact_3_status;
    /** @var int */ public $contact_4_id;
    /** @var string */ public $contact_4_status;
    /** @var int */ public $contact_5_id;
    /** @var string */ public $contact_5_status;
    /** @var string */ public $origine;
    /** @var int */ public $root_document_id;
    /** @var array */ public $audit;
    /** @var string */ public $update_time;
    
    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

    // Static fields
    /** @var \PpitCore\Model\GenericTable */ private static $table;

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
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->next_credit_consumption_date = (isset($data['next_credit_consumption_date'])) ? $data['next_credit_consumption_date'] : null;
        $this->last_credit_consumption_date = (isset($data['last_credit_consumption_date'])) ? $data['last_credit_consumption_date'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
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
        $this->origine = (isset($data['origine'])) ? $data['origine'] : null;
        $this->root_document_id = (isset($data['root_document_id'])) ? $data['root_document_id'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
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
    	$data['status'] =  $this->status;
    	$data['place_id'] = (int) $this->place_id;
    	$data['next_credit_consumption_date'] = ($this->next_credit_consumption_date) ? $this->next_credit_consumption_date : null;
    	$data['last_credit_consumption_date'] = ($this->last_credit_consumption_date) ? $this->last_credit_consumption_date : null;
    	$data['name'] =  $this->name;
    	$data['contact_1_id'] =  (int) $this->contact_1_id;
    	$data['contact_1_status'] =  $this->contact_1_status;
    	$data['contact_2_id'] =  (int) $this->contact_2_id;
    	$data['contact_2_status'] =  $this->contact_2_status;
    	$data['contact_3_id'] =  (int) $this->contact_3_id;
    	$data['contact_3_status'] =  $this->contact_3_status;
    	$data['contact_4_id'] =  (int) $this->contact_4_id;
    	$data['contact_4_status'] =  $this->contact_4_status;
    	$data['contact_5_id'] =  (int) $this->contact_5_id;
    	$data['contact_5_status'] =  $this->contact_5_status;
    	$data['origine'] =  $this->origine;
    	$data['root_document_id'] = (int) $this->root_document_id;
    	$data['audit'] = $this->audit;

    	return $data;
    }

    /**
     * Used for object (php) to relational (database) mapping.
     * @return array
     */
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['audit'] = json_encode($this->audit);
    	return $data;
    }

    /**
     * Returns an array of Community instances:
     * The list is filtered on communities matching the key-value pairs provided in the $params argument:
     * - if the key has the form 'min_<property_name>', only the rows having a value for this property greater than the argument are kept
     * - if the key has the form 'max_<property_name>', only the rows having a value for this property less than the argument are kept
     * - else only the row which are 'like' (in SQL sense) the argument are kept in the list.
     * The result is primarily ordered according to the value of $major with the direction (ASC or DESC) specified by $dir, and secondarily by 'name'
     * @param string $major
     * @param string $dir
     * @param array $params
     * @return Community[]
     */
    public static function getList($major, $dir, $params = array())
    {
    	$select = Community::getTable()->getSelect()->order(array($major.' '.$dir, 'name'));
    	$where = new Where;
       	foreach ($params as $propertyId => $property) {
			if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo(substr($propertyId, 4), $params[$propertyId]);
    		elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo(substr($propertyId, 4), $params[$propertyId]);
    		else $where->like($propertyId, '%'.$params[$propertyId].'%');
    	}
    	$where->notEqualTo('status', 'deleted');
    	$select->where($where);
    	$cursor = Community::getTable()->selectWith($select);
    	$communities = array();
    	foreach ($cursor as $community) $communities[$community->id] = $community;

    	return $communities;
    }
    
    /**
     * Retrieve from the database the Community having the giving value as the given specified column ('id' as a default).
     * @param string $id
     * @param string $column
     * @return instance of Community
     */
    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();

    	// Access control : only one's community except for the instance manager (context's community == 0)
    	if ($context->getCommunityId()) $id = $context->getCommunityId();
    	
    	$community = Community::getTable()->get($id, $column);
    	return $community;
    }

    /**
     * @return Community
     */
    public static function instanciate()
    {
    	$community = new Community;
    	$community->status = 'new';
    	return $community;
    }
    
    /**
     * Loads the data into the Community object depending of an array, typically constructed by the controller with value extracted from an HTTP request.
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
    	if (array_key_exists('next_credit_consumption_date', $data)) {
    		$next_credit_consumption_date = trim(strip_tags($data['next_credit_consumption_date']));
    		if ($next_credit_consumption_date && !checkdate(substr($next_credit_consumption_date, 5, 2), substr($next_credit_consumption_date, 8, 2), substr($next_credit_consumption_date, 0, 4))) return 'Integrity';
    		if ($this->next_credit_consumption_date != $next_credit_consumption_date) $auditRow['next_credit_consumption_date'] = $this->next_credit_consumption_date = $next_credit_consumption_date;
    	}

    	if (array_key_exists('status', $data)) {
    		$status = trim(strip_tags($data['status']));
    		if (strlen($status) > 255) return 'Integrity';
    		if ($this->status != $status) $auditRow['status'] = $this->status = $status;
    	}

    	if (array_key_exists('place_id', $data)) {
    		$place_id = (int) $data['place_id'];
    		if (!$place_id) return 'Integrity';
    		if ($this->place_id != $place_id) $auditRow['place_id'] = $this->place_id = $place_id;
    	}
    	 
		if (array_key_exists('name', $data)) {
	    	$name = trim(strip_tags($data['name']));
    		if ($name == '' || strlen($name) > 255) return 'Integrity';
    		if ($this->name != $name) $auditRow['name'] = $this->name = $name;
		}

		if (array_key_exists('contact_1_id', $data)) {
	    	$contact_1_id = (int) $data['contact_1_id'];
	    	if (!$contact_1_id) return 'Integrity';
    		if ($this->contact_1_id != $contact_1_id) $auditRow['contact_1_id'] = $this->contact_1_id = $contact_1_id;
		}

		if (array_key_exists('contact_1_status', $data)) {
	    	$contact_1_status = trim(strip_tags($data['contact_1_status']));
	    	if (strlen($contact_1_status) > 255) return 'Integrity';
    		if ($this->contact_1_status != $contact_1_status) $auditRow['contact_1_status'] = $this->contact_1_status = $contact_1_status;
		}

		if (array_key_exists('contact_2_id', $data)) {
	    	$contact_2_id = (int) $data['contact_2_id'];
	    	if (!$contact_2_id) return 'Integrity';
    		if ($this->contact_2_id != $contact_2_id) $auditRow['contact_2_id'] = $this->contact_2_id = $contact_2_id;
		}
    	
		if (array_key_exists('contact_2_status', $data)) {
	    	$contact_2_status = trim(strip_tags($data['contact_2_status']));
	    	if (strlen($contact_2_status) > 255) return 'Integrity';
    		if ($this->contact_2_status != $contact_2_status) $auditRow['contact_2_status'] = $this->contact_2_status = $contact_2_status;
		}

		if (array_key_exists('contact_3_id', $data)) {
			$contact_3_id = (int) $data['contact_3_id'];
			if (!$contact_3_id) return 'Integrity';
    		if ($this->contact_3_id != $contact_3_id) $auditRow['contact_3_id'] = $this->contact_3_id = $contact_3_id;
		}
		 
		if (array_key_exists('contact_3_status', $data)) {
			$contact_3_status = trim(strip_tags($data['contact_3_status']));
			if (strlen($contact_3_status) > 255) return 'Integrity';
    		if ($contact_3_status != $contact_3_status) $auditRow['contact_3_status'] = $this->contact_3_status = $contact_3_status;
		}

		if (array_key_exists('contact_4_id', $data)) {
			$contact_4_id = (int) $data['contact_4_id'];
			if (!$contact_4_id) return 'Integrity';
    		if ($this->contact_4_id != $contact_4_id) $auditRow['contact_4_id'] = $this->contact_4_id = $contact_4_id;
		}
		 
		if (array_key_exists('contact_4_status', $data)) {
			$contact_4_status = trim(strip_tags($data['contact_4_status']));
			if (strlen($contact_4_status) > 255) return 'Integrity';
    		if ($this->contact_4_status != $contact_4_status) $auditRow['contact_4_status'] = $this->contact_4_status = $contact_4_status;
		}

		if (array_key_exists('contact_5_id', $data)) {
			$contact_5_id = (int) $data['contact_5_id'];
			if (!$contact_5_id) return 'Integrity';
    		if ($this->contact_5_id != $contact_5_id) $auditRow['contact_5_id'] = $this->contact_5_id = $contact_5_id;
		}
		 
		if (array_key_exists('contact_5_status', $data)) {
			$contact_5_status = trim(strip_tags($data['contact_5_status']));
			if (strlen($contact_5_status) > 255) return 'Integrity';
    		if ($this->contact_5_status != $contact_5_status) $auditRow['contact_5_status'] = $this->contact_5_status = $contact_5_status;
		}
		
		if (array_key_exists('origine', $data)) {
	    	$origine = trim(strip_tags($data['origine']));
	    	if (strlen($origine) > 255) return 'Integrity';
    		if ($this->origine != $origine) $auditRow['origine'] = $this->origine = $origine;
		}
		$this->audit[] = $auditRow;
		return 'OK';
	}

    /**
     * Adds a new row in the database after checking that it does not conflict with an existing community with the same 'name'. 
     * In such a case the methods does not affect the database and returns 'Duplicate', otherwise it returns 'OK'.
     * Create a new documentary space for the new community which is given a right authorization on it.
     * @return string
     */
	public function add()
    {
    	$context = Context::getCurrent();

    	// Check consistency
    	if (Generic::getTable()->cardinality('core_community', array('status != ?' => 'deleted', 'name' => $this->name)) > 0) return 'Duplicate';

    	// Create the root document for the new community
    	$rootDoc = new Document;
    	$this->root_document_id = Document::getTable()->save($rootDoc);

    	$this->id = null;
    	Community::getTable()->save($this);

    	$rootDoc->acl = array('contacts' => array(), 'communities' => array($this->id => 'write'));
    	Document::getTable()->save($rootDoc);

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
    	$context = Context::getCurrent();
    	$Community = Community::get($this->id);
    	if ($update_time && $Community->update_time > $update_time) return 'Isolation';
    	Community::getTable()->save($this);
    	return 'OK';
    }

    /**
     * The method Community::consumeCredits() should only be invoked from a console action. Note that this methods is transverse to all logical P-Pit instances. It acts only on instances that have not the unlimited credits option.
     * The goal of this method is to daily compute the credit consumption depending on active communities.
     * A Community which is not suspended, blocked or closed consumes one credit per month, starting one month after its creation.
     * - A community is suspended by the administrator invoking a suspend action on it. At any moment the administrator can re-activate a suspended community;
     * - A community is blocked by the current process if the community should consume a credit (ie its 'next_credit_consumption_date' is reached) and no P-Pit Credits are available;
     * - A community is closed when the administrator deletes it.
     * The algorythm uses the next credit consumption date associated with each active community. It detects, instance per instance, communities that issue in less than 7 days.
     * - If the community has expired and there are available credits, it decrements one credit (in the Credit of type p-pit-community) for this instance. The new next credit consumption date is set to the current date + 31 days;
     * - If the community expires today and there are no available credits, the community is suspended and a notification is sent to the instance's administrators;
     * - If the comnunity is about to expire (in less than 7 days) and there are no available credtis, an alert notification is sent daily to the instance administrators.
     * @param string $live
     * @param string $mailTo
     */
    public static function consumeCredits($live, $mailTo)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	 
    	// Open log
    	if ($live) {
    		$writer = new Writer\Stream('data/log/console.txt');
    		$logger = new Logger();
    		$logger->addWriter($writer);
    	}
    
    	// Retrieve instances
    	$select = Instance::getTable()->getSelect();
    	$cursor = Instance::getTable()->selectWith($select);
    	$instances = array();
    	$instanceIds = array();
    	foreach ($cursor as $instance) {
    		$unlimitedCredits = (array_key_exists('unlimitedCredits', $instance->specifications)) ? $instance->specifications['unlimitedCredits'] : false;
    
    		// Log
    		if ($config['isTraceActive']) {
    			$logText = 'Instance : id='.$instance->id.', caption='.$instance->caption.', unlimitedCredits='.(($unlimitedCredits) ? 'true' : 'false');
    			if ($live) $logger->info($logText);
    			else print_r($logText."\n");
    		}
    
    		if (!$unlimitedCredits) {
    			$instance->administrators = array();
    			$instances[$instance->id] = $instance;
    			$instanceIds[] = $instance->id;
    		}
    	}
    
    	// Retrieve credits
    	$select = Credit::getTable()->getSelect();
    	$where = new Where();
    	$where->in('instance_id', $instanceIds);
    	$where->equalTo('type', 'p-pit-communities');
    	$select->where($where);
    	$cursor = Credit::getTable()->transSelectWith($select);
    	$credits = array();
    	foreach ($cursor as $credit) {
    		$credit->consumers = array();
    		$credits[$credit->instance_id] = $credit;
    	}

    	// Retrieve communities and count
    	$select = Community::getTable()->getSelect()
	    	->join('core_instance', 'core_community.instance_id = core_instance.id', array(), 'left');
    	$where = new Where();
    	$where->in('instance_id', $instanceIds);
    	$where->notEqualTo('core_community.status', 'closed');
    	$where->notEqualTo('core_community.status', 'suspended');
    	$select->where($where);
    	$cursor = Community::getTable()->transSelectWith($select);
    	foreach ($cursor as $community) {
    		if (array_key_exists($community->instance_id, $credits)) $credits[$community->instance_id]->consumers[] = $community;
    	}

    	// Retrieve administrators to be notified
    	$select = Vcard::getTable()->getSelect();
    	$where = new Where;
    	$where->like('roles', '%admin%');
    	$select->where($where);
    	$cursor = Vcard::getTable()->transSelectWith($select);
    	foreach ($cursor as $contact) {
    		if ($contact->is_notified) $instances[$contact->instance_id]->administrators[] = $contact;
    	}
    
    	// Check enough credits are available
    	foreach ($credits as $credit) {
  
			// Compute the credit consumption at -7 days and at due date
    		$counter7 = 0;
			$counter0 = 0;
			$dailyConsumption = 0;
			$creditModified = false;
			$blocked = array();
			foreach($credit->consumers as $community) {
				if ($community->status == 'blocked') $blocked[] = $community->name;
    			else {
					if ($community->next_credit_consumption_date <= date('Y-m-d', strtotime(date('Y-m-d').' + 7 days'))) $counter7++;
	    			if ($community->next_credit_consumption_date <= date('Y-m-d')) $counter0++;
	    			if ($community->next_credit_consumption_date <= date('Y-m-d')) {
	    				if ($credit->quantity > 0) {
		    				// Consume 1 credit
		    				$credit->quantity--;
		    				$credit_consumption_date = $community->next_credit_consumption_date;
							$community->next_credit_consumption_date = date('Y-m-d', strtotime($credit_consumption_date.' + 31 days'));
							$community->last_credit_consumption_date = $credit_consumption_date;
		    				$credit->audit[] = array(
		    						'period' => date('Y-m'),
		    						'quantity' => -1,
		    						'status' => 'used',
		    						'reference' => $community->name,
		    						'time' => Date('Y-m-d G:i:s'),
		    						'n_fn' => 'P-PIT',
		    						'comment' => 'Utilisation mensuelle pour la période du '.$context->decodeDate($community->last_credit_consumption_date).' au '.$context->decodeDate($community->next_credit_consumption_date),
		    				);
		    
		    				// Log
		    				if ($config['isTraceActive']) {
		   						$logText = 'Community : instance_id='.$community->instance_id.', id='.$community->id.', caption='.$community->name.', status='.$community->status;
		    					if ($live) $logger->info($logText);
		    					else print_r($logText."\n");
		    				}
	    				}
	    				else {
	    					$blocked[] = $community->name;
	    					$community->status = 'blocked';
			    			$credit->audit[] = array(
		    						'period' => date('Y-m'),
			    					'quantity' => 0,
			    					'status' => 'blocked',
			    					'reference' => $community->name,
			    					'time' => Date('Y-m-d G:i:s'),
			    					'n_fn' => 'P-PIT',
			    					'comment' => array(
										'en_US' => 'Community suspended due to lack of credit',
										'fr_FR' => 'Communauté suspendue faute de crédit suffisant',
									),
			    			);
	    				}
		    			$creditModified = true;
	    				if ($live) Community::getTable()->transSave($community);
	    			}
    			}
			}
			if ($creditModified && $live) Credit::getTable()->transSave($credit);

			// Notify a suspension of service
    		if ($blocked) {

    			// Log
    			$logText = 'ALERT : Not enough credits for P-PIT Communities available on instance '.$credit->instance_id.'. Available='.$credit->quantity.', 7 days estimation='.$counter7;
    			if ($live) $logger->info($logText);
    			else print_r($logText."\n");
    			
    			// Notify
    			if ($live) {
    				$instance = $instances[$credit->instance_id];
    				foreach ($instance->administrators as $contact) {
    					if (!$mailTo || !strcmp($contact->email, $mailTo)) { // Restriction on the given mailTo parameter
    						$title = sprintf($config['community/consumeCredit']['messages']['suspendedServiceTitle'][$contact->locale], 'P-PIT Communities');
    						$text = sprintf(
    								$config['community/consumeCredit']['messages']['suspendedServiceText'][$contact->locale],
    								$contact->n_first,
    								$instance->caption,
    								implode(' ; ', $blocked),
    								$credit->quantity
    						);
    						Context::sendMail($contact->email, $text, $title);
    					}
    				}
    			}
    		}
    		elseif ($credit->quantity >= 0 && $credit->quantity - $counter7 < 0) {
    
    			// Log
    			$logText = 'ALERT : Risk of credits lacking for P-PIT Communities on instance '.$credit->instance_id.'. Available='.$credit->quantity.', 7 days estimation='.$counter7;
    			if ($live) $logger->info($logText);
    			else print_r($logText."\n");
    
    			// Notify
    			if ($live) {
    				$instance = $instances[$credit->instance_id];
    				foreach ($instance->administrators as $contact) {
    					if (!$mailTo || !strcmp($contact->email, $mailTo)) { // Restriction on the given mailTo parameter
    						$title = sprintf($config['community/consumeCredit']['messages']['availabilityAlertTitle'][$contact->locale], 'P-PIT Communities');
    						$text = sprintf(
    								$config['community/consumeCredit']['messages']['availabilityAlertText'][$contact->locale],
    								$contact->n_first,
    								$instance->caption,
    								$credit->quantity,
    								$counter7
    						);
    						Context::sendMail($contact->email, $text, $title);
    					}
    				}
    			}
    		}
    	}
    }
    
    /**
     * Checks if this community can de deleted by calling the isUsed() method for each class which has registered itself in the 'ppitCoreDependencies' list.
     * As soon as an isUsed() method return true, the community is not deletable and so the method returns false.
     * @return boolean
     */
    public function isDeletable()
    {
    	// Check other dependencies
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
    	$context = Context::getCurrent();
    	$community = Community::get($this->id);
    	if ($community->update_time > $update_time) return 'Isolation';
		$this->status = 'deleted';
    	Community::getTable()->save($this);
    	return 'OK';
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
     * Returns the object to relational manager for the Community class
     */
    public static function getTable()
    {
    	if (!Community::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Community::$table = $sm->get('PpitCore\Model\CommunityTable');
    	}
    	return Community::$table;
    }
}