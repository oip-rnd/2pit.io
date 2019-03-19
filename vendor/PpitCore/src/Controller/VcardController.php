<?php
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
    	}
    
    	// Put
    	elseif ($this->request->isPut()) {
    		$vcard = Vcard::instanciate();
    		$data = json_decode($this->request->getContent(), true);
    
    		// Database update
    		$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
    		$connection->beginTransaction();
    		try {
    			$rc = $vcard->loadAndAdd($data);
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
    		$vcard = Vcard::get($id);
    		if (!$vcard) {
    			$this->getResponse()->setStatusCode('400');
    			return $this->getResponse();
    		}
    
    		$data = json_decode($this->request->getContent(), true);
    			
    		$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
    		$connection->beginTransaction();
    		try {
    			$rc = $vcard->loadAndUpdate($data, null);
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
    		$vcard = Vcard::get($id);
    		if (!$vcard) {
    			$this->getResponse()->setStatusCode('400');
    			return $this->getResponse();
    		}
    			
    		// Database update
    		$connection = Vcard::getTable()->getAdapter()->getDriver()->getConnection();
    		$connection->beginTransaction();
    		try {
    			$rc = $vcard->delete(null);
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
}
