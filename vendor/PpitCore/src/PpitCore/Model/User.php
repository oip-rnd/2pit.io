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
use PpitCore\Model\UserContact;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Expression;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Session\Container;

class User extends Context
{
	public $user_id;
    public $instance_id;
    public $username;
    public $vcard_id;
    public $password;
    public $password_init_token;
    public $password_init_validity;
    public $authentication_token;
    public $authentication_validity;
    public $nb_trials;
    public $state;
    public $requires_notifications = true;
    public $applications;
    public $update_time;

    // Joined properties
    public $roles;
    public $community_status;
    public $instance_caption;
    public $community_id;
    public $community;
    public $community_name;
    public $n_title;
    public $n_first;
    public $n_last;
    public $n_fn;
    public $role_caption;

	// Security
	public $identifier;
	public $token;
	public $current_password;
	public $new_password;
	
    protected $inputFilter;
    protected $delegationInputFilter;

    // Static fields
	private static $acl;
    private static $table;
    
    // Accessors
    public function getUserId() { return $this->user_id; }
    public function getUsername() { return $this->username; }
    public function getContactId() { return $this->vcard_id; }
    public function getRoles() { return $this->roles; }    
    public function getContacts() 
    { 
    	$select = UserContact::getTable()->getSelect()->where(array('user_id' => $this->getUserId()));
    	$cursor = UserContact::getTable()->transSelectWith($select);
    	$contacts = UserContact::getList();
 //   	foreach ($cursor as $userContact) $contacts[$userContact->vcard_id] = $userContact;
    	return $contacts;
    }

    public function hasRole($role) { 
    	return array_key_exists($role, Context::$exemplary->roles);
    }

    public function exchangeArray($data)
    {
        $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
 	   	$this->vcard_id = (isset($data['vcard_id'])) ? $data['vcard_id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
 	   	$this->password = (isset($data['password'])) ? $data['password'] : null;
 	   	$this->password_init_token = (isset($data['password_init_token'])) ? $data['password_init_token'] : null;
 	   	$this->password_init_validity = (isset($data['password_init_validity'])) ? $data['password_init_validity'] : null;
 	   	$this->authentication_token = (isset($data['authentication_token'])) ? $data['authentication_token'] : null;
 	   	$this->authentication_validity = (isset($data['authentication_validity'])) ? $data['authentication_validity'] : null;
 	   	$this->nb_trials = (isset($data['nb_trials'])) ? $data['nb_trials'] : null;
 	   	$this->state = (isset($data['state'])) ? $data['state'] : null;
 	   	$this->requires_notifications = (isset($data['requires_notifications'])) ? $data['requires_notifications'] : null;
 	   	$this->applications = (isset($data['applications'])) ? json_decode($data['applications'], true) : array();
 	   	$this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
 	   	
 	   	// Joined properties
 	   	$this->roles = (isset($data['roles'])) ? json_decode($data['roles'], true) : array();
 	   	$this->community_status = (isset($data['community_status'])) ? $data['community_status'] : null;
        $this->instance_caption = (isset($data['instance_caption'])) ? (int) $data['instance_caption'] : 0;
 	   	$this->community_id = (isset($data['community_id'])) ? (int) $data['community_id'] : 0;
 	   	$this->community_name = (isset($data['community_name'])) ? $data['community_name'] : null;
 	   	$this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
 	   	$this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
 	   	$this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
 	   	$this->role_caption = (isset($data['role_caption'])) ? $data['role_caption'] : null;
    }

    public function toArray()
    {
    	$data = array();
    	$data['user_id'] = (int) $this->user_id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['username'] =  $this->username;
    	$data['password'] = $this->password;
    	$data['password_init_token'] = $this->password_init_token;
    	$data['password_init_validity'] = $this->password_init_validity;
    	$data['authentication_token'] = $this->authentication_token;
    	$data['authentication_validity'] = $this->authentication_validity;
    	$data['nb_trials'] = $this->nb_trials;
    	$data['state'] = (int) $this->state;
    	$data['vcard_id'] = (int) $this->vcard_id;
    	$data['requires_notifications'] = (int) $this->requires_notifications;
    	$data['applications'] = json_encode($this->applications);
    	return $data;
    }

    public static function getList($instance_id, $community_id, $major, $dir)
    {
    	$context = Context::getCurrent();

    	// Prepare the SQL request
    	if (!$major) $major = 'username';
    	if (!$dir) $dir = 'ASC';
    	$select = User::getTable()->getSelect()
	    	->join('core_instance', 'core_user.instance_id = core_instance.id', array('instance_caption' => 'caption'), 'left')
	    	->join('core_vcard', 'core_user.vcard_id = core_vcard.id', array('n_fn'), 'left')
	    	->join('core_community', 'core_vcard.community_id = core_community.id', array('community_id' => 'id', 'community_name' => 'name', 'community_status' => 'status'), 'left')
	    	->order(array($major.' '.$dir, 'username'));
		
	    $select->where(array('core_vcard.community_id' => $community_id));
    	$user_cursor = User::getTable()->selectWith($select);
    	 
    	// Execute the request
    	$users = array();
    	foreach ($user_cursor as $user) $users[$user->user_id] = $user;
    	return $users;
    }
    
    public static function getNew($community_id = 0)
    {
    	$context = Context::getCurrent();
    	$user = new User;
    	
    	$user->instance_id = $context->getInstanceId();
    	if ($community_id) {
	    	$community = Community::getTable()->get($community_id);
	    	if ($community) $user->community_id = $community_id;
	    	else $user->community_id = $context->getCommunityId();
    	}
	    else $user->community_id = $context->getCommunityId();

	    $user->state = 1;
    	return $user;
    }

    public static function instanciate()
    {
    	$context = Context::getCurrent();
    	$user = new User;
    	$user->state = 1;
    	return $user;
    }
    
    public static function get($id, $column = 'user_id')
    {
    	$context = Context::getCurrent();

    	$user = User::getTable()->transGet($id, $column);
    	if ($user) {
            $contact = Vcard::getTable()->transGet($user->vcard_id);
    		if ($contact->community_id) {
	    		$user->community = Community::getTable()->get($contact->community_id);
	    		$user->community_status = $user->community->status;
	    	}
    	}
		return $user;
    }

    public function loadData($request, $contact, $instance_id = null)
    {
    	$context = Context::getCurrent();
        if ($request->getPost('username')) $this->username = trim(strip_tags($request->getPost('username')));
		else $this->username = $contact->email;
		$this->requires_notifications = $request->getPost('is_notified');
		$this->locale = $request->getPost('locale');
		if (!$this->locale) throw new \Exception('View error');

    	// Check for a duplicate user (same user name and different vcard_id)
    	$user = User::getTable()->transGet($this->username, 'username');
    	if ($user && $user->user_id != $this->user_id) return 'Duplicate';
    	return 'OK';
    }
    
    public function add($email = false, $notify = false)
    {
		$context = Context::getCurrent();
		$config = $context->getConfig();

		if (User::getTable()->transGet($this->username, 'username')) return 'Duplicate';
		$user_id = User::getTable()->save($this);
	
		if ($notify) {
			$context->getSecurityAgent()->requestPasswordInit($this, null);
		}
    	
	   	return 'OK';
    }
    
    public function update($update_time)
    {
    	// Check isolation and save
    	if ($update_time) {
			$user = User::getTable()->transGet($this->user_id);
			if ($user->update_time != $update_time) return 'Isolation';
    	}
		User::getTable()->save($this);
		return 'OK';
    }

    // Allow or not deleting a contact
    public function isUsed($object)
    {
    	if (get_class($object) == 'PpitCore\Model\Vcard') {
			if (Generic::getTable()->cardinality('core_user', array('vcard_id' => $object->id)) > 0) return true;
    	}
    	return false;
    }

    public function isDeletable()
    {
    	if (Generic::getTable()->cardinality('core_user_contact', array('user_id' => $this->user_id, 'vcard_id <> ?' => $this->vcard_id)) > 0) return false;
    	return true;
    }
    
    public function delete($update_time)
    {
    	$instance_id = $this->instance_id;
    	$vcard = Vcard::getTable()->get($this->vcard_id);
    	$community_id = $vcard->community_id;
    	 
    	// Access control
        if ($instance_id) {
    		$instance = Instance::getTable()->get($instance_id);
    		if (!$instance) return null;
    	}
    	if ($community_id) {
    		$community = Community::getTable()->get($community_id);
    		if (!$community) return null;
    	}

    	// Check isolation and save
    	if ($this->update_time != $update_time) return 'Isolation';
    	User::getTable()->delete($this->user_id);
    	UserContact::getTable()->multipleDelete(array('user_id' => $this->user_id));
    	return 'OK';
    }

    public static function getTable()
    {
    	if (!User::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		User::$table = $sm->get('PpitCore\Model\UserTable');
    	}
    	return User::$table;
    }
}
