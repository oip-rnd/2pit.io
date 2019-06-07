<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use PpitCore\Model\Account;
use PpitCore\Model\Community;
use PpitCore\Model\Generic;
use PpitCore\Model\Event;
use PpitCore\Model\Interaction;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitCore\ViewHelper\EventPlanningViewHelper;
use PpitCore\ViewHelper\SsmlEventViewHelper;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Form\CsrfForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use ZendDeveloperTools\Collector\EventCollectorInterface;

class EventController extends AbstractActionController
{
	public function indexAction()
    {
    	$context = Context::getCurrent();

    	// Retrieve parameters
    	$type = $this->params()->fromRoute('type', $context->getConfig('event/type')['default']);
		$category = $this->params()->fromRoute('category', 'generic');
    	$app = $this->params()->fromRoute('app');
    	$description = Event::getDescription($type);
    	if (array_key_exists('options', $description) && array_key_exists('internal_identifier', $description['options'])) $internalIdentifier = $description['options']['internal_identifier'];
    	else $internalIdentifier = false;
    	$params = $this->getFilters($this->params(), $description);

    	$personnalize = ($this->params()->fromQuery('personnalize'));
    	$place = Place::get($context->getPlaceId());
    	$community = Community::get($context->getCommunityId());
    		 
		$applicationId = ($app) ? $app : 'synapps';
		$applicationName = $context->localize($context->getConfig('menus/'.$applicationId)['labels']);
//		$currentEntry = 'event'; //$this->params()->fromQuery('entry', 'place');

		return new ViewModel(array(
    			'context' => $context,
    			'internalIdentifier' => $internalIdentifier,
				'type' => $type,
    			'params' => $params,
				'config' => $context->getConfig(),
    			'place' => $place,
    			'community' => $community,
    			'active' => 'application',
    			'app' => $app,
    			'applicationId' => $applicationId,
    			'applicationName' => $applicationName,
    			'category' => $category,
//				'currentEntry' => $currentEntry,
				'personnalize' => $personnalize,
				'content_description' => $description,
		));
    }
    
    public function indexAltAction()
    {
    	$view = $this->indexAction();
    	$view->setTerminal(true);
    	return $view;
    }

    public function calendarAction()
    {
    	$context = Context::getCurrent();
    	 
    	// Retrieve parameters
    	$type = $this->params()->fromRoute('type', $context->getConfig('event/type')['default']);
    	$category = $this->params()->fromRoute('category');
    	$app = $this->params()->fromRoute('app');
    
    	$applicationId = ($app) ? $app : 'synapps';
    	$applicationName = $context->localize($context->getConfig('menus/'.$applicationId)['labels']);

    	$description = Event::getDescription($type);
    	if (array_key_exists('options', $description) && array_key_exists('internal_identifier', $description['options'])) $internalIdentifier = $description['options']['internal_identifier'];

    	$planningMap = $context->getConfig('planningMap/' . $type);
    	if (!$planningMap) $planningMap = $context->getConfig('planningMap/generic');

    	$eventAccountSearchPage = $context->getConfig('core_account/event_account_search/'.$type);
    	if (!$eventAccountSearchPage) $eventAccountSearchPage = $context->getConfig('core_account/event_account_search/generic');
    	 
    	$view = new ViewModel(array(
    		'context' => $context,
    		'type' => $type,
    		'config' => $context->getConfig(),
    		'app' => $app,
    		'applicationId' => $applicationId,
    		'applicationName' => $applicationName,
    		'category' => $category,
			'content_description' => $description,
    		'planningMap' => $planningMap,
			'configProperties' => Account::getConfig('teacher'),
    		'eventAccountSearchPage' => $eventAccountSearchPage,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function getFilters($params, $description)
    {
    	$context = Context::getCurrent(/*$app*/);
    	
    	// Retrieve the query parameters
    	$filters = array();
    	$category = $params->fromRoute('category');
		if ($category) $filters['category'] = $category;
    	
    	foreach ($description['search']['properties'] as $propertyId => $unused) {
    		$property = ($params()->fromQuery($propertyId, null));
    		if ($property) $filters[$propertyId] = $property;
    		$min_property = ($params()->fromQuery('min_'.$propertyId, null));
    		if ($min_property!= null) $filters['min_'.$propertyId] = $min_property;
    		$max_property = ($params()->fromQuery('max_'.$propertyId, null));
    		if ($max_property != null) $filters['max_'.$propertyId] = $max_property;
    	}

    	return $filters;
    }

    public function searchAction()
    {
    	$context = Context::getCurrent(/*$app*/);
    	$type = $this->params()->fromRoute('type', '');
    	$description = Event::getDescription($type);

    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
    			'config' => $context->getconfig(),
				'places' => Place::getList(array()),
				'description' => $description,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function searchAltAction()
    {
    	return $this->searchAction();
    }
    
	private static function build_asc_sorter($key) {
	    return function ($a, $b) use ($key) {
	        return strnatcmp($a->properties[$key], $b->properties[$key]);
	    };
	}

	private static function build_desc_sorter($key) {
		return function ($a, $b) use ($key) {
			return strnatcmp($b->properties[$key], $a->properties[$key]);
		};
	}

    public function getList()
    {
    	$context = Context::getCurrent();
    	
    	// Retrieve parameters
    	$type = $this->params()->fromRoute('type', '');
    	$description = Event::getDescription($type);
    	$params = $this->getFilters($this->params(), $description);
		$limit = $this->params()->fromQuery('limit');
    	$major = ($this->params()->fromQuery('major', 'identifier'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC'));
    	$mask = ($this->params()->fromQuery('mask'));
    	$unmask = ($this->params()->fromQuery('unmask'));
    	$personnalize = ($this->params()->fromQuery('personnalize'));
    	 
    	$contact = Vcard::get($context->getContactId());
		if (!array_key_exists('event/masked'.(($type) ? '/'.$type : ''), $contact->specifications)) {
			$contact->specifications['event/masked'.(($type) ? '/'.$type : '')] = $context->getConfig('event/masked'.(($type) ? '/'.$type : ''));
		}
		$masked = array(); //&$contact->specifications['event/masked'.(($type) ? '/'.$type : '')];

		if ($mask || $unmask) {
			if ($unmask && array_key_exists($unmask, $masked)) unset($masked[$unmask]);
			elseif (!array_key_exists($mask, $masked))  $masked[$mask] = null;
			$contact->update(null);
		}
    	
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

    	// Retrieve the list
    	$dimensions = array();
		if (array_key_exists('dimensions', $description)) {
			foreach ($description['dimensions'] as $dimensionId => $dimension) {
				$cursor = Event::getList($dimension['type'], array(), '+'.$dimension['dimension_key'], null, null, 'search', array());
				$dimension['rows'] = array();
				foreach ($cursor as $row) $dimension['rows'][$row->properties[$dimension['dimension_key']]] = $row;
				$dimensions[$dimensionId] = $dimension;
			}
		}

		// Retrieve major ordering property. If 'database', use the database ordering capacity, else using a memory sort algorythm
		if (array_key_exists('storage', $description['properties'][$major])) {
			$majorStorage = $description['properties'][$major]['storage'];
		}
		else $majorStorage = 'database';
		if ($majorStorage != 'database') {
			$major = 'caption';
			$dir = 'ASC';
		}
		
		$events = Event::getList($type, $params, (($dir == 'DESC') ? '-' : '+').$major, $limit, null, $mode, $dimensions);
		$sum = 0;
		$distribution = array();
		foreach ($events as $event) {
			
			// Join the dimensions
			foreach ($dimensions as $dimension) {
				foreach ($dimension['properties'] as $property => $dimensionProperty) {
					if (array_key_exists($event->properties[$dimension['event_key']], $dimension['rows'])) {
						$event->properties[$property] = $dimension['rows'][$event->properties[$dimension['event_key']]]->properties[$dimensionProperty];
					}
					else $event->properties[$property] = '';
				}
			}
			// Aggregate the indicator values
			$majorSpecification = $description['properties'][$major];
			if ($majorSpecification['type'] == 'specific') $majorSpecification = $context->getConfig($majorSpecification['definition']);
			if ($majorSpecification['type'] == 'number') $sum += $event->properties[$major];
			elseif ($majorSpecification['type'] == 'select') {
				if (array_key_exists($event->properties[$major], $distribution)) $distribution[$event->properties[$major]]++;
				else $distribution[$event->properties[$major]] = 1;
			}
		}
		$average = (count($events)) ? round($sum / count($events), 1) : null;

		// Sort the result in case an in-memory criterion
		if ($majorStorage == 'memory') {
			if ($dir == 'ASC') usort($events, EventController::build_asc_sorter($major));
			else usort($events, EventController::build_desc_sorter($major));
		}

    	// Return the link list
		$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
				'places' => Place::getList(array()),
    			'config' => $context->getconfig(),
    			'events' => $events,
				'event' => Event::instanciate($type),
    			'mode' => $mode,
    			'params' => $params,
    			'major' => $major,
    			'dir' => $dir,
    			'masked' => $masked,
				'distribution' => $distribution,
    			'count' => count($events),
    			'sum' => $sum,
    			'average' => $average,
    			'personnalize' => $personnalize,
        		'description' => $description,
		));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function listAction()
    {
    	return $this->getList();
    }

    public function listAltAction()
    {
    	return $this->getList();
    }
    
    public function listV2Action()
    {
    	return $this->getList();
    }

    /**
     * Action for providing availability for a list of accounts from date to date, with weekly constraints and exception dates
     * The result is in JSON form ans can be used to populate a JS calendar
     */
    public function mapPlanningAction()
    {
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type');
    	$begin = $this->params()->fromQuery('begin');
    	$end = $this->params()->fromQuery('end');
    	return new JsonModel(EventPlanningViewHelper::displayMap($type, $begin, $end));
    }
       
    public function planningAction()
    {
    	$type = $this->params()->fromRoute('type');
    	$description = Event::getDescription($type);
    	$begin = $this->params()->fromQuery('begin');
    	$end = $this->params()->fromQuery('end');
    	$accounts = $this->params()->fromQuery('accounts', '*');
    	if ($accounts) {
			$events = Event::getList($type, ($accounts == '*') ? [] : ['account_id' => $accounts], '-update_time', null);
    	}
    	if ($begin && $end) return new JsonModel(EventPlanningViewHelper::displayPlanning($description, $events, $begin, $end));
    	else return new JsonModel(EventPlanningViewHelper::format($description, $this->getList()->events));
    }
/*
    public function concurrenciesAction()
    {
    	$type = $this->params()->fromRoute('type');
    	$description = Event::getDescription($type);
    	$category = $this->params()->fromRoute('category');
    	$accountIds = explode(',', $this->params()->fromQuery('accounts'));
        $begin = $this->params()->fromQuery('begin');
    	$end = $this->params()->fromQuery('end');
    	$accounts = array();
    	foreach ($accountIds as $account_id) {
    		if ($account_id) $accounts[$account_id] = Account::get($account_id)->getProperties();
    	}
		$events = Event::getList($type, [], '-update_time', null, null);
    	return new JsonModel(EventPlanningViewHelper::displayConcurrencies($description, $category, $accounts, $events, $begin, $end));
    }*/
    
    public function exportAction()
    {
    	$view = $this->getList();
    	$description = Event::getDescription($view->type);

   		include 'public/PHPExcel_1/Classes/PHPExcel.php';
   		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';

		$workbook = new \PHPExcel;
		(new SsmlEventViewHelper)->formatXls($workbook, $view, $description);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);

		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Events.xlsx ');
		ob_end_clean();
		$writer->save('php://output');
    	return $this->response;
    }

    public function distributeAction()
    {
    	$distribution = array();
    	foreach ($this->getList()->events as $event) {
    		if (array_key_exists($event->category, $distribution)) $distribution[$event->category]+= $event->value;
    		else $distribution[$event->category] = $event->value;
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

    public function synchronizeAction()
    {
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type', null);
    	$description = Event::getDescription($type);
    	$interaction = Interaction::instanciate();
    	$data = array();
    	$data['type'] = 'event';
    	$data['format'] = 'application/json';
    	$data['direction'] = 'output';
    	$data['reference'] = 'event_o_'.date('Y-m-d_H:i:s');
    	$params = $this->getFilters($this->params(), $description);
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
    	$context = Context::getCurrent(/*$app*/);
    	$type = $this->params()->fromRoute('type', '');

    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) {
    		$event = Event::get($id, 'id', array());
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

    public function detailAltAction()
    {
    	return $this->detailAction();
    }
    
    public function updateAction()
    {
    	$context = Context::getCurrent();

    	$type = $this->params()->fromRoute('type', null);
    	$category = $this->params()->fromQuery('category');
    	$description = Event::getDescription($type);
    	if (array_key_exists('options', $description) && array_key_exists('internal_identifier', $description['options'])) $internalIdentifier = $description['options']['internal_identifier'];
    	else $internalIdentifier = false;
    	$action = $this->params()->fromQuery('act', null);
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) {
    		$event = Event::get($id);
    		$type = $event->type;
    	}
    	else {
    		$event = Event::instanciate($type);
    		$event->category = $category;
    	}

		// Overwrite the account list as being the group members for type Calendar
    	if ($type == 'calendar') {
    		$group = Account::get($category, 'identifier', 'group', 'type');
    		if ($group) {
	    		$property = &$description['update']['account_id'];
	    		$property['modalities'] = [];
				foreach ($group->members as $account) {
					if ($account->type == 'teacher') {
						$property['modalities'][$account->id] = ['default' => $account->n_fn.' - '.$account->email];
					}
				}
    		}
    	}
    	 
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
		    	$data = [];
		    	$data['exception_dates'] = [];
		    	foreach($description['update'] as $propertyId => $options) {
					if (!array_key_exists('type', $options) || $options['type'] != 'separator') {
						if ($propertyId == 'n_fn') {
							if ($request->getPost($propertyId)) {
								$contact = current(Vcard::getList(null, array('n_fn' => $request->getPost($propertyId))));
								if ($contact) $data['vcard_id'] = $contact->id;
								else $data['vcard_id'] = null;
							}
							else $data['vcard_id'] = null;
						}
						elseif (substr($propertyId, 0, 9) == 'exception') {
							if ($request->getPost($propertyId)) $data['exception_dates'][] = $request->getPost($propertyId);
						}
						else $data[$propertyId] = $request->getPost(($propertyId));
					}
		    	}

		    	if ($event->loadData($data) != 'OK') throw new \Exception('View error');

	    		// Atomically save
	    		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
	    		$connection->beginTransaction();
	    		try {
	    			if (!$event->id) $rc = $event->add();
	    			elseif ($action == 'delete') $rc = $event->delete($request->getPost('event_update_time'));
	    			else {
//	    				$event->status = 'checked';
	    				$rc = $event->update($request->getPost('event_update_time'));
	    			}
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
    	if ($event->id) $event->join();

    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
    			'config' => $context->getconfig(),
    			'internalIdentifier' => $internalIdentifier,
    			'id' => $id,
    			'action' => $action,
    			'event' => $event,
				'places' => Place::getList(array()),
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message,
	    		'description' => $description,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateAltAction()
    {
    	return $this->updateAction();
    }
    
    /**
     * Restfull implementation
     * TODO : authentication + authorization + error description
     */
    public function v1Action()
    {
    	$context = Context::getCurrent();
    	if (!$context->wsAuthenticate($this->getEvent())) {
    		$this->getResponse()->setStatusCode('401');
    		return $this->getResponse();
    	}

    	// Retrieve parameters
    	$type = $this->params()->fromRoute('type', $context->getConfig('event/type')['default']);
    	$place_id = $this->params()->fromRoute('place_id', $context->getPlaceId());
    	$place = Place::get($place_id);
    	$perspective = 'generic';
    	$id = $this->params()->fromRoute('id');

    	// Retrieve the description of the content according to the given type
    	$description = Event::getDescription($type);

		$content = array();

		// Get
		if ($this->request->isGet()) {
			if ($id) {
				
				// Direct access mode
		    	$event = Event::get($id);
				if (!$event) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
		    	$content['data'] = $event->getProperties($type);
			}
			else {

				// List mode
				$filters = array();
				foreach ($description['search']['properties'] as $propertyId => $unused) {
					$property = ($this->params()->fromQuery($propertyId, null));
					if ($property) $filters[$propertyId] = $property;
				}
		    	$limit = $this->params()->fromQuery('limit');
				$order = $this->params()->fromQuery('order', '-update_time');
		    	$page = $this->params()->fromQuery('page');
		    	$per_page = $this->params()->fromQuery('per_page');
		    	$statusDef = $description['properties']['status'];
		    	if (!array_key_exists('status', $filters)) $filters['status'] = implode(',', $statusDef['perspectives'][$perspective]);
		    	$events = Event::getList($type, $filters, $order, $limit);
		    	$content['data'] = array();
		    	foreach ($events as $event) $content['data'][] = $event->getProperties();
			}
		}

		// Put
		elseif ($this->request->isPut()) {
			$event = Event::instanciate($type);
			$data = json_decode($this->request->getContent(), true);

	    	// Database update
	    	$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
	    	$connection->beginTransaction();
	    	try {
				$rc = $event->loadAndAdd($data);
 				if ($rc[0] != '200') {
					$this->getResponse()->setStatusCode($rc[0]);
					echo $rc[1];
					return $this->getResponse();
				}
				$connection->commit();
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
			$event = Event::get($id);
			if (!$event) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}

			$data = json_decode($this->request->getContent(), true);
			$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$event->loadAndUpdate($data);
				if ($rc != '200') {
					$connection->rollback();
				}
				$connection->commit();
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
			$event = Event::get($identifier, 'identifier');
			if (!$event) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}

			// Database update
			$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$event->delete((array_key_exists('update_time', $data)) ? $data['update_time'] : null);
				if ($rc == 'Isolation') {
					$this->getResponse()->setStatusCode('409');
					return $this->getResponse();
				}
				elseif ($rc != 'OK') {
					$this->getResponse()->setStatusCode('500');
					return $this->getResponse();
				}
				$connection->commit();
				$content = $event->getProperties();
			}
			catch (\Exception $e) {
				$connection->rollback();
				$this->getResponse()->setStatusCode('500');
				return $this->getResponse();
			}
		}

		$content['description'] = $description;
		
		// Output
		if ($this->request->getHeader('Content-Type')) $contentType = $this->request->getHeader('Content-Type')->getFieldValue();
		else $contentType = 'application/json';
		header('Content-Type', $contentType);
		if ($contentType == 'text/html') {
			$view = new ViewModel(array(
	    			'context' => $context,
	    			'type' => $type,
					'content' => $content,
			));
			$view->setTerminal(true);
			return $view;
		}
		elseif ($contentType == 'application/json') {
	       	ob_start("ob_gzhandler");
			echo json_encode($content, JSON_PRETTY_PRINT);
			ob_end_flush();
			return $this->response;
		}
    }
}
