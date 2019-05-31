<?php

namespace PpitCommitment\Controller;

use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\CommitmentMessage;
use PpitCommitment\Model\CommitmentYear;
use PpitCommitment\Model\Term;
use PpitCommitment\ViewHelper\PdfInvoiceViewHelper;
use PpitCommitment\ViewHelper\PpitPDF;
use PpitCommitment\ViewHelper\SsmlTermViewHelper;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Interaction;
use PpitCore\Model\Place;
use PpitCore\ViewHelper\ArrayToSsmlViewHelper;
use PpitCore\ViewHelper\SsmlCrossTableViewHelper;
use PpitCommitment\ViewHelper\SsmlDebitViewHelper;
use PpitCommitment\ViewHelper\XmlDebitViewHelper;
use Zend\Http\Client;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

require_once('vendor/TCPDF-master/tcpdf.php');

class TermController extends AbstractActionController
{
	public function indexAction()
    {
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type', 'generic');
    	$place = Place::get($context->getPlaceId());

		$app = $this->params()->fromRoute('app');
    	$applicationId = 'p-pit-engagements';
		$applicationName = 'P-Pit Engagements';
		$currentEntry = $this->params()->fromQuery('entry', 'term');
		$types = Context::getCurrent()->getConfig('commitment/types')['modalities'];
		$description = Term::getDescription($type);

    	return new ViewModel(array(
			'context' => $context,
			'type' => $type,
			'termProperties' => $description['properties'],
			'config' => $context->getConfig(),
			'place' => $place,
			'app' => $app,
			'active' => 'application',
			'applicationId' => $applicationId,
			'applicationName' => $applicationName,
			'types' => $types,
			'currentEntry' => $currentEntry,
			'termSearchPage' => $description['search'],
			'listPage' => $description['list'],
			'termUpdatePage' => $description['update'],
			'termGroupPage' => $description['groupUpdate'],
		));
	}

    public function getFilters($description, $params)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();

    	foreach ($description['properties'] as $propertyId => $rendering) {
    
    		$property = ($params()->fromQuery($propertyId, null));
    		if ($property !== null) $filters[$propertyId] = $property;
    		$min_property = ($params()->fromQuery('min_'.$propertyId, null));
    		if ($min_property !== null) $filters['min_'.$propertyId] = $min_property;
    		$max_property = ($params()->fromQuery('max_'.$propertyId, null));
    		if ($max_property !== null) $filters['max_'.$propertyId] = $max_property;
    	}
    	return $filters;
    }

    public function searchAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type', 'generic');
    	$description = Term::getDescription($type);
    	$accounts = Account::getList('business', [], '+name', null);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
				'termProperties' => $description['properties'],
    			'config' => $context->getconfig(),
    			'places' => Place::getList(array()),
    			'accounts' => $accounts,
    			'searchPage' => $description['search'],
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function getList()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type');
    	$description = Term::getDescription($type);
    	 
    	$params = $this->getFilters($description, $this->params());
		$limit = $this->params()->fromQuery('limit');
    	$major = $this->params()->fromQuery('major', 'due_date');
    	$dir = $this->params()->fromQuery('dir');
    	$order = ($dir == 'DESC') ? '-' : '+';
    	$order .= ($major);

    	// Retrieve the list
    	$terms = Term::getList($type, $params, $order, $limit);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
				'termProperties' => $description['properties'],
    			'config' => $context->getconfig(),
    			'places' => Place::getList(array()),
    			'terms' => $terms,
    			'mode' => (count($params) == 0) ? 'todo' : 'search',
    			'params' => $params,
    			'major' => $major,
    			'dir' => $dir,
				'listPage' => $description['list'],
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
    	$type = $this->params()->fromRoute('type', 'generic');
    	$description = Term::getDescription($type);
    	 
   		include 'public/PHPExcel_1/Classes/PHPExcel.php';
   		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';

		$workbook = new \PHPExcel;
		(new SsmlTermViewHelper)->formatXls($description, $workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);

		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Terms.xlsx ');
		$writer->save('php://output');

    	$view = new ViewModel(array());
    	$view->setTerminal(true);
    	return $view;
    }

    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type');
    	$description = Term::getDescription($type);
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $term = Term::get($id);
    	else $term = Term::instanciate($type);

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'termProperties' => $description['properties'],
    			'id' => $term->id,
    			'term' => $term,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function generateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$commitment_id = (int) $this->params()->fromRoute('commitment_id', 0);
    	$commitment = Commitment::get($commitment_id);
    	$term = Term::instanciate($commitment->type, $commitment_id);
    	$term->commitment_caption = $commitment->caption;
    	$term->default_means_of_payment = $commitment->default_means_of_payment;
    	$termProperties = Term::getConfig($commitment->type);
    	$termGenerateConfig = Term::getConfigGenerate($commitment->type, $termProperties);
    	$accounts = Account::getList('business', [], '+name', null);
    	$amountToDivide = $commitment->tax_inclusive;
    	$quantityToDivide = 0;
    	$unit_price = 0;
    	 
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
    
    			// Load the input data
    			$invoice_account_id = $request->getPost('term-invoice_account_id');
    			$numberOfTerms = $request->getPost('number_of_terms');
    			$termDate = $request->getPost('first_term_date');
				$year = substr($termDate, 0, 4);
				$month = substr($termDate, 5, 2);
				$day = substr($termDate, 8, 2);
				$periodicity = $request->getPost('periodicity');
    			$sameDayOfMonth = $request->getPost('same_day_of_month');
    			$status = $request->getPost('term-status');
    			$quantityToDivide = $request->getPost('quantityToDivide');
    			$unit_price = $request->getPost('term-unit_price');
    			$amountToDivide = $request->getPost('amountToDivide');
    			$paymentMean = $request->getPost('means_of_payment');
				$toDivide = ($amountToDivide) ? $amountToDivide : $quantityToDivide;
    			$termShare = round($toDivide / $numberOfTerms, 2);
    			$cumulativeAmount = 0;

		    	$data = array();
		    	$data['status'] = $status;
		    	$data['invoice_account_id'] = $invoice_account_id;
		    	$data['means_of_payment'] = $paymentMean;
    			foreach($termGenerateConfig as $propertyId => $property) {
					$property = $termProperties[$propertyId];
					$data[$propertyId] = $request->getPost(('term-'.$propertyId));
    			}

    			// Atomically save
    			$connection = Term::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
		    	try {
    				for ($i = 0; $i < $numberOfTerms; $i++) {
				    	$data['caption'] = 'Echéance '.($i + 1);
    					$data['due_date'] = $termDate;
    					$data['settlement_date'] = $termDate;
    					$data['collection_date'] = $termDate;
    					$month++;
    					if ($month == 13) { $month = 1; $year++; }
    					if ($month == 2 && $day > 28) $forcedDay = 28;
    					else $forcedDay = $day;
    					if ($sameDayOfMonth) $termDate = $year.'-'.sprintf('%02d', $month).'-'.$forcedDay;
    					else $termDate = date('Y-m-d', strtotime($termDate.' + '.$periodicity.' days'));
						if ($i == $numberOfTerms - 1) $termShare = $toDivide - $cumulativeAmount;
						if ($amountToDivide) $data['amount'] = $termShare;
						else {
							$data['quantity'] = $termShare;
							$data['unit_price'] = $unit_price;
						}
    					$cumulativeAmount += $termShare;
    					if ($term->loadData($data, $request->getFiles()->toArray()) != 'OK') throw new \Exception('View error');
    					$term->id = null;
		    			$rc = $term->add();
	    				if ($rc != 'OK') {
	    					$error = $rc;
	    					break;
	    				}
    				}
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
			}
		}

    	$view = new ViewModel(array(
    		'context' => $context,
    		'accounts' => $accounts,
    		'quantityToDivide' => $quantityToDivide,
    		'unit_price' => $unit_price,
    		'amountToDivide' => $amountToDivide,
    		'term' => $term,
    		'termProperties' => $termProperties,
    		'termGenerateConfig' => $termGenerateConfig,
    		'csrfForm' => $csrfForm,
    		'error' => $error,
    		'message' => $message,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type', 'generic');
    	
    	$description = Term::getDescription($type);
    	$configProperties = $description['properties'];
    	$updatePage = $description['update'];

    	$commitment_id = (int) $this->params()->fromRoute('commitment_id', 0);
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $term = Term::get($id);
    	else $term = Term::instanciate($type, $commitment_id);
    	$action = $this->params()->fromRoute('act', null);

    	$documentList = array();
/*    	if (is_array($context->getConfig('ppitDocument')) && array_key_exists('dropbox', $context->getConfig('ppitDocument'))) {
    		$dropbox = $context->getConfig('ppitDocument')['dropbox'];
    		$client = new Client(
	    			'https://api.dropboxapi.com/2/files/list_folder',
	    			array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
	    	);
	    	$client->setEncType('application/json');
	    	$client->setMethod('POST');
	    	$client->getRequest()->getHeaders()->addHeaders(array('Authorization' => 'Bearer '.$dropbox['credential']));
	    	$client->setRawBody(json_encode(array('path' => $dropbox['folders']['settlements'])));
	    	$response = $client->send();
	    	if (array_key_exists('entries', json_decode($response->getBody(), true))) {
		    	foreach (json_decode($response->getBody(), true)['entries'] as $entry) {
		    		$documentList[] = $entry['name'];
		    	}
	    	}
    	}
    	else*/ $dropbox = null;
 
    	$accounts = Account::getList('business', [], '+name', null);

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
		    	foreach($updatePage as $propertyId => $property) {
					$options = $property['options'];
		    		if ((!$options || !array_key_exists('readonly', $options) || !$options['readonly']) && $property['type'] != 'title') {
		    			$property = $configProperties[$propertyId];
		    			$data[$propertyId] = $request->getPost(('term-'.$propertyId));
		    		}
		    	}

				if (!array_key_exists('status', $data) || $data['status']) { // Temporary : Bug 03/2019
					if ($term->loadData($data, $request->getFiles()->toArray()) != 'OK') throw new \Exception('View error');
				}

	    		// Atomically save
	    		$connection = Term::getTable()->getAdapter()->getDriver()->getConnection();
	    		$connection->beginTransaction();
	    		try {
	    			if (!$term->id) $rc = $term->add();
	    			elseif ($action == 'delete') $rc = $term->delete($request->getPost('update_time'));
	    			else $rc = $term->update($request->getPost('term_update_time'));
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
				'termProperties' => $description['properties'],
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'action' => $action,
    			'term' => $term,
    			'accounts' => $accounts,
	    		'dropbox' => $dropbox,
	    		'documentList' => $documentList,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message,
    			'updatePage' => $updatePage,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function groupAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Retrieve the type
    	$type = $this->params()->fromRoute('type');
    	$description = Term::getDescription($type);
    
    	$request = $this->getRequest();
    	if (!$request->isPost()) return $this->redirect()->toRoute('home');
    	$nbTerm = $request->getPost('nb-term');
    
    	$terms = array();
    	for ($i = 0; $i < $nbTerm; $i++) {
    		$term = Term::get($request->getPost('term_'.$i));
    		$terms[] = $term;
    	}
    	$input = $term->properties;
    	$input['status'] = '';
    
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	$request = $this->getRequest();
    	if ($request->getPost('action') == 'update') {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		if ($csrfForm->isValid()) { // CSRF check
    			$data = array();
    			foreach ($description['groupUpdate'] as $propertyId => $options) {
    				if ($request->getPost($propertyId.'_check')) $data[$propertyId] = $request->getPost($propertyId);
    			}
    			foreach ($terms as $term) {
    				// Atomically save
    				$connection = Commitment::getTable()->getAdapter()->getDriver()->getConnection();
    				$connection->beginTransaction();
    				try {
    					if ($term->loadData($data) != 'OK') throw new \Exception('View error');
    					$rc = $term->update(null);
    					if ($rc != 'OK') {
    						$connection->rollback();
    						$error = $rc;
    					}
    					$message = 'OK';
    					$connection->commit();
    				}
    				catch (\Exception $e) {
    					$connection->rollback();
    					throw $e;
    				}
    			}
    		}
    	}
    	elseif ($request->getPost('action') == 'delete') {
    		foreach ($terms as $term) {
    			$rc = $term->delete(null);
    			if ($rc != 'OK') $error = $rc;
    			$message = 'OK';
    		}
    	}
    	$view = new ViewModel(array(
    		'context' => $context,
    		'configProperties' => $description['properties'],
    		'type' => $type,
    		'terms' => $terms,
    		'input' => $input,
    		'places' => Place::getList(array()),
    		'termGroupPage' => $description['groupUpdate'],
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function debitAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
		$cursor = Place::getList([]);
		$places = array();
		foreach ($cursor as $place_id => $place) $places[$place_id] = ['caption' => $place->caption];
    	
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	 
    	$view = new ViewModel(array(
    		'context' => $context,
    		'places' => $places,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function generateDebit($terms, $interaction_id, $sum, $collection_date, $config)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$content = array();
    	$content['GrpHdr'] = array();
    	$content['GrpHdr']['MsgId'] = $interaction_id;
    	$content['GrpHdr']['CreDtTm'] = date('Y-m-d').'T'.date('h:i:s');
    	$content['GrpHdr']['NbOfTxs'] = count($terms);
    	$content['GrpHdr']['CtrlSum'] = $sum;
    	$content['GrpHdr']['InitgPty'] = array();
    	$content['GrpHdr']['InitgPty']['Nm'] = $config['InitgPty/Nm'];
    	$content['PmtInf'] = array();
    	$content['PmtInf']['PmtInfId'] = $config['InitgPty/Nm'].' '.$interaction_id;
    	$content['PmtInf']['PmtMtd'] = 'DD';
    	$content['PmtInf']['NbOfTxs'] = count($terms);
    	$content['PmtInf']['CtrlSum'] = $sum;
    	$content['PmtInf']['PmtTpInf'] = array();
    	$content['PmtInf']['PmtTpInf']['SvcLvl'] = array();
    	$content['PmtInf']['PmtTpInf']['SvcLvl']['Cd'] = 'SEPA';
    	$content['PmtInf']['PmtTpInf']['LclInstrm'] = array();
    	$content['PmtInf']['PmtTpInf']['LclInstrm']['Cd'] = 'CORE';
    	$content['PmtInf']['PmtTpInf']['SeqTp'] = 'RCUR';
    	$content['PmtInf']['ReqdColltnDt'] = ($collection_date) ? $collection_date : date('Y-m-d');
    	$content['PmtInf']['Cdtr'] = array();
    	$content['PmtInf']['Cdtr']['Nm'] = $config['Cdtr/Nm'];
    	$content['PmtInf']['CdtrAcct'] = array();
    	$content['PmtInf']['CdtrAcct']['Id'] = array();
    	$content['PmtInf']['CdtrAcct']['Id']['IBAN'] = $config['CdtrAcct/Id/IBAN'];
    	$content['PmtInf']['CdtrAgt'] = array();
    	$content['PmtInf']['CdtrAgt']['FinInstnId'] = array();
    	$content['PmtInf']['CdtrAgt']['FinInstnId']['Othr'] = array();
    	$content['PmtInf']['CdtrAgt']['FinInstnId']['Othr']['Id'] = 'NOTPROVIDED';
    	$content['PmtInf']['CdtrSchmeId'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['Id'] = $config['CdtrSchmeId/Id/PrvtId/Othr/Id'];
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['SchmeNm'] = array();
    	$content['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['SchmeNm']['Prtry'] = 'SEPA';
    
    	$content['PmtInf']['DrctDbtTxInf'] = array();
    	foreach ($terms as $term) {
    		$row = array();
    		$row['PmtId'] = array();
    		$row['PmtId']['EndToEndId'] = substr(($term['reference']) ? $term['reference'] : $term['commitment_caption'], 0, 35);
    		$row['InstdAmt'] = round($term['amount'], 2);
    		$row['DrctDbtTx'] = array();
    		$row['DrctDbtTx']['MndtRltdInf'] = array();
    		$row['DrctDbtTx']['MndtRltdInf']['MndtId'] = $term['transfer_order_id'];
    		$row['DrctDbtTx']['MndtRltdInf']['DtOfSgntr'] = $term['transfer_order_date'];
    		$row['DbtrAgt'] = array();
    		$row['DbtrAgt']['FinInstnId'] = array();
    		$row['DbtrAgt']['FinInstnId']['Othr'] = array();
    		$row['DbtrAgt']['FinInstnId']['Othr']['Id'] = 'NOTPROVIDED';
    		$row['Dbtr'] = array();
    		$row['Dbtr']['Nm'] = $term['name'];
    		$row['DbtrAcct'] = array();
    		$row['DbtrAcct']['Id'] = array();
    		$row['DbtrAcct']['Id']['IBAN'] = (array_key_exists('bank_identifier', $term)) ? $term['bank_identifier'] : '';
/*			$row['RgltryRptg'] = array();
    		$row['RgltryRptg']['Dtls'] = array();
    		$row['RgltryRptg']['Dtls']['Cd'] = $config['DrctDbtTxInf/RgltryRptg/Dtls/Cd'];*/
    		$content['PmtInf']['DrctDbtTxInf'][] = $row;
    	}
    	return $content;
    }
    
    public function debitXmlAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$place_id = $this->params()->fromRoute('place_id');
    	$place = Place::get($place_id);
    	if ($place && array_key_exists('commitmentTerm/debit', $place->config)) $config = $place->config['commitmentTerm/debit'];
    	else $config = $context->getConfig('commitmentTerm/debit');

    	$termIds = explode(',', $this->params()->fromQuery('terms'));
    	$terms = array();
    	$sum = 0;
    	foreach ($termIds as $term_id) {
    		$term = Term::get($term_id, 'id');
    		$term->collection_date = date('Y-m-d');
    		$term->update(null);
    		$terms[$term->id] = $term->properties;
    		$sum += $term->amount;
    	}

    	// Instanciate an interaction row for storing the XML content in database
    	$interaction = Interaction::instanciate();
    	$interaction->status = 'new';
    	$interaction->type = 'application';
    	$interaction->category = 'debit';
    	$interaction->direction = 'O';
    	$interaction->format = 'text/xml';
    	$interaction->route = 'interaction/download';
    	$interaction->reference = $context->getFormatedName().'_'.date('Y-m-d_H:i:s');
    	$interaction->add();

    	$content = $this->generateDebit($terms, $interaction->id, $sum, date('Y-m-d'), $config);

    	header('Content-Type: application/xml; charset=utf-8');
		header("Content-disposition: attachment; filename=debit-".date('Y-m-d').".xml");
		$xmlContent = XmlDebitViewHelper::convert($content);
    	$interaction->content = $xmlContent;
		$interaction->update(null);
    	echo $xmlContent;
		return $this->response;
    }

    public function debitSsmlAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$place_id = $this->params()->fromRoute('place_id');
    	$place = Place::get($place_id);
    	if ($place && array_key_exists('commitmentTerm/debit', $place->config)) $config = $place->config['commitmentTerm/debit'];
    	else $config = $context->getConfig('commitmentTerm/debit');
    
    	$termIds = explode(',', $this->params()->fromQuery('terms'));
    	$terms = array();
    	$sum = 0;
    	foreach ($termIds as $term_id) {
    		$term = Term::get($term_id, 'id')->properties;
    		$terms[$term['id']] = $term;
    		$sum += $term['amount'];
    	}
    
    	$content = $this->generateDebit($terms, 0, $sum, $term['collection_date'], $config);
		SsmlDebitViewHelper::convert($content);
    	
    	$view = new ViewModel(array());
		$view->setTerminal(true);
		return $view;
    }

    public function checkDepositAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$cursor = Place::getList([]);
    	$places = array();
    	foreach ($cursor as $place_id => $place) $places[$place_id] = ['caption' => $place->caption];
    	 
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    
    	$view = new ViewModel(array(
    		'context' => $context,
    		'places' => $places,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function checkDepositSsmlAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$place_id = $this->params()->fromRoute('place_id');
    	$place = Place::get($place_id);
    	if ($place && array_key_exists('commitmentTerm/checkDeposit', $place->config)) $description = $place->config['commitmentTerm/checkDeposit'];
    	else $description = $context->getConfig('commitmentTerm/checkDeposit');
    
    	$termIds = explode(',', $this->params()->fromQuery('terms'));
    	$terms = array();
		$count = 0;
    	$sum = 0;
    	foreach ($termIds as $term_id) {
    		$term = Term::get($term_id, 'id')->properties;
    		$terms[$term['id']] = $term;
    		$sum += $term['amount'];
    		$count++;
    	}
    	$footer = ['count' => $count, 'sum' => $sum];
    
    	ArrayToSsmlViewHelper::convert($terms, $footer, $description);
    	return $this->response;
    	 
    	$view = new ViewModel(array());
    	$view->setTerminal(true);
    	return $view;
    }
    
	public function deleteAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the term
		$term = Term::get($id);
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
    			$connection = Term::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Delete the row
					$rc = $term->delete($term->update_time);
					if ($rc != 'OK') {
						$connection->rollback();
						$error = $rc;
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
    		'term' => $term,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }

    private function generateInvoice($type, $invoiceAccount, $term, $commitment, $invoice_identifier)
    {
    	$context = Context::getCurrent();
		$description = Term::getDescription($type);
    	
    	$invoice = array();
    	$invoiceSpecs = $context->getConfig('commitmentTerm/invoice/'.$type);
		if (!$invoiceSpecs) $invoiceSpecs = $context->getConfig('commitmentTerm/invoice/generic');
		
		if ($invoiceAccount->type == 'business') $invoice['customer_invoice_name'] = $invoiceAccount->name;
		$invoicingContact = null;
    	if ($invoiceAccount->contact_1_status == 'invoice') $invoicingContact = $invoiceAccount->contact_1;
    	elseif ($invoiceAccount->contact_2_status == 'invoice') $invoicingContact = $invoiceAccount->contact_2;
    	elseif ($invoiceAccount->contact_3_status == 'invoice') $invoicingContact = $invoiceAccount->contact_3;
    	elseif ($invoiceAccount->contact_4_status == 'invoice') $invoicingContact = $invoiceAccount->contact_4;
    	elseif ($invoiceAccount->contact_5_status == 'invoice') $invoicingContact = $invoiceAccount->contact_5;
    		
    	if (!$invoicingContact) {
    		if ($invoiceAccount->contact_1_status == 'main') $invoicingContact = $invoiceAccount->contact_1;
    		elseif ($invoiceAccount->contact_2_status == 'main') $invoicingContact = $invoiceAccount->contact_2;
    		elseif ($invoiceAccount->contact_3_status == 'main') $invoicingContact = $invoiceAccount->contact_3;
    		elseif ($invoiceAccount->contact_4_status == 'main') $invoicingContact = $invoiceAccount->contact_4;
    		elseif ($invoiceAccount->contact_5_status == 'main') $invoicingContact = $invoiceAccount->contact_5;
    	}
    	if (!$invoicingContact) $invoicingContact = $invoiceAccount->contact_1;
    		 
    	$invoice['customer_n_fn'] = '';
    	if ($invoicingContact->n_title || $invoicingContact->n_last || $invoicingContact->n_first) {
    		if ($invoicingContact->n_title) $invoice['customer_n_fn'] .= $invoicingContact->n_title.' ';
    		$invoice['customer_n_fn'] .= $invoicingContact->n_last.' ';
    		$invoice['customer_n_fn'] .= $invoicingContact->n_first;
    	}
    	if ($invoicingContact->adr_street) $invoice['customer_adr_street'] = $invoicingContact->adr_street;
    	if ($invoicingContact->adr_extended) $invoice['customer_adr_extended'] = $invoicingContact->adr_extended;
    	if ($invoicingContact->adr_post_office_box) $invoice['customer_adr_post_office_box'] = $invoicingContact->adr_post_office_box;
    	if ($invoicingContact->adr_zip) $invoice['customer_adr_zip'] = $invoicingContact->adr_zip;
    	if ($invoicingContact->adr_city) $invoice['customer_adr_city'] = $invoicingContact->adr_city;
    	if ($invoicingContact->adr_state) $invoice['customer_adr_state'] = $invoicingContact->adr_state;
    	if ($invoicingContact->adr_street) $invoice['customer_adr_country'] = $invoicingContact->adr_country;
	
    	$invoice['identifier'] = $invoice_identifier;
    	$invoice['date'] = date('Y-m-d');

    	foreach($invoiceSpecs['description'] as $line) {
    		$arguments = array();
    		foreach($line['params'] as $propertyId) {
    			if ($propertyId == 'date') $arguments[] = $context->decodeDate(date('Y-m-d'));
    			else {
    				$property = $description['properties'][$propertyId];
    				if ($propertyId == 'name') $arguments[] = $term->name;
    				elseif ($propertyId == 'caption') $arguments[] = $term->caption;
    				elseif ($property['type'] == 'date') $arguments[] = $context->decodeDate($term->properties[$propertyId]);
    				elseif ($property['type'] == 'number') $arguments[] = $context->formatFloat($term->properties[$propertyId], 2);
    				elseif ($property['type'] == 'select' && array_key_exists($term->properties[$propertyId], $property['modalities'])) $arguments[] = $context->localize($property['modalities'][$term->properties[$propertyId]]);
    				else $arguments[] = $term->properties[$propertyId];
    			}
    		}
    		$value = vsprintf($context->localize($line['right']), $arguments);
    		if ($value) $invoice['description'][]  = array('title' => $context->localize($line['left']), 'value' => $value);
    	}
		$invoice['currency_symbol'] = '€'; // $context->getConfig('commitment')['currencySymbol'];
    	$invoice['tax'] = 'excluding';
    	$line = array();
    	$line['caption'] = $term->caption;
    	$line['tax_rate'] = 0.2;
    	
    	// Compute the tax inclusive amount based on the given quantity and excluded tax unit price
    	if ($term->quantity) {
    		$line['quantity'] = $term->quantity;
    		$line['unit_price'] = $term->unit_price;
    		$line['amount'] = $line['unit_price'] * $line['quantity'];
    		$invoice['lines'] = array($line);
    		$invoice['excluding_tax'] = $line['amount'];
    		$invoice['taxable_1_total'] = $line['amount'];
    		$invoice['tax_1_amount'] = round($line['amount'] * 0.2, 2);
    		$invoice['tax_inclusive'] = $invoice['taxable_1_total'] + $invoice['tax_1_amount'];
    	}
    	
    	// Or compute back the excluded tax amount based on the given tax inclusive amount
    	else {
		    $line['unit_price'] = round($term->amount / (1 + $line['tax_rate']), 2);
	    	$line['quantity'] = 1;
	    	$line['amount'] = $line['unit_price'] * $line['quantity'];
	    	$invoice['lines'] = array($line);
	    	$invoice['excluding_tax'] = $line['amount'];
	    	$invoice['taxable_1_total'] = $line['amount'];
	    	$invoice['tax_1_amount'] = $term->amount - $line['unit_price'];
	    	$invoice['tax_inclusive'] = $term->amount;
    	}
    	
	    if (in_array($term->status, ['expected', 'to_invoice']) && $context->getConfig('commitment/invoice_bank_details')) {
		    $invoice['settled_amount'] = 0;
	    	$invoice['still_due'] = $term->amount;
	    }
	    else {
	    	$invoice['settled_amount'] = $term->amount;
	    	$invoice['still_due'] = 0;
	    }
	    $invoice['tax_mention'] = $context->getConfig('commitment/invoice_tax_mention');
	    if ($context->getConfig('commitment/invoice_bank_details')) {
    		$invoice['bank_details'] = $context->getConfig('commitment/invoice_bank_details');
    		$invoice['footer_mention_1'] = $context->getConfig('commitment/invoice_footer_mention_1');
    		$invoice['footer_mention_2'] = $context->getConfig('commitment/invoice_footer_mention_2');
    		$invoice['footer_mention_3'] = $context->getConfig('commitment/invoice_footer_mention_3');
    	}
    	return $invoice;
    }
    
    public function invoiceAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	$type = $this->params()->fromRoute('type');

    	// Retrieve the term
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('home');
    	$term = Term::get($id);
    	$commitment = Commitment::get($term->commitment_id);
    	if ($term->invoice_account_id) $invoiceAccount = Account::get($term->invoice_account_id);
    	else $invoiceAccount = Account::get($commitment->account_id);

    	$commitmentMessage = CommitmentMessage::instanciate('invoice');
    	
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

    			// Atomically save
    			$connection = CommitmentMessage::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
					if ($term->invoice_id) {
						$commitmentMessage = CommitmentMessage::get($term->invoice_id);
						$invoice_identifier = json_decode($commitmentMessage->content, true)['identifier'];
					}
					else {
	    				$year = CommitmentYear::getcurrent($commitment->place_id);
	    				if (!$year) $year = CommitmentYear::instanciate(date('Y'));
						$mask = $context->getConfig('commitment/invoice_identifier_mask');
						$arguments = array();
						foreach ($mask['params'] as $param) {
							if ($param == 'year') $arguments[] = date('Y');
							elseif ($param == 'month') $arguments[] = date('m');
							elseif ($param == 'counter') $arguments[] = $year->next_value;
						}
						$invoice_identifier = vsprintf($context->localize($mask['format']), $arguments);
	    				$commitmentMessage->status = 'new';
	    				$commitmentMessage->account_id = $invoiceAccount->id;
	    				$commitmentMessage->identifier = $context->getInstance()->fqdn.'_'.$invoice_identifier;
	    				$commitmentMessage->direction = 'O';
	    				$commitmentMessage->format = 'application/json';
	    				$year->increment();
					}
    				$invoice = $this->generateInvoice($type, $invoiceAccount, $term, $commitment, $invoice_identifier);
    				
					$commitmentMessage->content = json_encode($invoice, JSON_PRETTY_PRINT);
			    	if (!$commitmentMessage->id) $rc = $commitmentMessage->add();
				    else $rc = $commitmentMessage->update(null);
			    	
			    	if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
    				}
    				if (!$error) {
    					if (!$term->invoice_id) {
	    					$term->status = 'invoiced';
	    					$term->invoice_id = $commitmentMessage->id;
	    					$term->invoice_identifier = $invoice_identifier;
	    					$rc = $term->update($request->getPost('update_time'));
		    				if ($rc != 'OK') {
		    					$connection->rollback();
		    					$error = $rc;
		    				}
    					}
    				}
    			    if (!$error) {
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
    		'term' => $term,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }

    public function downloadInvoiceAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$id = $this->params()->fromRoute('id', null);
    	if (!$id) return $this->redirect()->toRoute('index');
    	$term = Term::get($id);
    	$commitment = Commitment::get($term->commitment_id);
    	if ($term->invoice_account_id) $invoiceAccount = Account::get($term->invoice_account_id);
    	else $invoiceAccount = Account::get($commitment->account_id);

    	$invoice = $this->generateInvoice($term->type, $invoiceAccount, $term, $commitment, null);

    	// create new PDF document
    	$pdf = new PpitPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    	PdfInvoiceViewHelper::render($pdf, $invoice, $commitment->account->place);
 
    	$content = $pdf->Output('invoice-'.$context->getInstance()->caption.'-'.$term->name.'.pdf', 'I');
    	return $this->response;
    }
    
	public function statusMonthAction()
	{
		// Retrieve context and parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type');
		$min_due_date = $this->params()->fromQuery('min_due_date', date('2018-09-01'));
		$max_due_date = $this->params()->fromQuery('max_due_date', date('2019-09-30'));
		$description = Term::getDescription($type);

		// Retrieve the terms
		$terms = Term::getList($type, ['min_due_date' => $min_due_date, 'max_due_date' => $max_due_date], '+due_date,+name', null);
		$months = array(
			'2018-09' => ['columnId' => 'B', 'labels' => ['default' => 'September', 'fr_FR' => 'Septembre']],
			'2018-10' => ['columnId' => 'C', 'labels' => ['default' => 'October', 'fr_FR' => 'Octobre']],
			'2018-11' => ['columnId' => 'D', 'labels' => ['default' => 'November', 'fr_FR' => 'Novembre']],
			'2018-12' => ['columnId' => 'E', 'labels' => ['default' => 'December', 'fr_FR' => 'Décembre']],
			'2019-01' => ['columnId' => 'F', 'labels' => ['default' => 'January', 'fr_FR' => 'Janvier']],
			'2019-02' => ['columnId' => 'G', 'labels' => ['default' => 'February', 'fr_FR' => 'Février']],
			'2019-03' => ['columnId' => 'H', 'labels' => ['default' => 'March', 'fr_FR' => 'Mars']],
			'2019-04' => ['columnId' => 'I', 'labels' => ['default' => 'April', 'fr_FR' => 'Avril']],
			'2019-05' => ['columnId' => 'J', 'labels' => ['default' => 'May', 'fr_FR' => 'Mai']],
			'2019-06' => ['columnId' => 'K', 'labels' => ['default' => 'June', 'fr_FR' => 'Juin']],
			'2019-07' => ['columnId' => 'L', 'labels' => ['default' => 'July', 'fr_FR' => 'Juillet']],
			'2019-08' => ['columnId' => 'M', 'labels' => ['default' => 'August', 'fr_FR' => 'Août']],
			'2019-09' => ['columnId' => 'N', 'labels' => ['default' => 'September', 'fr_FR' => 'Septembre']],
		);
		$statuses = array();
		$matrix = array();
		foreach ($description['properties']['status']['modalities'] as $statusId => $labels) {
			$statuses[$statusId] = ['labels' => $labels];
			$matrix[$statusId] = array();
			foreach ($months as $monthId => $unused) $matrix[$statusId][$monthId] = 0;
		}
		foreach ($terms as $term) {
			$period = substr($term->collection_date, 0, 7);
			if (array_key_exists($period, $matrix[$term->status]) && $term->status && $term->collection_date) $matrix[$term->status][$period] += $term->amount;
		}

		include 'public/PHPExcel_1/Classes/PHPExcel.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
		
		$workbook = new \PHPExcel;
		(new SsmlCrossTableViewHelper)->formatXls($workbook, $matrix, $months, $statuses, ['default' => 'Terms in volume by status x month', 'fr_FR' => 'Échéances en volume par statut x mois']);
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
		
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=terms-status-month.xlsx ');
		$writer->save('php://output');
		
		return $this->response;
	}
}
