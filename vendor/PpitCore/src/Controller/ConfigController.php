<?php
namespace PpitCore\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ConfigController extends AbstractActionController
{	
	public function v1Action() {
		$context = Context::getCurrent();

		// Authentication
		if (!$context->isAuthenticated() /*&& !$context->wsAuthenticate($this->getEvent())*/) {
			$this->getResponse()->setStatusCode('401');
			return $this->getResponse();
		}

		// Parameters
		$identifier = $this->params()->fromRoute('identifier');
		$config = Config::get($identifier, 'identifier');
		$content = [];
		
		// Get
		if ($this->request->isGet()) {
			if ($identifier) {
				
				// Direct access mode
				if (!$config) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
		    	$content = $config->content();
			}
			else {

				// List mode
			}
		}

		// Put
		elseif ($this->request->isPut()) {
					
			if ($context->getConfig('specificationMode') == 'config') {
				$this->getResponse()->setStatusCode('400');
				echo json_encode(['Config update is only available in "database" not "config" mode']);
				return $this->getResponse();
			}
			
			if ($config) {
				$this->getResponse()->setStatusCode('400');
				echo json_encode(['The config $identifier already exists']);
				return $this->getResponse();
			}
			$data = json_decode($this->request->getContent(), true);

	    	// Database update
	    	$connection = Config::getTable()->getAdapter()->getDriver()->getConnection();
	    	$connection->beginTransaction();
	    	try {
				$rc = Config::loadAndAdd($data);
				if ($rc[0] != '200') {
					$this->getResponse()->setStatusCode($rc[0]);
					echo $rc[1];
					return $this->getResponse();
				}
				$connection->commit();
	    	}
			catch (\Exception $e) {
				$connection->rollback();
				$this->getResponse()->setStatusCode('500');
				echo json_encode([$rc]);
				return $this->getResponse();
			}
		}

		// Post
		elseif ($this->request->isPost()) {
			
			if ($context->getConfig('specificationMode') == 'config') {
				$this->getResponse()->setStatusCode('400');
				echo json_encode(['Config update is only available in "database" not "config" mode']);
				return $this->getResponse();
			}
				
			if (!$config) {
				$this->getResponse()->setStatusCode('400');
				echo json_encode(["The config $identifier does not exist"]);
				return $this->getResponse();
			}
			$data = json_decode($this->request->getContent(), true);

			// Database update
			$connection = Config::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$rc = $config->loadAndUpdate($data);
				if ($rc[0] != '200') {
					$this->getResponse()->setStatusCode($rc[0]);
					echo json_encode([$rc]);
					return $this->getResponse();
				}
				$connection->commit();
			}
			catch (\Exception $e) {
				$connection->rollback();
				$this->getResponse()->setStatusCode('500');
				echo json_encode(['Unknown exception']);
				return $this->getResponse();
			}
		}
		
		// Delete
		elseif ($this->request->isDelete()) {

			if ($context->getConfig('specificationMode') == 'config') {
				$this->getResponse()->setStatusCode('400');
				echo json_encode(['Config update is only available in "database" not "config" mode']);
				return $this->getResponse();
			}

			if (!$config) {
				$this->getResponse()->setStatusCode('400');
				echo json_encode(["The config $identifier does not exist"]);
				return $this->getResponse();
			}

			// Database update
			$connection = Config::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$config->delete((array_key_exists('update_time', $data)) ? $data['update_time'] : null);
				if ($rc == 'Isolation') {
					$this->getResponse()->setStatusCode('409');
					echo json_encode([$rc]);
					return $this->getResponse();
				}
				elseif ($rc != 'OK') {
					$this->getResponse()->setStatusCode('500');
					echo json_encode([$rc]);
					return $this->getResponse();
				}
				$connection->commit();
			}
			catch (\Exception $e) {
				$connection->rollback();
				$this->getResponse()->setStatusCode('500');
				echo json_encode(['Unknown exception']);
				return $this->getResponse();
			}
		}

		// Description
		$content['description'] = array();

		// Output
       	ob_start("ob_gzhandler");
		echo json_encode($content, JSON_PRETTY_PRINT);
		ob_end_flush();
		return $this->response;
	}

	public function uploadAction()
	{
		$context = Context::getCurrent();

		// Authentication
		if (!$context->isAuthenticated() /*&& !$context->wsAuthenticate($this->getEvent())*/) {
			$this->getResponse()->setStatusCode('401');
			return $this->getResponse();
		}

		// Parameters
		$identifier = $this->params()->fromRoute('identifier');
		$config = Config::get($identifier, 'identifier');
		
		$path = $this->request->getFiles()->toArray()['path'];
		$account->contact_1->savePhoto($photoPath);
		echo $account->contact_1->photo_link_id;

		return $this->response;
	}
	
	public function serializeAction() {
		$context = Context::getCurrent();
		
		// Parameters
		$place_identifier = $this->params()->fromRoute('place_identifier');

		$place = Place::get($place_identifier, 'identifier');
		$category = $this->params()->fromQuery('category');
		$data['content'] = $context->getConfig($category.'/'.$place_identifier);
		echo json_encode($data['content'], JSON_PRETTY_PRINT);
/*		$config = Config::get($place_identifier.'_'.$category, 'identifier', $place->id);
		if (!$config) $config = Config::instanciate($place->id, $place_identifier.'_'.$category);
		print_r($config->loadAndUpdate($data));*/
		return $this->response;
	}
}
