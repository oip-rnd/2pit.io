<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use PpitCore\Model\App;
use PpitCore\Model\Community;
use PpitCore\Model\Event;
use PpitCore\Model\Generic;
use PpitCore\Model\Interaction;
use PpitCore\Model\Place;
use PpitCore\ViewHelper\SsmlEventViewHelper;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Form\CsrfForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class EventController extends AbstractActionController
{
    public function indexAction()
    {
    	$app = App::get('synapps', 'identifier');
    	$context = Context::getCurrent($app);
    	$type = $this->params()->fromRoute('type', '');
    	$place = Place::getTable()->transGet($context->getPlaceId());
    	$community = Community::get($context->getCommunityId());
    		 
		$applicationId = 'p-pit-synapps';
		$applicationName = 'SynApps by P-Pit';
		$currentEntry = $this->params()->fromQuery('entry', 'place');

		$distribution = Generic::getTable()->distribution('core_event', array(), 'category');
				
    	return new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'community' => $community,
    			'active' => 'application',
    			'app' => $app,
    			'applicationId' => $applicationId,
    			'applicationName' => $applicationName,
    			'currentEntry' => $currentEntry,
    			'distribution' => $distribution,
    	));
    }

    public function getFilters($params)
    {
    	$app = App::get('synapps', 'identifier');
    	$context = Context::getCurrent($app);
    	$type = $this->params()->fromRoute('type', '');
		
    	// Retrieve the query parameters
    	$filters = array();

    	foreach ($context->getConfig('event/search'.(($type) ? '/'.$type : ''))['main'] as $propertyId => $rendering) {
    
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
    	$app = App::get('synapps', 'identifier');
    	$context = Context::getCurrent($app);
    	$type = $this->params()->fromRoute('type', '');

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
    			'config' => $context->getconfig(),
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function getList()
    {
    	$app = App::get('synapps', 'identifier');
    	$context = Context::getCurrent($app);
    	$type = $this->params()->fromRoute('type', '');

    	$params = $this->getFilters($this->params());
    	$major = ($this->params()->fromQuery('major', 'identifier'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC'));

    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

    	// Retrieve the list
		$specification = $context->getConfig('event'.(($type) ? '/'.$type : ''));
		$dimensions = array();
		if (array_key_exists('dimensions', $specification)) {
			foreach ($specification['dimensions'] as $dimension) {
				$cursor = Event::getList($dimension['type'], array(), $dimension['dimension_key'], 'ASC', 'search');
				$dimension['rows'] = array();
				foreach ($cursor as $row) $dimension['rows'][$row->properties[$dimension['dimension_key']]] = $row;
				$dimensions[] = $dimension;
			}
		}
		$events = Event::getList($type, $params, $major, $dir, $mode);
		foreach ($events as $event) {
			foreach ($dimensions as $dimension) {
				foreach ($dimension['properties'] as $property => $dimensionProperty) $event->properties[$property] = $dimension['rows'][$event->properties[$dimension['event_key']]]->properties[$dimensionProperty];
			}
		}
		
    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
    			'config' => $context->getconfig(),
    			'events' => $events,
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

    public function getAction()
    {
    	return new JsonModel($this->getList()->events);
    }
    
    public function exportAction()
    {
    	$view = $this->getList();

   		include 'public/PHPExcel_1/Classes/PHPExcel.php';
   		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';

		$workbook = new \PHPExcel;
		(new SsmlEventViewHelper)->formatXls($workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);

		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Events.xlsx ');
		$writer->save('php://output');
    	return $this->response;
    }

    public function synchronizeAction()
    {
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type', null);
    	$interaction = Interaction::instanciate();
    	$data = array();
    	$data['type'] = 'event';
    	$data['format'] = 'application/json';
    	$data['direction'] = 'output';
    	$data['reference'] = 'event_o_'.date('Y-m-d_H:i:s');
    	$params = $this->getFilters($this->params());
    	$list = $this->getList()->events;
    	$content = array();
    	$content['action'] = 'synchronize';
    	$content['type'] = $type;
    	$content['params'] = $params;
    	$content['rows'] = array();
    	foreach ($list as $row) {
    		$content['rows'][] = $row->properties;
    	}
    	$data['content'] = json_encode($content, JSON_PRETTY_PRINT);
    	$interaction->loadData($data);
    	$interaction->add();
    	echo 'Done';
    	return $this->response;
    }
    
    public function detailAction()
    {
    	$app = App::get('synapps', 'identifier');
    	$context = Context::getCurrent($app);
    	$type = $this->params()->fromRoute('type', '');

    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) {
    		$event = Event::get($id);
    		$type = $event->type;
    	}
    	else $event = Event::instanciate();

    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'event' => $event,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateAction()
    {
    	$app = App::get('synapps', 'identifier');
    	$context = Context::getCurrent($app);

    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) {
    		$event = Event::get($id);
    		$type = $event->type;
    	}
    	else $event = Event::instanciate();
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
	
    			// Load the input data
		    	$data = array();
		    	foreach($context->getConfig('event/update'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
		    		$data[$propertyId] = $request->getPost(($propertyId));
		    	}
				if ($event->loadData($data) != 'OK') throw new \Exception('View error');

	    		// Atomically save
	    		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
	    		$connection->beginTransaction();
	    		try {
	    			if (!$event->id) $rc = $event->add();
	    			elseif ($action == 'delete') $rc = $event->delete($request->getPost('event_update_time'));
	    			else $rc = $event->update($request->getPost('event_update_time'));
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
    	$event->properties = $event->getProperties();

    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'action' => $action,
    			'event' => $event,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }
}
