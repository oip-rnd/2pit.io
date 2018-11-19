<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use PpitCore\Model\Community;
use PpitCore\Model\Place;
use PpitCore\ViewHelper\SsmlPlaceViewHelper;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Form\CsrfForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PlaceController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
    	$place = Place::get($context->getPlaceId());
    	$applicationId = 'p-pit-admin';
		$applicationName = 'P-Pit Admin';
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

    	foreach ($context->getConfig('corePlace/search')['main'] as $propertyId => $rendering) {
    
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
    	$major = ($this->params()->fromQuery('major', 'identifier'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC'));

    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

    	// Retrieve the list
    	$places = Place::getList($params, $major, $dir, $mode);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'places' => $places,
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
		(new SsmlPlaceViewHelper)->formatXls($workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);

		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Places.xlsx ');
		$writer->save('php://output');

    	$view = new ViewModel(array());
    	$view->setTerminal(true);
    	return $view;
    }

    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $place = Place::get($id);
    	else $place = Place::instanciate();

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'place' => $place,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $place = Place::get($id);
    	else $place = Place::instanciate();
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

    			// Retrieve the existing community
/*    			$community = null;
    			$community_identifier = $context->getConfig('corePlace/community')['type'].'/'.$place->identifier;
    			$community_name = $context->getConfig('corePlace/community')['name'].' '.$place->caption;
    			if ($action == 'update') $community = Community::get($community_identifier, 'identifier');
    			if (!$community) {
    				$community = Community::instanciate();
    				$community->home_title = $context->getConfig('corePlace/community')['home_title'];
    				$community->home_description = $context->getConfig('corePlace/community')['home_description'];
    			}*/

    			// Load the input data
    			if ($action != 'delete') {
			    	$data = array();
			    	foreach($context->getConfig('corePlace/update') as $propertyId => $unused) {
			    		$data[$propertyId] = $request->getPost(($propertyId));
			    	}
					if ($place->loadData($data) != 'OK') throw new \Exception('View error');
    			}
    			
	    		// Atomically save
	    		$connection = Place::getTable()->getAdapter()->getDriver()->getConnection();
	    		$connection->beginTransaction();
	    		try {
	    			if (!$place->id) $rc = $place->add();
	    			elseif ($action == 'delete') $rc = $place->delete($request->getPost('place_update_time'));
	    			else $rc = $place->update($request->getPost('place_update_time'));
    				if ($rc != 'OK') $error = $rc;
	    			if ($error) $connection->rollback();
	    			else {
/*	    				if ($action != 'delete' && ($community_identifier != $community->identifier || $community_name != $community->name)) {
	    					$community->identifier = $community_identifier;
	    					$community->name = $community_name;
	    					if (!$community->id) $rc = $community->add();
	    					else $rc = $community->update(null);
		    				if ($rc != 'OK') $error = $rc;
			    			if ($error) $connection->rollback();
	    				}*/
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
    	$place->properties = $place->toArray();
    	
    	// The toArray() method, as the ORM for place replaces null values of closing_date by '9999-12-31' for filtering and ordering reason.
    	// In the present context of using the toArray() method, we shouldn't have this transformation so we cancel it. 
    	if (!$place->closing_date) $place->properties['closing_date'] == null;
    	 
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'action' => $action,
    			'place' => $place,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function serializeAction() {
    	$context = Context::getCurrent();
    
    	// Parameters
    	$place_identifier = $this->params()->fromRoute('place_identifier');
    	$place = Place::get($place_identifier, 'identifier');
    	$place->config = $context->getConfig('place_config/'.$place_identifier);
//    	$place->update(null);
    	echo json_encode($place->config, JSON_PRETTY_PRINT);
    	return $this->response;
    }
    
    public function adminAction()
    {
    	$context = Context::getCurrent();
    	echo json_encode(['commitmentTerm/debit' => $context->getConfig('commitmentTerm/debit')], JSON_PRETTY_PRINT);
    	return $this->response;
    }
}
