<?php
namespace PpitUser\Model;

use PpitCore\Model\Context;
use PpitCore\Model\Community;
use PpitCore\Model\Instance;
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
	private static $cipher  = MCRYPT_RIJNDAEL_128;
	private static $mode    = 'cbc';
	
	private $acl;

	protected function getAcl() { return $this->acl; }

	public function wsAuthenticate($sm)
	{
		$context = Context::getCurrent();
		
		// Initialize the logger
		$writer = new \Zend\Log\Writer\Stream('data/log/interaction.txt');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
	
		// Check basic authentication
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$username = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
		} 
		elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
				list($username, $password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
		}
		else {

			// Write to the log
			$logger->info($_SERVER['QUERY_STRING'].';401;');
			$sm->get('Response')->setStatusCode('401');
			header('location: user/login');
			return false;
		}
		$request = $sm->get('Request');
		$fqdn = (method_exists($request, 'getUri')) ? $request->getUri()->getHost() : null;
		if ($fqdn) $instance = Instance::get($fqdn, 'fqdn');
		$instance_id = $instance->id;
		if ($this->authenticate($username, $password) != 'OK') {

			// Write to the log
			$logger->info($_SERVER['QUERY_STRING'].';401;'.$username.';');
			$sm->get('Response')->setStatusCode('401');
			header('location: user/login');
			return false;
		}
		else {
			$user = User::getTable()->transGet($username, 'username');
			$select = UserContact::getTable()->getSelect()->where(array('user_id' => $user->user_id));
			$userContacts = UserContact::getTable()->transSelectWith($select);
			foreach ($userContacts as $userContact) if ($userContact->instance_id == $instance_id) $vcard_id = $userContact->vcard_id;
			if ($user->vcard_id != $vcard_id) $user->vcard_id = $vcard_id;
			$user->update(null);
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
		foreach ($config['ppit_roles'] as $role => $unused) {
			$acl -> addRole(new Role($role), array());
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
		$config = Context::getCurrent()->getConfig();
		if (array_key_exists($userRole, $config['ppit_roles']) && $this->acl->isAllowed($userRole, $route)) return true;
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
					if (array_key_exists($userRole, $config['ppit_roles']) && $this->acl->isAllowed($userRole, $route)) $isAllowed = true;
				}
			}
	
			if (!$isAllowed) {
				$url = $e->getRouter()->assemble(array(), array('name' => 'home'));
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
	
	public function requestPasswordInit($user, $sendMail, $url, $contact = null) {
		$context = Context::getCurrent();
		$config = $context->getConfig();
	
		$user->password_init_token = md5(uniqid(rand(), true));
		$user->password_init_validity = date('Y-m-d', strtotime(date('Y-m-d').' '.$config['ppitUserSettings']['tokenValidity'].' day'));
		$rc = $user->update(null);
		if ($rc != 'OK') return null;
	
		// Send the email to the user
		if ($sendMail) {
			$email_body = $config['ppitUserSettings']['messages']['addText'][$context->getLocale()];
			$link = $url->fromRoute('user/initpassword', ['id' => $user->user_id], ['force_canonical' => true]).'?hash='.$user->password_init_token;
			$email_body = sprintf($email_body, $user->username, $link, $link);
			$email_title = $config['ppitUserSettings']['messages']['addTitle'][$context->getLocale()];
	        if (!$contact) $contact = Vcard::getTable()->transGet($user->vcard_id);
			Context::sendMail($contact->email, $email_body, $email_title, null);
		}
		
		return $user->password_init_token;
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
	    	if ($user->state == 0) return 'Activation';
    		else {
    			// Reset the number of login trials
    			$user->nb_trials = 0;
    			$user->update(null);

    			$container = new Container('Zend_Auth');
    			$container->user_id = $user->user_id;
    			$container->token_value = null; // Login-based session prevals on the previously token-based session

//    			Context::updateFromUserId($context->getConfig(), $user->user_id);
    			 
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
    	$context = Context::getCurrent();

    	// Check that the contact is linked to the current user
    	$select = UserContact::getTable()->getSelect()->where(array('user_id' => $user->user_id, 'vcard_id' => $vcard_id));
    	$cursor = UserContact::getTable()->transSelectWith($select);
    	if (count($cursor) == 0) return 'Authentication';
    	
    	// Update the user's current contact
    	$user->instance_id = $context->getInstanceId();
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
    
    /**
     * encapsulates all the model logic and security for adding a new user. 
     * Callable both from any kind of controller: web client, web-service, app
     */
    public function register($username, $vcard_id, $password, $activated = false)
    {
    	$context = Context::getCurrent();
    	 
    	// Search for an already existing account
    	$user = User::get($username, 'username');

    	if (!$user) {
	    	// Create the user
	    	$user = User::instanciate();
	    	$user->instance_id = $context->getInstanceId();
	    	$user->state = ($activated) ? 1 : 0; // Account to be activated before use
	    	$user->username = $username;
	    	$user->vcard_id = $vcard_id;
	    	$rc = $user->add();
	    	if ($rc != 'OK') throw new \Exception($rc);
    		if ($password && $this->changePassword($user, $username, null, $password, null) != 'OK') throw new \Exception();
    	}
    	else {
	    	$user->vcard_id = $vcard_id;
    		$rc = $user->update(null);
    		if ($rc != 'OK') throw new \Exception($rc);
    	}
    	
    	$userContact = UserContact::instanciate();
    	$userContact->user_id = $user->user_id;
    	$userContact->vcard_id = $vcard_id;
    	if ($userContact->add() != 'OK') throw new \Exception();
    	return $user->user_id;
    }

    public function requestAuthenticationToken($identity, $account_id)
    {
    	$context = Context::getCurrent();
    
    	$user = User::getTable()->transget($identity, 'username');
    	if (!$user) return 'Not exists';
    	$user->authentication_token = md5(uniqid(rand(), true));
    	$user->authentication_validity = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' 7200 seconds'));
    	$rc = $user->update(null);
    	if ($rc != 'OK') return null;
    	return $user->authentication_token;
    }
    
    public function activate($user_id, $authentication_token, $update_time)
    {
    	$user = User::getTable()->transget($user_id);
    	if ($user->state == 1) return 'Already activated';
    	if ($user->authentication_token != $authentication_token || $user->authentication_validity < date('Y-m-d')) return 'Unauthorized';
    	$user->authentication_token = null;
    	$user->authentication_validity = null;
    	$user->state = 1;
    	$user->nb_trials = 0;
    	return $user->update($update_time);
    }

    public function protectPrivateData($data) {
    	$context = Context::getCurrent();
    	$safe = $context->getConfig('ppitUserSettings')['safe'][$context->getInstance()->caption];
    	$passphrase = (array_key_exists('passphrase', $safe)) ? $safe['passphrase'] : null;
    	if (!$passphrase) return $data;
    	$keyHash = md5($passphrase);
    	$key = substr($keyHash, 0, mcrypt_get_key_size(SecurityAgent::$cipher, SecurityAgent::$mode));
    	$iv  = substr($keyHash, 0, mcrypt_get_block_size(SecurityAgent::$cipher, SecurityAgent::$mode));
    	$encrypted = mcrypt_encrypt(SecurityAgent::$cipher, $key, $data, SecurityAgent::$mode, $iv);
    	return base64_encode($encrypted);
    }

    public function unprotectPrivateData($data) {
    	$context = Context::getCurrent();
    	$safe = $context->getConfig('ppitUserSettings')['safe'][$context->getInstance()->caption];
    	$passphrase = (array_key_exists('passphrase', $safe)) ? $safe['passphrase'] : null;
    	if (!$passphrase) return $data;
    	$keyHash = md5($passphrase);
    	$key = substr($keyHash, 0, mcrypt_get_key_size(SecurityAgent::$cipher, SecurityAgent::$mode) );
    	$iv  = substr($keyHash, 0, mcrypt_get_block_size(SecurityAgent::$cipher, SecurityAgent::$mode) );
    	$decoded = base64_decode($data);
    	return rtrim(mcrypt_decrypt(SecurityAgent::$cipher, $key, $decoded, SecurityAgent::$mode, $iv));
    }
}
