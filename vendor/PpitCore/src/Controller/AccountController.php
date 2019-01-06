<?php

namespace PpitCore\Controller;

use PpitContact\Model\ContactMessage;
use PpitCommitment\ViewHelper\PpitPDF;
use PpitCore\ViewHelper\EventPlanningViewHelper;
use PpitCore\ViewHelper\PdfIndexCardViewHelper;
use PpitCore\ViewHelper\SsmlAccountViewHelper;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Community;
use PpitCore\Model\Csrf;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Event;
use PpitCore\Model\GroupAccount;
use PpitCore\Model\Instance;
use PpitCore\Model\Interaction;
use PpitCore\Model\Place;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use Zend\Db\Sql\Where;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
	/***
	 * Controller for the container page. Provides parameters both for the html container page and the js file (account-scripts) that 
	 * controls the container and the panels (index, search, list, detail, update)
	 */
	public function indexAction()
	{
		// Retrieve the context and the current place
		$context = Context::getCurrent();
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;

		// Retrieve the parameters and the page and panels configuration
		$entry = $this->params()->fromRoute('entry', 'account');
		$type = $this->params()->fromRoute('type', 'business');
		$app = $this->params()->fromRoute('app');
		$configProperties = Account::getConfig($type);
    	$description = $context->getConfig('core_account/'.$type);
    	if (array_key_exists('options', $description) && array_key_exists('internal_identifier', $description['options'])) $internalIdentifier = $description['options']['internal_identifier'];
    	else $internalIdentifier = false;
		$vcardProperties = Vcard::getConfig();
		if ($type == 'p-pit-studies') $commitmentProperties = \PpitCommitment\Model\Commitment::getDescription('p-pit-studies')['update'];
		else $commitmentProperties = null;

    	// Retrieve the available email templates and the signature to use in the emails
    	$templates = array();
		if ($context->getConfig('specificationMode') == 'config') $templates = $context->getConfig('emailing/'.$place_identifier);
		else {
			$config = Config::get($place_identifier.'_emailing', 'identifier');
			if ($config) $templates = $config->content;
		}

		// Retrieve the application
		$applicationId = ($app) ? $app : 'p-pit-engagements';
		$applicationName = $context->localize($context->getConfig('menus/'.$applicationId)['labels']);
		
		// Define the initial status depending on the perspective
		if ($entry == 'contact') $status = 'new'; 
		else $status = 'active';

    	return new ViewModel(array(
    			'context' => $context,
				'configProperties' => $configProperties,
    			'internalIdentifier' => $internalIdentifier,
				'vcardProperties' => $vcardProperties,
    			'place' => $place,
    			'app' => $app,
    			'applicationName' => $applicationName,
    			'applicationId' => $applicationId,
    			'entry' => $entry,
    			'type' => $type,
				'page' => $context->getConfig('core_account/index/'.$type),
				'indexPage' => $context->getConfig('core_account/index/'.$type),
    			'searchPage' => Account::getConfigSearch($type, $configProperties),
    			'listPage' => Account::getConfigList($type, $configProperties),
				'detailPage' => $context->getConfig('core_account/detail/'.$type),
    			'updatePage' => Account::getConfigUpdate($type, $configProperties),
				'updateContactPage' => $context->getConfig('core_account/updateContact/'.$type),
    			'groupUpdatePage' => Account::getConfigGroupUpdate($type, $configProperties),
    			'commitmentProperties' => $commitmentProperties,
    			'templates' => $templates,
    			'status' => $status,
    	));
    }

    public function indexAltAction ()
    {
    	$view = $this->indexAction();
    	$view->setTerminal(true);
    	return $view;
    }
    
    /**
     * contactIndex is a variant of index which has to be declared as a separate route with a distinct Access Control List (ACL).
     * The role are not the same for managing contacts and clients for example.
     */
/*    public function contactIndexAction()
    {
    	return $this->indexAction();
    }*/

    /**
     * Retrieve and format search parameters from the query. The search configuration (core_account/search/<type>) describes the
     * search criteria. A simple criteria has the name of the property. A range criteria is a tuplet where the property name is
     * prefixed by min_ and max_ respectively.
     */
    public function getFilters($params, $type)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();

    	foreach ($context->getConfig('core_account/search/'.$type)['properties'] as $propertyId => $unused) {
    
    		$property = ($params()->fromQuery($propertyId, null));
    		if ($property !== null) $filters[$propertyId] = $property;
    		$min_property = ($params()->fromQuery('min_'.$propertyId, null));
    		if ($min_property !== null) $filters['min_'.$propertyId] = $min_property;
    		$max_property = ($params()->fromQuery('max_'.$propertyId, null));
    		if ($max_property !== null) $filters['max_'.$propertyId] = $max_property;
    	}
    	return $filters;
    }

	/**
	 * Action for the search engine panel
	 */
	public function searchAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the parameters and the panel configuration
		$entry = $this->params()->fromRoute('entry');
		$type = $this->params()->fromRoute('type', null);
		$configProperties = Account::getConfig($type);
		$searchPage = Account::getConfigSearch($type, $configProperties);

		$view = new ViewModel(array(
				'context' => $context,
				'configProperties' => $configProperties,
				'places' => Place::getList(array()),
				'entry' => $entry,
				'type' => $type,
				'searchPage' => $searchPage,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function searchAltAction()
	{
		return $this->searchAction();
	}
	
	/**
	 * Method for constructing the list panel, shared by the list and export actions
	 */
	public function getList()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		 
		// Retrieve the parameters and the panel configuration
		$entry = $this->params()->fromRoute('entry');
		$type = $this->params()->fromRoute('type');
		$status = $this->params()->fromQuery('status');
		$params = $this->getFilters($this->params(), $type);
		$limit = $this->params()->fromQuery('limit');
		$major = ($this->params()->fromQuery('major', $context->getConfig('core_account/'.$type)['order']));
		$dir = ($this->params()->fromQuery('dir'));
		$order = (($dir == 'DESC') ? '-' : '+').$major;
		$configProperties = Account::getConfig($type);
    	$listPage = Account::getConfigList($type, $configProperties);
		$eventAccountListPage = $context->getConfig('core_account/event_account_list/'.$type);

		// Define the status list for the current perspective
		$statusDef = $context->getConfig('core_account/'.$type.'/property/status');
		if (!$statusDef) $statusDef = $context->getConfig('core_account/generic/property/status');
		if ($statusDef['definition'] != 'inline') $statusDef = $context->getConfig($statusDef['definition']);
		$statusList = implode(',', $statusDef['perspectives'][$entry]);

		// Mode todo-list
		if (!$params) { 
			$todoDef = $context->getConfig('core_account/todo/'.$type.'/'.$entry);
			if (!$todoDef) $todoDef = $context->getConfig('core_account/todo/generic/'.$entry);
			if ($todoDef) {
				foreach ($todoDef['filters'] as $propertyId => $value) $params[$propertyId] = implode(',', $value);
				$order = ''; $first = true;
				foreach ($todoDef['order'] as $propertyId => $direction) {
					$sign = (($direction == 'DESC') ? '-' : '+');
					if ($first) {
						$major = $propertyId;
						$dir = $direction;
					}
					else $order .= ',';
					$first = false;
					$order .= $sign.$propertyId;
				}
			}
		}

		if (!array_key_exists('status', $params)) $params['status'] = $statusList;

		// Retrieve the list
		$accounts = Account::getList($type, $params, $order, $limit);
		
		// Compute derivated properties
		// Aggregate according to the major property (the one on which the list is ordered):
		// A numeric property is summed
		// A modal property is distributed along its modalities (ex. 3/5 Female, 2/5 Male and 1/5 Undefined for a gender property)
		$computedProperties = array();
		foreach ($configProperties as $propertyId => $property) if ($property['type'] == 'computed') $computedProperties[$propertyId] = $property;
		$sum = 0;
		$distribution = array();
		foreach ($accounts as $account) {
			$majorSpecification = $configProperties[$major];
			if ($majorSpecification['type'] == 'number') $sum += $account->properties[$major];
			elseif ($majorSpecification['type'] == 'select') {
				if (array_key_exists($account->properties[$major], $distribution)) $distribution[$account->properties[$major]]++;
				else $distribution[$account->properties[$major]] = 1;
			}
			
			foreach ($computedProperties as $propertyId => $property) {
				if (array_key_exists('rules', $property)) {
					$account->properties[$propertyId] = null;
					foreach ($property['rules'] as $ruleId => $rule) {
						$matched = true;
						foreach ($rule as $predicateId => $unused) if (!$account->properties[$predicateId]) $matched = false;
						if ($matched) {
							$account->properties[$propertyId] = $ruleId;
							break;
						}
					}
				}
			}
		}
		$average = (count($accounts)) ? round($sum / count($accounts), 1) : null;
		 
		// Delegate presentation to the view layer giving it the configuration and parameters
		$view = new ViewModel(array(
			'context' => $context,
			'configProperties' => $configProperties,
			'accounts' => $accounts,
			'places' => Place::getList(array()),
			'entry' => $entry,
			'type' => $type,
			'params' => $params,
			'major' => $major,
			'dir' => $dir,
			'count' => count($accounts),
			'sum' => $sum,
			'average' => $average,
			'distribution' => $distribution,
			'listPage' => $listPage,
			'eventAccountListPage' => $eventAccountListPage,
		));
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * Action for the list panel
	 */
	public function listAction()
	{
		return $this->getList();
	}
	
	public function listAltAction()
	{
		return $this->listAction();
	}

	/**
	 * Action for exporting to Excel
	 */
	public function exportAction()
	{
		$view = $this->getList();
		$description = Account::getDescription($view->type);
		 
		include 'public/PHPExcel_1/Classes/PHPExcel.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/CachedObjectStorageFactory.php';

		$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		 
		$workbook = new \PHPExcel;
		(new SsmlAccountViewHelper)->formatXls($description, $workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
		
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Comptes.xlsx ');
		ob_end_clean();
		$writer->save('php://output');

		$view = new ViewModel(array());
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * Action for exporting to CSV
	 */
	public function exportCsvAction()
	{
		$context = Context::getCurrent();
		$view = $this->getList();
		header('Content-Type: text/csv; charset=utf-8');
		header("Content-disposition: filename=contact-".date('Y-m-d').".csv");
		echo "\xEF\xBB\xBF";

		$configProperties = Account::getConfig($view->type);
		$configExport = Account::getConfigExport($view->type, $configProperties);
		
		foreach($configExport as $propertyId => $property) {
			print_r($context->localize($property['labels']).';');
		}
		print_r("\n");

		foreach ($view->accounts as $account) {
			foreach($configExport as $propertyId => $property) {
				if ($account->properties[$propertyId]) {
					if ($propertyId == 'contact_history');
					elseif ($propertyId == 'place_id') print_r($account->place_caption);
					elseif ($property['type'] == 'date') print_r($context->decodeDate($account->properties[$propertyId]));
					elseif ($property['type'] == 'number') print_r($context->formatFloat($account->properties[$propertyId], 2));
					elseif ($property['type'] == 'select')  print_r((array_key_exists($account->properties[$propertyId], $property['modalities'])) ? $context->localize($property['modalities'][$account->properties[$propertyId]]) : $account->properties[$propertyId]);
					else print_r($account->properties[$propertyId]);
				}
				print_r(';');
			}
			print_r("\n");
		}
		return $this->response;
	}

	/**
	 * To be moved to EventController after moving the model logic to the model layer
	 */
	public function eventAccountListAction()
	{
		return $this->getList();
	}
	
	/**
	 * Action for controlling grouped actions
	 */
	public function groupAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the parameters and the panel configuration
		$type = $this->params()->fromRoute('type');
		$configProperties = Account::getConfig($type);
		$groupUpdatePage = Account::getConfigGroupUpdate($type, $configProperties);

		// Retrieve the places
		$places = array();
		foreach (Place::getList(array()) as $place) $places[$place->id] = $place->caption;
		
		// Retrieve the selected list of accounts and provide a json-like array of the data for the view layer
		$request = $this->getRequest();
		if (!$request->isPost()) return $this->redirect()->toRoute('home');
		$nbAccount = $request->getPost('nb-account');
		$content = array();
		$content['data'] = array();
		$content['data']['selection'] = array();
		$accountIds = array();
		for ($i = 0; $i < $nbAccount; $i++) {
			$accountIds[] = $request->getPost('account_'.$i);
		}
		$accounts = array();
		foreach ($accountIds as $account_id) {
			$account = Account::get($account_id);
			if ($account->type == 'group') {
				foreach (GroupAccount::getList(GroupAccount::getDescription('generic'), ['group_id' => $account->id]) as $groupAccount) {
					$accounts[$groupAccount->account_id] = $groupAccount;
					$content['data']['selection'][$groupAccount->account_id] = $groupAccount->properties;
				}
			}
			else {
				$accounts[$account_id] = $account;
				$content['data']['selection'][$account_id] = $account->properties;
			}
		}

		// Provide a structure for storing the input values
		$account = array();
		$content['data']['account'] = $account;

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
				foreach ($groupUpdatePage as $propertyId => $property) {
					if ($request->getPost($propertyId.'_check')) $content['data']['account'][$propertyId] = $request->getPost($propertyId);
				}
				foreach ($accounts as $account) {
		    		$account->currently_updated_by = null;
					if ($account->loadData($type, $content['data']['account']) != 'OK') throw new \Exception('View error');
					$rc = $account->update($request->getPost('update_time'));
				}
			}
			$message = 'OK';
		}
		$view = new ViewModel(array(
				'context' => $context,
				'configProperties' => $configProperties,
				'groupUpdatePage' => $groupUpdatePage,
				'type' => $type,
				'content' => $content,
				'places' => $places,
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function groupAltAction()
	{
		return $this->groupAction();
	}
	
	/**
	 * Add the selection to a group
	 */
	public function addToGroupAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		$groupDescription = GroupAccount::getDescription();

		// Retrieve the available groups
		$cursor = Account::getList('group', []);
		$groups = array();
		foreach ($cursor as $group) {
			$groups[$group->id] = array('identifier' => $group->identifier, 'name' => $group->name);
		}

		// Retrieve the selected list of accounts and provide a json-like array of the data for the view layer
    	$accountIds = explode(',', $this->params()->fromQuery('accounts'));
		$accounts = array();
		foreach ($accountIds as $accountId) {
			$account = Account::get($accountId);
			$accounts[] = $account;
			$content['data']['selection'][$account->id] = $account->properties;
		}
	
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
				$selectedGroup = $request->getPost('group_id');
				$groupAccount = GroupAccount::instanciate();
				$content = array('data' => array());
				$content['data']['group_id'] = $selectedGroup;
				foreach ($accounts as $account) {
					$content['data']['account_id'] = $account->id;
					$rc = $groupAccount->loadAndAdd($groupDescription, $content);
					if ($rc[0] != '200') {
						print_r($content);
						$error = $rc[0];
					}
				}
			}
			$message = 'OK';
		}

		$view = new ViewModel(array(
			'context' => $context,
			'groups' => $groups,
			'csrfForm' => $csrfForm,
			'message' => $message,
			'error' => $error,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function sendMessageAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		$eventConfigProperties = Event::getConfigProperties('email');
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		
    	// Retrieve the type
    	$type = $this->params()->fromRoute(0);

    	// Retrieve the available email templates and the signature to use in the emails
		$templates = array();
		if ($context->getConfig('specificationMode') == 'config') $templates = $context->getConfig('emailing/'.$place_identifier);
		else {
			$config = Config::get($place_identifier.'_emailing', 'identifier');
			if ($config) $templates = $config->content;
		}

		foreach ($templates as &$template) {
			if (array_key_exists('route', $template)) {
				$client = new Client(
					$this->url()->fromRoute($template['route'], [], ['query' => ['locale' => $context->getLocale()], 'force_canonical' => true]),
					array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
				);
				$client->setMethod('GET');
				$template['text'] = ['default' => $client->send()->getBody()];
			}
		}
		
    	$signature = $context->getConfig('core_account/sendMessage')['signature'];
    	if ($signature['definition'] != 'inline') $signature = $context->getConfig($signature['definition']);

		// Instanciate the email data structure (emails are sent in batch mode)
    	$mail = ContactMessage::instanciate();
    	$mail->type = 'email';
    	$mail->subject = $context->localize($context->getConfig('core_account/sendMessage/generic')['subject']);
    	if (array_key_exists('themes', $context->getConfig('core_account/sendMessage'))) $mail->body = $context->getConfig('core_account/sendMessage')['themes']['theme_1'];
    	else $mail->body = $context->getConfig('core_account/sendMessage/generic')['body'];

    	// Retrieve the available document list for email attachments in the case where a dropbox account is configured for the current instance
    	$documentList = array();
    	if ($context->getConfig('ppitDocument') && array_key_exists('dropbox', $context->getConfig('ppitDocument')) && array_key_exists('contact', $context->getConfig('ppitDocument')['dropbox']['folders'])) {
	    	$dropbox = $context->getConfig('ppitDocument')['dropbox'];
	    	$client = new Client(
	    			'https://api.dropboxapi.com/2/files/list_folder',
	    			array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
	    			);
	    	$client->setEncType('application/json');
	    	$client->setMethod('POST');
	    	$client->getRequest()->getHeaders()->addHeaders(array('Authorization' => 'Bearer '.$dropbox['credential']));
	    	$client->setRawBody(json_encode(array('path' => $dropbox['folders']['contact'])));
	    	try {
		    	$response = $client->send();
		    	foreach (json_decode($response->getBody(), true)['entries'] as $entry) {
		    		$documentList[] = $entry['name'];
		    	}
	    	}
	    	catch (\Exception $e) {}
    	}
    	else $dropbox = null;

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	$message = null;
    	$selectedTemplateId = null;
    	$body = null;
    	
    	// Process the posted form
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		// Retrieve the accounts previously selected for the grouped action
    		$nbAccount = $request->getPost('nb-account');
    		$accounts = array();
    		for ($i = 0; $i < $nbAccount; $i++) {
    			$account = Account::get($request->getPost('account_'.$i));
    			$accounts[] = $account;
    		}

    		// Load a data structure with the posted value
    		$data = array();
			$data['type'] = 'email';
			foreach($accounts as $account) {
				$data['to'] = array();
	    		$data['cci'] = array();
				if ($context->getConfig('core_account/mailTo')) {
	    			foreach ($context->getConfig('core_account/mailTo') as $toMail => $toName) $data['cci'][$toMail] = $toName;
	    		}
	    		else {
					if ($account->email) {
						if ($request->getPost('mask_recipients')) $data['cci'][$account->email] = $account->n_first;
						else $data['to'][$account->email] = $account->n_first;
					}
				    if ($account->email_2) {
						if ($request->getPost('mask_recipients')) $data['cci'][$account->email_2] = $account->n_first_2;
						else $data['to'][$account->email_2] = $account->n_first_2;
					}
				    if ($account->email_3) {
						if ($request->getPost('mask_recipients')) $data['cci'][$account->email_3] = $account->n_first_3;
						else $data['to'][$account->email_3] = $account->n_first_3;
					}
				    if ($account->email_4) {
						if ($request->getPost('mask_recipients')) $data['cci'][$account->email_4] = $account->n_first_4;
						else $data['to'][$account->email_4] = $account->n_first_4;
					}
				    if ($account->email_5) {
						if ($request->getPost('mask_recipients')) $data['cci'][$account->email_5] = $account->n_first_5;
						else $data['to'][$account->email_5] = $account->n_first_5;
					}
	    		}

				// Retrieve the selected email template and with the "from" value 
				$selectedTemplateId = $request->getPost('template_id');
    			$selectedTemplate = $templates[$selectedTemplateId];
    			if (array_key_exists('route', $selectedTemplate)) {
    				$client = new Client(
    					$this->url()->fromRoute($selectedTemplate['route'], [], ['query' => ['mode' => 'personnalized', 'locale' => $account->locale], 'force_canonical' => true]),
    					array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
    				);
    				$client->setMethod('GET');
    				$text = $client->send()->getBody();
    			}
    			else $text = $context->localize($selectedTemplate['text'], $account->locale);

    			// Retrieve the text from the form if the email text is customizable in the view and add the signature at the location of the tag '%s'
    			$characters = ['à', 'À', 'â', 'Â', 'ä', 'Ä', 'é', 'É', 'è', 'È', 'ê', 'Ê', 'ë', 'Ë', 'î', 'Î', 'ï', 'Ï', 'ô', 'Ô', 'ö', 'Ö', 'ù', 'Ù', 'û', 'Û', 'ü', 'Ü', 'ç', 'Ç'];
    			$encoded = ['&agrave;', '&Agrave;', '&acirc;', '&Acirc;', '&auml;', '&Auml;', '&eacute;', '&Eacute;', '&egrave;', '&Egrave;', '&ecirc;', '&Ecirc;', '&euml;', '&Euml;', '&icirc;', '&Icirc;', '&iuml;', '&Iuml;', '&ocirc;', '&Ocirc;', '&ouml;', '&Ouml;', '&ugrave;', '&Ugrave;', '&ucirc;', '&Ucirc;', '&uuml;', '&Uuml;', '&ccedil;', '&Ccedil;'];
    			$text = str_replace($characters, $encoded, $text);

    			if (array_key_exists('cci', $selectedTemplate)) $data['cci'][$selectedTemplate['cci']] = $selectedTemplate['cci'];
	    		$data['from_mail'] = $selectedTemplate['from_mail'];
	    		$data['from_name'] = $selectedTemplate['from_name'];

	    		$data['subject'] = $request->getPost($selectedTemplateId.'_subject');
	    		$attachment = $request->getPost('attachment');
    			

    			$body = '';
    			if ($context->getConfig('core_account/mailTo')) {
    				if ($account->email) $body .= $account->email;
    				if ($account->email_2) $body .= $account->email_2;
    				if ($account->email_3) $body .= $account->email_3;
    				if ($account->email_4) $body .= $account->email_4;
    				if ($account->email_5) $body .= $account->email_5;
    				$body .= '<br>';
    			}

    			$body .= $text;

    			// Generate an authentication token that can be passed as a value for the variable 'authentication_token' in the text.
    			// This token allows the email's addressee to access the target page as being authenticated
    			// Finally, replace all the variables in the text by their value in the account data structure
    			$account->properties['authentication_token'] = md5(uniqid(rand(), true));
   				if (array_key_exists('params', $selectedTemplate)) {
   					$arguments = array();
   					foreach ($selectedTemplate['params'] as $param) {
						$arguments[] = htmlentities($account->properties[$param]);
   					}
					$body = vsprintf($body, $arguments);
   				}

    			// Load the data in the email data model
    			$data['body'] = $body;
    			if ($mail->loadData($data) != 'OK') throw new \Exception('View error');
	    			
    			// Atomicity
	   			$connection = ContactMessage::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				$rc = $mail->add();
    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
    				}
    				else {
    					$account->authentication_token = $account->properties['authentication_token'];
						$account->update(null);

						// Log an event for CRM analysis
						$event = Event::instanciate('email');
						$eventData = array();
						$eventData['status'] = 'new';
						$eventData['place_id'] = $account->place_id;
						$eventData['account_id'] = $account->id;
						$eventData['category'] = $selectedTemplateId;
						$eventData['description'] = $body;
						$eventData['property_1'] = json_encode($data['to']);
						$eventData['property_3'] = json_encode($data['cci']);
						$eventData['property_4'] = $data['subject'];
						$eventData['property_5'] = $data['from_name'].' ['.$data['from_mail'].']';

						$rc = $event->loadAndAdd($eventData, $eventConfigProperties);

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

    	// Return the view
    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
    			'templates' => $templates,
    			'selectedTemplateId' => $selectedTemplateId,
    			'body' => $body,
//    			'signature' => $signature,
    			'mail' => $mail,
	    		'documentList' => $documentList,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function passwordRequestAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
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
	    		$nbAccount = $request->getPost('nb-account');
	    		$accounts = array();
	    		for ($i = 0; $i < $nbAccount; $i++) {
		    		$account = Account::get($request->getPost('account_'.$i));
		    		$accounts[] = $account;
	    			if ($account->email || $account->email_2 || $account->email_3 || $account->email_4 || $account->email_5) {
		    			$user = User::get($account->contact_1_id, 'vcard_id');
		    			if (!$user) {
		    				$user = User::instanciate();
		    				$username = strtolower(substr($account->n_first, 0, 1).$account->n_last);
		    				$user->username = $username;
		    				for($j = 1; true; $j++) {
		    					$existingUser = User::getTable()->transGet($user->username, 'username');
		    					if (!$existingUser) break;
		    					else $user->username = $username.$j;
		    				}
							$user->vcard_id = $account->contact_1_id;
							$user->add(false, false);
							$userContact = UserContact::instanciate();
							$userContact->user_id = $user->user_id;
							$userContact->vcard_id = $account->contact_1_id;
							$userContact->add();
		    			}
		    			if (!$error) {
			    			$password_init_token = $context->getSecurityAgent()->requestPasswordInit($user, false, $this->url);
			    			 
			    			// Insert a mail in the queue
			    			$contact = Vcard::getTable()->transGet($user->vcard_id);
			    			$mail = ContactMessage::instanciate();
			    			$mail->type = 'email';
			    			 
			    			$data = array();
			    			$data['to'] = array();
			    			if ($account->email) $data['to'][$account->email] = $account->email;
			    			if ($account->email_2) $data['to'][$account->email_2] = $account->email_2;
			    			if ($account->email_3) $data['to'][$account->email_3] = $account->email_3;
			    			if ($account->email_4) $data['to'][$account->email_4] = $account->email_4;
			    			if ($account->email_5) $data['to'][$account->email_5] = $account->email_5;
			    			$data['cci'] = array();
			    			$selectedTemplateId = $request->getPost('template_id');
			    			$data['subject'] = $context->getConfig()['ppitUserSettings']['messages']['addTitle'][$context->getLocale()];
			    			$data['from_mail'] = $context->getConfig()['ppitUserSettings']['messages']['from_mail'];
			    			$data['from_name'] = $context->getConfig()['ppitUserSettings']['messages']['from_name'];
			    			$body = $context->getconfig()['ppitUserSettings']['messages']['addText'][$context->getLocale()];
							$url = $this->getServiceLocator()->get('viewhelpermanager')->get('url');
			   				$link = $url('user/initpassword', array('id' => $user->user_id), array('force_canonical' => true)).'?hash='.$password_init_token;
			    			$body = sprintf($body, $user->username, $link, $link);
			    			$data['body'] = $body;
			    			if ($mail->loadData($data) != 'OK') throw new \Exception('View error');
			    			$rc = $mail->add();
		    			}
	    			}
	    		}
    			$message = 'OK';
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function dropboxLinkAction()
    {
    	$context = Context::getCurrent();
    	$document = $this->params()->fromRoute('document', 0);
		$dropbox = $context->getConfig('ppitDocument')['dropbox'];
    	$client = new Client(
    			'https://api.dropboxapi.com/2/files/get_temporary_link',
    			array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
    			);
    	$client->setEncType('application/json');
    	$client->setMethod('POST');
    	$client->getRequest()->getHeaders()->addHeaders(array('Authorization' => 'Bearer '.$dropbox['credential']));
    	$client->setRawBody(json_encode(array('path' => $dropbox['folders']['contact'].'/'.$document)));
    	$response = $client->send();
    	$this->response->http_status = $response->renderStatusLine();
    	$result = json_decode($response->getBody(), true);
    	if (is_array($result) && array_key_exists('link', $result)) return $this->redirect()->toUrl($result['link']);
    	else {
	    	$this->response->http_status = 400;
    		return $this->response;
    	}
    }

    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$type = $this->params()->fromRoute('type', 0);
    	$configProperties = Account::getConfig($type);
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $account = Account::get($id);
    	else $account = Account::instanciate($type);
//    	$this->getAccountProperties($account);
    	if (!$type) $type = $account->type;
    	$view = new ViewModel(array(
    			'context' => $context,
				'configProperties' => $configProperties,
    			'type' => $type,
    			'id' => $account->id,
    			'account' => $account,
				'detailPage' => $context->getConfig('core_account/detail/'.$type),
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function detailAltAction()
    {
    	return $this->detailAction();
    }
    
    /**
     * Action for providing availability for a list of accounts from date to date, with weekly constraints and exception dates
     * The result is in JSON form ans can be used to populate a JS calendar
     */
    public function getAvailabilityAction()
    {
    	$context = Context::getCurrent();
    	$accountIds = explode(',', $this->params()->fromQuery('accounts'));
    	$begin = $this->params()->fromQuery('begin');
    	$end = $this->params()->fromQuery('end');
    	$accounts = array();
    	foreach ($accountIds as $account_id) {
    		if ($account_id) $accounts[$account_id] = Account::get($account_id)->getProperties();
    	}
    	return new JsonModel(EventPlanningViewHelper::displayAvailability($accounts, $begin, $end));
    }
    
    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the entry and type
    	$type = $this->params()->fromRoute('type');
    	$configProperties = Account::getConfig($type);
    	$updatePage = Account::getConfigUpdate($type, $configProperties);
    	$description = $context->getConfig('core_account/'.$type);
    	if (array_key_exists('options', $description) && array_key_exists('internal_identifier', $description['options'])) $internalIdentifier = $description['options']['internal_identifier'];
    	else $internalIdentifier = false;
    	 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	$action = $this->params()->fromQuery('act', null);
		$passphrase = $this->params()->fromQuery('passphrase');
    	
    	if ($id) {
    		$account = Account::get($id, 'id', $passphrase);
    		if ($action == 'update' && !$account->currently_updated_by) {
	    		$account->currently_updated_by = $context->getFormatedName();
	    		$account->update(null);
    		}
    	}
    	else $account = Account::instanciate($type);

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
	    		$account->currently_updated_by = null;
    			if ($action == 'delete') {
    				$return = $account->delete($request->getPost('update_time'));
    				if ($return != 'OK') $error = $return;
	    			else $message = 'OK';
    			}
    			else {
    				if ($account->id) {
						// Unlink the current place community for the account type
						if ($account->place_id) {
							$place = Place::get($account->place_id);
							$community = Community::get($account->type.'/'.$place->identifier, 'identifier');
							if ($place && $community && array_key_exists($community->id, $account->contact_1->communities)) {
		    					unset($account->contact_1->communities[$community->id]);
		    				}
						}
    				}
    				
    				$data = array();
    				$photo = null;
    				$passphrase = null;
					foreach ($updatePage as $propertyId => $property) {
						if ((!array_key_exists('readonly', $property) || !$property['readonly']) && ($propertyId != 'identifier' || !$internalIdentifier) && $property['type'] != 'title') {
							if ($property['type'] == 'photo') {
								if (array_key_exists($propertyId, $request->getFiles()->toArray())) $photo = $request->getFiles()->toArray()[$propertyId];
							}
							elseif ($property['type'] == 'structure') {
								$data[$propertyId] = array();
								for ($i = 0; $i < $property['max_occurences']; $i++) {
									$row = array();
									foreach ($property['fields'] as $fieldId => $field) {
										$value = $request->getPost($propertyId.'_'.$fieldId.'_'.$i);
										if ($value) $row[$fieldId] = $value;
									}
									if ($row) $data[$propertyId][] = $row;
								}
							}
							elseif ($property['type'] == 'datetime') {
								$data[$propertyId] = $request->getPost($propertyId);
								if ($data[$propertyId]) {
									if ($request->getPost($propertyId.'-time')) $data[$propertyId] .= ' '.$request->getPost($propertyId.'-time');
									else $data[$propertyId] .= ' 00:00:00';
								}
							}
							else {
								$data[$propertyId] = $request->getPost($propertyId);
								if ($data[$propertyId] == 'null') $data[$propertyId] = null; // JS returns the string 'null' for multiple select input without selection
							}
						}
					}

//					if ($type) $data['credits'] = array($type => true);

	    			// Atomically save
	    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
	    			$connection->beginTransaction();
	    			try {
	    				if (!$account->id) {
	    					$rc = $account->loadAndAdd($data, $configProperties);
	    					if ($rc[0] == '206') $account = Account::get($rc[1]); // Partially accepted on an already existing account which is returned as rc[1]
	    					elseif ($rc[0] != '200') $error = $rc;
	    				}
	    				else {

	    					// Save the contact
	    					if ($photo) {
								$account->contact_1->savePhoto($photo);
								$account->contact_1->photo_link_id = null;
							}
	    					$rc = $account->loadAndUpdate($data, $configProperties, $account->contact_1->update_time);
	    					if ($rc[0] != '200') $error = $rc;
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
	    			$action = null;
				}
    		}
    	}
    	$account->properties = $account->getProperties($account->type, Account::getDescription($type));
    	$view = new ViewModel(array(
    			'context' => $context,
				'configProperties' => $configProperties,
    			'internalIdentifier' => $internalIdentifier,
    			'type' => $type,
    			'id' => $id,
    			'action' => $action,
    			'account' => $account,
				'places' => Place::getList(array()),
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message,
				'detailPage' => $context->getConfig('core_account/detail/'.$type),
    			'updatePage' => $updatePage,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function updateAltAction()
    {
    	return $this->updateAction();
    }
    
    public function updateUserAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	// Retrieve the type
    	$type = $this->params()->fromRoute('type');
    
    	$id = (int) $this->params()->fromRoute('id', 0);
    	$action = $this->params()->fromRoute('act', null);
    	if (!$id) return $this->redirect()->fromRoute('home'); 
    	
    	// Retrieve the account
    	$account = Account::get($id);
    	$account->user = User::get($account->contact_1_id, 'vcard_id');
    	if ($account->user) {
    		$account->username = $account->user->username;
    		$new_password = null;
    	}
    	else {
    		$account->user = User::instanciate();
    		$username = strtolower(substr($account->n_first, 0, 1).$account->n_last);
    		$account->user->username = $username;
    		for($i = 1; true; $i++) {
    			$existingUser = User::getTable()->transGet($account->user->username, 'username');
    			if (!$existingUser) break;
    			else $account->user->username = $username.$i;
    		}
    		$account->username = $account->user->username;

	    	// Generate the new password
	    	$new_password = '';
	    	$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
	    		
	    	for($i = 0; $i < 6; $i++)
	    	{
	    		$new_password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
	    	}
    	}

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
    			$place = Place::get($account->place_id);
    			$community = Community::get('p-pit-studies/'.$place->identifier, 'identifier');

    			// Load the input data
    			$data = array();
    			$data['communities'] = array($community->id => true);
    			$data['roles'] = array();
    			$data['perimeters'] = array();
    			if ($type) $data['credits'] = array($type => true);
    			$account->username = $request->getPost('username');
    			$data['username'] = $account->username;
    			$data['state'] = $request->getPost('state');
    			$data['is_notified'] = $request->getPost('is_notified');
    			$new_password = $request->getPost('new_password');
    			$data['new_password'] = $new_password;
    			$data['locale'] = $request->getPost('locale');
    			$data['is_demo_mode_active'] = false;
 
    			if ($account->contact_1->loadData($data) != 'OK') throw new \Exception('View error');

					// Load the user data
					$rc = $account->user->loadData($request, $account->contact_1);
					if ($rc == 'Integrity') throw new \Exception('View error');
					elseif ($rc == 'Duplicate') $error = 'Duplicate user';
					$account->user->state = $data['state'];

    				if (!$error) {
    				// Atomically save
    				$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    				$connection->beginTransaction();
    				try {
    					$account->contact_1->update($account->contact_1->update_time);
    					if (!$account->user->user_id) {
    						// Create a new user
    						$user = User::getNew();
    						$account->user = $user;
    						$user->username = $data['username'];
    						$user->vcard_id = $account->contact_1_id;
    						if ($account->is_notified && !$data['new_password']) {
    							$rc = $user->add(false, true);
    						}
    						else $rc = $user->add(false, false);
    						if ($rc != 'OK') $error = $rc;
    						$userContact = UserContact::instanciate();
    						$userContact->user_id = $user->user_id;
    						$userContact->vcard_id = $account->contact_1_id;
    						$userContact->add();
    						$account->username = $user->username;
    					}
    					if (!$error) {
    						$rc = $account->user->update(null);
    						if ($rc != 'OK') $error = $rc;
    					}
    					if (!$error) {
	    					if ($data['new_password']) {
	    						$account->user->new_password = $data['new_password'];
	    						if ($rc != 'OK') $error = $rc;
	    						else $context->getSecurityAgent()->changePassword($account->user, $account->user->username, null, $account->user->new_password, null);
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
    				$action = null;
    			}
    		}
    	}
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'type' => $type,
    			'id' => $id,
    			'action' => $action,
    			'account' => $account,
    			'new_password' => $new_password,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function updateContactAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$id = (int) $this->params()->fromRoute('id', 0);
    	$contactNumber = $this->params()->fromRoute('contactNumber', 0);
    	$account = Account::get($id);
    	$type = $account->type;
    	$configProperties = Account::getConfig($type);

    	if ($contactNumber == 'contact_1') {
    		if (!$account->contact_1) $account->contact_1 = Vcard::instanciate();
    		$contact = $account->contact_1;
    		$contact_status = $account->contact_1_status;
    	}
    	elseif ($contactNumber == 'contact_2') {
    		if (!$account->contact_2) $account->contact_2 = Vcard::instanciate();
    		$contact = $account->contact_2;
    		$contact_status = $account->contact_2_status;
    	}
    	elseif ($contactNumber == 'contact_3') {
    		if (!$account->contact_3) $account->contact_3 = Vcard::instanciate();
    		$contact = $account->contact_3;
    		$contact_status = $account->contact_3_status;
    	}
    	elseif ($contactNumber == 'contact_4') {
    		if (!$account->contact_4) $account->contact_4 = Vcard::instanciate();
    		$contact = $account->contact_4;
    		$contact_status = $account->contact_4_status;
    	}
    	elseif ($contactNumber == 'contact_5') {
    		if (!$account->contact_5) $account->contact_5 = Vcard::instanciate();
    		$contact = $account->contact_5;
    		$contact_status = $account->contact_5_status;
    	}

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
    			foreach ($context->getConfig('core_account/updateContact'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
					$property = $context->getConfig('vcard/properties')[$propertyId];
					$data[$propertyId] = $request->getPost($propertyId);
    			}

    			if ($contact->loadData($data) != 'OK') throw new \Exception('View error');
    			$contact_status = $request->getPost('contact_status');
    			
    			// Atomically save
    			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				if (!$contact->id) $rc = $contact->add();
    				elseif ($action == 'delete') $rc = $contact->delete($request->getPost('update_time'));
    				else $rc = $contact->update($contact->update_time);

    				if ($rc != 'OK') {
    					$connection->rollback();
    					$error = $rc;
    				}
    				else {
    					if ($contactNumber == 'contact_1') {
							$contact_1_status = $account->contact_1_status = $contact_status;
							if ($contact_status == 'invoice') {
								if ($account->contact_2_status == 'invoice') $account->contact_2_status = '';
								if ($account->contact_3_status == 'invoice') $account->contact_3_status = '';
								if ($account->contact_4_status == 'invoice') $account->contact_4_status = '';
								if ($account->contact_5_status == 'invoice') $account->contact_5_status = '';
							}
    						$account->contact_1_id = $contact->id;
    						$account->contact_1 = $contact;
    					}
    					elseif ($contactNumber == 'contact_2') {
							$account->contact_2_status = $contact_status;
    						if ($contact_status == 'invoice') {
								if ($account->contact_1_status == 'invoice') $account->contact_1_status = '';
								if ($account->contact_3_status == 'invoice') $account->contact_3_status = '';
								if ($account->contact_4_status == 'invoice') $account->contact_4_status = '';
								if ($account->contact_5_status == 'invoice') $account->contact_5_status = '';
							}
							$account->contact_2_id = $contact->id;
    						$account->contact_2 = $contact;
    					}
    					elseif ($contactNumber == 'contact_3') {
							$account->contact_3_status = $contact_status;
    					    if ($contact_status == 'invoice') {
								if ($account->contact_1_status == 'invoice') $account->contact_1_status = '';
								if ($account->contact_2_status == 'invoice') $account->contact_2_status = '';
								if ($account->contact_4_status == 'invoice') $account->contact_4_status = '';
								if ($account->contact_5_status == 'invoice') $account->contact_5_status = '';
							}
							$account->contact_3_id = $contact->id;
    						$account->contact_3 = $contact;
    					}
    					elseif ($contactNumber == 'contact_4') {
							$account->contact_4_status = $contact_status;
    					    if ($contact_status == 'invoice') {
								if ($account->contact_1_status == 'invoice') $account->contact_1_status = '';
								if ($account->contact_2_status == 'invoice') $account->contact_2_status = '';
								if ($account->contact_3_status == 'invoice') $account->contact_3_status = '';
								if ($account->contact_5_status == 'invoice') $account->contact_5_status = '';
							}
							$account->contact_4_id = $contact->id;
    						$account->contact_4 = $contact;
    					}
    					elseif ($contactNumber == 'contact_5') {
							$account->contact_5_status = $contact_status;
    					    if ($contact_status == 'invoice') {
								if ($account->contact_1_status == 'invoice') $account->contact_1_status = '';
								if ($account->contact_2_status == 'invoice') $account->contact_2_status = '';
								if ($account->contact_3_status == 'invoice') $account->contact_3_status = '';
								if ($account->contact_4_status == 'invoice') $account->contact_4_status = '';
							}
							$account->contact_5_id = $contact->id;
    						$account->contact_5 = $contact;
    					}
    					$account->update($request->getPost('update_time'));

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
    	$contact->properties = $contact->toArray();
    	$view = new ViewModel(array(
    			'context' => $context,
				'configProperties' => $configProperties,
    			'type' => $type,
    			'id' => $id,
    			'contactNumber' => $contactNumber,
    			'action' => $action,
    			'account' => $account,
    			'contact' => $contact,
    			'contact_status' => $contact_status,
				'detailPage' => $context->getConfig('core_account/detail'.$type),
    			'updateContactPage' => $context->getConfig('core_account/updateContact/'.$type),
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function updateContactAltAction()
    {
    	return $this->updateContactAction();
    }
    
    public function indexCardAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the entry and type
    	$type = $this->params()->fromRoute('type');

    	$id = (int) $this->params()->fromRoute('id', 0);
		$account = Account::get($id);
		$account->properties = $account->getProperties();
		if ($account->place_id) $place = Place::get($account->place->id); else $place = null;

		// create new PDF document
		$pdf = new PpitPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		PdfIndexCardViewHelper::render($pdf, $place, $account);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$content = $pdf->Output('index-card-'.$context->getInstance()->caption.'-'.$account->name.'.pdf', 'I');
		return $this->response;
	}

	public function fbgetleadsAction()
	{
		$context = Context::getCurrent();
		
		// Authentication
		if (!$context->isAuthenticated() && !$context->wsAuthenticate($this->getEvent())) {
			$this->getResponse()->setStatusCode('401');
			return $this->getResponse();
		}
		
		$type = $this->params()->fromRoute('type', 'generic');
	
		$client = new Client(
			'https://graph.facebook.com/v3.2/2213184422239422/leadgen_forms',
			['adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30]
		);
		$client->setEncType('application/json');
		$client->getRequest()->getHeaders()->addHeaders(array('Authorization' => 'Bearer EAAIWKgrCgLgBAHnXpwxHKDz77IIETg5OMfHRyOzyLJ40om3IpGEEZBQaaJtoE55OtgepAZB07sjDEov9sI435RUMFgXnzZAZAbdGBeJNLxdgpL2o9sPhj0UbtCalPcpcDcwNZAM7CZAUZBqRjllizs9Ig1usL38yqApeh9Sbhe7ZCKaS0K72ZCP3a'));
		$client->setMethod('GET');
		$response = $client->send();
		if ($response->getStatusCode() != 200) {
			echo $response->renderStatusLine()."<br>";
			echo $response->getContent();
			return $this->response;
		}
		$pages = json_decode(gzdecode($response->getContent()), true);
		$urls = array();
		foreach ($pages['data'] as $page) $urls[] = 'https://graph.facebook.com/v3.2/'.$page['id'].'/leads';
		$url = current($urls);
		
		$i = 0;
		while (true) {
			echo 'Request: '.$url."<br>\n";
			$client = new Client(
				$url,
				['adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30]
			);
			$client->setEncType('application/json');
			$client->getRequest()->getHeaders()->addHeaders(array('Authorization' => 'Bearer EAAIWKgrCgLgBAHnXpwxHKDz77IIETg5OMfHRyOzyLJ40om3IpGEEZBQaaJtoE55OtgepAZB07sjDEov9sI435RUMFgXnzZAZAbdGBeJNLxdgpL2o9sPhj0UbtCalPcpcDcwNZAM7CZAUZBqRjllizs9Ig1usL38yqApeh9Sbhe7ZCKaS0K72ZCP3a'));
			$client->setMethod('GET');
			$response = $client->send();
			if ($response->getStatusCode() == 200) {
				$content = gzdecode($response->getContent());
				if ($content) {
					$content = json_decode($content, true);
					$leads = $content['data'];
					foreach ($leads as $lead) {
						$existing = Account::get('FB-'.$lead['id'], 'identifier');
						if (!$existing) {
							$account = Account::instanciate($type);
							$data = array();
							$data['identifier'] = 'FB-'.$lead['id'];
							$data['status'] = 'new';
							$data['origine'] = 'facebook';
							$data['opening_date'] = substr($lead['created_time'], 0, 10);
							$data['callback_date'] = date('Y-m-d');
							$data['date_1'] = date('Y-m-d');
							$rest = 'Facebook data:';
							foreach ($lead['field_data'] as $property) {
								if ($property['name'] == 'email') $data['email'] = $property['values'][0];
								elseif ($property['name'] == 'first_name') $data['n_first'] = $property['values'][0];
								elseif ($property['name'] == 'last_name') $data['n_last'] = $property['values'][0];
								elseif ($property['name'] == 'phone_number') $data['tel_cell'] = $property['values'][0];
								elseif ($property['name'] == 'street_address') $data['adr_street'] = $property['values'][0];
								elseif ($property['name'] == 'city') $data['adr_city'] = $property['values'][0];
								elseif ($property['name'] == 'post_code') $data['adr_zip'] = $property['values'][0];
								elseif ($property['name'] == 'country') $data['adr_country'] = $property['values'][0];
								else $rest .= (' '.$property['name'].': '.$property['values'][0]);
							}
							$data['contact_history'] = $rest;
							$rc = $account->loadAndAdd($data, Account::getConfig($type));
							echo ++$i.': '.$lead['id'].' '.$rc[0]."<br>\n";
						}
					}
					if (array_key_exists('next', $content['paging'])) $url = $content['paging']['next'];
					else $url = next($urls);
				}
				else $url = next($urls);
			}
			else $url = next($urls);
			
			if (!$url) break;
		}
		return $this->response;
	}
	
	/**
	 * Restfull implementation
	 * TODO : authorization + error description
	 */
	public function v1Action()
	{
		$context = Context::getCurrent();

		// Authentication
		if (!$context->isAuthenticated() && !$context->wsAuthenticate($this->getEvent())) {
			$this->getResponse()->setStatusCode('401');
			return $this->getResponse();
		}

		// Parameters
		$type = $this->params()->fromRoute('type');
		$perspective = $this->params()->fromRoute('perspective');
		$id = $this->params()->fromRoute('id');
		$identifier = $this->params()->fromQuery('identifier');
		$passphrase = $this->params()->fromQuery('passphrase'); // Deprecated

		$data = json_decode($this->request->getContent(), true);
		
		$content = array();
		$description = Account::getDescription('p-pit-studies');

		// Get
		if ($this->request->isGet()) {
			if ($id) {
				
				// Direct access mode
		    	$account = Account::get($id);
				if (!$account) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
		    	$content['data'] = $account->getProperties($type, $description);
			}
			else {

				// List mode
				$filters = array();
				foreach ($context->getConfig('core_account/search'.(($type) ? '/'.$type : ''))['properties'] as $propertyId => $unused) {
					$property = ($this->params()->fromQuery($propertyId, null));
					if ($property) $filters[$propertyId] = $property;
				}
		    	$limit = $this->params()->fromQuery('limit');
				$order = $this->params()->fromQuery('order', '+name');
		    	$page = $this->params()->fromQuery('page');
		    	$per_page = $this->params()->fromQuery('per_page');
		    	$statusDef = $context->getConfig('core_account/'.$type.'/property/status');
				if ($statusDef['definition'] != 'inline') $statusDef = $context->getConfig($statusDef['definition']);
		    	if (!array_key_exists('status', $filters)) $filters['status'] = implode(',', $statusDef['perspectives'][$perspective]);
		    	$accounts = Account::getList($type, $filters, $order, $limit, null, $page, $per_page);
		    	$content['data'] = array();
		    	foreach ($accounts as $account) $content['data'][$account->id] = $account->getProperties();
			}
		
			// Description
			$content['description'] = array();
			$content['description']['type'] = $type;
			$content['description']['properties'] = $description['properties'];
			$content['description']['list'] = $description['list'];
		}

		// Put
		elseif ($this->request->isPut()) {

			// Log the attempts to add an account
			$interaction = Interaction::instanciate();
			$reference = $context->getFormatedName().'_'.date('Y-m-d_H:i:s');
			$intData = array();
			$intData['type'] = 'web_service';
			$intData['category'] = ($type) ? $type : 'unknown';
			$intData['format'] = $this->getRequest()->getHeaders()->get('content-type')->getFieldValue();
			$intData['direction'] = 'input';
			$intData['route'] = 'account/v1';
			$intData['reference'] = $reference;
			$intData['content'] = $this->request->getContent();
			$interaction->loadData($intData);
			$interaction->add();

			if ($identifier) {
				$account = Account::get($identifier, 'identifier');
				if ($account) {
					$this->getResponse()->setStatusCode('400');
					$interaction->http_status = '400 - Existing account based on identifier';
					$interaction->update(null);
					echo json_encode(['Trial to create an account with an already existing identifier']);
					return $this->getResponse();
				}
			}
			$account = Account::instanciate($type);
			$requestTypes = $context->getConfig('core_account/requestTypes'.(($type) ? '/'.$type : ''));
			if ($requestTypes) {
		    	if (array_key_exists('request', $data) && array_key_exists($data['request'], $requestTypes)) {
		    		$requestType = $context->getConfig('core_account/requestTypes'.(($type) ? '/'.$type : ''))[$data['request']][$context->getLocale()];
		    	}
		    	else {
		    		$requestType = $context->getConfig('core_account/requestTypes'.(($type) ? '/'.$type : ''))['general_information'][$context->getLocale()];
		    	}
			}
			else $requestType = '';
	    	if (array_key_exists('comment', $data)) $requestComment = $data['comment'];
	    	else $requestComment = '';
    		$data['contact_history'] = 'Request: '.$requestType.' - Comment: '.$requestComment;

	    	// Database update
	    	$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
	    	$connection->beginTransaction();
	    	try {
				$rc = $account->loadAndAdd($data, $description['properties']);
	    		if ($rc[0] == '206') { // Partially accepted on an already existing account which is returned as rc[1]
					$interaction->http_status = '206 - ' . $rc[1];
					$interaction->update(null);
	    			$this->getResponse()->setStatusCode($rc[0]);
					$content['data'] = ['id' => $rc[1]];
					$connection->commit();
	    		}
				elseif ($rc[0] != '200') {
					$interaction->http_status = '200';
					$interaction->update(null);
					$this->getResponse()->setStatusCode($rc[0]);
				    $this->getResponse()->setReasonPhrase($rc[1]);
					$connection->rollback();
				    return $this->getResponse();
				}
				else {
					$content['data'] = ['id' => $rc[1]];
					$connection->commit();
				}
	    	}
			catch (\Exception $e) {
				$connection->rollback();
				return ['500', $rc];
			}
		}
		
		// Post
		elseif ($this->request->isPost()) {
			if ($identifier) $account = Account::get($identifier, 'identifier');
			else $account = Account::get($id);
			if (!$account) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}

			$data = json_decode($this->request->getContent(), true);
			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$rc = $account->loadAndUpdate($data, $description['properties']);
				if ($rc[0] != '200') {
					$connection->rollback();
					$this->getResponse()->setStatusCode($rc[0]);
					echo $rc[1];
					return $this->getResponse();
				}
				else $connection->commit();
			}
			catch (\Exception $e) {
				$connection->rollback();
				$this->getResponse()->setStatusCode('500');
				return $this->getResponse();
			}
		}

		// Delete
		elseif ($this->request->isDelete()) {
			if ($identifier) $account = Account::get($identifier, 'identifier');
			else $account = Account::get($id);
			if (!$account) {
				$this->getResponse()->setStatusCode('400');
				return $this->getResponse();
			}
			
			// Database update
			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				$rc = $account->delete(null);
				if ($rc != 'OK') {
					$this->getResponse()->setStatusCode('500');
					return $this->getResponse();
				}
				$connection->commit();
			}
			catch (\Exception $e) {
				$connection->rollback();
				$this->getResponse()->setStatusCode('500');
				return $this->getResponse();
			}
		}

		// Output
/*		if ($this->request->getHeader('Content-Type')) $contentType = $this->request->getHeader('Content-Type')->getFieldValue();
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
		elseif ($contentType == 'application/json') {*/
	       	ob_start("ob_gzhandler");
			echo json_encode($content, JSON_PRETTY_PRINT);
			ob_end_flush();
			return $this->response;
		}
//	}
}
