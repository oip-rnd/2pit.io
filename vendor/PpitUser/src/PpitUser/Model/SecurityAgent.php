<?php
namespace PpitUser\Model;

use PpitCore\Model\Context;
use PpitCore\Model\Community;
use PpitCore\Model\Token;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use Zend\Authentication\Result;
//use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\TableGateway\TableGateway;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Session\Container;

class SecurityAgent
{    
	private $acl;

	protected function getAcl() { return $this->acl; }

	public function wsAuthenticate($sm)
	{
		// Initialize the logger
		$writer = new \Zend\Log\Writer\Stream('data/log/interaction.txt');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
	
		// Check basic authentication
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$username = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
		} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
				list($username, $password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
		}
		if ($this->authenticate($username, $password) == 'Authentication') {

			// Write to the log
			$logger->info($_SERVER['QUERY_STRING'].';401;'.$username.';');
			$sm->get('Response')->setStatusCode('401');
			header('location: user/login');
			return false;
		}
		else {
			$user = User::getTable()->transGet($username, 'username');
			Context::updateFromUserId(Context::getCurrent()->getConfig(), $user->user_id);
			$container = new Container('Zend_Auth');
			$container->user_id = $user->user_id;
			return true;
		}
	}
	
	public function getUserId()
	{
		// Retrieve the currentUser
		$container = new Container('Zend_Auth');
		return $container->user_id;
	}
	
	public function initAcl($e, $config)
	{
		$acl = new \Zend\Permissions\Acl\Acl();
		$acl -> addRole(new Role('guest'), array());
		$acl -> addRole(new Role('user'), array());
		
		foreach ($config['ppitApplications'] as $application) {
			foreach ($application['roles'] as $role => $properties) {
				$acl -> addRole(new Role($role), array());
			}
		}
	
		//adding routes
		$routes = $config['bjyauthorize']['guards']['BjyAuthorize\Guard\Route'];
		foreach ($routes as $index => $route) {
			$acl -> addResource(new Resource($route['route']));
	
			//adding restrictions
			foreach ($route['roles'] as $role) {
				$acl -> allow($role, $route['route']);
			}
		}
		$this->acl = $acl;
	}

	public function isallowed($userRole, $route)
	{
		if ($this->acl->isAllowed($userRole, $route)) return true;
	}
	
	public function checkAcl($e)
	{
		$app = $e->getApplication();
		$serviceManager = $app->getServiceManager();
	
		$config = $serviceManager->get('config');
		if ($e->getRequest() instanceof \Zend\Http\PhpEnvironment\Request && $config['ppitUserSettings']['checkAcl']) {
			// Keep in session the eventually given token
			$route = $e->getRouteMatch()->getMatchedRouteName();
			$container = new Container('Zend_Auth');
			$token_value = $e->getRequest()->getQuery()->token;
			if ($token_value) {
				$container = new Container('Zend_Auth');
				$container->token_value = $token_value;
			}
	
			$isAllowed = false;
	
			// Check if a valid token is provided
			if (Token::authenticate($route, $e->getRouteMatch())) $isAllowed = true;
			else {
				// Check ACL for the user roles
				foreach(Context::getCurrent()->getRoles() as $userRole) {
					if ($this->acl->isAllowed($userRole, $route)) $isAllowed = true;
				}
			}
	
			if (!$isAllowed) {
				$url = $e->getRouter()->assemble(array(), array('name' => 'user/expired'));
				$response = $e->getResponse();
				$response->getHeaders()->addHeaderLine('Location', $url);
				$response->setStatusCode(302);
				$response->sendHeaders();
				$stopCallBack = function($event) use ($response){
					$event->stopPropagation();
					return $response;
				};
				$e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack,-10000);
				return $response;
			}
		}
	}
	
	public function checkPasswordIntegrity($password, $strongPassword = true)
	{
		$regex = "/(?=.*[A-Z])(?=.*[0-9]).{8,}$/";
		if ($strongPassword && !preg_match($regex, $password)) return 'Integrity';
		return 'OK';
	}
	
	public function requestPasswordInit($user, $update_time) {
		$context = Context::getCurrent();
		$config = $context->getConfig();
	
		$user->password_init_token = md5(uniqid(rand(), true));
		$user->password_init_validity = date('Y-m-d', strtotime(date('Y-m-d').' '.$config['ppitUserSettings']['tokenValidity'].' day'));
		$rc = $user->update($update_time);
		if ($rc != 'OK') return $rc;
	
		// Send the email to the user
		$url = $context->getServiceManager()->get('viewhelpermanager')->get('url');
		$email_body = $config['ppitUserSettings']['messages']['addText'][$context->getLocale()];
		$email_body = sprintf($email_body, $user->username, $context->getConfig()['ppitCoreSettings']['domainName'].$url('user/initpassword', array('id' => $user->user_id)).'?hash='.$user->password_init_token);
		$email_title = $config['ppitUserSettings']['messages']['addTitle'][$context->getLocale()];
        $contact = Vcard::getTable()->transGet($user->vcard_id);
		Context::sendMail($contact->email, $email_body, $email_title, null);
		
		return 'OK';
	}

	public function initPassword($user, $token, $username, $new_password, $update_time)
	{
		$context = Context::getCurrent();
		$config = $context->getConfig();
		if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // Instance 0 is for demo => Not updatable

			if (date('Y-m-d') > $user->password_init_validity) return 'Expiry';
			if ($token != $user->password_init_token) return 'Authentication';
			if ($username != $user->username) return 'Authentication';
				
			// Check new password integrity and authenticate
			$rc = $this->checkPasswordIntegrity($new_password, $context->getConfig()['ppitUserSettings']['strongPassword']);
			if ($rc != 'OK') return $rc;
			else {
				$data = $user->toArray();
				$bcrypt = new Bcrypt();
				$bcrypt->setCost(14);
				$user->password = $bcrypt->create($new_password);
				$user->password_init_token = null;
				$user->password_init_validity = null;
				return $user->update($update_time);
			}
		}
	}
	
	public function changePassword($user, $username, $current_password, $new_password, $update_time)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
		if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // Instance 0 is for demo => Not updatable

			// Check new password integrity and authenticate
			$rc = $this->checkPasswordIntegrity($new_password, $context->getConfig()['ppitUserSettings']['strongPassword']);
			if ($rc != 'OK') return $rc;
			if ($current_password) {
				$rc = User::getTable()->authenticate($user->username, $current_password);
				if (!$rc->isValid()) return 'Authentication';
			}
       		$data = $user->toArray();
       		$bcrypt = new Bcrypt();
    		$bcrypt->setCost(14);
    		$user->password = $bcrypt->create($new_password);
    		$user->password_init_token = null;
    		$user->password_init_validity = null;
    		return $user->update($update_time);
    	}
    }
    
    public function revoke($user, $state, $update_time)
    {
    	$user->state = $state;
    	$user->nb_trials = 0;
	    return $user->update($update_time);
    }
    
    public function authenticate($identity, $credential)
    {
    	$context = Context::getCurrent();
		$auth = new AuthenticationService();
    	$dbAdapter = User::getTable()->getAdapter();
 
    	$credentialValidationCallback = function($dbCredential, $requestCredential) {
    		$bcrypt = new Bcrypt();
    		$bcrypt->setCost(14);
    		return $bcrypt->verify($requestCredential, $dbCredential);
    	};
    	$authAdapter = new AuthAdapter($dbAdapter, 'core_user', 'username', 'password', $credentialValidationCallback);

    	$authAdapter
	    	->setIdentity($identity)
    		->setCredential($credential);
    	 
		$result = $auth->authenticate($authAdapter);
    	if (!$result->isValid()) {
    		$user = User::getTable()->transget($identity, 'username');
    		if ($user){
    			$user->nb_trials++;
    			if ($user->nb_trials == 5) $user->state = 0;
    			User::getTable()->save($user);
    		}
    		return 'Authentication';
    	}
    	else {
    		$user = User::getTable()->transGet($result->getIdentity(), 'username');
    	    $contact = Vcard::getTable()->transGet($user->vcard_id);
	    	if ($contact->community_id) {
	    		$user->community = Community::getTable()->transGet($contact->community_id);
	    		$user->community_status = $user->community->status;
	    	}
	    	if ($user->state == 0) return 'Authentication';
    		elseif ($user->community_status == 'blocked' || $user->community_status == 'suspended') return 'Suspended';
    		else {
    			// Reset the number of login trials
    			$user->nb_trials = 0;
    			$user->update(null);

    			$container = new Container('Zend_Auth');
    			$container->user_id = $user->user_id;
    			$container->token_value = null; // Login-based session prevals on the previously token-based session
    			
    			return 'OK';
    		}
    	}
    }
    
    public function demoAuthenticate($username)
    {
    	$context = Context::getCurrent();

    	$container = new Container('Zend_Auth');
    	$container->user_id = null;
    	
    	$user = User::getTable()->transGet($username, 'username');
    	if ($user->instance_id != 0) return 'Authentication';
    	
    	$container = new Container('Zend_Auth');
    	$container->user_id = $user->user_id;
    	$container->token_value = null; // Login-based session prevals on the previously token-based session
    	
    	return 'OK';
    }
    
    public function changeContact($user, $vcard_id, $update_time)
    {
    	// Check that the contact is linked to the current user
    	$select = UserContact::getTable()->getSelect()->where(array('user_id' => $user->user_id, 'vcard_id' => $vcard_id));
    	$cursor = UserContact::getTable()->transSelectWith($select);
    	if (count($cursor) == 0) return 'Authentication';
    	
    	// Update the user's current contact
    	$user->vcard_id = $vcard_id;
    	$user->update($update_time);
    	
    	return 'OK';
    }
    
    public function logout()
    {
    	$container = new Container('Zend_Auth');
		$container->user_id = null;
		$container->token_value = null;
    }
}
