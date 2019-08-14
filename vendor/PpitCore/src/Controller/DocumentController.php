<?php
namespace PpitCore\Controller;

use DOMPDFModule\View\Model\PdfModel;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Document;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DocumentController extends AbstractActionController
{
    public function searchAction()
    {
    	
    }
    
	public function listAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	$content = [
    		'context' => $context,
    	];
    		 
    	// Get the list and description as content 
		$content = array_merge($content, $this->v1Action());

    	// Return the link list
    	$view = new ViewModel($content);
    	$view->setTerminal(true);
    	return $view;
    }

    public function exportAction()
    {
    	
    }
    
	public function uploadAction()
	{
    	// Retrieve the context and parameters
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type');
    	$folder = $this->params()->fromRoute('folder');
    	$place_id = $this->params()->fromQuery('place_id');
    
    	$document = Document::instanciate();
    	
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    
    		if ($csrfForm->isValid()) { // CSRF check
				$files = $request->getFiles()->toArray();
				if ($files) {
					$file = current($files);
					$data['type'] = $type;
					$data['place_id'] = $place_id;
					$data['folder'] = $folder;
					$data['mime'] = $file['type'];
					$data['name'] = $file['name'];
					$data['binary_content'] = $file;
					$document->loadData($data);

	    			// Atomically save
	    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
	    			$connection->beginTransaction();
	    			try {
	    				$rc = $document->add();
	    				if ($rc != 'OK') $error = $rc;
	    				else {
	    					$message = 'OK';
	    					$connection->commit();
	    				}
	    			}
	    			catch (\Exception $e) {
	    				$connection->rollback();
	    				throw $e;
	    			}
				}
    		}
    	}
    	$view = new ViewModel(array(
			'context' => $context,
			'type' => $type,
			'folder' => $folder,
			'place_id' => $place_id,
			'csrfForm' => $csrfForm,
			'error' => $error,
			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
	}

	public function archiveAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		 
		$content = [
			'context' => $context,
		];
		 
		// transmit the 'POST' request that switch the status between 'new' and 'archived' => Todo: Create a new request to send to 'document/v1' as a REST web-service
		$content = array_merge($content, $this->v1Action());
	
		// Return the link list
		$view = new ViewModel($content);
		$view->setTerminal(true);
		return $view;
	}

	public function deleteAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
			
		$content = [
			'context' => $context,
		];
			
		// Transmit the 'DELETE' request => Todo: Create a new request to send to 'document/v1' as a REST web-service
		$content = array_merge($content, $this->v1Action());
	
		// Return the link list
		$view = new ViewModel($content);
		$view->setTerminal(true);
		return $view;
	}
	
	public function downloadAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Check the presence of the id parameter for the entity to download
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('index');

		// Retrieve the document and its parent directory
		$document = Document::get($id);
		
		if ($document->type == 'binary') {
			$this->response->setContent($document->binary_content);
			header('Content-Disposition: attachment; filename="' . $document->name .'"');
			header('Content-Type: ' . $document->mime);
			return $this->response;
		}
		else {
			$file = 'data/documents/'.$document->id;
		
			$response = new Stream();
			$response->setStream(fopen($file, 'r'));
			$response->setStreamName(basename($file));
			$headers = new Headers();
			$headers->addHeaders(array(
				'Content-Disposition' => 'attachment; filename="' . $document->name .'"',
				'Content-Type' => $document->mime,
				'Content-Length' => filesize($file)
			));
			$response->setHeaders($headers);
			$response->setStatusCode(200);
			return $response;
		}
	}

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// retrieve the document identifier as route parameter
    	$identifier = $this->params()->fromRoute('identifier');
    	$document = Document::get($identifier, 'identifier');

    	if (!$document) {
    		$this->getResponse()->setStatusCode('400');
    		echo json_encode(["The config $identifier does not exist"]);
    		return $this->getResponse();
    	}
    	 
    	// Process the post request with CSRF check
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check

    			// Load the input data
		    	$data = json_decode($request->getContent(), true);
    			try {
					$rc = $document->loadAndUpdate($data);
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
    	}
    	 
    	echo json_encode([
    		'identifier' => $identifier,
    		'csrfForm' => $csrfForm,
    	]);
		return $this->getResponse();
    }

	/**
	 * Restfull implementation
	 * TODO : authorization + error description
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
				
		$type = $arguments->fromRoute('type');
		if ($type) $filters['type'] = ['eq', $type];
				
		$folder = $arguments->fromQuery('folder');
		if ($folder) $filters['folder'] = ['eq', $folder];
    	
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

		$type = $this->params()->fromRoute('type');
		$folder = $this->params()->fromRoute('folder');
		$id = $this->params()->fromRoute('id');
		$place_id = $this->params()->fromQuery('place_id');
		$description = Document::getDescription($type);

		$content = [];

		// Get
		if ($requestType == 'GET') {
			if ($id !== null) {
		
				// Direct access mode
				if ($id) $document = Document::get($id);
				else $document = Document::instanciate();
				$content['document'] = $document->getProperties();
			}
			else {
	
				// List mode
				$columns = $this->params()->fromQuery('columns');
				if ($columns) $columns = explode(',', $columns);
				
				$filters = $this->getFilters($this->params(), $description);

				$order = $this->params()->fromQuery('order', 'name');
				if ($order) $order = explode(',', $order);

				$limit = $this->params()->fromQuery('limit');

				$select = Document::getSelect($type, $columns, $filters, $order, $limit);
				$documents = Document::getTable()->selectWith($select);
				$content['arguments'] = ['columns' => $columns, 'filters' => $filters, 'order' => $order, 'limit' => $limit];
				
				$content['documents'] = [];
				foreach ($documents as $document) {
					if (!$columns) $data = $document->getProperties();
					else {
						$documentProperties = $document->getProperties();
						$data = [];
						foreach ($columns as $column) $data[$column] = $documentProperties[$column];
					}
					$content['documents'][$document->id] = $data;
					$content['documents'][$document->id]['is_deletable'] = $document->isDeletable();
				}
			}
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
