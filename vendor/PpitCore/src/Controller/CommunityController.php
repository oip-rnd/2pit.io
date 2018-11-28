<?php

namespace PpitCore\Controller;

use PpitContact\Model\ContactMessage;
use PpitCore\Model\Community;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Event;
use PpitCore\Model\Instance;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitCore\Form\CsrfForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CommunityController extends AbstractActionController
{
    public function indexAction()
    {    	
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$place = Place::get($context->getPlaceId());

    	$view = new ViewModel(array(
    			'context' => $context,
				'config' => $context->getconfig(),
    			'place' => $place,
    	));
   		$view->setTerminal(true);
   		return $view;
    }

    public function listAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Filter
		$filter = array();
    	$name = $this->params()->fromQuery('name', NULL);
    	if ($name) $filter['name'] = $name;

		// Order
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'name';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    
		$communities = Community::getList($major, $dir, $filter);
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'name' => $name,
    			'major' => $major,
    			'dir' => $dir,
    			'communities' => $communities,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function dataListAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Filter
    	$filter = array();
    	$identifier = $this->params()->fromQuery('name', NULL);
    	if ($identifier) $filter['name'] = $identifier;
    
    	// Order
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'name';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    
		$communities = Community::getList($major, $dir, $filter);
       	$data = array();
    	foreach ($communities as $community) {
    		$data[] = array(
    				'id' => $community->id,
    				'name' => $community->name,
    		);
    	}
    	return new JsonModel(array('data' => $data));
    }
    
    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $community = Community::get($id);
    	else $community = Community::instanciate();
    	
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    
    		if ($csrfForm->isValid()) { // CSRF check
    			$community->loadDataFromRequest($request);

    			// Atomicity
    			$connection = Community::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Add or update
    				if (!$id) $return = $community->add();
    				else $return = $community->update($request->getPost('update_time'));
   					if ($return != 'OK') {
	    				$connection->rollback();
						$error = $return;
					}
					else {
						$connection->commit();
						$message = $return;
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
    			'community' => $community,
       			'id' => $id,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error
    	));
   		$view->setTerminal(true);
   		return $view;
    }
    
    public function sendMessageAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	$community_id = (int) $this->params()->fromRoute('community_id', 0);
//    	if (!$community_id) return $this->redirect()->toRoute('index');
    	$contacts = Vcard::getList($community_id, array());
    	$mail = ContactMessage::instanciate();
    	$mail->type = 'email';
    	$mail->subject = $context->getConfig('core_account/sendMessage')['subject'][$context->getLocale()];
    	$mail->body = $context->getConfig('core_account/sendMessage')['body'][$context->getLocale()];

    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    	
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    	
    		if ($csrfForm->isValid()) {
    			$data = array();
    			$data['type'] = 'email';
    			$data['to'] = array();
    			foreach($contacts as $contact) {
	    			if ($contact->email) {
	    				if ($request->getPost('mask_recipients')) $data['cci'][] = $contact->email;
	    				else $data['to'][$contact->email] = $contact->email;
	    			}
    			}
    			if (!$data['to']) $data['to'][$context->getConfig('core_account/sendMessage')['to']] = $context->getConfig('core_account/sendMessage')['to'];
    			$data['subject'] = $request->getPost('subject');
    			$data['from_mail'] = $context->getConfig('core_account/sendMessage')['from_mail'];
    			$data['from_name'] = $context->getConfig('core_account/sendMessage')['from_name'];
    			$data['body'] = $request->getPost('body');
    			if ($mail->loadData($data) != 'OK') throw new \Exception('View error');

    			// Atomicity
    			$connection = ContactMessage::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
					$rc = $mail->add();
					if ($rc != 'OK') {
						$connection->rollback();
						$error = $rc;
					}
					else {
						$connection->commit();
						$message = 'OK';
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
    			'community_id' => $community_id,
    			'contacts' => $contacts,
    			'mail' => $mail,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
	public function deleteAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the organizational unit
		$community = Community::get($id);
    	
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		
    		if ($csrfForm->isValid()) {

    			// Atomicity
    			$connection = Community::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Delete the row
					$return = $community->delete($request->getPost('update_time'));
					if ($return != 'OK') {
						$connection->rollback();
						$error = $return;
					}
					else {
						$connection->commit();
						$message = $return;
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
    		'community' => $community,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }
}
    