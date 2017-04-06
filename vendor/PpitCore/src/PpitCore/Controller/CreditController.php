<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\Account;
use PpitCommitment\ViewHelper\PdfInvoiceViewHelper;
use PpitCommitment\ViewHelper\PpitPDF;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Credit;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Vcard;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Client;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Logger;
use Zend\Log\Writer;

class CreditController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');
		$instance_id = $context->getInstanceId();

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    	));
    }

    public function getFilters($params)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();
    	 
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
    
    	// Retrieve the credits
    	$params = $this->getFilters($this->params());
    	$major = ($this->params()->fromQuery('major', 'type'));
    	$dir = ($this->params()->fromQuery('dir', 'ASC')); 
    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';
       	$credits = Credit::getList($params, $major, $dir, $mode);

    	// Submit the P-Pit get-list message
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$url = $context->getConfig()['ppitCommitment/P-Pit']['commitmentListMessage']['url'].'/'.$context->getInstance()->caption;
    	$client = new Client(
    			$url,
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    	);
    	 
    	$username = $context->getConfig()['ppitCommitment/P-Pit']['commitmentListMessage']['user'];
    	$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
    	$client->setEncType('text/xml');
    	$client->setMethod('GET');
    	$response = $client->send();
    	$invoices = array();
    	$orders = array();
    	foreach (json_decode($response->getContent(), true) as $commitmentData) {
    		$commitment = new Commitment();
    		$commitment->exchangeArray($commitmentData);
    		if ($commitment->status != 'deleted' || $commitment->status != 'canceled') {
    			$orders[] = $commitment;
    		}
    	}
    	 
    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'credits' => $credits,
    			'mode' => $mode,
    			'params' => $params,
    			'major' => $major,
    			'dir' => $dir,
    			'orders' => $orders,
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
		(new SsmlCreditViewHelper)->formatXls($workbook, $view);		
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
    	if ($id) $credit = Credit::get($id);
    	else $credit = Credit::instanciate();
    	
    	$credit->periods = array();
    	foreach ($credit->audit as $event) {
    		if (!array_key_exists($event['period'], $credit->periods)) $credit->periods[$event['period']] = array();
    		$credit->periods[$event['period']][] = $event;
    	}
    	 
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $credit->id,
    			'credit' => $credit,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function downloadInvoiceAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	$id = $this->params()->fromRoute('id');
    	$proforma = $this->params()->fromQuery('proforma', null);
    	 
    	// Submit the P-Pit commitment-get message
    	$safe = $context->getConfig()['ppitUserSettings']['safe'];
    	$url = $context->getConfig()['ppitCommitment/P-Pit']['invoiceGetMessage']['url'].'/'.$id;
    	if ($proforma) $url .= '?proforma=1';
    	$client = new Client(
    			$url,
    			array(
    					'adapter' => 'Zend\Http\Client\Adapter\Curl',
    					'maxredirects' => 0,
    					'timeout'      => 30,
    			)
    			);
    
    	$username = $context->getConfig()['ppitCommitment/P-Pit']['invoiceGetMessage']['user'];
    	$client->setAuth($username, $safe['p-pit'][$username], Client::AUTH_BASIC);
//    	$client->setEncType('text/xml');
    	$client->setMethod('GET');
    	$response = $client->send();
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'content' => $response->getContent(),
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
	public function deleteAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);

    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the organizational unit
		$credit = Credit::get($id);
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		
    		if ($csrfForm->isValid()) {

    			// Atomicity
    			$connection = Credit::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Delete the row
					$return = $Credit->delete($credit->update_time);
					if ($return != 'OK') {
						$connection->rollback();
						$error = $return;
					}
					else {
						$connection->commit();
						$message = $return;
					}
    			}
           	    catch (\Exception $e) {
	    			$connection->rollback();
	    			throw $e;
	    		}
    		}  
    	}
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'credit' => $credit,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }

    public function useAction()
    {
    	$request = $this->getRequest();
		$context = Context::getCurrent();
		$config = $context->getConfig();
    	
    	// Make sure that we are running in a console and the user has not tricked our
    	// application into running this action from a public web server.
    	if (!$request instanceof ConsoleRequest){
    		throw new \RuntimeException('You can only use this action from a console!');
    	}
    	
    	$live = $request->getParam('live', null) || $request->getParam('l', null);
    	$mailTo = $request->getParam('mailTo', null);

    	// Log
		$logText = 'In \\PpitCore\\Controller\CreditController\\useAction {';
    	if ($live) {
	    	$writer = new Writer\Stream('data/log/console.txt');
	    	$logger = new Logger();
	    	$logger->addWriter($writer);
	    	$logger->info($logText);
    	} else print_r($logText."\n");

    	foreach ($config['creditConsumers'] as $creditConsumer) {
    		
    		// Log
    		$logText = '  Calling '.$creditConsumer.' {';
    		if ($live) $logger->info($logText);
    		else print_r($logText."\n");
    		
    		call_user_func($creditConsumer, $live, $mailTo);

    		if ($live) $logger->info('  }');
    		else print_r("  }\n");
    	}
    	if ($live) $logger->info('}');
    	else print_r("}\n");
    }

    public function repairAction()
    {
    	$select = Credit::getTable()->getSelect()->where(array('type' => 'p-pit-communities'));
    	$cursor = Credit::getTable()->transSelectWith($select);
    	foreach ($cursor as $credit) {
    		$audit = array();
    		foreach ($credit->audit as $event) {
    			$new = array();
    			$new['period'] = $event['period'];
    			if ($event['status'] == 'used' && $event['quantity'] > 0) $new['quantity'] = - (int) $event['quantity'];
    			else $new['quantity'] = $event['quantity'];
    			$new['status'] = $event['status'];
    			$new['reference'] = $event['reference'];
    			$new['time'] = $event['time'];
    			$new['n_fn'] = $event['n_fn'];
    			$new['comment'] = $event['comment'];
    			$audit[] = $new;
    		}
    		$credit->audit = $audit;
    		Credit::getTable()->transSave($credit);
    	}
    }
}
