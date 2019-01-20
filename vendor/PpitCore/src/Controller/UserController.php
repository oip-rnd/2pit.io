<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use FacebookAds\Api;
use FacebookAds\Http\RequestInterface;
use FacebookAds\Object\Ad;
use PpitCore\Model\ContactMessage;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Csrf;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Instance;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use ViewHelper\SsmlUserViewHelper;
use Zend\Crypt\Password\Bcrypt;
use Zend\Http\Client;
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
		$instance = Instance::get($context->getInstanceId());
    	$place = Place::get($context->getPlaceId());
		$apps = Vcard::get($context->getContactId())->applications;
    	$app = $this->params()->fromRoute('app', 'p-pit-admin');
		$applicationId = ($app) ? $app : 'p-pit-admin';
		$applicationName = $context->localize($context->getConfig('menus/'.$applicationId)['labels']);
    	
		$menu = $context->getConfig('menus/'.$app)['entries'];
		$currentEntry = $this->params()->fromQuery('entry', 'account');

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'applicationName' => $applicationName,
    			'applicationId' => $applicationId,
    			'active' => 'application',
    			'instance' => $instance,
				'place' => $place,
    			'menu' => $menu,
    			'currentEntry' => $currentEntry,
    			'apps' => $apps,
    			'app' => $app,
    			'places' => Place::getList(array()),
    	));
    }

    public function getFilters($params)
    {
    	$context = Context::getCurrent();
    	 
    	// Retrieve the query parameters
    	$filters = array();
    
    	foreach ($context->getConfig('coreUser/search')['main'] as $propertyId => $rendering) {
    
    		$property = ($params()->fromQuery($propertyId, null));
    		if ($property) $filters[$propertyId] = $property;
    		$min_property = ($params()->fromQuery('min_'.$propertyId, null));
    		if ($min_property) $filters['min_'.$propertyId] = $min_property;
    		$max_property = ($params()->fromQuery('max_'.$propertyId, null));
    		if ($max_property) $filters['max_'.$propertyId] = $max_property;
    	}
    
    	return $filters;
    }
    
	public function searchAction()
    {
    	// Retrieve the context
		$context = Context::getCurrent();
		$app = $this->params()->fromRoute('app', 'p-pit-admin');
		
		$instance_id = $context->getInstanceId();
		
    	$view = new ViewModel(array(
        	'context' => $context,
			'config' => $context->getconfig(),
    		'places' => Place::getList(array()),
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function getList()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$instance_id = $context->getInstanceId();
    	$app = $this->params()->fromRoute('app');

    	$params = $this->getFilters($this->params());
    	$major = ($this->params()->fromQuery('major', 'n_fn'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC'));
    
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';
    
    	// Retrieve the list
    	$users = User::getList($instance_id, $app, $params, $major, $dir, $mode);
    
    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'users' => $users,
    			'mode' => $mode,
    			'params' => $params,
    			'major' => $major,
    			'dir' => $dir,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function listAction()
    {
    	return $this->getList();
    }
    
    public function exportAction()
    {
    	$view = $this->getList();
    	$app = $this->params()->fromRoute('app', 'p-pit-admin');
    	 
    	include 'public/PHPExcel_1/Classes/PHPExcel.php';
    	include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
    
    	$workbook = new \PHPExcel;
    	(new SsmlUserViewHelper)->formatXls($workbook, $view);
    	$writer = new \PHPExcel_Writer_Excel2007($workbook);

    	header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    	header('Content-Disposition:inline;filename=P-Pit_Users.xlsx ');
    	$writer->save('php://output');
		return $this->response;
    }
    
    public function updateAction()
    {
    	// Retrieve the context
		$context = Context::getCurrent();
		$apps = Vcard::get($context->getContactId())->applications;
    	$app = $this->params()->fromRoute('app', 'p-pit-admin');
		
    	// Retrieve the user in update mode or create a new one in add mode
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) {
    		$user = User::get($id);
    		if (!$user) $this->redirect()->toRoute('index'); // Not allowed
    		$contact = $user->contact;
    	}
    	else {
    		$user = User::getNew();
    		$contact = Vcard::instanciate();
    		$contact->applications[$app] = $app;
    		$contact->roles[$context->getConfig('ppitApplications')[$app]['defaultRole']] = $context->getConfig('ppitApplications')[$app]['defaultRole'];
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

					if ($context->hasRole('admin')) {
				    	$data['roles'] = array();
				    	foreach ($context->getConfig('manageable_roles') as $roleId) {
				    		if ($request->getPost('role_'.$roleId)) $data['roles'][$roleId] = $roleId;
				    	}
	
				    	$data['applications'] = array();
				    	$first = true;
				    	foreach ($apps as $appId => $app) {
				    		if ($request->getPost('application_'.$appId)) {
				    			$data['applications'][$appId] = ($first) ? true : false;
				    			$first = false;
				    		}
				    	}
					}

			    	$data['perimeters'] = array();
			    	$data['specifications'] = array();
			    	
			    	foreach ($context->getConfig('perimeters') as $applicationId => $application) {
			    		if ($applicationId == 'p-pit-admin') {
			    			$myPerimeter = Vcard::get($context->getContactId())->perimeters;
					    	if (array_key_exists('p-pit-admin', $myPerimeter) && array_key_exists('place_id', $myPerimeter['p-pit-admin'])) {
				    			$authorizedPlaces = $myPerimeter['p-pit-admin']['place_id'];
					    	}
					    	else $authorizedPlaces = array();
				    		foreach ($places as $place) {
				    			if ($request->getPost('place_'.$place->id)) $authorizedPlaces[] = $place->id;
				    		}
				    		if (count($authorizedPlaces)) $data['perimeters']['p-pit-admin']['place_id'] = $authorizedPlaces;
			    		}
			    		else {
			    			foreach ($application as $specificationId => $specification) {
			    				$perimeter = array();
						    	foreach ($context->getConfig($specification)['modalities'] as $modalityId => $unused) {
						    		if ($request->getPost($specificationId.'_'.$modalityId)) $perimeter[] = $modalityId;
						    	}
						    	if (count($perimeter)) {
						    		$data['perimeters'][$applicationId][$specificationId] = $perimeter;
						    	}
			    			}
			    		}
			    	}
			    	$data['locale'] = $request->getPost('locale');
			    	$data['is_notified'] = $request->getPost('is_notified');
			    	if (!$contact->id) $data['is_demo_mode_active'] = true;
			    	if ($contact->loadData($data) != 'OK') throw new \Exception('View error');
			    	$rc = $user->loadData($request, $contact);
			    	if ($rc != 'OK') $error = $rc;
					else {
						// Save the contact
						$rc = $contact->add();
						if ($rc != 'OK') $error = $rc;
						else {
							$vcard_id = $contact->id;
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
						}
		        		if ($rc != 'OK') $error = $rc;

						if (!$error) {

							if ($creationMode) {
								// Save the user-contact link
								$userContact = UserContact::instanciate();
								$userContact->user_id = $user->user_id;
								$userContact->vcard_id = $vcard_id;
								$rc = $userContact->add();
								if ($rc == 'OK') $context->getSecurityAgent()->requestPasswordInit($user, true, $this->url(), $contact);
								else $error = $rc;
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
        $view = new ViewModel(array(
        	'context' => $context,
        	'apps' => $apps,
			'config' => $context->getconfig(),
        	'id' => $id,
    		'contact' => $contact,
        	'places' => $places,
    		'locales' => $context->getConfig()['locales'],
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
/*		    		if ($request->getUri()->getHost() != $context->getInstance()->fqdn) {
		    			if ($context->getInstance()->fqdn) return $this->redirect()->toUrl('https://'.$context->getInstance()->fqdn);
		    		}*/
		    		if ($this->params()->fromQuery('redirect')) {
		    			return $this->redirect()->toRoute($this->params()->fromQuery('redirect'), array(), array('query' => $this->params()->fromQuery()));
		    		}
		    		elseif ($this->params()->fromQuery('auth')) {
				
				    	// Submit the P-Pit get-authenticate message
				    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
				    	$url = $this->params()->fromQuery('auth').'/user/get-authenticate';
				    	$client = new Client(
				    			$url,
				    			array(
				    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
				    					'maxredirects' => 0,
				    					'timeout'      => 30,
				    			)
				    	);
				    	$username = 'to be configured';
				    	$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
				    	$client->setMethod('POST');
				    	$token = md5(uniqid(rand(), true));
				    	$client->setParameterPost(array(
				    		'authentication_token' => $token,
					    	'authentication_validity' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' 300 seconds')),
				    	));
				    	$response = $client->send();
		    			return $this->redirect()->toUrl($this->params()->fromQuery('auth').'?identity='.$identity.'&authentication_token='.$token);
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
    			'title' => 'SynApps by 2Pit',
				'description' => 'Imagine an easy point of access to relevant informations and data, focused on multi-cultural sharing.',
    	));
   		return $view;
    }

    public function googleLoginAction()
    {
    	$view = new ViewModel(array());
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function maintainSessionAction()
    {
    	return $this->response;
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

    	$place = Place::get($context->getPlaceId());
    	
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
    			'place' => $place,
    			'active' => 'password',
    			'id' => $id,
    			'csrfForm' => $csrfForm,
    			'title' => 'SynApps by 2Pit',
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

    	$place = Place::get($context->getPlaceId());
    	
    	// Retrieve the existing user
    	$user = User::get($id);

    	// Retrieve the contact
    	$contact = Vcard::getTable()->get($user->vcard_id);

    	$view = new ViewModel(array(
    			'context' => $context,
    			'place' => $place,
    			'user' => $user,
    			'config' => $context->getConfig(),
    			'id' => $id,
    			'title' => 'SynApps by 2Pit',
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
    	$place = Place::get($context->getPlaceId());
    	
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
    			$context->getSecurityAgent()->requestPasswordInit($user, true, $this->url());
       			$message = 'OK';
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'place' => $place,
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
    			$context->getSecurityAgent()->requestPasswordInit($user, true, $this->url());
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

    public function generatePasswordAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');
    	$user = User::get($id);

    	// Generate the new password
    	$new_password = '';
    	$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
    	 
    	for($i = 0; $i < 6; $i++)
    	{
    		$new_password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
    	}
    	 
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
    			if ($request->getPost('new_password')) {
			    	$new_password = $request->getPost('new_password');
    				$context->getSecurityAgent()->changePassword($user, $user->username, null, $new_password, null);
    			}
    			$message = 'OK';
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'csrfForm' => $csrfForm,
    			'id' => $id,
    			'user' => $user,
    			'new_password' => $new_password,
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
    	$place = Place::get($context->getPlaceId());
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('user/logout');
    	}
		
    	$hash = $this->params()->fromQuery('hash', NULL);
    	
    	// Retrieve the existing user
    	$user = User::get($id);

    	// Retrieve the current contact and instance
    	$contact = Vcard::getTable()->transget($user->username, 'email');
    	if ($contact) $instance = Instance::get($contact->instance_id);
    	else $instance = Instance::get($user->instance_id);
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
    		'place' => $place,
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

    public function authenticateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$instance = Instance::get($context->getInstanceId());
    	$url = $context->getConfig('ppitCommitment/P-Pit')['userAuthenticateRedirect']['domain'].'/user/login?auth=http://'.'p-pit.test';
		return $this->redirect()->toUrl($url);
    }
/*
    public function getAuthenticateAction()
    {
		$context = Context::getCurrent();

    	// Check basic authentication
    	if (isset($_SERVER['PHP_AUTH_USER'])) {
    		$username = $_SERVER['PHP_AUTH_USER'];
    		$password = $_SERVER['PHP_AUTH_PW'];
    	} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    		if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
    			list($username, $password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    	}
    	if (!$context->getSecurityAgent()->authenticate($username, $password)) {
    		$this->getResponse()->setStatusCode('401');
	    	return $this->getResponse();
    	}
    	else {
			$user = User::get($username, 'username');
    		if (!$user) {
				$this->getResponse()->setStatusCode('422');
		    	return $this->getResponse();
			}
			$user->authentication_token = $this->getRequest()->getPost('authentication_token');
			$user->authentication_validity = $this->getRequest()->getPost('authentication_validity');
			$user->update(null);
			$this->getResponse()->setStatusCode('200');
	    	return $this->getResponse();
    	}
    }

    public function getApplicationsAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Check basic authentication
    	if (isset($_SERVER['PHP_AUTH_USER'])) {
    		$username = $_SERVER['PHP_AUTH_USER'];
    		$password = $_SERVER['PHP_AUTH_PW'];
    	} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    		if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
    			list($username, $password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    	}
    	if (!$context->getSecurityAgent()->authenticate($username, $password)) {
    		$this->getResponse()->setStatusCode('401');
	    	return $this->getResponse();
    	}
    	else {
			$user = User::get($username, 'username');
    		if (!$user) {
				$this->getResponse()->setStatusCode('422');
		    	return $this->getResponse();
			}
			$this->getResponse()->setContent(json_encode($user->applications));
			$this->getResponse()->setStatusCode('200');
	    	return $this->getResponse();
    	}
    }

    public function generatePasswordAction()
    {
    	$context = Context::getCurrent();
    	$place = Place::get($context->getPlaceId());
    	$username = $this->params()->fromRoute('username', 0);
    	if (!$username) {
    		return $this->redirect()->toRoute('home');
    	}
    	
    	// Retrieve the existing user
    	$user = User::get($username, 'username');

    	// Check if that user belongs to my perimeter
    	$myPerimeters = Vcard::get($context->getContactId())->perimeters;
    	if (array_key_exists('p-pit-admin', $myPerimeters) && array_key_exists('place_id', $myPerimeters['p-pit-admin']) && (!in_array($user->contact->place_id, $myPerimeters['p-pit-admin']['place_id']))) {
    		return $this->redirect()->toRoute('home');
    	}

    	// Generate the new password
    	$new_password = '';
    	$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
    		
    	for($i = 0; $i < 6; $i++)
    	{
    		$new_password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
    	}

		$data = $user->toArray();
		$bcrypt = new Bcrypt();
		$bcrypt->setCost(14);
		$user->password = $bcrypt->create($new_password);
		$user->password_init_token = null;
		$user->password_init_validity = null;
		$user->update(null);
    	echo $user->username.' => '.$new_password."\n";
    	return $this->response;
    }*/
    
	/**
	 * Restfull implementation
	 * TODO : authorization + error description
	 */
    		
	public function v1Action()
	{
		$context = Context::getCurrent();
		$type = $this->params()->fromQuery('type');
		$request = $this->params()->fromQuery('request', 'logout');
		$content = array();

		// Get
		if ($this->request->isGet()) {

			if ($request == 'activate') {
				$user_id = $this->params()->fromRoute('id');
				$account_id = $this->params()->fromQuery('account_id');
				if ($account_id) $account = Account::get($account_id);
				$authentication_token = $this->params()->fromQuery('hash');
				$rc = $context->getSecurityAgent()->activate($user_id, $authentication_token, null);
				if ($rc == 'Already activated') {
					return $this->redirect()->toRoute($context->getConfig('defaultRoute'), [], ['query' => ['message' => 'Already activated']]);
				}
				if ($rc == 'Unauthorized') {
					return $this->redirect()->toRoute($context->getConfig('defaultRoute'), [], ['query' => ['error' => 'Authentication']]);
				}
				if ($account) {
					$account->status = 'active';
					$account->update(null);
				}
				return $this->redirect()->toRoute('probonopro/landing' /*$context->getConfig('defaultRoute')*/, [], ['query' => ['message' => 'activated']]);
			}
			elseif ($request == 'request-activation') {
				$identity = $this->params()->fromQuery('identity');
				$vcard = Vcard::get($identity, 'email');
				if (!$vcard) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
				$account = Account::get($vcard->id, 'contact_1_id');
				if (!$account) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
				$userContact = UserContact::get($vcard->id, 'vcard_id');
				$user = User::getTable()->transGet($userContact->user_id);
				$token = $context->getSecurityAgent()->requestAuthenticationToken($user->username, false);
    
				// Send the OTP by email
				$email_body = $context->localize($context->getConfig('user/messages/activation/text'));
				$link = $this->url()->fromRoute('user/v1', ['id' => $user->user_id], ['force_canonical' => true]).'?account_id='.$account->id.'&request=activate'.'&hash='.$token;
				$email_body = sprintf($email_body, $link);
				$email_title = $context->localize($context->getConfig('user/messages/activation/title'));
				Context::sendMail($user->username, $email_body, $email_title, null);
				$this->getResponse()->setStatusCode('200');
				return $this->getResponse();
			}

			if ($request == 'lost-password') {
				$identity = $this->params()->fromQuery('identity');
/*				$vcard = Vcard::get($identity, 'email');
				if (!$vcard) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
				$userContact = UserContact::get($vcard->id, 'vcard_id');
				$user = User::getTable()->transGet($userContact->user_id);*/
				$user = User::getTable()->transGet($identity, 'username');
				if (!$user) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
				$token = $context->getSecurityAgent()->requestPasswordInit($user, false, $this->url());
				$userContact = UserContact::get($user->user_id, 'user_id');
				$vcard = Vcard::get($userContact->vcard_id);

				// Send the OTP by email
				$email_body = $context->localize($context->getConfig('user/messages/lost_password/text'));
		    	$link = $this->url()->fromRoute('landing/template2', [], ['force_canonical' => true]).'?panel=modalInitPasswordForm'.'&hash='.$token;
				$email_body = sprintf($email_body, $link);
				$email_title = $context->localize($context->getConfig('user/messages/lost_password/title'));
				Context::sendMail($vcard->email, $email_body, $email_title, null);
				$this->getResponse()->setStatusCode('200');
				return $this->getResponse();
			}
				
			// Authentication
			if (!$context->isAuthenticated()) {
				$identity = $_SERVER['PHP_AUTH_USER'];
				$credential = $_SERVER['PHP_AUTH_PW'];

				// Check that the user has an account on the current instance
				$user = User::getTable()->transGet($identity);
				if (!$user) {
					$actionStatus = ['401', 'Unauthorized'];
					$this->getResponse()->setStatusCode('401');
				}
				else {
					$userContact = UserContact::transGet($context->getInstanceId(), 'instance_id', $user->user_id, 'user_id');
					if (!$userContact) {
						$actionStatus = ['401', 'Unauthorized'];
						$this->getResponse()->setStatusCode('401');
					}
					elseif ($user->vcard_id != $userContact->vcard_id) {
						$user->vcard_id = $userContact->vcard_id;
						$user->update(null);
					}
				}
				
				$rc = $context->getSecurityAgent()->authenticate($identity, $credential);
				if ($rc != 'OK') {
					$this->getResponse()->setStatusCode('401');
					$this->getResponse()->setReasonPhrase($rc);
					return $this->getResponse();
				}
				$this->getResponse()->setStatusCode('200');
				return $this->getResponse();
			}
				
			// Logout
			if ($request == 'logout') {
				$context->getSecurityAgent()->logout();
				$this->getResponse()->setStatusCode('200');
				return $this->getResponse();
			}
		}

		// Put
		elseif ($this->request->isPut()) {
		}
		
		// Post
		elseif ($this->request->isPost()) {
			
			if ($request == 'register') {
				$data = array();
				$data['email'] = $this->request->getPost('email');
				$data['n_first'] = $this->request->getPost('n_first');
				$data['n_last'] = $this->request->getPost('n_last');
				$data['password'] = $this->request->getPost('password');
				$data['locale'] = $this->request->getPost('locale');
				$data['origine'] = $this->request->getPost('origine');
				if ($type) {
					$connection = User::getTable()->getAdapter()->getDriver()->getConnection();
					$connection->beginTransaction();
					try {
						$account = Account::instanciate($type);
						$rc = $account->loadAndAdd($data, Account::getConfig($type));
						if ($rc[0] == 206) $account = $rc[1];
						$content['data'] = $account->getProperties();
						$account->status = 'registered';
						$account->update(null);
						
						// Check that the user does not already exist
				    	$user = User::getTable()->transGet($account->email, 'username');
				    	if ($user) {
							$connection->rollback();
				    		$this->getResponse()->setStatusCode('206');
				    		echo json_encode('Trial to register an already existing user, based on email address');
				    		return $this->getResponse();
				    	}

				    	// Check that the email address belongs to the accepted domains
				    	if ($context->getConfig('user/acceptedRegistrationDomain')) {
				    		$domain = explode('@', $data['email'])[1];
				    		if (!in_array($domain, $context->getConfig('user/acceptedRegistrationDomain'))) {
				    			$connection->rollback();
				    			$this->getResponse()->setStatusCode('401');
				    			$this->getResponse()->setReasonPhrase('accepted domain');
				    			echo json_encode('The email domain does not belong to accepted domains');
				    			return $this->getResponse();
				    		}
				    	}

				    	$user_id = $context->getSecurityAgent()->register($account->email, $account->contact_1_id, $this->request->getPost('password'));
				    	$user = User::getTable()->transGet($user_id);
						$token = $context->getSecurityAgent()->requestAuthenticationToken($user->username, false);
				    	
						// Send the OTP by email
						$email_body = $context->localize($context->getConfig('user/messages/activation/text'));
						$link = $this->url()->fromRoute('user/v1', ['id' => $user->user_id], ['force_canonical' => true]).'?account_id='.$account->id.'&request=activate'.'&hash='.$token;
						$email_body = sprintf($email_body, $link);
						$email_title = $context->localize($context->getConfig('user/messages/activation/title'));
						Context::sendMail($user->username, $email_body, $email_title, null);
				    	
						$connection->commit();
						
						$this->getResponse()->setStatusCode('200');
			    		return $this->getResponse();
					}
					catch (\Exception $e) {
						$connection->rollback();
			    		$this->getResponse()->setStatusCode('500');
			    		return $this->getResponse();
					}
				}
			}

			// Init password
			if ($request == 'init-password') {
				$identity = $this->getRequest()->getPost('identity');
				$password = $this->getRequest()->getPost('password');
				$token = $this->params()->fromQuery('hash');
				$vcard = Vcard::get($identity, 'email');
				if (!$vcard) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
				$userContact = UserContact::get($vcard->id, 'vcard_id');
				$user = User::getTable()->transGet($userContact->user_id);
				$rc = $context->getSecurityAgent()->initPassword($user, $token, $user->username, $password, null);
				if ($rc != 'OK') {
					$this->getResponse()->setStatusCode('401');
					$this->getResponse()->setReasonPhrase($rc);
					return $this->response;
				}
				else {
					$this->getResponse()->setStatusCode('200');
					return $this->response;
				}
			}

			// Authentication
			if (!$context->isAuthenticated()) {
				$identity = $_SERVER['PHP_AUTH_USER'];
				$credential = $_SERVER['PHP_AUTH_PW'];
				$rc = $context->getSecurityAgent()->authenticate($identity, $credential);
				if ($rc != 'OK') {
					$this->getResponse()->setStatusCode('401');
					$this->getResponse()->setReasonPhrase($rc);
					return $this->getResponse();
				}
			}
		}

		// Delete
		elseif ($this->request->isDelete()) {
		}

		// Output
	   	ob_start("ob_gzhandler");
		echo json_encode($content, JSON_PRETTY_PRINT);
		ob_end_flush();
		return $this->response;
	}
	
	public function fbwebhookAction()
	{
		$writer = new Writer\Stream('data/log/ppitUser_demo.txt');
		$logger = new Logger();
		$logger->addWriter($writer);
		
		// Challenge for authorizing my App to access data on Facebook
		$challenge = $_REQUEST['hub_challenge'];
		$verify_token = $_REQUEST['hub_verify_token'];
		
		if ($verify_token === 'abc123') {
			echo $challenge;
		}

		$input = json_decode(file_get_contents('php://input'), true);
		$logger->info(print_r($input, true));
		
		// Initialize a new Session and instantiate an API object
/*		Api::init(
			'{your-app-id}', // App ID
			'{your-app-secret}',
			$_SESSION['facebook_access_token'] // Your user access token
		);
		
		// Grant read access on leads for a user
		$params = array(
			'user_id' => '<USER_ID>',
		);
		
		$response = Api::instance()->call(
			'/me/leadgen_whitelisted_users',
			RequestInterface::METHOD_POST,
			$params
		);


		// Get all the leads on an ad
		$ad = new Ad('<AD_ID>');
		$leads = $ad->getLeads();*/
		
		$this->getResponse()->setStatusCode('200');
		$this->getResponse()->setReasonPhrase('OK');
		return $this->response;
	}
	
	public function fbpageaccessAction()
	{		
	}
}
