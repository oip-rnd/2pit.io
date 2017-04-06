<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use PpitContact\Model\ContactMessage;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Community;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Instance;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Validator\Callback;
use Zend\View\Model\ViewModel;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;

class UserController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');

		$community_id = (int) $context->getCommunityId();
		
		$menu = $context->getConfig('menus')['p-pit-admin'];
		$currentEntry = $this->params()->fromQuery('entry', 'account');

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'menu' => $menu,
    			'community_id' => $community_id,
    			'currentEntry' => $currentEntry,
    			'places' => Place::getList(array()),
    	));
    }
	
	public function searchAction()
    {
    	// Retrieve the context
		$context = Context::getCurrent();

		$instance_id = $context->getInstanceId();
		$community_id = (int) $this->params()->fromRoute('community_id', 0);
		if (!$community_id) $community_id = $context->getCommunityId();
		$community = Community::get($community_id);

    	// Retrieve the available role list
    	$roles = $context->getConfig()['ppitUserSettings']['roles'];

    	$view = new ViewModel(array(
        	'context' => $context,
			'config' => $context->getconfig(),
    		'community_id' => $community_id,
    		'community' => $community,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
	
	public function listAction()
    {
    	// Retrieve the context
		$context = Context::getCurrent();

		$instance_id = $context->getInstanceId();
		$community_id = (int) $this->params()->fromRoute('community_id', 0);

    	// Retrieve the available role list
    	$roles = $context->getConfig()['ppitUserSettings']['roles'];

    	$major = $this->params()->fromQuery('major', 'n_fn');
    	$dir = $this->params()->fromQuery('dir', 'ASC');
    	$users = User::getList($instance_id, $community_id, $major, $dir);

    	$view = new ViewModel(array(
        	'context' => $context,
			'config' => $context->getconfig(),
    		'community_id' => $community_id,
    		'major' => $major,
    		'dir' => $dir,
    		'users' => $users,
    		'roles' => $roles
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateAction()
    {
    	// Retrieve the context
		$context = Context::getCurrent();
		$instance = Instance::get($context->getInstanceId());

		$community_id = (int) $context->getCommunityId();
		if ($community_id) $community = Community::get($community_id);

    	// Retrieve the user in update mode or create a new one in add mode
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) {
    		$user = User::get($id);
    		if (!$user) $this->redirect()->toRoute('index'); // Not allowed
    		$contact = Vcard::get($user->vcard_id);
    	}
    	else {
    		$user = User::getNew($community_id);
    		$contact = Vcard::instanciate($community_id);
    	}
    	 
    	if ($community_id) {
	    	// Retrieve the vcards
    		$vcards = Vcard::getList($community_id, array());
    		$communities = null;
    	}
    	else {
    		// Retrieve the communities
			$communities = Community::getList('name', 'ASC');
    		$vcards = null;
    	}

    	$places = Place::getList(array());
    	
		// Instanciate the csrf form
        $csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
        	$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
        	$csrfForm->setData($request->getPost());
        	
        	if ($csrfForm->isValid()) { // CSRF check

        		// Atomically save
        		$connection = User::getTable()->getAdapter()->getDriver()->getConnection();
        		$connection->beginTransaction();
        		try {
	    			// Load the data    
			    	$data = array();
			    	$data['credits'] = array('p-pit-studies' => true);
			    	$data['n_title'] = $request->getPost('n_title');
			    	$data['n_last'] =  $request->getPost('n_last');
			    	$data['n_first'] = $request->getPost('n_first');
			    	$data['email'] = $request->getPost('email');
			    	$data['tel_work'] = $request->getPost('tel_work');
			    	$data['tel_cell'] = $request->getPost('tel_cell');

			    	$data['roles'] = array();
					foreach ($context->getConfig('ppitApplications') as $application) {
				    	foreach ($application['roles'] as $roleId => $role) {
				    		if ($request->getPost('role_'.$roleId)) $data['roles'][$roleId] = true;
				    		else $data['roles'][$roleId] = false;
				    	}
					}

			    	$data['perimeters'] = array();
			    	$data['perimeters']['p-pit-studies'] = array();
			    	$data['applications'] = array();
			    	foreach ($instance->applications as $applicationId => $default) {
			    		if ($request->getPost('application_'.$applicationId)) $data['applications'][$applicationId] = $default;
			    	}

			    	$authorizedPlaces = array();
					foreach ($places as $place) {
						if ($request->getPost('place_'.$place->id)) $authorizedPlaces[] = $place->id;
			    	}
		    		if (count($authorizedPlaces)) $data['perimeters']['p-pit-studies']['place_id'] = $authorizedPlaces;

		    		if ($context->getConfig('student/property/discipline')) {
			    		$disciplines = array();
				    	foreach ($context->getConfig('student/property/discipline')['modalities'] as $disciplineId => $unused) {
							if ($request->getPost('discipline_'.$disciplineId)) $disciplines[] = $disciplineId;
				    	}
				    	if (count($disciplines)) $data['perimeters']['p-pit-studies']['property_1'] = $disciplines;
		    		}
	
		    		if ($context->getConfig('student/property/level')) {
		    			$levels = array();
				    	foreach ($context->getConfig('student/property/level')['modalities'] as $levelId => $unused) {
							if ($request->getPost('level_'.$levelId)) $levels[] = $levelId;
				    	}
				    	if (count($levels)) $data['perimeters']['p-pit-studies']['property_4'] = $levels;
		    		}

			    	$data['locale'] = $request->getPost('locale');
			    	$data['is_notified'] = $request->getPost('is_notified');
			    	$data['is_demo_mode_active'] = true;
			    	 
			    	if ($contact->loadData($data, $community_id) != 'OK') throw new \Exception('View error');
			    	$rc = $user->loadData($request, $contact);
					if ($rc != 'OK') $error = $rc;
					else {
						// Save the contact
		        		$vcard_id = Vcard::optimize($contact)->id;
		        		$user->vcard_id = $vcard_id;

	        			// Save the user
		        		if ($user->user_id) {
	        				$rc = $user->update($request->getPost('update_time'));
	        				$creationMode = false;
	        			}
		        		else {
		        			$rc = $user->add($contact->email);
		        			$creationMode = true;
		        		}

		        		if ($rc != 'OK') $error = $rc;
						else {

							if ($creationMode) {
								// Save the user-contact link
								$userContact = UserContact::instanciate();
								$userContact->user_id = $user->user_id;
								$userContact->vcard_id = $vcard_id;
								$rc = $userContact->add();
								if ($rc == 'OK') $rc = $context->getSecurityAgent()->requestPasswordInit($user, null);
								if ($rc != 'OK') $error = $rc;
							}
							if ($rc == 'OK') {
				    			$connection->commit();
				    			$message = 'OK';
							}
						}
					}
        		}
        	    catch (\Exception $e) {
		    		$connection->rollback();
		    		throw $e;
		    	}
        	}
        }

    	// Retrieve the available locale list
    	$locales = $context->getConfig()['ppitUserSettings']['locales'];
        $view = new ViewModel(array(
        	'context' => $context,
        	'instance' => $instance,
			'config' => $context->getconfig(),
        	'community_id' => $community_id,
        	'id' => $id,
    		'contact' => $contact,
    		'communities' => $communities,
        	'vcards' => $vcards,
        	'places' => $places,
        	'csrfForm' => $csrfForm,
        	'user' => $user,
        	'message' => $message,
    		'error' => $error,
        ));
   		$view->setTerminal(true);
   		return $view;
    }

    public function revokeAction()
    {
    	// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the user
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) $this->redirect()->toRoute('index');
    	$user = User::get($id);
    	if (!$user) $this->redirect()->toRoute('index'); // Not allowed
    	
		// Instanciate the csrf form
        $csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
        	$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
        	$csrfForm->setData($request->getPost());
        	
        	if ($csrfForm->isValid()) {
				$rc = $context->getSecurityAgent()->revoke($user, $request->getPost('state'), $request->getPost('update_time'));
        		if ($rc != 'OK') $error = $rc;
	        	else $message = 'OK';
        	}
        }
        $view = new ViewModel(array(
        	'context' => $context,
			'config' => $context->getconfig(),
        	'id' => $id,
        	'csrfForm' => $csrfForm,
        	'user' => $user,
        	'message' => $message,
    		'error' => $error,
        ));
   		$view->setTerminal(true);
   		return $view;
    }

    public function deleteAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the user
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) $this->redirect()->toRoute('index');

    	$user = User::get($id);
    	$request = $this->getRequest();
    	if ($request->isPost()) if (!$user) $user = new User; // Form displaying post-delete for confirmation
    	else if (!$user) $this->redirect()->toRoute('index'); // Not allowed
    	 
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check
    			$return = $user->delete($request->getPost('update_time'));
    			if ($return != 'OK') $error = $return;
    			else $message = 'OK';
    		}
    	}
    	$view= new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'csrfForm' => $csrfForm,
    			'user' => $user,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function loginAction()
    {
    	// Clean the session
    	$container = new Container('Zend_Auth');
		$container->user_id = null;
    	
    	$context = Context::getCurrent();

    	$instance_caption = $this->params()->fromRoute('instance_caption', null);
    	if ($instance_caption) Context::$instance = Instance::get($instance_caption, 'caption');
    
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {

    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check

		    	// Check if user is not revoked
		    	$identity = $request->getPost('identity');
		    	$credential = $request->getPost('credential');
		    	$rc = $context->getSecurityAgent()->authenticate($identity, $credential);
		    	if ($rc == 'OK') {
		    		// Redirect
		    		if ($this->params()->fromQuery('redirect')) {
		    			return $this->redirect()->toRoute($this->params()->fromQuery('redirect'), array(), array('query' => $this->params()->fromQuery()));
		    		}
	    			else return $this->redirect()->toRoute('home');
		    	}
		    	else $error = $rc;
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => Context::getCurrent(),
				'config' => $context->getconfig(),
    			'redirect' => $this->params()->fromQuery('redirect'),
    			'active' => 'login',
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    			'title' => 'P-Pit Plug & Play IT',
				'description' => 'Proposez et recevez des services en ligne tout en conservant vos applications existantes : P-Pit Engagements dématérialise bons de commandes et factures, P-Pit Studies la suite complète Sport-études.',
    			'robots' => 'index, follow',
    	));
   		return $view;
    }
    
    public function expiredAction()
    {
    	return $this->loginAction();
    }

    public function demoAction()
    {
    	$context = Context::getCurrent();
    	$request = $this->getRequest();

    	$rc = $context->getSecurityAgent()->demoAuthenticate($this->params()->fromRoute('username', 'democrite'));
    	if ($rc == 'OK') {
	    	$logText = "\t";
	    	foreach ($request->getHeaders()->toArray() as $headerId => $headerValue) $logText .= $headerValue."\t";
	    	$logText .= "\n";
	    	$writer = new Writer\Stream('data/log/ppitUser_demo.txt');
	    	$logger = new Logger();
	    	$logger->addWriter($writer);
	    	$logger->info($logText);
	    	
	    	// Redirect
	    	return $this->redirect()->toRoute($this->params()->fromQuery('redirect'), array(), array('query' => $this->params()->fromQuery()));
    	}
    	else return $this->redirect()->toRoute('home');
    }
    
    protected function passwordAction()
    {
        // Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('user/index');
    	}
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$config = $context->getConfig();

    	// Retrieve the object and control access
    	$user = User::get($id);
    	$vcard = Vcard::getTable()->get($context->getContactId());

		// Instanciate the csrf form
        $csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
	    $request = $this->getRequest();
	    if ($request->isPost()) {

    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());

    		if ($csrfForm->isValid()) { // CSRF check
    			if ($request->getPost('new_password') != $request->getPost('new_password2')) $error = 'Integrity';
    			else {
    				$rc = $context->getSecurityAgent()->changePassword($user, $user->username, $request->getPost('current_password'), $request->getPost('new_password'), null);
	    			if ($rc != 'OK') $error = $rc;
	    			else {
			    		$url = $context->getServiceManager()->get('viewhelpermanager')->get('url');
				 		$email_body = $config['ppitUserSettings']['messages']['passwordChangedText'][$context->getLocale()];
				 		$email_title = $config['ppitUserSettings']['messages']['passwordChangedTitle'][$context->getLocale()];
			    		ContactMessage::sendMail($vcard->email, $email_body, $email_title, null);
	
						$this->redirect()->toRoute('user/passwordChanged', array('id' => $id));
	    			}
    			}
		    }
	    }
    	$view = new ViewModel(array(
    			'context' => $context,
				'config' => $context->getconfig(),
    			'active' => 'password',
    			'id' => $id,
    			'csrfForm' => $csrfForm,
    			'title' => 'P-Pit Plug & Play IT',
    			'message' => $message,
    			'error' => $error,
    	));
   		return $view;
    }

    protected function passwordChangedAction()
    {
        // Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('user/logout');
    	
    	// Retrieve the user
    	$context = Context::getCurrent();

    	// Retrieve the existing user
    	$user = User::get($id);

    	// Retrieve the contact
    	$contact = Vcard::getTable()->get($user->vcard_id);

    	$view = new ViewModel(array(
    			'context' => $context,
    			'user' => $user,
    			'config' => $context->getConfig(),
    			'id' => $id,
    			'title' => 'P-Pit Plug & Play IT',
    	));
   		return $view;
    }

    public function logoutAction()
    {
    	$context = Context::getCurrent();
    	$instance_caption = $this->params()->fromRoute('instance_caption', null);
    	$context->getSecurityAgent()->logout();
		if ($this->params()->fromQuery('redirect')) {
			return $this->redirect()->toUrl($this->params()->fromQuery('redirect'));
		}
		else return $this->redirect()->toRoute('home/index', array('instance_caption' => $instance_caption));
	}

    public function lostPasswordAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$instance_caption = $this->params()->fromRoute('instance_caption', null);
    	if ($instance_caption) Context::$instance = Instance::get($instance_caption, 'caption');

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check
    
    			$user = User::get($request->getPost('username'), 'username');
    			if (!$user) return $this->redirect()->toRoute('index');
    			$context->getSecurityAgent()->requestPasswordInit($user, null);
       			$message = 'OK';
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'csrfForm' => $csrfForm,
    			'locale' => 'fr_FR',
    			'error' => $error,
    			'message' => $message,
    	));
    	return $view;
    }

    public function passwordRequestAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');
    	$user = User::get($id);
    
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check
    			$context->getSecurityAgent()->requestPasswordInit($user, null);
    			$message = 'OK';
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'csrfForm' => $csrfForm,
    			'id' => $id,
    			'user' => $user,
    			'locale' => 'fr_FR',
    			'error' => $error,
    			'message' => $message,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function initpasswordAction()
    {
    	$context = Context::getCurrent();
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('user/logout');
    	}
		
    	$hash = $this->params()->fromQuery('hash', NULL);
    	
    	// Retrieve the existing user
    	$user = User::get($id);

    	// Retrieve the current contact and instance
    	$contact = Vcard::getTable()->transget($user->vcard_id);
    	$instance = Instance::get($contact->instance_id);
    	Context::$instance = $instance;
	    
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$request = $this->getRequest();
    	$error = null;
	    if ($request->isPost()) {
	    	$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
	    	$csrfForm->setData($request->getPost());

	    	if ($csrfForm->isValid()) { // CSRF check
		    	$username = $request->getPost('identifier');
		    	$new_password = $request->getPost('password');
				$rc = $context->getSecurityAgent()->initPassword($user, $hash, $username, $new_password, null);
		    	if ($rc == 'OK') return $this->redirect()->toRoute('user/passwordChanged', array('id' => $id));
		    	else  $error = $rc;
	    	}
	    }
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'csrfForm' => $csrfForm,
    		'id' => $id,
    		'hash' => $hash,
    		'user' => $user,
    		'error' => $error,
    		'locale' => 'fr_FR',
    	));
   		return $view;
    }
    
    public function changeContactAction()
    {
		$context = Context::getCurrent();
		$vcard_id = $this->params()->fromRoute('vcard_id');
		$user = User::getTable()->transGet($context->getUserId());
		$context->getSecurityAgent()->changeContact($user, $vcard_id, null);
		return $this->redirect()->toRoute('home');
    }
}
