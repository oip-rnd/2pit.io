<?php
namespace PpitCore\Controller;

use DOMPDFModule\View\Model\PdfModel;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Document;
use PpitCore\Model\DocumentPart;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DocumentController extends AbstractActionController
{
	public function downloadAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Check the presence of the id parameter for the entity to download
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('index');

		// Retrieve the document and its parent directory
		$document = Document::getTable()->get($id);
	
		$file = 'data/documents/'.$document->id;
	
		$response = new Stream();
		$response->setStream(fopen($file, 'r'));
		$response->setStatusCode(200);
		$response->setStreamName(basename($file));
	
		$headers = new Headers();
		$headers->addHeaders(array(
				'Content-Disposition' => 'attachment; filename="' . $document->name .'"',
				'Content-Type' => $document->mime,
				'Content-Length' => filesize($file)
		));
		$response->setHeaders($headers);
		return $response;
	}

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// retrieve the document identifier as route parameter
    	$identifier = $this->params()->fromRoute('identifier');
print_r($identifier); exit;
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
}
