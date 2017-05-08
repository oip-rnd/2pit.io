<?php
namespace PpitCore\Controller;

use DOMPDFModule\View\Model\PdfModel;
use PpitCommitment\Model\Account;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Document;
use PpitCore\Model\DocumentPart;
use PpitCore\Model\Place;
use PpitCore\Model\Page;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DocumentController extends AbstractActionController
{
    public function homeAction()
    {
    	$context = Context::getCurrent();
    	$place = Place::getTable()->transGet($context->getPlaceId());
    	$request = $this->getRequest();
    	$fqdn = $request->getUri()->getHost();
    	 
		$documents = array();

		$menu = Page::get('menu', 'identifier');
		$page = Page::get('home', 'identifier');
		$clocks = Page::get('clocks', 'identifier');
		foreach ($page->specification['rows'] as $rowId => $row) {
			foreach ($row as $entryId => $entryDef) {
				$documents[$entryId] = array();
				if ($entryDef['type'] == 'list') {
					foreach ($entryDef['content'] as $itemId => $item) {
						$documents[$entryId][$itemId] = Document::getWithPath($item['path'].$item['directory'].'/'.$item['name']);
						$documents[$entryId][$itemId]->retrieveContent();
					}
				}
			}
		}

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'menu' => $menu,
    			'page' => $page,
    			'pageId' => 'news',
    			'clocks' => $clocks,
    			'fqdn' => $fqdn,
				'description' => $page->specification['description'][$context->getLocale()],
    			'documents' => $documents,
    			'robots' => 'index, follow',
    			'homePage' => true,
    	));
    }
    
	public function indexAction()
	{
		$context = Context::getCurrent();
    	$place = Place::getTable()->transGet($context->getPlaceId());
		$parent_id = $this->params()->fromRoute('parent_id', null);
		if (!$parent_id) {
			if ($context->getCommunityId()) {
				$community = Community::get($context->getCommunityId());
				$parent_id = $community->root_document_id;
			}
			else $parent_id = Document::get('0', 'parent_id')->id;
		}
	
		return new ViewModel(array(
				'context' => $context,
				'config' => $context->getConfig(),
				'place' => $place,
				'parent_id' => $parent_id,
		));
	}
	
	public function getFilters($params)
	{
		$context = Context::getCurrent();
		 
		// Retrieve the query parameters
		$filters = array();
	
		foreach ($context->getConfig('document/search')['main'] as $propertyId => $rendering) {
	
			$property = ($params()->fromQuery($propertyId, null));
			if ($property) $filters[$propertyId] = $property;
			$min_property = ($params()->fromQuery('min_'.$propertyId, null));
			if ($min_property) $filters['min_'.$propertyId] = $min_property;
			$max_property = ($params()->fromQuery('max_'.$propertyId, null));
			if ($max_property) $filters['max_'.$propertyId] = $max_property;
		}
	
		foreach ($context->getConfig('document/search')['more'] as $propertyId => $rendering) {
			 
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

		$parent_id = $this->params()->fromRoute('parent_id', null);
		
		// Return the link list
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'parent_id' => $parent_id,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function getList()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		if ($context->getCommunityId()) {
			$community = Community::get($context->getCommunityId());
			$root_id = $community->root_document_id;
		}
		else $root_id = Document::get('0', 'parent_id')->id;

		$parent_id = (int) $this->params()->fromRoute('parent_id', null);
		$parent = Document::get($parent_id, 'id', $root_id);
		
		$params = $this->getFilters($this->params());
		$major = $this->params()->fromQuery('major', 'name');
    	$dir = $this->params()->fromQuery('dir', 'ASC');
		$documents = Document::getList($parent, $params, $major, $dir);
	
		if (count($params) == 0) $mode = 'todo'; else $mode = 'search';
	
		// Return the link list
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'documents' => $documents,
				'parent' => $parent,
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
		(new SsmlJournalViewHelper)->formatXls($workbook, $view);
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
	
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=Fichier.xlsx ');
		$writer->save('php://output');
	}

	public function detailAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		$id = (int) $this->params()->fromRoute('id', 0);
		$document = Document::get($id);
		$document->retrieveContent();
		
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'id' => $id,
				'document' => $document,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function dropboxRegisterAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		require_once "vendor/dropbox/dropbox-sdk/lib/Dropbox/autoload.php";
	
		$appInfo = \Dropbox\AppInfo::loadFromJsonFile("config/autoload/dropbox.p-pit.fr.json");
		$webAuth = new \Dropbox\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
		$authorizeUrl = $webAuth->start();
/*		list($accessToken, $dropboxUserId) = $webAuth->finish('9CRvqMLXD40AAAAAAAAAwmg0ezXl70d8AUOxicoG--w');
		echo "Access Token: " . $accessToken . "<br>";
		list($accessToken, $dropboxUserId) = $webAuth->finish('9CRvqMLXD40AAAAAAAAAvqyegio40cQuqFlvC5MxNqY');*/
//		$dbxClient = new \Dropbox\Client('9CRvqMLXD40AAAAAAAAAw01AjFizF3WrpmnYhseJ8RXng1QXiokyfjKqLczV5aQ6', "PHP-Example/1.0");
		
		$view = new ViewModel(array());
		return $view;
	}

	public function addAction()
	{
		// Check the presence of the parent id
		$parent_id = (int) $this->params()->fromRoute('parent_id', 0);
	
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the parent
		$parent = Document::get($parent_id);
		$parent_id = $parent->id;
	
		$document = Document::instanciate($parent_id);
	
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
	
			if ($csrfForm->isValid()) { // CSRF check
				$data = array();
				$data['parent_id'] = $parent_id;
				$data['name'] = $request->getPost('name');
				$data['directory'] = $request->getPost('directory');
				$data['url'] = $request->getPost('url');
				$rc = $document->loadData($data);
				if ($return != 'OK') throw new \Exception('View error');
				$document->save();
				if ($document->type == 'uploaded') $document->saveFile($request->getFiles()->toArray());
				$message = 'OK';
			}
		}
	
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
				'parent_id' => $parent_id,
				'parent' => $parent,
				'document' => $document,
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
}
