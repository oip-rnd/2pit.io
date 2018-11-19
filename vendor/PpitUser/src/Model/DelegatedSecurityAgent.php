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

class DelegatedSecurityAgent extends SecurityAgent
{    
	
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

			$identity = $e->getRequest()->getQuery('identity');
			$authentication_token = $e->getRequest()->getQuery('authentication_token');
			if ($authentication_token) {
				$user = User::get($identity, 'username');
				if ($user->authentication_token == $authentication_token && $user->authentication_validity >= date('Y-m-d H:i:s')) {
					$user->authentication_token = null;
					$user->authentication_validity = null;
					$user->update(null);
					$container = new Container('Zend_Auth');
					$container->user_id = $user->user_id;
					$container->token_value = null; // Login-based session prevals on the previously token-based session
				}
			}
			else {
						
				// Check if a valid token is provided
				if (Token::authenticate($route, $e->getRouteMatch())) $isAllowed = true;
				else {
					// Check ACL for the user roles
					foreach(Context::getCurrent()->getRoles() as $userRole) {
						if (parent::getAcl()->isAllowed($userRole, $route)) $isAllowed = true;
					}
				}
		
				if (!$isAllowed) {
					$url = $e->getRouter()->assemble(array(), array('name' => 'user/authenticate'));
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
	}
}
