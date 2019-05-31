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

class UserContactSource
{
    public $id;
    public $instance_id;
    public $status;
    public $user_id;
    public $vcard_id;
    public $update_time;

    // Joined properties
    public $n_fn;
    public $email;
    public $tel_work;
    public $tel_cell;
    public $instance_caption;
    public $community_name;

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
        $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->vcard_id = (isset($data['vcard_id'])) ? $data['vcard_id'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
        
        // Joined properties
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->instance_caption = (isset($data['instance_caption'])) ? $data['instance_caption'] : null;
        $this->community_name = (isset($data['community_name'])) ? $data['community_name'] : null;
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['status'] = $this->status;
    	$data['user_id'] = (int) $this->user_id;
    	$data['vcard_id'] =  $this->vcard_id;
    	return $data;
    }

    public static function getList($user_id = null)
    {
    	$context = Context::getCurrent();
    	$select = UserContactSource::getTable()->getSelect()
    		->join('core_instance', 'core_user_contact.instance_id = core_instance.id', array('instance_caption' => 'caption'), 'left')
	    	->join('core_vcard', 'core_user_contact.vcard_id = core_vcard.id', array('n_fn', 'email', 'tel_work', 'tel_cell'), 'left')
    		->join('core_user', 'core_user_contact.user_id = core_user.user_id', array(), 'left')
    		->where(array('core_user_contact.user_id' => ($user_id) ? $user_id : $context->getUserId()))
    		->order(array('instance_caption', 'n_fn'));
    	$cursor = UserContactSource::getTable()->transSelectWith($select);
    	$contacts = array();
    	foreach ($cursor as $contact) $contacts[$contact->vcard_id] = $contact;
    	return $contacts;
    }

    public static function get($id, $column = 'id')
    {
		$userContact = UserContactSource::getTable()->get($id, $column);
		if ($userContact) {
			$contact = VcardSource::getTable()->get($userContact->vcard_id);
			$userContact->n_fn = $contact->n_fn;
			$userContact->email = $contact->email;
			$userContact->tel_work = $contact->tel_work;
			$userContact->tel_cell = $contact->tel_cell;
			$community = Community::getTable()->get($contact->community_id);
			if ($community) $userContact->community_name = $community->name;
		}
		return $userContact;
    }

    public static function instanciate()
    {
    	$context = Context::getCurrent();
    	$userContact = new UserContactSource;
    	$userContact->status = 'new';
    	$userContact->user_id = $context->getUserId();
    	return $userContact;
    }
    
    public function loadData($data)
    {
    	$context = Context::getCurrent();
    	$this->vcard_id = (int) $data['vcard_id'];

    	// Check integrity
    	if ($this->vcard_id == '' || !UserSource::getTable()->get($this->user_id)) return 'Integrity';
    	return 'OK';
    }

    public function loadDataFromRequest($request)
    {
    	$context = Context::getCurrent();
		$data = array();
		$data['vcard_id'] = $request->getPost('vcard_id');
		if ($this->loadData($data) != 'OK') throw new \Exception('View error');
    }

    public function add()
    {
    	$context = Context::getCurrent();
    
    	// Check consistency
    	if (Generic::getTable()->cardinality('core_user_contact', array('user_id' => $this->user_id, 'vcard_id' => $this->vcard_id)) > 0) return 'Duplicate';

    	$this->id = null;
    	UserContactSource::getTable()->save($this);

    	return ('OK');
    }
    
    public function update($update_time)
    {
    	$userContact = UserContactSource::get($this->id);
    
    	// Isolation check
    	if ($userContact->update_time > $update_time) return 'Isolation';
    
    	UserContactSource::getTable()->save($this);
    
    	return 'OK';
    }
    
    public function isDeletable() {

    	return true;
    }

    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$userContact = UserContactSource::get($this->id);
    
    	// Isolation check
    	if ($userContact->update_time > $update_time) return 'Isolation';
    
    	UserContactSource::getTable()->delete($this->id);
    
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
    	if (!UserContactSource::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		UserContactSource::$table = $sm->get('PpitCore\Model\UserContactSourceTable');
    	}
    	return UserContactSource::$table;
    }
}