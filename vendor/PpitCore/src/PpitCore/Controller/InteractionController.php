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
use PpitCore\Model\Instance;
use PpitCore\Model\Interaction;
use PpitCore\Model\Place;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\ViewHelper\SsmlInteractionViewHelper;
use Zend\Http\Client;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class InteractionController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
    	$place = Place::getTable()->transGet($context->getPlaceId());
    	
		$applicationId = 'p-pit-synapps';
		$applicationName = 'SynApps by P-Pit';
		$currentEntry = $this->params()->fromQuery('entry', 'place');

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'active' => 'application',
    			'applicationId' => $applicationId,
    			'applicationName' => $applicationName,
    			'currentEntry' => $currentEntry,
    	));
    }

    public function getFilters($params)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();

    	foreach ($context->getConfig('interaction/search')['main'] as $propertyId => $rendering) {
    
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
    	$major = ($this->params()->fromQuery('major', 'update_time'));
    	$dir = ($this->params()->fromQuery('dir', 'DESC'));

    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

    	// Retrieve the list
    	$interactions = Interaction::getList($params, $major, $dir, $mode);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'interactions' => $interactions,
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

   		include 'public/PHPExcel_1/Classes/PHPExcel.php';
   		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';

		$workbook = new \PHPExcel;
		(new SsmlInteractionViewHelper)->formatXls($workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);

		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Interactions.xlsx ');
		$writer->save('php://output');
    	return $this->response;
    }

    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $interaction = Interaction::get($id);
    	else $interaction = Interaction::instanciate();

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'interaction' => $interaction,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $interaction = Interaction::get($id);
    	else $interaction = Interaction::instanciate();
    	$action = $this->params()->fromRoute('act', null);
    	 
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	if ($action == 'delete') $message = 'confirm-delete';
    	elseif ($action) $message =  'confirm-update';
    	else $message = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$message = null;
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check
    			if ($action == 'process') {
    				if ($interaction->direction == 'output') $rc = $intraction->processOutput();
    				else $rc = $interaction->processInput();
    				if ($rc != 'OK') $error = $rc;
    				else $message = $rc;
    			}
    			else {
	
	    			// Load the input data
			    	$data = array();
			    	foreach($context->getConfig('interaction/update') as $propertyId => $unused) {
			    		$data[$propertyId] = $request->getPost(($propertyId));
			    	}
					if ($interaction->loadData($data) != 'OK') throw new \Exception('View error');
					$files = $request->getFiles()->toArray();
					if ($files) {
						$file = current($files);
					    if ($file['size'] > $context->getConfig()['maxUploadSize']) $error = 'Size';
				    	else {
				    		$this->content = file_get_contents($file['tmp_name']);
						}
						$interaction->getFileContent($file);
					}

		    		// Atomically save
		    		$connection = Interaction::getTable()->getAdapter()->getDriver()->getConnection();
		    		$connection->beginTransaction();
		    		try {
		    			if (!$interaction->id) $rc = $interaction->add();
		    			elseif ($action == 'delete') $rc = $interaction->delete($request->getPost('interaction_update_time'));
		    			elseif ($action == 'update') $rc = $interaction->update($request->getPost('interaction_update_time'));
		    			if ($rc != 'OK') $error = $rc;
		    			if ($error) $connection->rollback();
		    			else {
		    				$connection->commit();
		    				$message = 'OK';
		    			}
		    		}
		    		catch (\Exception $e) {
		    			$connection->rollback();
		    			throw $e;
		    		}
		    		$action = null;
	    		}
    		}
	    }
    	$interaction->properties = $interaction->getProperties();
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'action' => $action,
    			'interaction' => $interaction,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function receiveAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Initialize the logger
    	$writer = new \Zend\Log\Writer\Stream('data/log/commitment-message.txt');
    	$logger = new \Zend\Log\Logger();
    	$logger->addWriter($writer);
    	
    	$type = $this->params()->fromRoute('type', 'generic');
    	$reference = $this->params()->fromQuery('reference', 'generic');
    	if (!$context->wsAuthenticate($this->getEvent())) {
    		$this->getResponse()->setStatusCode('401');
    		return $this->getResponse();
    	}
    	else {
    		// Log the received message
			$interaction = Interaction::instanciate();
			$data = array();
			$data['type'] = $type;
			$data['format'] = $this->getRequest()->getHeaders()->get('content-type')->getFieldValue();
			$data['direction'] = 'input';
			$data['reference'] = $reference;
			$data['content'] = utf8_encode($this->getRequest()->getContent());
			$rc = $interaction->loadData($data);
			if ($rc != 'OK') {
		    	$logger->info('interaction/receive'.';422;'.$rc.';');
				$this->getResponse()->setStatusCode('422');
		    	return $this->getResponse();
			}

			// Atomically load and save the interaction
			$connection = Interaction::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
		    	$rc = $interaction->add();
	
		    	if ($rc != 'OK') {
		    		$connection->rollback();
	
		    		// Update the message with any return code from the account insert or update
		    		$message->http_status = $rc;
		    		$message->update($message->update_time);
		    			
		    		return $this->getResponse();
		    	}

				$interaction->http_status = '200';
				$interaction->update(null);
				$connection->commit();
		    	$this->getResponse()->setStatusCode('200');
	    	}
    	   	catch (\Exception $e) {
    			$connection->rollback();
	    			
    			// Write to the log
    			$logger->info('interaction/receive/'.';500;'.$e->getMessage().';');
    			$this->getResponse()->setStatusCode('500');
    			return $this->getResponse();
    	    }

			// Atomically process the interaction
			$connection = Interaction::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$rc = $interaction->processInput();
				if ($rc != 'OK') {
		    		$connection->rollback();
	
		    		// Update the message with any return code from the account insert or update
		    		$interaction->http_status = $rc;
		    		$interaction->update($interaction->update_time);
		    			
		    		return $this->getResponse();
		    	}
		    	$connection->commit();
			}
    	   	catch (\Exception $e) {
    			$connection->rollback();
	    			
    			// Write to the log
    			$logger->info('interaction/receive/'.';500;'.$e->getMessage().';');
    			$this->getResponse()->setStatusCode('500');
    			return $this->getResponse();
    	    }
    	    return $this->getResponse();
		}
    }
}
