<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Credit;
use PpitCore\Model\Csrf;
use PpitCore\Model\Account;
use PpitCore\Model\Instance;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use Zend\Console\Request as ConsoleRequest;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class InstanceController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
    	$place = Place::get($context->getPlaceId());

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
    	));
    }

	public function getFilters($params)
	{
		// Retrieve the query parameters
		$filters = array();
		
		$caption = ($params()->fromQuery('caption', null));
		if ($caption) $filters['caption'] = $caption;
		
		return $filters;
	}

    public function searchAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	$params = $this->getFilters($this->params());
    
    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function getList()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	$params = $this->getFilters($this->params());
    
    	$major = ($this->params()->fromQuery('major', 'caption'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC'));
    
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';
    
    	// Retrieve the list
    	$instances = Instance::getList($params, $major, $dir, $mode);
    
    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'instances' => $instances,
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
    	return $this->getList();
    }
    
    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $instance = Instance::get($id);
    	else $instance = Instance::instanciate();
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $instance->id,
    			'instance' => $instance,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	// Retrieve the instance
    	$id = (int) $this->params()->fromRoute('id', 0);
 	  	$action = $this->params()->fromRoute('act', null);
     	if ($id) $instance = Instance::get($id);
		else $instance = Instance::instanciate();

		// Instanciate the csrf form
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
    	if ($action == 'delete') $message = 'confirm-delete';
    	elseif ($action) $message =  'confirm-update';
    	else $message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
			$message = null;

			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());

			if ($csrfForm->isValid()) { // CSRF check

				// Load the input data
				$data = array();
				foreach($context->getConfig('coreInstance/update') as $propertyId => $unused) {
					$data[$propertyId] = $request->getPost(($propertyId));
				}
				if ($instance->loadData($data) != 'OK') throw new \Exception('View error');
				
				// Atomically save
		        try {
			    	$connection = Instance::getTable()->getAdapter()->getDriver()->getConnection();
		    		$connection->beginTransaction();
		    		if ($action == 'update') {
		    			if ($instance->id) $instance->update($request->getPost('update_time'));
		    			else $instance->add();
		    		}
					else if ($action == 'delete') $instance->delete();
			        $connection->commit();
			    	$message = 'OK';
				}
		    	catch (Exception $e) {
		    		$connection->rollback();
		    		throw $e;
		    	}
    		}
    	}
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'instance' => $instance,
    		'id' => $id,
    		'action' => $action,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
		$view->setTerminal(true);
		return $view;
    }

    public function tryAction()
    {
    	$context = Context::getCurrent();
    	$place = Place::get($context->getPlaceId());
    
    	$request = $this->getRequest();
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'place' => $place,
    	));
    	return $view;
    }

    public function tryAddAction()
    {
    	$context = Context::getCurrent();
    	$place = Place::get($context->getPlaceId());

    	$request = $this->getRequest();
    	 
    	$instance = Instance::instanciate();
    	$contact = Vcard::instanciate();
    	$credit = Credit::instanciate();
    	$user = User::getNew();
    
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check
    			
    			$applications = array('synapps' => true, 'p-pit-admin' => false);
    			 
    			// Retrieve the data from the request
    			$data = array();
    			$caption = explode('/', $request->getPost('caption'));
    			$caption = implode('_', $caption);
    			$data['caption'] = $caption;
    			$data['is_active'] = 1;
    			$data['applications'] = $applications;
    			$rc = $instance->loadData($data);
    			if ($rc != 'OK') throw new \Exception('View error');
    
    			$data = array();
    			$data['applications'] = $applications;
    			$data['n_title'] = $request->getPost('n_title');
    			$data['n_first'] = $request->getPost('n_first');
    			$data['n_last'] = $request->getPost('n_last');
    			$data['email'] = $request->getPost('email');
    			$data['tel_work'] = $request->getPost('tel_work');
    			$data['tel_cell'] = null;
    			$data['roles'] = array('admin' => 'admin');
    			$data['is_notified'] = 1;
    			$data['is_demo_mode_active'] = 1;
    			$data['specifications'] = array();
    			$rc = $contact->loadData($data);
    			if ($rc != 'OK') throw new \Exception('View error');
    			$rc = $user->loadData($request, $contact, $instance->id);
    
    			// Atomically save
    			$connection = Instance::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				// Add the instance, the main contact and the user
    				$rc = $instance->add();
    
    				if ($rc != 'OK') {
    					if ($rc == 'Duplicate') $error = 'Duplicate instance';
    					$connection->rollback();
    				}
    				else {
    					$contact->instance_id = $instance->id;
    					Vcard::getTable()->transSave($contact);
    					$user->instance_id = $instance->id;
    					$user->vcard_id = $contact->id;
    					$user->email = $contact->email;
    					$rc = $user->add($contact->email, true);
    					$userContact = new UserContact;
    					$userContact->instance_id = $instance->id;
    					$userContact->user_id = $user->user_id;
    					$userContact->vcard_id = $contact->id;
    					UserContact::getTable()->transSave($userContact);
    
    					$credit->instance_id = $instance->id;
    					$credit->status = 'active';
    					$credit->type = 'p-pit-communities';
    					$credit->quantity = 0;
    					$credit->activation_date = date('Y-m-d');
    					Credit::getTable()->transSave($credit);
    
    					if ($rc != 'OK') {
    						if ($rc == 'Duplicate') $error = 'Duplicate identifier';
    						else $error = $rc;
    						$connection->rollback();
    					}
    					else {
    						mkdir('public/logos/'.$instance->caption);
    						mkdir('public/img/'.$instance->caption);
    						$connection->commit();
    						$message = 'OK';
    					}
    				}
    			}
    			catch (\Exception $e) {
    				$connection->rollback();
    				throw $e;
    			}
    		}
    	}
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'place' => $place,
    			'instance' => $instance,
    			'contact' => $contact,
    			'user' => $user,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function charterAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$content = $context->getConfig('instance/charter');
		echo $context->localize($content);
		return $this->response;
    }

    public function validateCharterAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$content = $context->getConfig('instance/charter');
    	echo $context->localize($content);
    	if ($this->request->isPost() && $this->request->getPost('charter_checked')) {
    		$account = Account::get($context->getContactId(), 'contact_1_id');
    		$account->charter_validation_time = date('Y-m-d H:i:s');
    		$rc = $account->update(null);
    		if ($rc != 'OK') $this->getResponse()->setStatusCode('500');
    	}
    	return $this->response;
    }
    
    public function generalTermsOfUseAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$locale = $this->params()->fromQuery('locale');
    	$content = $context->getConfig('instance/general_terms_of_use');
		echo $context->localize($content, $locale);
		return $this->response;
    }

    public function validateGeneralTermsOfUseAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$content = $context->getConfig('instance/general_terms_of_use');
    	echo $context->localize($content);
    	if ($this->request->isPost() && $this->request->getPost('gtou_checked')) {
    		$account = Account::get($context->getContactId(), 'contact_1_id');
    		$account->terms_of_use_validation_time = date('Y-m-d H:i:s');
    		$rc = $account->update(null);
    		if ($rc != 'OK') $this->getResponse()->setStatusCode('500');
    	}
    	return $this->response;
    }
    
    public function legalNoticesAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the instance
    	$instance = Instance::get($context->getInstanceId());
    	$place = Place::get($context->getPlaceId());
    	
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'instance' => $instance,
    			'place' => $place,
    	));
    	return $view;
    }
    
    public function serializeAction()
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
		$place = Place::get($context->getPlaceId());
    	 
    	$instance = Instance::get($context->getInstanceId());

    	// Transient: Serialize a list of the entries from all menus
    	$menuEntries = [];
    	foreach ($context->getApplications() as $applicationId => $application) {
    		if ($context->getConfig('menus/'.$applicationId)) {
    			foreach ($context->getConfig('menus/' . $applicationId)['entries'] as $entryId => $entryDef) {
    				$menuEntries[$entryId] = ['menuId' => $applicationId, 'menu' => $application, 'definition' => $entryDef];
    			}
    		}
    	}
    	$tab = $this->params()->fromRoute('entryId', 'serialize');
    	
    	// Retrieve the application
    	$app = $menuEntries[$tab]['menuId'];
    	$applicationName = $context->localize($menuEntries[$tab]['menu']['labels']);

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$statusCode = null;
    	if ($this->request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($this->request->getPost());
    		if ($csrfForm->isValid()) {
		    	$config = json_decode($this->request->getPost('specifications'), true);
		    	if ($config) {
			    	$instance->specifications_backup = json_encode($instance->specifications, JSON_PRETTY_PRINT);
		    		$instance->specifications = $config;
			    	$instance->update($instance->update_time);
			    	$statusCode = ['200'];
		    	}
    		}
    	}

    	// Feed the layout
    	$this->layout('/layout/core-layout');
    	$this->layout()->setVariables(array(
    		'context' => $context,
    		'place' => $place,
    		'tab' => $tab,
    		'app' => $app,
    		'applicationName' => $applicationName,
    		'pageScripts' => 'ppit-core/view-controller/instance',
    	));
    	 
    	$view = new ViewModel(array(
    		'context' => $context,
    		'instance' => $instance,
    		'specifications' => json_encode($context->getInstance()->specifications, JSON_PRETTY_PRINT),
    		'statusCode' => $statusCode,
    	));
    	return $view;
    }

    public function addImageAction() {
    
    	// Retrieve the context
    	$context = Context::getCurrent();
    
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
				// Write the loaded images
				$files = $request->getFiles()->toArray();
				if ($files) foreach ($files as $file) Instance::saveFile($file, './public/img/');
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function addLogoAction() {
    
    	// Retrieve the context
    	$context = Context::getCurrent();
    
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
				// Write the loaded images
				$files = $request->getFiles()->toArray();
				if ($files) foreach ($files as $file) Instance::saveFile($file, './public/logos/');
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }
	
	/**
	 * Restfull implementation
	 * TODO : authorization + error description
	 */
	public function v1Action()
	{
		$context = Context::getCurrent();
	
		// Authentication
		if (!$context->isAuthenticated() && !$context->wsAuthenticate($this->getEvent())) {
			$this->getResponse()->setStatusCode('401');
			return $this->getResponse();
		}

		$id = $this->params()->fromRoute('id');
		$content = array();

		// Get
		if ($this->request->isGet()) {
			if ($id) {
				
				// Direct access mode
		    	$instance = Instance::get($id);
				if (!$instance) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
		    	$content['data'] = $instance->getProperties();
			}
			else {

				// List mode
				$filters = array();
		    	$limit = $this->params()->fromQuery('limit');
				$order = $this->params()->fromQuery('order', '+name');
		    	$page = $this->params()->fromQuery('page');
		    	$per_page = $this->params()->fromQuery('per_page');
		    	$statusDef = $context->getConfig('core_account/'.$type.'/property/status');
				if ($statusDef['definition'] != 'inline') $statusDef = $context->getConfig($statusDef['definition']);
		    	if (!array_key_exists('status', $filters)) $filters['status'] = implode(',', $statusDef['perspectives'][$perspective]);
		    	$instances = Instance::getList($type, $filters, $order, $limit, null, $page, $per_page);
		    	$content['data'] = array();
		    	foreach ($instances as $instance) $content['data'][$instance->id] = $instance->getProperties();
			}
		}

		// Put
		elseif ($this->request->isPut()) {
			$instance = Instance::instanciate();
			$data = json_decode($this->request->getContent(), true);
				
	    	// Database update
	    	$connection = Instance::getTable()->getAdapter()->getDriver()->getConnection();
	    	$connection->beginTransaction();
	    	try {
				$rc = $instance->loadAndAdd($data);
	    		if ($rc[0] == '206') { // Partially accepted on an already existing account which is returned as rc[1]
					$this->getResponse()->setStatusCode($rc[0]);
					$content['data'] = ['id' => $rc[1]];
					$connection->commit();
	    		}
				elseif ($rc[0] != '200') {
					$this->getResponse()->setStatusCode($rc[0]);
				    $this->getResponse()->setReasonPhrase($rc[1]);
					$connection->rollback();
				    return $this->getResponse();
				}
				else {
					$content['data'] = ['id' => $rc[1]];
    				mkdir('public/logos/'.$instance->caption);
    				mkdir('public/img/'.$instance->caption);
					$connection->commit();
				}
	    	}
			catch (\Exception $e) {
				$connection->rollback();
				return ['500', $rc];
			}
		}
		
		// Post
		elseif ($this->request->isPost()) {
			if (!$id) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}
			$instance = Instance::get($id);
			if (!$instance) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}

			$data = json_decode($this->request->getContent(), true);
			
			$connection = Instance::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$rc = $instance->loadAndUpdate($data, null);
				if ($rc[0] != '200') {
					$connection->rollback();
					$this->getResponse()->setStatusCode($rc[0]);
					echo $rc[1];
					return $this->getResponse();
				}
				else $connection->commit();
			}
			catch (\Exception $e) {
				$connection->rollback();
				$this->getResponse()->setStatusCode('500');
				return $this->getResponse();
			}
		}

		// Delete
		elseif ($this->request->isDelete()) {
			if (!$id) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}
			$instance = Instance::get($id);
			if (!$instance) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}
			
			// Database update
			$connection = Instance::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$rc = $instance->delete(null);
				if ($rc != 'OK') {
					$this->getResponse()->setStatusCode('500');
					return $this->getResponse();
				}
				$connection->commit();
			}
			catch (\Exception $e) {
				$connection->rollback();
				$this->getResponse()->setStatusCode('500');
				return $this->getResponse();
			}
		}

		// Output
       	ob_start("ob_gzhandler");
		echo json_encode($content, JSON_PRETTY_PRINT);
		ob_end_flush();
		return $this->response;
	}
}
