<?php
namespace PpitCore\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Vcard;
use PpitCore\Model\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class VcardController extends AbstractActionController
{
	public $emailRegex = "/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/";
	public $telRegex = "/^\+?([0-9\. ]*)$/";

	public function indexAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		$instance_id = (int) $this->params()->fromRoute('instance_id', 0);
		$community_id = (int) $this->params()->fromRoute('community_id', 0);
		$community = Community::getTable()->get($community_id);
	
		$major = $this->params()->fromQuery('major', NULL);
		$dir = $this->params()->fromQuery('dir', 'ASC');
		$filter = $this->params()->fromQuery('filter', NULL);
		$vcards = Vcard::getList($community_id, array(), $major, $dir);
	
		// Return the page
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'instance_id' => $instance_id,
				'community_id' => $community_id,
				'community' => $community,
				'major' => $major,
				'dir' => $dir,
				'vcards' => $vcards,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function searchAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		
		// Retrieve the community
		$community_id = (int) $this->params()->fromRoute('community_id', 0);
		if ($community_id) $community = Community::getTable()->get($community_id);
		$properties = null; // To be managed in configuration layer

		// Return the link list
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'community_id' => $community_id,
				'properties' => $properties,
		));
		return $view;
	}
	public function getFilters($params)
	{
		// Retrieve the query parameters
		$filters = array();
	
		$n_fn = ($params()->fromQuery('n_fn', null));
		if ($n_fn) $filters['n_fn'] = $n_fn;

		$email = ($params()->fromQuery('email', null));
		if ($email) $filters['email'] = $email;

		$tel_cell = ($params()->fromQuery('tel_cell', null));
		if ($tel_cell) $filters['tel_cell'] = $tel_cell;

		$tel_work = ($params()->fromQuery('tel_work', null));
		if ($tel_work) $filters['tel_work'] = $tel_work;
		
		$sex = ($params()->fromQuery('sex', null));
		if ($sex) $filters['sex'] = $sex;

		$adr_city = ($params()->fromQuery('adr_city', null));
		if ($adr_city) $filters['adr_city'] = $adr_city;

		$adr_state = ($params()->fromQuery('adr_state', null));
		if ($adr_state) $filters['adr_state'] = $adr_state;

		$adr_country = ($params()->fromQuery('adr_country', null));
		if ($adr_country) $filters['adr_country'] = $adr_country;
		
		$min_birth_date = ($params()->fromQuery('min_birth_date', null));
		if ($min_birth_date) $filters['min_birth_date'] = $min_birth_date;
	
		$max_birth_date = ($params()->fromQuery('max_birth_date', null));
		if ($max_birth_date) $filters['max_birth_date'] = $max_birth_date;

		$place_of_birth = ($params()->fromQuery('place_of_birth', null));
		if ($place_of_birth) $filters['place_of_birth'] = $place_of_birth;

		$nationality = ($params()->fromQuery('nationality', null));
		if ($nationality) $filters['nationality'] = $nationality;
		
		return $filters;
	}

    public function getList()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	// Retrieve the community id
		$community_id = (int) $this->params()->fromRoute('community_id', 0);
		if ($community_id) $community = Community::getTable()->get($community_id);
		$properties = null; // To be managed in configuration layer

		// Retrieve the vcard list
    	$params = $this->getFilters($this->params());
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';
    	$major = $this->params()->fromQuery('major', 'n_fn');
    	$dir = $this->params()->fromQuery('dir', 'ASC');
    	$vcards = Vcard::getList($community_id, $params, $major, $dir, $mode);
    
    	// Return the page
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'community_id' => $community_id,
    			'major' => $major,
    			'dir' => $dir,
    			'mode' => $mode,
    			'params' => $params,
    			'vcards' => $vcards,
				'properties' => $properties,
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

    public function listRestAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	$community_id = (int) $this->params()->fromRoute('community_id', 0);
    
    	$data = array();
    	$filters = array('n_fn' => $this->params()->fromQuery('filter', NULL));
    	$vcards = Vcard::getList($community_id, $filters, 'n_last', 'ASC');
    	foreach ($vcards as $vcard) {
    		$data[] = array(
    				'id' => $vcard->id,
    				'label' => $vcard->n_fn.' - '.(($vcard->email) ? $vcard->email : (($vcard->tel_cell) ? $vcard->tel_cell : $vcard->tel_work)),
    				'n_title' => $vcard->n_title,
    				'n_last' => $vcard->n_last,
    				'n_first' => $vcard->n_first,
    				'email' => $vcard->email,
    				'tel_work' => $vcard->tel_work,
    				'tel_cell' => $vcard->tel_cell,
    				'adr_street' => $vcard->adr_street,
    				'adr_extended' => $vcard->adr_extended,
    				'adr_post_office_box' => $vcard->adr_post_office_box,
    				'adr_zip' => $vcard->adr_zip,
    				'adr_city' => $vcard->adr_city,
    				'adr_state' => $vcard->adr_state,
    				'adr_country' => $vcard->adr_country,
    				'sex' => $vcard->sex,
    				'birth_date' => $vcard->birth_date,
    				'place_of_birth' => $vcard->place_of_birth,
    				'nationality' => $vcard->nationality,
    		);
    	}
    	return new JsonModel(array('data' => $data));
    }

    public function detailAction()
    {
    	// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the community
		$community_id = (int) $context->getCommunityId();
		if ($community_id) $community = Community::get($community_id);
				
    	// Retrieve the vcard
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');
    	$contact = Vcard::get($id);
    	if (!$contact) $this->redirect()->toRoute('index'); // Not allowed
        $view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
        	'contact' => $contact,
        	'community_id' => $community_id,
        	'id' => $id,
        ));
   		$view->setTerminal(true);
   		return $view;
    }

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the community
		$community_id = (int) $context->getCommunityId();
		if ($community_id) $community = Community::get($community_id);
    	    
    	// Check the presence of an id parameter (update case)
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) {
    		$contact = Vcard::get($id);
    		if (!$contact) $this->redirect()->toRoute('index'); // Not allowed
    	}
    	else $contact = Vcard::instanciate($community_id);

    	if ($community_id) {
    		// Retrieve the vcards
    		$vcards = Vcard::getList($community_id, array(), null, null);
    		$communities = null;
    	}
    	else {
    		// Retrieve the communities
    		$communities = Community::getList('name', 'ASC');
    		$vcards = null;
    	}
    
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    
    	$error = null;
    	$message = null;
    	$request = $this->getRequest();
    	if ($request->isPost())
    	{
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    
    		if ($csrfForm->isValid())
    		{
		    	$data = array();
		    	if ($request->getPost('community_id')) $data['community_id'] = $request->getPost('community_id');
		    	$data['n_title'] = $request->getPost('n_title');
		    	$data['n_last'] =  $request->getPost('n_last');
		    	$data['n_first'] = $request->getPost('n_first');
		    	$data['email'] = $request->getPost('email');
		    	$data['tel_work'] = $request->getPost('tel_work');
		    	$data['tel_cell'] = $request->getPost('tel_cell');
		    
		    	// Retrieve the input value for authorized properties (restriction list at community level, no restriction if no community)
		    	$data['adr_street'] = $request->getPost('adr_street');
		    	$data['adr_extended'] = $request->getPost('adr_extended');
		    	$data['adr_post_office_box'] = $request->getPost('adr_post_office_box');
		    	$data['adr_zip'] = $request->getPost('adr_zip');
		    	$data['adr_city'] = $request->getPost('adr_city');
		    	$data['adr_state'] = $request->getPost('adr_state');
		    	$data['adr_country'] = $request->getPost('adr_country');
		    	$data['sex'] = $request->getPost('sex');
		    	$data['birth_date'] = $request->getPost('birth_date');
		    	$data['place_of_birth'] = $request->getPost('place_of_birth');
		    	$data['nationality'] = $request->getPost('nationality');

		    	$data['roles'] = array();
		    	foreach ($context->getConfig('ppitApplications') as $application) {
		    		foreach ($application['role'] as $roleId => $role) {
		    			if ($request->getPost('role_'.$roleId)) $data['roles'][$roleId] = true;
		    			else $data['roles'][$roleId] = false;
		    		}
		    	}
		    	$data['is_notified'] = $request->getPost('is_notified');
		    	$data['is_demo_mode_active'] = $request->getPost('is_demo_mode_active');
		    	if ($contact->loadData($data, $community_id) != 'OK') throw new \Exception('View error');
    			
    			// Atomically save
    			$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				// Save
    				if ($contact->id) $contact->update($request->getPost('update_time'));
    				else $contact->add();
    
    				$connection->commit();
    
    				$message = 'OK';
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
    			'contact' => $contact,
	    		'communities' => $communities,
    			'vcards' => $vcards,
    			'community_id' => $community_id,
				'properties' => null, // To be managed in configuration layer
    			'id' => $id,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error
    	));
    	$view->setTerminal(true);
    	return $view;
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
    
    public function deleteAction()
    {
		// Control access
    	$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) return $this->redirect()->toRoute('index');

    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the contact
    	$contact = Vcard::get($id);
    	if (!$contact) $this->redirect()->toRoute('index'); // Not allowed

	    $csrfForm = new CsrfForm();
	    $csrfForm->addCsrfElement('csrf');
	    $message = null;
	    $error = null;
	    $request = $this->getRequest();
        if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		
    		if ($csrfForm->isValid()) { // CSRF check
    			 
			   	$contact->delete($request->getPost('update_time'));

				$message = 'OK';
    		}
        }

        $view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
        	'csrfForm' => $csrfForm,
        	'contact' => $contact,
    		'id' => $id,
    		'message' => $message,
        	'error' => $error
        ));
   		$view->setTerminal(true);
   		return $view;
    }
}
