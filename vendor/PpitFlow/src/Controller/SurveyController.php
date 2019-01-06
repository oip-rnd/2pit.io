<?php
namespace PpitFlow\Controller;

use PpitContact\Model\ContactMessage;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Event;
use PpitCore\Model\GroupAccount;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SurveyController extends AbstractActionController
{
	public function fillAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$id = $this->params()->fromRoute('id');
		$token = $this->params()->fromQuery('hash', null);
		$survey = $this->params()->fromQuery('survey');
		$locale = $this->params()->fromQuery('locale');
		$availableSkills = $context->getConfig('matching/skills');

		// Retrieve the account and the survey in progress and anthenticate
		$event = Event::get($id);
		$description = Event::getDescription($event->type);
		$place = Place::get($event->place_id);
		$account = Account::get($event->account_id);
		if (!$locale) $locale = $event->locale;
		if (!$locale) $locale = $context->getLocale();
		if ($token != $event->authentication_token) return $this->redirect()->toRoute('landing/template2', ['place_identifier' => $place->identifier]);

		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') $content = $context->getConfig($survey.'/'.$place->identifier);
		else $content = Config::get($place->identifier.'_'.$survey, 'identifier')->content;
		$steps = explode(',', $event->description);
		$step = reset($steps); // Get the first step in the list
		if (!$step) $step = 'steps'; // As a default, show the form 'steps' that allows the user to choose the steps he wants to follow

		$content['form'] = $content['forms'][$step];
		if (!array_key_exists('options', $content['form'])) $content['form']['options'] = array();
		if (!array_key_exists('examples', $content['form']['options'])) $content['form']['options']['examples'] = false;
		
		$viewData = array();
		$viewData['account_id'] = $event->account_id;
		$viewData['photo_link_id'] = ($event->photo_link_id) ? $event->photo_link_id : 'no-photo.png';

		// Card
		foreach ($content['card'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$inputId];
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			$content['card'][$inputId] = $property;
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
			if ($id) {
				if ($inputId != $propertyId) $viewData[$inputId] = (in_array($property['value'], explode(',', $event->properties[$propertyId])) ? $property['value'] : null);
				elseif (array_key_exists($propertyId, $event->properties)) $viewData[$inputId] = $event->properties[$propertyId];
				$queryValue = $this->params()->fromQuery($inputId);
				if ($queryValue !== null) $viewData[$inputId] = $queryValue;
			}
			else $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;
		}
		
		// Form
		foreach ($content['form']['inputs'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$inputId];
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
				if (array_key_exists('rows', $options)) $property['rows'] = $options['rows'];
				if (array_key_exists('class', $options)) $property['class'] = $options['class'];
				if (array_key_exists('labels', $options)) $property['labels'] = $options['labels'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			$content['form']['inputs'][$inputId] = $property;
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
			if ($id) {
				if ($inputId != $propertyId) $viewData[$inputId] = (in_array($property['value'], explode(',', $event->properties[$propertyId])) ? $property['value'] : null);
				elseif (array_key_exists($propertyId, $event->properties)) $viewData[$inputId] = $event->properties[$propertyId];
				$queryValue = $this->params()->fromQuery($inputId);
				if ($queryValue !== null) $viewData[$inputId] = $queryValue;
			}
			else $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;
		}
		// Process the post data after input
		$message = $error = null;
		if ($this->request->isPost()) {
			$data = array();
			$data['status'] = 'completed';
			$data['place_id'] = $place->id;
			$data['account_id'] = $event->account_id;
			$data['category'] = $survey;
			$data['subcategory'] = $step;
			array_shift($steps); // Get the first step in the list
			$data['description'] = implode(',', $steps);
			
			$accountData = array();
			
			foreach ($content['form']['inputs'] as $inputId => $property) {
				if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
				else $propertyId = $inputId;
				if (!$id || $property['updatable']) {
					$viewData[$propertyId] = $this->request->getPost($propertyId);
					$viewData[$inputId] = $this->request->getPost($inputId);

					if ($property['type'] == 'checkbox') {
						if (array_key_exists($propertyId, $data) && $data[$propertyId]) {
							if ($viewData[$inputId]) $data[$propertyId] .= ','.$viewData[$inputId];
						}
						else $data[$propertyId] = $viewData[$inputId];
					}
					elseif ($property['type'] == 'chips') {
						$data[$propertyId] = '';
						foreach ($property['repository'] as $entryId => $unused) {
							$viewData[$inputId.'-'.$entryId] = $this->request->getPost($inputId.'-'.$entryId);
							if ($viewData[$inputId.'-'.$entryId]) {
								if (array_key_exists($propertyId, $data) && $data[$propertyId]) $data[$propertyId] .= ','.$entryId;
								else $data[$propertyId] = $entryId;
							}
						}
					}
					elseif ($property['type'] == 'date') { // Workaround due to a bug in MDBootstrap that ignores formatSubmit
						$data[$propertyId] = substr($viewData[$propertyId], 6, 4).'-'.substr($viewData[$propertyId], 3, 2).'-'.substr($viewData[$propertyId], 0, 2);
					}
					else $data[$propertyId] = $viewData[$propertyId];

					if (array_key_exists('account_property', $property)) $accountData[$property['account_property']] = $data[$propertyId];
				}
			}

			$event = Event::instanciate($content['type']);
			if (!$token) $token = md5(uniqid(rand(), true));
			$event->authentication_token = $token;
			$rc = $event->loadAndAdd($data, $description['properties']); // Duplicate the event for storing the current step's data 

			$id = $event->id;
			if (in_array($rc[0], ['200'])) {
				if ($accountData) {
					$rc = $account->loadAndUpdate($accountData);
					if (in_array($rc[0], ['200'])) $message = 'OK';
					else $error = $rc[1];
				}
				else $message = 'OK';
				if ($message == 'OK' && $event->description) return $this->redirect()->toRoute($this->getEvent()->getRouteMatch()->getMatchedRouteName(), ['id' => $event->id], array('query' => array('hash' => $token, 'survey' => $survey)));
			}
			else $error = $rc[1];
		}

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'place_identifier' => $place->identifier,
			'id' => $id,
			'token' => $token,
			'survey' => $survey,
			'locale' => $locale,
			'template' => [],
			'content' => $content,
			'viewData' => $viewData,
			'message' => $message,
			'error' => $error,
			'availableSkills' => $availableSkills,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function template1Action()
	{
		return $this->fillAction();
	}
	
	public function template2Action()
	{
		return $this->fillAction();
	}

	public function selectTestAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		$place_identifier = $this->params()->fromQuery('place_identifier');
		$place = Place::get($place_identifier, 'identifier');
		if (!$place) $place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		
		// Retrieve the available surveys and the signature to use in the emails
		$tests = array();
		foreach ($context->getConfig('flow/tests') as $testId => $testDefinition) {
			if ($context->getConfig('specificationMode') == 'config') $tests[$testId] = $context->getConfig($testDefinition)['header']['title'];
			else {
				$config = Config::get($place_identifier.'_'.$testId, 'identifier');
				if ($config) $tests[$testId] = $config->content['header']['title'];
			}
		}
    	$view = new ViewModel(array(
			'context' => $context,
			'tests' => $tests,
    	));
    	$view->setTerminal(true);
    	return $view;
	}
	
	public function newRequestAction()
	{
		$context = Context::getCurrent();
		$account = Account::get($context->getContactId(), 'contact_1_id');
		$place = Place::get($account->place_id);
		$vcard = Vcard::get($account->contact_1_id);

		// Retrieve the available surveys and the signature to use in the emails
		$tests = array();
		foreach ($context->getConfig('flow/tests') as $testId => $testDefinition) {
			if ($context->getConfig('specificationMode') == 'config') $tests[$testId] = $context->getConfig($testDefinition);
			else {
				$config = Config::get($place_identifier.'_'.$testId, 'identifier');
				if ($config) $tests[$testId] = $config->content;
			}
		}
		
		$eventData = array();
		$eventData['place_id'] = $place->id;
		$eventData['account_id'] = $account->id;
		$eventData['vcard_id'] = $account->contact_1_id; // Deprecated, the join is on the account id, not on the vcard id
		$eventData['category'] = 'test_request';
		$eventData['subcategory'] = 'steps';
		$eventData['description'] = 'requestor,opinion';
/*		foreach ($tests['test_request']['forms'] as $formId => $unused) {
			if ($eventData['description']) $eventData['description'] .= ',';
			$eventData['description'] .= $formId;
		}*/
		
		// Generate the root event (subcategory 'steps') that gives access to the test
		$event = Event::instanciate('course_test');
		$event->properties = $event->getProperties();
		$event->place_caption = $place->caption;
		$event->place_identifier = $place->identifier;
		$vcard = Vcard::get($account->contact_1_id);
		$event->n_fn = $vcard->n_fn;
		$event->n_first = $vcard->n_first;
		$event->n_last = $vcard->n_last;
		$event->email = $vcard->email;
		$event->photo_link_id = $vcard->photo_link_id;
		$event->locale = $vcard->locale;
		$event->authentication_token = md5(uniqid(rand(), true));
		$event->properties['authentication_token'] = $event->authentication_token;
		$rc = $event->loadAndAdd($eventData, Event::getConfigProperties('survey'));
		if ($rc['0'] != '200') {
			$eventConnection->rollback();
			$error = $rc;
		}
		return $this->redirect()->toRoute('survey/template2', ['id' => $event->id], ['query' => ['hash' => $event->authentication_token, 'survey' => 'test_request']]);
	}
	
	public function inviteToTestAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		$eventConfigProperties = Event::getConfigProperties('email');
		$place_identifier = $this->params()->fromQuery('place_identifier');
		$place = Place::get($place_identifier, 'identifier');
		if (!$place) $place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
	
    	// Retrieve the available surveys and the signature to use in the emails
		$tests = array();
		foreach ($context->getConfig('flow/tests') as $testId => $testDefinition) {
			if ($context->getConfig('specificationMode') == 'config') $tests[$testId] = $context->getConfig($testDefinition);
			else {
				$config = Config::get($place_identifier.'_'.$testId, 'identifier');
				if ($config) $tests[$testId] = $config->content;
			}
		}

		// Retrieve the selected email template and with the "from" value
		$selectedTestId = $this->params()->fromQuery('test_id');
		$selectedTest = $tests[$selectedTestId];
    	$signature = $context->getConfig('core_account/sendMessage')['signature'];
    	if ($signature['definition'] != 'inline') $signature = $context->getConfig($signature['definition']);

    	$accountIds = explode(',', $this->params()->fromQuery('accounts'));
    	$emails = array();
    	$error = null;
    	$eventConnection = Event::getTable()->getAdapter()->getDriver()->getConnection();
		$eventConnection->beginTransaction();
		$accounts = array();
		foreach ($accountIds as $account_id) {
			$account = Account::get($account_id);
			if ($account->type == 'group') {
				foreach (GroupAccount::getList(GroupAccount::getDescription('generic'), ['group_id' => $account->id]) as $groupAccount) {
					$accounts[$groupAccount->account_id] = ['n_first' => $groupAccount->n_first, 'n_last' => $groupAccount->n_last, 'email' => $groupAccount->email, 'n_fn' => $groupAccount->n_fn, 'locale' => $groupAccount->locale];
				}
			}
			else $accounts[$account->id] = ['n_first' => $account->n_first, 'n_last' => $account->n_last, 'email' => $account->email, 'n_fn' => $account->n_fn, 'locale' => $account->locale];
		}
		try {
	    	foreach ($accounts as $account_id => $account) {
	    		$data = array();
    			$data['n_fn'] = $account['n_fn'];
	    		$data['type'] = 'email';
	    		$data['to'] = array();
	    		$data['cci'] = array();
	    		if ($context->getConfig('core_account/mailTo')) {
	    			foreach ($context->getConfig('core_account/mailTo') as $toMail => $toName) $data['cci'][$toMail] = $toName;
	    		}
	    		else $data['to'][$account['email']] = $account['n_first'];
	    		
	    		$invitation = $selectedTest['invitation'];
	    		 
	    		if (array_key_exists('cci', $invitation)) $data['cci'][$invitation['cci']] = $invitation['cci'];
	    		$data['from_mail'] = $invitation['from_mail'];
	    		$data['from_name'] = $invitation['from_name'];
	    		$data['subject'] = $context->localize($invitation['subject'], $account['locale']);

	    		// Retrieve the text from the form if the email text is customizable in the view and add the signature at the location of the tag '%s'
    			$characters = ['à', 'À', 'â', 'Â', 'ä', 'Ä', 'é', 'É', 'è', 'È', 'ê', 'Ê', 'ë', 'Ë', 'î', 'Î', 'ï', 'Ï', 'ô', 'Ô', 'ö', 'Ö', 'ù', 'Ù', 'û', 'Û', 'ü', 'Ü', 'ç', 'Ç'];
    			$encoded = ['&agrave;', '&Agrave;', '&acirc;', '&Acirc;', '&auml;', '&Auml;', '&eacute;', '&Eacute;', '&egrave;', '&Egrave;', '&ecirc;', '&Ecirc;', '&euml;', '&Euml;', '&icirc;', '&Icirc;', '&iuml;', '&Iuml;', '&ocirc;', '&Ocirc;', '&ouml;', '&Ouml;', '&ugrave;', '&Ugrave;', '&ucirc;', '&Ucirc;', '&uuml;', '&Uuml;', '&ccedil;', '&Ccedil;'];
	    		$text = $context->localize($invitation['text'], $account['locale']);
	    		$text = str_replace($characters, $encoded, $text);
	    		$signatureBody = $context->localize($signature['body'], $account['locale']);
	    		
	    		$body = '';
	    		if ($context->getConfig('core_account/mailTo')) {
	    			$body .= $account['email'];
	    			$body .= '<br>';
	    		}
	    		
	    		$theme = $context->getConfig('core_account/sendMessage')['themes']['theme_2'];
	    		if ($theme['definition'] != 'inline') $theme = $context->getConfig($theme['definition']);
	    		$body .= sprintf($theme, $text, $signatureBody);

	    		$event = Event::get($selectedTest['type'], 'type', $account_id, 'account_id', $selectedTestId, 'category', 'steps', 'subcategory');
	    		if (!$event) {
		    		$eventData = array();
		    		$eventData['place_id'] = $place->id;
		    		$eventData['account_id'] = $account_id;
		    		$eventData['vcard_id'] = $account['contact_1_id']; // Deprecated, the join is on the account id, not on the vcard id
		    		$eventData['category'] = $selectedTestId;
		    		$eventData['subcategory'] = 'steps';
		    		$eventData['description'] = '';
		    		foreach ($selectedTest['forms'] as $formId => $unused) {
		    			if ($eventData['description']) $eventData['description'] .= ',';
		    			$eventData['description'] .= $formId;
		    		}
		    		 
		    		// Generate an authentication token that can be passed as a value for the variable 'authentication_token' in the text.
		    		// This token allows the email's addressee to access the target page as being authenticated
		
		    		// Generate the root event (subcategory 'steps') that gives access to the test
		    		$event = Event::instanciate('course_test');
	    			$event->properties = $event->getProperties();
		    		$event->place_caption = $place->caption;
			    	$event->place_identifier = $place->identifier;
			    	$vcard = Vcard::get($account['contact_1_id']);
			    	$event->n_fn = $vcard->n_fn;
			    	$event->n_first = $vcard->n_first;
			    	$event->n_last = $vcard->n_last;
			    	$event->email = $vcard->email;
			    	$event->photo_link_id = $vcard->photo_link_id;
			    	$event->locale = $vcard->locale;
		    		$event->authentication_token = md5(uniqid(rand(), true));
		    		$event->properties['authentication_token'] = $event->authentication_token;
		    		$rc = $event->loadAndAdd($eventData, Event::getConfigProperties('survey'));
		    		if ($rc['0'] != '200') {
		    			$eventConnection->rollback();
		    			$error = $rc;
		    		}
	    		}
				$renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
		    	$event->properties['base_path'] = 'https://'.$context->getInstance()->fqdn.$renderer->basePath('');

	    		// Replace all the variables in the text by their value in the account data structure
	    		// And replace the 'link' variable to the URL to Dropbox
	    		if (array_key_exists('params', $invitation)) {
	    			$arguments = array();
	    			foreach ($invitation['params'] as $param) {
	    				$arguments[] = htmlentities($event->properties[$param]);
	    			}
	    			$body = vsprintf($body, $arguments);
	    		}
	    		else $body = sprintf($body, $link);
	    		$data['body'] = $body;
	    		$emails[$account_id] = $data;
	    	}
	    	if (!$error) $eventConnection->commit();
		}
		catch (\Exception $e) {
			$eventConnection->rollback();
			throw $e;
		}

    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	
    	// Process the posted form
    	$request = $this->getRequest();
    	if (!$error && $request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		if ($csrfForm->isValid()) { // CSRF check
				foreach ($emails as $data) {
	    			$mail = ContactMessage::instanciate();
					$mail->type = 'email';
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
    	}

    	// Return the view
    	$view = new ViewModel(array(
    			'context' => $context,
    			'tests' => $tests,
    			'selectedTestId' => $selectedTestId,
    			'emails' => $emails,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
	public function patchAction()
	{
		$events = Event::getList('course_test', ['subcategory' => 'contributor']);
		foreach ($events as $event) {
			$account = Account::get($event->account_id);
			if ($account) {
				$updated = false;
				if (!$account->json_property_1 && $event->property_1) {
					$account->json_property_1 = $event->property_1;
					$updated = true;
				}
				if (!$account->property_2 && $event->property_2) {
					$account->property_2 = $event->property_2;
					$updated = true;
				}
				if (!$account->property_15 && $event->property_16) {
					$account->property_15 = $event->property_16;
					$updated = true;
				}
				if (!$account->property_16 && $event->property_17) {
					$account->property_16 = $event->property_17;
					$updated = true;
				}
				if ($updated) {
					echo 'Account: '.$account->properties['n_fn']."\n";
//					$account->update(null);
				}
			}
		}
		return $this->response;
	}
}
