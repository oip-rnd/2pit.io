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
	public function uploadAction()
	{
    	// Retrieve the context and parameters
    	$context = Context::getCurrent();
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
					$data['type'] = 'binary';
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
    			'folder' => $folder,
    			'place_id' => $place_id,
	    		'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
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
	public function getAction()
	{
		$context = Context::getCurrent();
	
		// Parameters
		$type = $this->params()->fromRoute('type');
		$id = $this->params()->fromRoute('id');
		$place_id = $this->params()->fromQuery('place_id');
		$folder = $this->params()->fromQuery('folder');
	
		$content = array();
	
		// Get
		if ($id) {

		// Direct access mode
			$document = Document::get($id);
			if (!$document) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}
			$content['data'] = $document->getProperties();
		}
		else {

			// List mode
			$filters = array();

			$place_id = $this->params()->fromQuery('place_id');
			if ($place_id) $filters['place_id'] = $place_id;
			
			$folder = $this->params()->fromQuery('folder');
			if ($folder) $filters['folder'] = $folder;
			
			$order = $this->params()->fromQuery('order', '+name');
			$limit = $this->params()->fromQuery('limit');
			$columns = $this->params()->fromQuery('columns');

			$documents = Document::getList($type, $filters, $order, $limit, $columns);

			$content['data'] = array();
			foreach ($documents as $document) {
				$content['data'][$document->id] = $document->getProperties();
				unset($content['data'][$document->id]['binary_content']);
			}
		}
	
		echo json_encode($content, JSON_PRETTY_PRINT);
		return $this->response;
	}
}
