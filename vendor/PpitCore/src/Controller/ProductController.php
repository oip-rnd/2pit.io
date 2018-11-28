<?php
namespace PpitCore\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use PpitCore\Model\Product;
use PpitCore\Model\Vcard;
use ViewHelper\SsmlProductViewHelper;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ProductController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');
    	$place = Place::get($context->getPlaceId());
		
		$type = $this->params()->fromRoute('type', 'p-pit-studies');
		$types = Context::getCurrent()->getConfig('commitment/types')['modalities'];
		
		// Retrieve the type
		$type = $this->params()->fromRoute('type', 0);

		$menu = $context->getConfig('menu');
		
    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
				'type' => $type,
				'types' => $types,
    			'menu' => $menu,
//    			'contact' => $contact,
    	));
    }
    
    public function criteriaAction()
    {
    	$context = Context::getCurrent();

    	$instance_caption = $this->params()->fromRoute('instance_caption', null);
    	$type = $this->params()->fromRoute('type', null);
    	 
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$safeEntry = $safe[$instance_caption];
    	$username = null;
    	$password = null;
    
    	// Check basic authentication
    	if (isset($_SERVER['PHP_AUTH_USER'])) {
    		$username = $_SERVER['PHP_AUTH_USER'];
    		$password = $_SERVER['PHP_AUTH_PW'];
    	} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    		if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
    			list($username, $password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    	}
    	if (!array_key_exists($username, $safeEntry) || $password != $safeEntry[$username]) {
    		 
    		// Write to the log
    		$logger->info('product/criteria/'.$instance_caption.'/'.$type.';401;'.$username);
    		$this->getResponse()->setStatusCode('401');
    		return $this->getResponse();
    	}
    	else {
    		return new JsonModel($context->getConfig('ppitProduct'.(($type) ? '/'.$type : ''))['criteria']);
    	}
    }
    
	public function getFilters($params, $type = null)
	{
		// Retrieve the context
		$context = Context::getCurrent();
		
		// Retrieve the query parameters
		$filters = array();
		
		$brand = ($params()->fromQuery('brand', null));
		if ($brand) $filters['brand'] = $brand;

		$identifier = ($params()->fromQuery('identifier', null));
		if ($identifier) $filters['identifier'] = $identifier;

		$caption = ($params()->fromQuery('caption', null));
		if ($caption) $filters['caption'] = $caption;
		
		$min_price = ($params()->fromQuery('min_price', null));
		if ($min_price) $filters['min_price'] = $min_price;
		
		$max_price = ($params()->fromQuery('max_price', null));
		if ($max_price) $filters['max_price'] = $max_price;

		for ($i = 1; $i < 20; $i++) {
		
			$property = ($params()->fromQuery('property_'.$i, null));
			if ($property) $filters['property_'.$i] = $property;
			$min_property = ($params()->fromQuery('min_property_'.$i, null));
			if ($min_property) $filters['min_property_'.$i] = $min_property;
			$max_property = ($params()->fromQuery('max_property_'.$i, null));
			if ($max_property) $filters['max_property_'.$i] = $max_property;
		}
		
		$def = $context->getConfig('ppitProduct'.(($type) ? '/'.$type : ''));
		if ($def) foreach ($def['criteria'] as $criterion => $unused) {
			$value = ($params()->fromQuery($criterion, null));
			if ($value) $filters[$criterion] = $value;
		}
		
		return $filters;
	}
	
   	public function searchAction()
   	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the type
		$type = $this->params()->fromRoute('type', 0);

   		// Return the link list
   		$view = new ViewModel(array(
   				'context' => $context,
				'config' => $context->getconfig(),
   				'type' => $type,
   		));
		$view->setTerminal(true);
       	return $view;
   	}

   	public function getList()
   	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the order type
		$type = $this->params()->fromRoute('type', 'generic');

		$params = $this->getFilters($this->params(), $type);
		$limit = $this->params()->fromQuery('limit');
		$major = ($this->params()->fromQuery('major', 'caption'));
		$dir = ($this->params()->fromQuery('dir', 'ASC'));

		// Retrieve the list
		$products = Product::getList($type, $params, (($dir == 'DESC') ? '-' : '+').$major, $limit);

   		// Return the link list
   		$view = new ViewModel(array(
   				'context' => $context,
				'config' => $context->getconfig(),
   				'type' => $type,
   				'products' => $products,
   				'mode' => 'search',
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
		$context = Context::getCurrent();

		// Parameters
		$type = $this->params()->fromRoute('type');
		
		$content = [];
   		$filters = array();
		foreach ($context->getConfig('core_product/search/'.$type)['properties'] as $propertyId => $unused) {
			$property = ($this->params()->fromQuery($propertyId, null));
			if ($property) $filters[$propertyId] = $property;
		}
		$limit = $this->params()->fromQuery('limit', 50);
		$order = $this->params()->fromQuery('order', '+caption');
    	$page = $this->params()->fromQuery('page');
    	$per_page = $this->params()->fromQuery('per_page');
    	$products = Product::getList($type, $filters, $order, $limit, null, $page, $per_page);
    	$content['data'] = array();
    	foreach ($products as $product) $content['data'][$product->id] = $product->getProperties();
   		
		include 'public/PHPExcel_1/Classes/PHPExcel.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/CachedObjectStorageFactory.php';
		
       	$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$workbook = new \PHPExcel;
		(new SsmlProductViewHelper)->formatXls($type, $workbook, $content);
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
		
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Products.xlsx ');
		$writer->save('php://output');

		return $this->response;
   	}

   	public function serviceListAction()
   	{
   		$context = Context::getCurrent();
   		$writer = new Writer\Stream('data/log/commitment_try.txt');
   		$logger = new Logger();
   		$logger->addWriter($writer);

   		$instance_caption = $context->getInstance()->caption;
   		$type = 'service';

   		$safe = $context->getConfig()['ppitUserSettings']['safe'];
   		$safeEntry = $safe[$instance_caption];
   		$username = null;
   		$password = null;
   	
   		// Check basic authentication
   		if (isset($_SERVER['PHP_AUTH_USER'])) {
   			$username = $_SERVER['PHP_AUTH_USER'];
   			$password = $_SERVER['PHP_AUTH_PW'];
   		} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
   			if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
   				list($username, $password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
   		}
   		if (!array_key_exists($username, $safeEntry) || $password != $safeEntry[$username]) {
   			 
   			// Write to the log
   			$logger->info('product/serviceList/'.$instance_caption.'/'.$type.';401;'.$username);
   			$this->getResponse()->setStatusCode('401');
   			return $this->getResponse();
   		}
   		else {
   			$result = array();
			$params = $this->getFilters($this->params(), 'service');
//			$params['is_available'] = true; // Temporary
   			foreach (Product::getList('service', $params, 'identifier', 'ASC', 'search') as $product) {
   				$item = array(
   						'identifier' => $product->identifier,
   						'caption' => $product->caption,
   						'description' => $product->description,
   				);
   				foreach ($context->getConfig('ppitProduct/service')['properties'] as $propertyId => $property) {
   					$item[$property['property_name']] = $product->toArray()[$propertyId];
   				}
   				$item['variants'] = $product->variants;
   				$result[] = $item;
   			}
   			return new JsonModel($result);
   		}
   	}
   	
    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	// Retrieve the type
		$type = $this->params()->fromRoute('type', 0);
    	
		$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $product = Product::get($id);
    	else $product = Product::instanciate($type);

    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'id' => $product->id,
    		'product' => $product,
    	));
		$view->setTerminal(true);
		return $view;
    }

    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$type = $this->params()->fromRoute('type', null);

    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $product = Product::get($id);
    	else $product = Product::instanciate();

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
    			$data['type'] = $request->getPost(('product-type'));
		    	$data['brand'] = $request->getPost(('product-brand'));
				$data['identifier'] = $request->getPost(('product-identifier'));
				$data['caption'] = $request->getPost(('product-caption'));
				$data['description'] = $request->getPost(('product-description'));
				$data['is_available'] = $request->getPost(('product-is_available'));
				$variantNumber = $request->getPost('product-variant-number');
				$data['variants'] = array();
				for ($i = 0; $i < $variantNumber; $i++) {
					$variant = array('price' => $request->getPost('product-price_'.$i));
					$data['variants'][] = $variant;
				}
				foreach ($context->getConfig('ppitProduct/update'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
					$data[$propertyId] = $request->getPost('product-'.$propertyId);
				}
				$data['tax_1_share'] = $request->getPost(('product-tax_1_share'))/100;
				$data['tax_2_share'] = $request->getPost(('product-tax_2_share'))/100;
				$data['tax_3_share'] = $request->getPost(('product-tax_3_share'))/100;
				if ($product->loadData($data) != 'OK') throw new \Exception('View error');
	    		// Atomically save
	    		$connection = Product::getTable()->getAdapter()->getDriver()->getConnection();
	    		$connection->beginTransaction();
	    		try {
	    			if (!$product->id) $rc = $product->add();
	    			elseif ($action == 'delete') {
	    				$product->status = 'deleted';
	    				$rc = $product->update($request->getPost('update_time'));
	    			}
	    			else {
    					$rc = $product->update($request->getPost('update_time'));
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
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'type' => $type,
    			'id' => $id,
    			'action' => $action,
    			'product' => $product,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    /**
     * Restfull implementation
     */
    public function v1Action() {
		$context = Context::getCurrent();

		// Authentication
		if (!$context->isAuthenticated() && !$context->wsAuthenticate($this->getEvent())) {
			$this->getResponse()->setStatusCode('401');
			return $this->getResponse();
		}

		// Parameters
		$type = $this->params()->fromRoute('type');
		$id = $this->params()->fromRoute('id');
		$product = Product::get($id);
		$content = [];
		
		// Get
		if ($this->request->isGet()) {
			if ($id) {
				
				// Direct access mode
				if (!$product) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
		    	$content = $product->getProperties();
			}
			else {

				// List mode
				$filters = array();
				foreach ($context->getConfig('core_product/search/'.$type)['properties'] as $propertyId => $unused) {
					$property = ($this->params()->fromQuery($propertyId, null));
					if ($property) $filters[$propertyId] = $property;
				}
				$limit = $this->params()->fromQuery('limit', 50);
				$order = $this->params()->fromQuery('order', '+caption');
		    	$page = $this->params()->fromQuery('page');
		    	$per_page = $this->params()->fromQuery('per_page');
		    	$products = Product::getList($type, $filters, $order, $limit, null, $page, $per_page);
		    	$content['data'] = array();
		    	foreach ($products as $product) $content['data'][$product->id] = $product->getProperties();
			}
		}

		// Put
		elseif ($this->request->isPut()) {
			if ($product) {
				$this->getResponse()->setStatusCode('400');
				echo json_encode(['The product $id already exists']);
				return $this->getResponse();
			}
			$data = json_decode($this->request->getContent(), true);

	    	// Database update
	    	$connection = Config::getTable()->getAdapter()->getDriver()->getConnection();
	    	$connection->beginTransaction();
	    	try {
				$rc = $product->loadAndAdd($data);
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

			if (!$product) {
				$this->getResponse()->setStatusCode('400');
				echo json_encode(["The config $id does not exist"]);
				return $this->getResponse();
			}
			$data = json_decode($this->request->getContent(), true);

			// Database update
			$connection = Config::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$rc = $product->loadAndUpdate($data);
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

			if (!$product) {
				$this->getResponse()->setStatusCode('400');
				echo json_encode(["The config $identifier does not exist"]);
				return $this->getResponse();
			}

			// Database update
			$connection = Config::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$product->delete((array_key_exists('update_time', $data)) ? $data['update_time'] : null);
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

		// Return the view according to the content type
		if ($this->request->getHeader('Content-Type')) $contentType = $this->request->getHeader('Content-Type')->getFieldValue();
		else $contentType = 'application/json';
		if ($contentType == 'text/html') {
			$view = new ViewModel(array(
				'context' => $context,
				'type' => $type,
				'content' => $content,
			));
			$view->setTerminal(true);
			return $view;
		}
		elseif ($contentType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {

			include 'public/PHPExcel_1/Classes/PHPExcel.php';
			include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
			include 'public/PHPExcel_1/Classes/PHPExcel/CachedObjectStorageFactory.php';
			
	       	$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
			$cacheSettings = array( ' memoryCacheSize ' => '8MB');
			\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

			$workbook = new \PHPExcel;
			(new SsmlProductViewHelper)->formatXls($type, $workbook, $content);
			$writer = new \PHPExcel_Writer_Excel2007($workbook);
			
			header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition:inline;filename=P-Pit_Products.xlsx ');
			$writer->save('php://output');

			return $this->response;
		}
		elseif ($contentType == 'application/json') {
	       	ob_start("ob_gzhandler");
			echo json_encode($content, JSON_PRETTY_PRINT);
			ob_end_flush();
			return $this->response;
		}
	}
}
