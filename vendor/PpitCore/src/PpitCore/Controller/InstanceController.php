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
use PpitCore\Model\Csrf;
use PpitCore\Model\Document;
use PpitCore\Model\Instance;
use PpitCore\Model\Place;
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

    public function acceptAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the instance and place
    	$instance = Instance::get($context->getInstanceId());
    	$place = Place::getTable()->transGet($context->getPlaceId());
    	 
    	$document = Document::getTable()->transGet($context->getConfig()['document/ethicalCharter']);
    	$document->retrieveContent();

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
    
				$data = array();
    			$data['validated_ethical_charter_id'] = $document->id;
				$data['status'] = 'accepted';
				$instance->loadData($data);

    			// Atomically save
    			try {
    				$connection = Instance::getTable()->getAdapter()->getDriver()->getConnection();
    				$connection->beginTransaction();
					$instance->update($request->getPost('update_time'));
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
    			'place' => $place,
    			'document' => $document,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	return $view;
    }

    public function legalNoticesAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the instance
    	$instance = Instance::get($context->getInstanceId());
    	$place = Place::getTable()->transGet($context->getPlaceId());

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
    	$request = $this->getRequest();

    	// Make sure that we are running in a console and the user has not tricked our
    	// application into running this action from a public web server.
    	if (!$request instanceof ConsoleRequest){
    		throw new \RuntimeException('You can only use this action from a console!');
    	}
    	
    	// Get user email from console and check if the user used --verbose or -v flag
    	$id = $request->getParam('id');
    	$instance = Instance::get($id);
    	$instance->specifications = $config['specifications'];
    	$instance->update($instance->update_time);
    	
    	return "Done\n";
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
				if ($files) foreach ($files as $file) Instance::saveFile($file);
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
				if ($files) foreach ($files as $file) Instance::saveFile($file);
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
}
