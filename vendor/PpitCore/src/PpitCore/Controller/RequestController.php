<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use PpitCore\Model\Generic;
use PpitCore\Model\Place;
use PpitCore\Model\Request;
use PpitCore\Model\Vcard;
use PpitCore\ViewHelper\SsmlRequestViewHelper;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Form\CsrfForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use ZendDeveloperTools\Collector\EventCollectorInterface;

class RequestController extends AbstractActionController
{
	public function getConfigProperties($type) {
		$context = Context::getCurrent();
		$properties = array();
		foreach($context->getConfig('request'.(($type) ? '/'.$type : ''))['properties'] as $propertyId => $property) {
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			$properties[$propertyId] = $property;
		}
		return $properties;
	}
	
	public function indexAction()
	{
		// Context
		$context = Context::getCurrent();
		$contentType = $this->request->getHeader('content-type');
		if (!$contentType) $contentType = 'text/html';
		else $contentType =$contentType->getFieldValue();
		$place = Place::get($context->getPlaceId());
		$applicationId = 'synapps';
		$app = $this->params()->fromRoute('app');
		
		// Parameters
		$type = $this->params()->fromRoute('type');
		$personnalize = ($this->params()->fromQuery('personnalize'));

		$configProperties = $this->getConfigProperties($type);
		if ($contentType == 'text/html') {
			return new ViewModel(array(
	    			'context' => $context,
	    			'type' => $type,
	    			'place' => $place,
	    			'applicationId' => $applicationId,
	    			'app' => $app,
					'personnalize' => $personnalize,
					'page' => $context->getConfig('request/index'.(($type) ? '/'.$type : '')),
					'searchPage' => $context->getConfig('request/search'.(($type) ? '/'.$type : '')),
					'listPage' => $context->getConfig('request/list'.(($type) ? '/'.$type : '')),
					'detailPage' => $context->getConfig('request/detail'.(($type) ? '/'.$type : '')),
					'updatePage' => $context->getConfig('request/update'.(($type) ? '/'.$type : '')),
					'configProperties' => $configProperties,
			));
		}
		elseif ($contentType == 'application/json') {
			return json_encode($configProperties);
		}
    }

    public function getFilters($type, $configProperties)
    {
    	$context = Context::getCurrent();
    	$filters = array();
    	foreach ($context->getConfig('request/search'.(($type) ? '/'.$type : ''))['properties'] as $propertyId => $unused) {
    		$value = ($this->params()->fromQuery($propertyId, null));
    		if ($value) $filters[$propertyId] = array('value' => $value, 'definition' => $configProperties[$propertyId]);
    		$value = ($this->params()->fromQuery('min_'.$propertyId, null));
    		if ($value != null) $filters['min_'.$propertyId] = array('value' => $value, 'definition' => $configProperties[$propertyId]);
    		$value = ($this->params()->fromQuery('max_'.$propertyId, null));
    		if ($value != null) $filters['max_'.$propertyId] = array('value' => $value, 'definition' => $configProperties[$propertyId]);
    	}
    	return $filters;
    }

    public function searchAction()
    {
    	$context = Context::getCurrent();
		$contentType = $this->request->getHeader('content-type');
		if (!$contentType) $contentType = 'text/html';
		else $contentType =$contentType->getFieldValue();
    	$type = $this->params()->fromRoute('type', '');

		$configProperties = $this->getConfigProperties($type);
		if ($contentType == 'text/html') {
	    	$view = new ViewModel(array(
	    			'context' => $context,
	    			'type' => $type,
					'places' => Place::getList(array()),
					'page' => $context->getConfig('request/search'.(($type) ? '/'.$type : '')),
	    			'configProperties' => $configProperties,
	    	));
	    	$view->setTerminal(true);
	    	return $view;
		}
    	elseif ($contentType == 'application/json') {
			return json_encode($configProperties);
		}
    }

    public function listAction()
    {
    	$context = Context::getCurrent();
		$contentType = $this->request->getHeader('content-type');
		if (!$contentType) $contentType = 'text/html';
		else $contentType =$contentType->getFieldValue();
    	
    	// Parameters
    	$type = $this->params()->fromRoute('type', '');
    	$params = $this->getFilters($type, $this->getConfigProperties($type));
    	$major = ($this->params()->fromQuery('major', 'identifier'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC'));
    	$mask = ($this->params()->fromQuery('mask'));
    	$unmask = ($this->params()->fromQuery('unmask'));
    	$personnalize = ($this->params()->fromQuery('personnalize'));

    	$contact = Vcard::get($context->getContactId());
		if (!array_key_exists('event/masked'.(($type) ? '/'.$type : ''), $contact->specifications)) {
			$contact->specifications['event/masked'.(($type) ? '/'.$type : '')] = $context->getConfig('event/masked'.(($type) ? '/'.$type : ''));
		}
		$masked = &$contact->specifications['request/masked'.(($type) ? '/'.$type : '')];
		if ($mask || $unmask) {
			if ($unmask && array_key_exists($unmask, $masked)) unset($masked[$unmask]);
			elseif (!array_key_exists($mask, $masked))  $masked[$mask] = null;
			$contact->update(null);
		}

		if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

    	// Retrieve the list
		$configProperties = $this->getConfigProperties($type);
		$requests = Request::getList($type, $params, $major, $dir, $mode);

		$sum = 0;
		$distribution = array();
		foreach ($requests as $request) {

			// Aggregate the indicator values
			$majorProperty = $configProperties[$major];
			if ($majorProperty['type'] == 'number') $sum += $request->properties[$major];
			elseif ($majorProperty['type'] == 'select') {
				if (array_key_exists($request->properties[$major], $distribution)) $distribution[$request->properties[$major]]++;
				else $distribution[$request->properties[$major]] = 1;
			}
		}
		$average = (count($requests)) ? round($sum / count($requests), 1) : null;

		// Return the content
		if ($contentType == 'text/html') {
			$view = new ViewModel(array(
	    			'context' => $context,
	    			'type' => $type,
					'places' => Place::getList(array()),
					'requests' => $requests,
	    			'mode' => $mode,
	    			'params' => $params,
	    			'major' => $major,
	    			'dir' => $dir,
	    			'masked' => $masked,
					'distribution' => $distribution,
	    			'count' => count($requests),
	    			'sum' => $sum,
	    			'average' => $average,
	    			'personnalize' => $personnalize,
					'page' => $context->getConfig('request/list'.(($type) ? '/'.$type : '')),
					'configProperties' => $configProperties,
			));
	    	$view->setTerminal(true);
	    	return $view;
		}
        elseif ($contentType == 'application/json') {
			return json_encode($requests);
		}
        elseif ($contentType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        	include 'public/PHPExcel_1/Classes/PHPExcel.php';
        	include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';

        	$workbook = new \PHPExcel;
        	(new SsmlRequestViewHelper)->formatXls($workbook, $view);
        	$writer = new \PHPExcel_Writer_Excel2007($workbook);
        	
        	header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        	header('Content-Disposition:inline;filename=P-Pit_Requests.xlsx ');
        	$writer->save('php://output');
        }
    }

    public function distributeAction()
    {
    	$distribution = array();
    	foreach ($this->getList()->events as $event) {
    		if (array_key_exists($event->caption, $distribution)) $distribution[$event->caption]+= $event->value;
    		else $distribution[$event->caption] = $event->value;
    	}
//    	$distribution = Generic::getTable()->distribution('core_event', $params, $group);
    	$colors = array('#F7464A', '#46BFBD', '#FDB45C', '#4D5360');
    	$highlights = array('#FF5A5E', '#5AD3D1', '#FFC870', '#616774');
    	$data = array();
    	$i=0;
    	foreach ($distribution as $value => $number) {
    		$data[] = array(
    				'value' => $number,
    				'color' => $colors[$i % 4],
    				'highlight' => $highlights[$i % 4],
    				'label' => $value,
    		);
    		$i++;
    	}
    	return new JsonModel($data);
    }
    
	public function detailAction()
	{
		$context = Context::getCurrent();
		$contentType = $this->request->getHeader('content-type');
		if (!$contentType) $contentType = 'text/html';
		else $contentType =$contentType->getFieldValue();
		
		$type = $this->params()->fromRoute('type', '');
		$id = (int) $this->params()->fromRoute('id', 0);
		if ($id) {
			$request = Request::get($id, 'id', array());
			$type = $request->type;
		}
		else $request = Request::instanciate($type);

		$configProperties = $this->getConfigProperties($type);
		if ($contentType == 'text/html') {
			$view = new ViewModel(array(
					'context' => $context,
					'type' => $type,
					'config' => $context->getconfig(),
					'id' => $id,
					'request' => $request,
					'page' => $context->getConfig('request/detail'.(($type) ? '/'.$type : '')),
		    		'configProperties' => $configProperties,
			));
			$view->setTerminal(true);
	    	return $view;
		}
		elseif ($contentType == 'application/json') {
			return json_encode($configProperties);
		}
	}

    public function updateAction()
    {
    	$context = Context::getCurrent();
		$contentType = $this->request->getHeader('content-type');
		if (!$contentType) $contentType = 'text/html';
		else $contentType = $contentType->getFieldValue();
    	
    	$type = $this->params()->fromRoute('type', null);
    	$action = $this->params()->fromQuery('act', null);
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $request = Request::get($id);
    	else $request = Request::instanciate($type);

    	$page = $context->getConfig('request/update'.(($type) ? '/'.$type : ''));

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	if ($action == 'delete') $message = 'confirm-delete';
    	elseif ($action) $message =  'confirm-update';
    	else $message = null;
    	if ($this->getRequest()->isPost()) {
    		$message = null;
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($this->getRequest()->getPost());

    		if ($csrfForm->isValid()) { // CSRF check
	
    			// Load the input data
		    	$data = array();
		    	foreach($page['properties'] as $propertyId => $options) {
					if (!array_key_exists('type', $options) || $options['type'] != 'separator') {
						if ($propertyId == 'n_fn') {
							if ($this->getRequest()->getPost($propertyId)) {
								$contact = current(Vcard::getList(null, array('n_fn' => $this->getRequest()->getPost($propertyId))));
								if ($contact) $data['vcard_id'] = $contact->id;
								else $data['vcard_id'] = null;
							}
							else $data['vcard_id'] = null;
						}
			    		else $data[$propertyId] = $this->getRequest()->getPost(($propertyId));
					}
		    	}

		    	if ($action != 'delete') {
			    	if ($request->loadData($data) != 'OK') throw new \Exception('View error');
		    	}

	    		// Atomically save
	    		$connection = Request::getTable()->getAdapter()->getDriver()->getConnection();
	    		$connection->beginTransaction();
	    		try {
	    			if (!$request->id) $rc = $request->add();
	    			elseif ($action == 'delete') $rc = $request->delete($this->getRequest()->getPost('update_time'));
	    			else $rc = $request->update($this->getRequest()->getPost('update_time'));
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
    	$request->properties = $request->getProperties();
//    	if ($request->id) $request->join();

    	$configProperties = $this->getConfigProperties($type);
    	if ($contentType == 'text/html' || substr($contentType, 0, 19) == 'multipart/form-data') {
	    	$view = new ViewModel(array(
	    			'context' => $context,
	    			'type' => $type,
	    			'config' => $context->getconfig(),
	    			'id' => $id,
	    			'action' => $action,
	    			'request' => $request,
					'places' => Place::getList(array()),
	    			'csrfForm' => $csrfForm,
	    			'error' => $error,
	    			'message' => $message,
	    			'page' => $page,
	    			'configProperties' => $configProperties,
	    	));
    		$view->setTerminal(true);
    		return $view;
    	}
    	elseif ($contentType == 'application/json') {
    		return json_encode($configProperties);
    	}
    }
}
