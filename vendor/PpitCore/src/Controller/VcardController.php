<?php
/**
 * 2pit V2.0
 *
 * @link      https://github.com/2pit-io/2pit.io/tree/master/vendor/PpitCore
 * @license   https://github.com/2pit-io/2pit.io/blob/master/vendor/PpitCore/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use PpitCore\Model\User;
use PpitCore\Model\Vcard;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class VcardController extends AbstractActionController
{
    public function indexAction ()
    {
    	// Retrieve the context and parameters
    	$context = Context::getCurrent();
    	$entry = $this->params()->fromRoute('entry', 'vcard');

    	// Retrieve the description
    	$description = Vcard::getDescription();

		// Transient: Serialize a list of the entries from all menus
		$menuEntries = [];
		foreach ($context->getApplications() as $applicationId => $application) {
			if ($context->getConfig('menus/'.$applicationId)) {
				foreach ($context->getConfig('menus/' . $applicationId)['entries'] as $entryId => $entryDef) {
					$menuEntries[$entryId] = ['menuId' => $applicationId, 'menu' => $application, 'definition' => $entryDef];
				}
			}
		}
		$tab = $this->params()->fromRoute('entryId', 'vcard');

		// Retrieve the application
		$app = $menuEntries[$tab]['menuId'];
		$applicationName = $context->localize($menuEntries[$tab]['menu']['labels']);
		
    	// Feed the layout
		$this->layout('/layout/core-layout');
		$this->layout()->setVariables(array(
			'context' => $context,
			'entry' => $entry,
			'description' => $description,
			'tab' => $tab,
			'app' => $app,
			'applicationName' => $applicationName,
			'pageScripts' => 'ppit-core/view-controller/vcard',
		));
    	
		$view = $this->indexAction();
    	return $view;
    }
    
	public function getAction()
	{
		return $this->v1Action();
/*		$context = Context::getCurrent();
		$id = $this->params()->fromRoute('id');
		
		if ($id) {

			// Direct access mode
			$vcard = Vcard::get($id);
			if (!$vcard) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}
			$content['data'] = $vcard->getProperties();
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
			$vcards = Vcard::getList($type, $filters, $order, $limit, null, $page, $per_page);
			$content['data'] = array();
			foreach ($vcards as $vcard) $content['data'][$vcard->id] = $vcard->getProperties();
		}
		
		return $content;*/
	}
/*	
	public function processPut($data)
	{
		$context = Context::getCurrent();
		$content = array();
		
		$vcard = Vcard::instanciate();

		// Database update
		$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {
			$rc = $vcard->loadAndAdd($data);
			if ($rc[0] != '200') {
				$connection->rollback();
				$content['statusCode'] = $rc[0];
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = $rc[1];
				$this->getResponse()->setReasonCode($content['reasonCode']);
			}
			else {
				$connection->commit();
				$content['statusCode'] = '200';
				$this->getResponse()->setStatusCode($content['statusCode']);
			}
		}
		catch (\Exception $e) {
			$connection->rollback();
			$content['statusCode'] = '500';
			$this->getResponse()->setStatusCode($content['statusCode']);
			$content['reasonCode'] = $e->getMessage();
			$this->getResponse()->setReasonCode($content['reasonCode']);
		}
		
		return $content;
	}*/

	public function putAction()
	{
		// Retrieve the context and parameters
		$context = Context::getCurrent();
		$put = true; // $this->request->isPut();

		// Instanciate the form token
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
	
		// Build content for the view including the token to pass back
		$content = [
			'context' => $context,
			'csrfForm' => $csrfForm,
			'put' => $put,
			'statusCode' => '200',
		];
		
		// Process the put request with the valid form token against CSRF
		if ($put) {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->getRequest()->getPost());
			if (!$csrfForm->isValid()) { // CSRF check
				$content['statusCode'] = '200';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = 'Expired';
				return $this->getResponse();
			}
			else {
/*				$data = json_decode($this->request->getContent(), true);
				$content = array_merge($content, $this->processPut($data));*/
				$content = array_merge($content, json_decode($this->v1Action()->getContent(), true));
			}
		}

		$view = new ViewModel($content);
		$view->setTerminal(true);
		return $view;
	}
/*	
	public function processPost($id, $data)
	{
		$context = Context::getCurrent();
		$content = [];
		
		// Retrieve the ressource to update
		if (!$id) {
			$content['statusCode'] = '400';
			$this->getResponse()->setStatusCode($content['statusCode']);
			$content['reasonCode'] = 'id is expected on a post request';
			$this->getResponse()->setReasonCode($content['reasonCode']);
			return $content;
		}
		$vcard = Vcard::get($id);
		if (!$vcard) {
			$content['statusCode'] = '400';
			$this->getResponse()->setStatusCode($content['statusCode']);
			$content['reasonCode'] = 'Resource not found for given id';
			$this->getResponse()->setReasonCode($content['reasonCode']);
			return $content;
		}
			
		$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {
			$rc = $vcard->loadAndUpdate($data, null);
			if ($rc[0] != '200') {
				$connection->rollback();
				$content['statusCode'] = $rc[0];
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = $rc[1];
				$this->getResponse()->setReasonCode($content['reasonCode']);
			}
			else {
				$connection->commit();
				$content['statusCode'] = '200';
				$this->getResponse()->setStatusCode($content['statusCode']);
			}
		}
		catch (\Exception $e) {
			$connection->rollback();
			$content['statusCode'] = '500';
			$this->getResponse()->setStatusCode($content['statusCode']);
			$content['reasonCode'] = $e->getMessage();
			$this->getResponse()->setReasonCode($content['reasonCode']);
		}
	
		return $content;
	}*/
	
	public function postAction()
	{
		// Retrieve the context and parameters
		$context = Context::getCurrent();
		$id = $this->params()->fromRoute('id');
		
		// Instanciate the form token
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		
		// Build content for the view including the token to pass back
		$content = [
			'context' => $context,
			'id' => $id,
			'csrfForm' => $csrfForm,
			'post' => $this->request->isPost(),
		];
		
		// Process the put request with the valid form token against CSRF
		if ($this->getRequest()->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->getRequest()->getPost());
			if (!$csrfForm->isValid()) { // CSRF check
				$content['statusCode'] = '200';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = 'Expired';
				return $this->getResponse();
			}
			else {
/*				$data = json_decode($this->request->getContent(), true);
				$content = array_merge($content, $this->processPost($id, $data));*/
				$content = array_merge($content, json_decode($this->v1Action()->getContent(), true));
			}
    	}
    	
    	$view = new ViewModel([$content]);
    	$view->setTerminal(true);
    	return $view;
	}
/*	
	public function processDelete($id)
	{
		$context = Context::getCurrent();
		$content = array();

		// Retrieve the ressource to update
		if (!$id) {
			$content['statusCode'] = '400';
			$this->getResponse()->setStatusCode($content['statusCode']);
			$content['reasonCode'] = 'id is expected on a post request';
			$this->getResponse()->setReasonCode($content['reasonCode']);
			return $content;
		}
		$vcard = Vcard::get($id);
		if (!$vcard) {
			$content['statusCode'] = '400';
			$this->getResponse()->setStatusCode($content['statusCode']);
			$content['reasonCode'] = 'Resource not found for given id';
			$this->getResponse()->setReasonCode($content['reasonCode']);
			return $content;
		}
		
		// Database update
		$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {
			$rc = $vcard->delete(null);
			if ($rc != 'OK') {
				$connection->rollback();
				$content['statusCode'] = '400';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = $rc;
				$this->getResponse()->setReasonCode($content['reasonCode']);
			}
			else {
				$connection->commit();
				$content['statusCode'] = '200';
				$this->getResponse()->setStatusCode($content['statusCode']);
			}
		}
		catch (\Exception $e) {
			$connection->rollback();
			$content['statusCode'] = '500';
			$this->getResponse()->setStatusCode($content['statusCode']);
			$content['reasonCode'] = $e->getMessage();
			$this->getResponse()->setReasonCode($content['reasonCode']);
		}
		
		return $content;
	}*/

	public function deleteAction()
	{
		// Retrieve the context and parameters
		$context = Context::getCurrent();
		$id = $this->params()->fromRoute('id');
	
		// Instanciate the form token
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
	
		// Build content for the view including the token to pass back
		$content = [
			'context' => $context,
			'id' => $id,
			'csrfForm' => $csrfForm,
			'delete' => $this->request->isDelete(),
		];
	
		// Process the put request with the valid form token against CSRF
		if ($this->getRequest()->isDelete()) {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->getRequest()->getPost());
			if (!$csrfForm->isValid()) { // CSRF check
				$content['statusCode'] = '200';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = 'Expired';
				return $this->getResponse();
			}
			else {
/*				$data = json_decode($this->request->getContent(), true);
				$content = array_merge($content, $this->processDelete($id));*/
				$content = array_merge($content, json_decode($this->v1Action()->getContent(), true));
			}
		}
		 
		$view = new ViewModel([$content]);
		$view->setTerminal(true);
		return $view;
	}
	
	public function dataRecoveryAction()
	{
		foreach (Vcard::getList(null, []) as $profileId => $profile) {
			$account = \Model\Account::get($profileId, 'contact_1_id');
			if ($account && $account->type == 'pbc') {
				$updated = false;
/*    			if ($account->property_2 || $account->property_3 || $account->property_15 || $account->property_16) {
					if (!$profile->tiny_1) $profile->tiny_1 = $account->property_15;
					if (!$profile->tiny_2) $profile->tiny_2 = $account->property_2;
					if (!$profile->tiny_3) $profile->tiny_3 = $account->property_3;
					if (!$profile->tiny_4) $profile->tiny_4 = $account->property_16;
					$updated = true;
				}*/
				 
				if (!$profile->tiny_3 && $account->comment_1) {
					$profile->tiny_3 = $account->comment_1;
					$updated = true;
				}
				 
				if ($updated) {
					print_r(json_encode($profile, JSON_PRETTY_PRINT));
					$profile->update(null);
				}
			}
	
		}
	
		return $this->response;
	}
	
	public function photoAction()
    {
		// Control access 
		$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the contact
    	$contact = Vcard::get($id);
    	if (!$contact) $this->redirect()->toRoute('index'); // Not allowed

    	if ($contact->photo_link_id) $file = 'data/documents/'.$contact->photo_link_id;
    	else $file = 'data/photos/'.$contact->id.'.jpg';
    	if (!file_exists($file)) $file = 'public/img/no-photo.png';
    	$type = 'image/jpeg';
    	header('Content-Type:'.$type);
    	header('Content-Length: ' . filesize($file));
    	readfile($file);
    	$view = new ViewModel(array());
    	$view->setTerminal(true);
    	return $view;
    }

    public function demoModeAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    		
		$contact = Vcard::get($context->getContactId());
    	if (!$contact) $this->redirect()->toRoute('index'); // Not allowed
    	if ($contact->is_demo_mode_active) $contact->is_demo_mode_active = false;
    	else $contact->is_demo_mode_active = true;
    	$contact->update(null);
    	return $this->redirect()->toRoute('home');
    }

	/**
	 * Restfull implementation
	 * TODO : authorization
	 */

    /**
     * Retrieve and format search parameters from the query. The search configuration (core_account/search/<type>) describes the
     * search criteria. A simple criteria has the name of the property. A range criteria is a tuplet where the property name is
     * prefixed by min_ and max_ respectively.
     */
    public static function getFilters($arguments, $description)
    {
    	$context = Context::getCurrent();
    
    	// Retrieve the query parameters
    	$filters = array();
    
    	foreach ($description['search']['properties'] as $propertyId => $property) {
    
    		$argument = ($arguments($propertyId, null));
    		if ($argument) {
    			if (!in_array($property['type'], ['select', 'multiselect'])) $filters[$propertyId] = $argument;
    			else {
    				$argument = explode(',', $argument);
    				if (!in_array($argument[0], ['eq', 'ne', 'gt', 'ge', 'lt', 'le', 'in', 'between', 'like', 'null', 'not_null'])) {
    					$filters[$propertyId] = ['like', $argument];
    				}
    				else $filters[$propertyId] = $argument;
    			}
    		}
    	}
    	return $filters;
    }
    
    public function v1Action()
    {
    	$context = Context::getCurrent();
    
    	// Authentication
    	if (!$context->isAuthenticated() && !$context->wsAuthenticate($this->getEvent())) {
    		$this->getResponse()->setStatusCode('401');
    		return $this->getResponse();
    	}
    
    	$id = $this->params()->fromRoute('id');
    	$description = Vcard::getDescription();

    	$content = array();
    
    	// Get
    	if ($this->request->isGet()) {
    		if ($id) {
    
    			// Direct access mode
    			$vcard = Vcard::get($id);
    			if (!$vcard) {
    				$this->getResponse()->setStatusCode('400');
					$content['reasonCode'] = 'Resource not found for given id';
					$this->getResponse()->setReasonCode($content['reasonCode']);
    				return $this->getResponse();
    			}
    			$content['data'] = $vcard->getProperties();
    		}
    		else {
    
    			// List mode
    			$columns = $this->params()->fromQuery('limit');
    			if ($columns) $columns = explode(',', $columns);
    			$filters = $this->getFilters($this->params()->fromQuery(), $description);
    			$order = $this->params()->fromQuery('order', '+n_fn');
    			$limit = $this->params()->fromQuery('limit');
    			$select = Vcard::getSelect($columns, $filters, $order, $limit);
    			$vcards = Vcard::getTable()->selectWith($select);
    			$content['data'] = array();
    			foreach ($vcards as $vcard) $content['data'][$vcard->id] = $vcard->getProperties();
    		}
    	}
    
    	// Put
    	elseif ($this->request->isPut()) {
    		$vcard = Vcard::instanciate();
    		$data = json_decode($this->request->getContent(), true);
    		$rc = $vcard->loadDataV2($data);
    		if ($rc != 'OK') {
    			$content['statusCode'] = '500';
    			$this->getResponse()->setStatusCode($content['statusCode']);
    			$content['reasonCode'] = 'vcard->loadData: ' . $rc;
    			$this->getResponse()->setReasonCode($content['reasonCode']);
    			return $this->getResponse();
    		}
    		
    		// Database update
    		$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
    		$connection->beginTransaction();
    		try {
    			$rc = $vcard->add();
    			if ($rc[0] != '200') {
					$connection->rollback();
					$content['statusCode'] = $rc[0];
					$this->getResponse()->setStatusCode($content['statusCode']);
					$content['reasonCode'] = $rc[1];
					$this->getResponse()->setReasonCode($content['reasonCode']);
    				return $this->getResponse();
    			}
    			else {
    				$content['data'] = ['id' => $rc[1]];
    				$connection->commit();
    			}
    		}
    		catch (\Exception $e) {
				$connection->rollback();
				$content['statusCode'] = '500';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = $e->getMessage();
				$this->getResponse()->setReasonCode($content['reasonCode']);
    			return $this->getResponse();
    		}
    	}
    
    	// Post
    	elseif ($this->request->isPost()) {
    		if (!$id) {
				$content['statusCode'] = '400';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = 'id is expected on a post request';
				$this->getResponse()->setReasonCode($content['reasonCode']);
    			return $this->getResponse();
    		}
			$vcard = Vcard::get($id);
			if (!$vcard) {
				$content['statusCode'] = '400';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = 'Resource not found for given id';
				$this->getResponse()->setReasonCode($content['reasonCode']);
    			return $this->getResponse();
			}
    
    		$data = json_decode($this->request->getContent(), true);
    		$rc = $vcard->loadDataV2($data);
    		if ($rc != 'OK') {
				$content['statusCode'] = '500';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = 'vcard->loadData: ' . $rc;
				$this->getResponse()->setReasonCode($content['reasonCode']);
				return $this->getResponse();
    		}
    		
    		$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
    		$connection->beginTransaction();
    		try {
				$rc = $vcard->update(null);
    			if ($rc[0] != '200') {
					$connection->rollback();
					$content['statusCode'] = $rc[0];
					$this->getResponse()->setStatusCode($content['statusCode']);
					$content['reasonCode'] = $rc[1];
					$this->getResponse()->setReasonCode($content['reasonCode']);
    				return $this->getResponse();
    			}
    			else $connection->commit();
    		}
    		catch (\Exception $e) {
				$connection->rollback();
				$content['statusCode'] = '500';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = $e->getMessage();
				$this->getResponse()->setReasonCode($content['reasonCode']);
    			return $this->getResponse();
    		}
    	}
    
    	// Delete
    	elseif ($this->request->isDelete()) {
    	    if (!$id) {
				$content['statusCode'] = '400';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = 'id is expected on a post request';
				$this->getResponse()->setReasonCode($content['reasonCode']);
    			return $this->getResponse();
    		}
			$vcard = Vcard::get($id);
			if (!$vcard) {
				$content['statusCode'] = '400';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = 'Resource not found for given id';
				$this->getResponse()->setReasonCode($content['reasonCode']);
    			return $this->getResponse();
			}
    		
    		// Database update
    		$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
    		$connection->beginTransaction();
    		try {
    			$rc = $vcard->delete(null);
    			if ($rc != 'OK') {
					$connection->rollback();
					$content['statusCode'] = '400';
					$this->getResponse()->setStatusCode($content['statusCode']);
					$content['reasonCode'] = $rc;
					$this->getResponse()->setReasonCode($content['reasonCode']);
    				return $this->getResponse();
    			}
    			$connection->commit();
    		}
    		catch (\Exception $e) {
				$connection->rollback();
				$content['statusCode'] = '500';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonCode'] = $e->getMessage();
				$this->getResponse()->setReasonCode($content['reasonCode']);
    			return $this->getResponse();
    		}
    	}

    	// Output
    	ob_start("ob_gzhandler");
    	echo json_encode($content, JSON_PRETTY_PRINT);
    	ob_end_flush();
    	return $this->response;
    }
/*	
	public function v1Action()
	{
		$context = Context::getCurrent();
	
		// Authentication
		if (!$context->isAuthenticated() && !$context->wsAuthenticate($this->getEvent())) {
			$this->getResponse()->setStatusCode('401');
			return $this->getResponse();
		}
		
		// Get
		if ($this->request->isGet()) {
			$content = $this->getAction();
		}
	
		// Put
		elseif ($this->request->isPut()) {
			$data = json_decode($this->getRequest()->getContent(), true);
			$content = $this->processPut($data);
		}
	
		// Post
		elseif ($this->request->isPost()) {
			$id = $this->params()->fromRoute('id');
			$data = json_decode($this->getRequest()->getContent(), true);
			$content = $this->processPost($id, $data);
		}
	
		// Delete
		elseif ($this->request->isDelete()) {
			$content = $this->processDelete();
		}
	
		// Output
		ob_start("ob_gzhandler");
		echo json_encode($content, JSON_PRETTY_PRINT);
		ob_end_flush();
		return $this->getResponse();
	}*/
}
