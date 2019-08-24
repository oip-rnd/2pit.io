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
    	$place = Place::get($context->getPlaceId());
    	$entry = $this->params()->fromRoute('entry', 'vcard');

    	// Retrieve the description
    	$config = Vcard::getDescription();

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
			'place' => $place,
			'entry' => $entry,
			'config' => $config,
			'tab' => $tab,
			'app' => $app,
			'applicationName' => $applicationName,
			'pageScripts' => 'ppit-core/view-controller/vcard',
		));
    	
    	return new ViewModel(array(
			'context' => $context,
    	));
    }

    public function searchAction()
    {
    	// Retrieve the context and description
    	$context = Context::getCurrent();
    	$config = Vcard::getDescription();
    
    	// Return the link list
    	$view = new ViewModel(array(
    		'context' => $context,
    		'searchPage' => $config['search'],
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function listAction()
    {
    	// Retrieve the context and description
    	$context = Context::getCurrent();
    	$config = Vcard::getDescription();
    	
    	$content = [
    		'context' => $context,
			'config' => $config,
    	];
    		 
    	// Get the list and description as content 
		$content = array_merge($content, $this->v1Action());

    	// Return the link list
    	$view = new ViewModel($content);
    	$view->setTerminal(true);
    	return $view;
    }

    public function detailAction()
    {
		// Retrieve the context and parameters
		$context = Context::getCurrent();
    	$config = Vcard::getDescription();
		$id = $this->params()->fromRoute('id');

		if ($this->request->isGet()) $requestType = 'GET';
		elseif ($this->request->isPost()) $requestType = 'POST';
		elseif ($this->request->isDelete()) $requestType = 'DELETE';

		// Instanciate the form token
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		
		// Build content for the view including the token to pass back
		$content = [
			'context' => $context,
			'id' => $id,
			'config' => $config,
			'csrfForm' => $csrfForm,
			'requestType' => $requestType,
			'statusCode' => '200',
		];

		// Get the list and description as content
		$content = array_merge($content, $this->v1Action('GET'));
	
		// Process the POST or DELETE request with the valid form token against CSRF
		if ($requestType == 'POST' || $requestType == 'DELETE') {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->getRequest()->getPost());
			if (!$csrfForm->isValid()) { // CSRF check
				$content['statusCode'] = '400';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonPhrase'] = 'Expired';
			}
			else {
				$content = array_merge($content, $this->v1Action());
				if (array_key_exists('vcard', $content)) $content['id'] = $content['vcard']['id'];
				else {
					$content['vcard'] = Vcard::instanciate()->getProperties();
					$content['vcard']['is_deletable'] = true;
					$content['statusCode'] = '400';
					$this->getResponse()->setStatusCode($content['statusCode']);
					$content['reasonPhrase'] = 'Isolation';
					$this->getResponse()->setReasonPhrase($content['reasonPhrase']);
				}
			}
		}
		// Return the link list
		$view = new ViewModel($content);
		$view->setTerminal(true);
		return $view;
	}
    
	public function getAction()
	{
		return $this->v1Action();
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
			$argument = ($arguments->fromQuery($propertyId, null));
			if ($argument) {
				$argument = explode(',', $argument);
				if (!in_array($argument[0], ['eq', 'ne', 'gt', 'ge', 'lt', 'le', 'in', 'between', 'like', 'null', 'not_null'])) {
					if (count($argument) > 1) $filters[$propertyId] = array_merge(['in'], $argument);
					else $filters[$propertyId] = array_merge(['like'], $argument);
				}
				else $filters[$propertyId] = $argument;
			}
		}
		return $filters;
	}

	public function v1Action($requestType = null)
	{
		$context = Context::getCurrent();
		if (!$requestType) {
			if ($this->request->isGet()) $requestType = 'GET';
			elseif ($this->request->isPost()) $requestType = 'POST';
			elseif ($this->request->isDelete()) $requestType = 'DELETE';
		}
		
		// Authentication
		if (!$context->isAuthenticated() && !$context->wsAuthenticate($this->getEvent())) {
			$this->getResponse()->setStatusCode('401');
			return $this->getResponse();
		}

		$id = $this->params()->fromRoute('id');
		$description = Vcard::getDescription();

		$content = [];

		// Get
		if ($requestType == 'GET') {
			if ($id !== null) {

				// Direct access mode
				if ($id) $vcard = Vcard::get($id);
				else $vcard = Vcard::instanciate();
				$content['vcard'] = $vcard->getProperties();
				$content['vcard']['is_deletable'] = $vcard->isDeletable();
			}
			else {

				// List mode
				$columns = $this->params()->fromQuery('columns');
				if ($columns) $columns = explode(',', $columns);

				$filters = $this->getFilters($this->params(), $description);

				$order = $this->params()->fromQuery('order', 'n_fn');
				if ($order) $order = explode(',', $order);

				$limit = $this->params()->fromQuery('limit');

				$select = Vcard::getSelect($columns, $filters, $order, $limit);
				$vcards = Vcard::getTable()->selectWith($select);
				$content['arguments'] = ['columns' => $columns, 'filters' => $filters, 'order' => $order, 'limit' => $limit];

				$content['vcards'] = [];
				foreach ($vcards as $vcard) {
					if (!$columns) $data = $vcard->getProperties();
					else {
						$vcardProperties = $vcard->getProperties();
						$data = [];
						foreach ($columns as $column) $data[$column] = $vcardProperties[$column];
					}
					$content['vcards'][$vcard->id] = $data;
				}
			}
		}

		// Post
		elseif ($requestType == 'POST') {
		
			$data = [];
			foreach ($description['detail']['properties'] as $propertyId => $property) {
				$value = $this->request->getPost($propertyId);
				if ($value !== null) $data[$propertyId] = $value;
			}
			
			if ($id) {
				$vcard = Vcard::get($id);
				if (!$vcard) {
					$content['statusCode'] = '400';
					$this->getResponse()->setStatusCode($content['statusCode']);
					$content['reasonPhrase'] = 'Resource not found for given id';
					$this->getResponse()->setReasonPhrase($content['reasonPhrase']);
				}
			}
			else {
				$vcard = Vcard::instanciate();
			}
			$rc = $vcard->loadDataV2($data);
			if ($rc != 'OK') {
				$content['statusCode'] = '500';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonPhrase'] = 'vcard->loadData: ' . $rc;
				$this->getResponse()->setReasonPhrase($content['reasonPhrase']);
			}
			else {
				$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
				$connection->beginTransaction();
				try {
					$update_time = $this->request->getPost('update_time');
					$rc = $vcard->update($update_time);
					if ($rc != 'OK') {
						$connection->rollback();
						$content['statusCode'] = '400';
						$this->getResponse()->setStatusCode($content['statusCode']);
						$content['reasonPhrase'] = $rc;
						$this->getResponse()->setReasonPhrase($content['reasonPhrase']);
					}
					else $connection->commit();
				}
				catch (\Exception $e) {
					$content['statusCode'] = '500';
					$this->getResponse()->setStatusCode($content['statusCode']);
					$content['reasonPhrase'] = $e->getMessage();
					$this->getResponse()->setReasonPhrase($content['reasonPhrase']);
				}
			}
			$content['vcard'] = $vcard->getProperties();
			$content['vcard']['is_deletable'] = $vcard->isDeletable();
		}

		// Delete
		elseif ($requestType == 'DELETE') {
			if (!$id) {
				$content['statusCode'] = '400';
				$this->getResponse()->setStatusCode($content['statusCode']);
				$content['reasonPhrase'] = 'id is expected on a post request';
				$this->getResponse()->setReasonPhrase($content['reasonPhrase']);
			}
			else {
				$vcard = Vcard::get($id);
				if (!$vcard) {
					$content['statusCode'] = '400';
					$this->getResponse()->setStatusCode($content['statusCode']);
					$content['reasonPhrase'] = 'Resource not found for given id';
					$this->getResponse()->setReasonPhrase($content['reasonPhrase']);
				}
				else {

					// Database update
					$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
					$connection->beginTransaction();
					try {
						$rc = $vcard->delete(null);
						if ($rc != 'OK') {
							$connection->rollback();
							$content['statusCode'] = '400';
							$this->getResponse()->setStatusCode($content['statusCode']);
							$content['reasonPhrase'] = $rc;
							$this->getResponse()->setReasonPhrase($content['reasonPhrase']);
						}
						else {
							$connection->commit();
							$content['vcard'] = $vcard->getProperties();
						}
					}
					catch (\Exception $e) {
						$connection->rollback();
						$content['statusCode'] = '500';
						$this->getResponse()->setStatusCode($content['statusCode']);
						$content['reasonPhrase'] = $e->getMessage();
						$this->getResponse()->setReasonPhrase($content['reasonPhrase']);
					}
				}
			}
		}
return $content;
		// Output
		ob_start("ob_gzhandler");
		echo json_encode($content, JSON_PRETTY_PRINT);
		ob_end_flush();
		return $this->response;
	}
}
