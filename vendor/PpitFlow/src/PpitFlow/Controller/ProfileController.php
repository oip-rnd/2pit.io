<?php
namespace PpitFlow\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Event;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{
	public function indexAction()
	{
		return $this->template1Action();
	}
	
	public function homeAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = Place::get($context->getPlaceId());
			$place_identifier = $place->identifier;
		}

		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('profile/'.$place_identifier);
			if (!$content) $content = $context->getConfig('profile/generic');
		}
		else $content = Config::get($place_identifier.'_profile', 'identifier')->content;
		$locale = $this->params()->fromQuery('locale');
		
		$account_id = $this->params()->fromRoute('account_id');
		$account = null;
		if ($account_id) {
			$account = Account::get($account_id);
			$charter_status = $account->getCharterStatus();
			$gtou_status = $account->getGtouStatus();
		}
		elseif ($context->isAuthenticated()) {
			$account = Account::get($context->getContactId(), 'contact_1_id');
			$charter_status = $account->getCharterStatus();
			$gtou_status = $account->getGtouStatus();
		}
		else {
			$account = Account::instanciate($context->getConfig('landing_account_type'));
			$charter_status = null;
			$gtou_status = null;
		}

		if (!$locale) if ($account) $locale = $account->locale; else $locale = $context->getLocale();

		$description = Event::getDescription('request');
		$viewData = array();
		$viewData['photo_link_id'] = ($account->photo_link_id) ? $account->photo_link_id : 'no-photo.png';
		
		$accounts = Account::getList('pbc', ['status' => 'active'], '+name', null);
		$viewData['accounts'] = array();
		foreach ($accounts as $accountId => $account) $viewData['accounts'][$accountId] = $account->getProperties();

		// Feed the layout
		$this->layout('/layout/flow-layout');
		$this->layout()->setVariables(array(
			'context' => $context,
			'place_identifier' => $place_identifier,
			'account_id' => $account->id,
			'panel' => $this->params()->fromQuery('panel', null),
			'token' => $this->params()->fromQuery('hash', null),
			'accountType' => $context->getConfig('landing_account_type'),
			'header' => $content['header'],
			'intro' => $content['intro'],
			'footer' => $content['footer'],
			'locale' => $locale,
//			'photo_link_id' => ($account) ? $account->photo_link_id : null,
			'charter_status' => $charter_status,
			'gtou_status' => $gtou_status,
			'pageScripts' => 'ppit-flow/profile/home-scripts',
			'tooltips' => $content['tooltips'],
			'message' => null,
			'error' => null,
		));

		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'description' => $description,
			'viewData' => $viewData,
			'accounts' => $accounts,
			'content' => $content,
		));
		return $view;
	}
	
	public function listAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = Place::get($context->getPlaceId());
			$place_identifier = $place->identifier;
		}
		$request_id = $this->params()->fromRoute('request_id');
		
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('profile/'.$place_identifier);
			if (!$content) $content = $context->getConfig('profile/generic');
		}
		else $content = Config::get($place_identifier.'_profile', 'identifier')->content;
		$locale = $this->params()->fromQuery('locale');

		$description = Account::getDescription('pbc');
		foreach ($content['list']['properties'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$inputId];
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
				if (array_key_exists('focused', $options)) $property['focused'] = $options['focused'];
				if (array_key_exists('protected', $options)) $property['protected'] = $options['protected'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			if (!array_key_exists('focused', $property)) $property['focused'] = false;
			if (!array_key_exists('protected', $property)) $property['protected'] = false;
			$content['list']['properties'][$inputId] = $property;
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
		}
		
		$myAccountId = Account::get($context->getContactId(), 'contact_1_id')->id;
		$account_id = $this->params()->fromRoute('account_id');
		$account = null;
		if ($account_id) {
			$account = Account::get($account_id);
			$charter_status = $account->getCharterStatus();
			$gtou_status = $account->getGtouStatus();
		}
		elseif ($context->isAuthenticated()) {
			$account = Account::get($context->getContactId(), 'contact_1_id');
			$charter_status = $account->getCharterStatus();
			$gtou_status = $account->getGtouStatus();
		}
		else {
			$account = Account::instanciate($context->getConfig('landing_account_type'));
			$charter_status = null;
			$gtou_status = null;
		}
	
		if (!$locale) if ($account) $locale = $account->locale; else $locale = $context->getLocale();
	
		$viewData = array();
		$viewData['photo_link_id'] = ($account->photo_link_id) ? $account->photo_link_id : 'no-photo.png';

		$accounts = array();
		if ($request_id) {
			$request = Event::get($request_id);
			$skills = $request->property_2;
			foreach (explode(',', $skills) as $skill) {
				$result = Account::getList('pbc', ['status' => 'active', 'profile_tiny_2' => $skill], '+name', null);
				foreach ($result as $account_id => $account) {
					
					// Exclude from the potential matching list myself aand the already matched accounts
					if ($account_id != $myAccountId && (!$request->matched_accounts || !in_array($account_id, explode(',', $request->matched_accounts)))) {
						$accounts[$account_id] = $account->getProperties();
					}
				}
			}
		}
		else {
			$result = Account::getList('pbc', ['status' => 'active'], '+name', null);
			foreach ($result as $account_id => $account) {
				
				// Exclude from the potential matching list myself aand the already matched accounts
				if ($account_id != $myAccountId && (!$request->matched_accounts || !in_array($account_id, explode(',', $request->matched_accounts)))) {
					$accounts[$account_id] = $account->getProperties();
				}
			}
		}
	
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'description' => $description,
			'accounts' => $accounts,
			'content' => $content,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function detailAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = Place::get($context->getPlaceId());
			$place_identifier = $place->identifier;
		}
		$request_id = $this->params()->fromRoute('request_id');
		$request = Event::get($request_id);
		$account_id = $this->params()->fromRoute('account_id');
		$account = Account::get($account_id);
		$matched = (in_array($account_id, explode(',', $request->matched_accounts)));
		$actionIds = $this->params()->fromQuery('actions');
		if ($actionIds) $actionIds = explode(',', $actionIds);
		else $actionIds = array();

		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('profile/'.$place_identifier);
			if (!$content) $content = $context->getConfig('profile/generic');
		}
		else $content = Config::get($place_identifier.'_profile', 'identifier')->content;
		$locale = $this->params()->fromQuery('locale');
		if (!$locale) if ($account) $locale = $account->locale; else $locale = $context->getLocale();
		
		$description = Account::getDescription('pbc');
		foreach ($content['list']['properties'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$inputId];
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
				if (array_key_exists('focused', $options)) $property['focused'] = $options['focused'];
				if (array_key_exists('protected', $options)) $property['protected'] = $options['protected'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			if (!array_key_exists('focused', $property)) $property['focused'] = false;
			if (!array_key_exists('protected', $property)) $property['protected'] = false;
			$content['list']['properties'][$inputId] = $property;
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
		}

		$actions = array();
		foreach ($actionIds as $actionId) $actions[$actionId] = $content['detail']['actions'][$actionId];
		$content['detail']['actions'] = $actions;

		$viewData = $account->getProperties();
		$viewData['photo_link_id'] = ($account->photo_link_id) ? $account->photo_link_id : 'no-photo.png';
	
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'description' => $description,
			'request_id' => $request_id,
			'request_caption' => $request->caption,
			'account_id' => $account_id,
			'account' => $viewData,
			'matched' => $matched,
			'content' => $content,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function updateAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$description = Account::getDescription('pbc');
		$instance_caption = $context->getInstance()->caption;
		$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = Place::get($context->getPlaceId());
			$place_identifier = $place->identifier;
		}
		$account = Account::get($context->getContactId(), 'contact_1_id');
		$locale = $this->params()->fromQuery('locale');
	
		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('profile/'.$place_identifier);
			if (!$content) $content = $context->getConfig('profile/generic');
		}
		else $content = Config::get($place_identifier.'_profile', 'identifier')->content;
	
		$profile = Vcard::get($context->getContactId());
	
		$viewData = array();
		$viewData['profile_id'] = $profile->id;
		$viewData['photo_link_id'] = ($profile->photo_link_id) ? $profile->photo_link_id : 'no-photo.png';
	
		foreach ($content['form']['inputs'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$inputId];
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
				if (array_key_exists('focused', $options)) $property['focused'] = $options['focused'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			if (!array_key_exists('focused', $property)) $property['focused'] = false;
			$content['form']['inputs'][$inputId] = $property;
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
			//			if ($id) {
			if ($inputId != $propertyId) $viewData[$inputId] = (in_array($property['value'], explode(',', $profile->properties[$propertyId])) ? $property['value'] : null);
			else $viewData[$inputId] = $profile->properties[$propertyId];
			$queryValue = $this->params()->fromQuery($inputId);
			if ($queryValue !== null) $viewData[$inputId] = $queryValue;
			/*			}
				else $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;*/
		}

		// An incentive to complete the profil is displayed if the profile is empty
		if ($account->properties['completeness'] && $account->properties['completeness'] != '0_not_completed') $content['form']['incentive'] = null;
		
		// Process the post data after input
		$message = $error = null;
		if ($this->request->isPost()) {
			$data = array();
			$data['status'] = 'basic';
			$data['place_id'] = $place->id;
			foreach ($content['form']['inputs'] as $inputId => $property) {
				if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
				else $propertyId = $inputId;
				if ($property['updatable']) {
					if ($property['type'] != 'chips') {
						$viewData[$propertyId] = $this->request->getPost($propertyId);
						$viewData[$inputId] = $this->request->getPost($inputId);
					}
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
						$viewData[$propertyId] = $data[$propertyId]; // Updating the data to display in the confirmation form
					}
					elseif ($property['type'] == 'date') { // Workaround due to a bug in MDBootstrap that ignores formatSubmit
						$data[$propertyId] = substr($viewData[$propertyId], 6, 4).'-'.substr($viewData[$propertyId], 3, 2).'-'.substr($viewData[$propertyId], 0, 2);
					}
					else $data[$propertyId] = $viewData[$propertyId];
	
					if (array_key_exists('profile_property', $property)) $profileData[$property['profile_property']] = $data[$propertyId];
				}
			}
	
			$rc = $profile->loadAndUpdate($data, $content['form']['inputs']);
	
			if (in_array($rc[0], ['200'])) $message = 'OK';
			else $error = $rc;
		}
	
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'content' => $content,
			'viewData' => $viewData,
			'message' => $message,
			'error' => $error,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function template1Action() // Deprecated
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$description = Account::getDescription('pbc');

		$instance_caption = $context->getInstance()->caption;
		$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = Place::get($context->getPlaceId());
			$place_identifier = $place->identifier;
		}
		$account = Account::get($context->getContactId(), 'contact_1_id');
		$charter_status = $account->getCharterStatus();
		$gtou_status = $account->getGtouStatus();

		$links = $context->getConfig('public/'.$instance_caption.'/links');
		$locale = $this->params()->fromQuery('locale');
		
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('profile/'.$place_identifier);
			if (!$content) $content = $context->getConfig('profile/generic');
		}
		else $content = Config::get($place_identifier.'_profile', 'identifier')->content;

		$profile = Vcard::get($context->getContactId());

		$viewData = array();
		$viewData['profile_id'] = $profile->id;
		$viewData['photo_link_id'] = ($profile->photo_link_id) ? $profile->photo_link_id : 'no-photo.png';
		
		foreach ($content['form']['inputs'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$inputId];
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
				if (array_key_exists('focused', $options)) $property['focused'] = $options['focused'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			if (!array_key_exists('focused', $property)) $property['focused'] = false;
			$content['form']['inputs'][$inputId] = $property;
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
//			if ($id) {
				if ($inputId != $propertyId) $viewData[$inputId] = (in_array($property['value'], explode(',', $profile->properties[$propertyId])) ? $property['value'] : null);
				else $viewData[$inputId] = $profile->properties[$propertyId];
				$queryValue = $this->params()->fromQuery($inputId);
				if ($queryValue !== null) $viewData[$inputId] = $queryValue;
/*			}
			else $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;*/
		}

		// Process the post data after input
		$message = $error = null;
		if ($this->request->isPost()) {
			$data = array();
			$data['status'] = 'basic';
			$data['place_id'] = $place->id;
			foreach ($content['form']['inputs'] as $inputId => $property) {
				if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
				else $propertyId = $inputId;
				if ($property['updatable']) {
					if ($property['type'] != 'chips') {
						$viewData[$propertyId] = $this->request->getPost($propertyId);
						$viewData[$inputId] = $this->request->getPost($inputId);
					}
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
						$viewData[$propertyId] = $data[$propertyId]; // Updating the data to display in the confirmation form
					}
					elseif ($property['type'] == 'date') { // Workaround due to a bug in MDBootstrap that ignores formatSubmit
						$data[$propertyId] = substr($viewData[$propertyId], 6, 4).'-'.substr($viewData[$propertyId], 3, 2).'-'.substr($viewData[$propertyId], 0, 2);
					}
					else $data[$propertyId] = $viewData[$propertyId];

					if (array_key_exists('profile_property', $property)) $profileData[$property['profile_property']] = $data[$propertyId];
				}
			}

			$rc = $profile->loadAndUpdate($data, $content['form']['inputs']);

			if (in_array($rc[0], ['200'])) $message = 'OK';
			else $error = $rc;
		}

		// Requests

		if ($context->getConfig('specificationMode') == 'config') $requestContent = $context->getConfig('request/'.$place_identifier);
		else $requestContent = Config::get($place_identifier.'_request', 'identifier')->content;
		
		// Feed the layout
		$this->layout('/layout/flow-layout');
		$this->layout()->setVariables(array(
			'context' => $context,
			'place_identifier' => $place_identifier,
			'account_id' => $account->id,
			'panel' => $this->params()->fromQuery('panel', null),
			'token' => $this->params()->fromQuery('hash', null),
			'accountType' => $context->getConfig('landing_account_type'),
			'header' => $content['header'],
			'intro' => $content['intro'],
			'footer' => $content['footer'],
			'locale' => $locale,
			'photo_link_id' => ($account) ? $account->photo_link_id : null,
			'charter_status' => $charter_status,
			'gtou_status' => $gtou_status,
			'pageScripts' => 'ppit-flow/profile/profile-scripts',
			'form' => $content['form'],
			'tooltips' => $content['tooltips'],
			'message' => $message,
			'error' => $error,
			
			// Requests
			'requestContent' => $requestContent,
		));
		
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'content' => $content,
			'viewData' => $viewData,
			'message' => $message,
			'error' => $error,
		));
		return $view;
	}

	public function template2Action()
	{
		return $this->template1Action();
	}

	public function profileAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$account_id = $this->params()->fromRoute('account_id');
		$account = Account::get($account_id);
		$profile = Vcard::get($account->contact_1_id);
		$locale = $this->params()->fromQuery('locale');
		$currentRequestId = $this->params()->fromQuery('request');
		if ($currentRequestId) $currentRequest = Event::get($currentRequestId);
		else $currentRequest = null;

		// Look if the target account already belongs to my matched accounts, in which case protected data can be displayed
		$myAccount = Account::get($context->getContactId(), 'contact_1_id');
		$matchedAccounts = explode(',', $myAccount->property_2);
		if (in_array($account_id, $matchedAccounts)) $matched = true;
		else $matched = false;

		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('profile/'.$place_identifier);
			if (!$content) $content = $context->getConfig('profile/generic');
		}
		else $content = Config::get($place_identifier.'_profile', 'identifier')->content;
		
		$description = Account::getDescription('pbc');
		$viewData = array();
		$viewData['profile_id'] = $profile->id;
		$viewData['photo_link_id'] = ($profile->photo_link_id) ? $profile->photo_link_id : 'no-photo.png';
		
		foreach ($content['detail']['properties'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$inputId];
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
				if (array_key_exists('focused', $options)) $property['focused'] = $options['focused'];
				if (array_key_exists('protected', $options)) $property['protected'] = $options['protected'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			if (!array_key_exists('focused', $property)) $property['focused'] = false;
			if ($matched || !array_key_exists('protected', $property)) $property['protected'] = false;
			$content['detail']['properties'][$inputId] = $property;
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
			//			if ($id) {
			if ($inputId != $propertyId) $viewData[$inputId] = (in_array($property['value'], explode(',', $profile->properties[$propertyId])) ? $property['value'] : null);
			else $viewData[$inputId] = $profile->properties[$propertyId];
			$queryValue = $this->params()->fromQuery($inputId);
			if ($queryValue !== null) $viewData[$inputId] = $queryValue;
			/*			}
			 else $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;*/
		}
		
		$actions = array();
		if ($currentRequest) {
			if (in_array($account_id, explode(',', $currentRequest->matched_accounts))) {
				$currentMatching = $currentRequest->matching_log[$account_id];
				if ($currentMatching['action'] == 'propose') {
					$actions['accept'] = $content['detail']['actions']['accept'];
					$actions['decline'] = $content['detail']['actions']['decline'];
				}
				elseif (in_array($currentMatching['action'], ['accept', 'give_feedback'])) {
					if ($currentRequest->status == 'realized') $actions['feedback'] = $content['detail']['actions']['feedback'];
				}
				else {
					$actions['abandon'] = $content['detail']['actions']['abandon'];
				}
			}
			else $actions['contact'] = $content['detail']['actions']['contact'];
		}
		$content['detail']['actions'] = $actions;

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'content' => $content,
			'locale' => $locale,
			'viewData' => $viewData,
			'matched' => $matched,
		));
		$view->setTerminal(true);
		return $view;
	}
/*	
	public function contactAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$account_id = $this->params()->fromRoute('account_id');
		$otherAccount = Account::get($account_id);
		$locale = $this->params()->fromQuery('locale');
		
		// Mark the other account as matched in the requests in basket
		$myProfile = Vcard::get($context->getContactId());
		if (array_key_exists('request_basket', $myProfile->specifications)) {
			foreach ($myProfile->specifications['request_basket'] as $requestId => $unused) {
				$request = Event::get($requestId);
				$matchedAccounts = explode(',', $request->matched_accounts);
				$matchedAccounts[] = $account_id;
				$request->matchedAccounts = implode(',', $matchedAccounts);
				$request->log[] = array(
					'time' => Date('Y-m-d G:i:s'),
					'user_id' => $context->getUserId(),
					'n_fn' => $context->getFormatedName(),
					'action' => 'profile/contact',
					'matched_account' => $account_id,
				);
				$request->update(null);
			}
		}
		
		// Mark the other account as matched in my account
		$myAccount = Account::get($context->getContactId(), 'contact_1_id');
		$matchedAccounts = explode(',', $myAccount->property_2);
		$matchedAccounts[] = $account_id;
		$myAccount->property_2 = implode(',', $matchedAccounts);
		$myAccount->update(null);
		return $this->response;
	}
*/
	public function removeContactAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$account_id = $this->params()->fromRoute('account_id');
		$otherAccount = Account::get($account_id);
		$locale = $this->params()->fromQuery('locale');
	
		// Mark the other account as unmatched in the requests in basket
		$myProfile = Vcard::get($context->getContactId());
		if (array_key_exists('request_basket', $myProfile->specifications)) {
			foreach ($myProfile->specifications['request_basket'] as $requestId => $unused) {
				$request = Event::get($requestId);
				$matchedAccounts = array(); 
				foreach(explode(',', $request->matched_accounts) as $item) {
					if ($item != $otherAccount->id) $matchedAccounts[] = $item;
				}
				$request->matched_accounts = implode(',', $matchedAccounts);
				$request->log[] = array(
					'time' => Date('Y-m-d G:i:s'),
					'user_id' => $context->getUserId(),
					'n_fn' => $context->getFormatedName(),
					'action' => 'profile/removeContact',
					'unmatched_account' => $otherAccount->id,
				);
				$request->update(null);
			}
		}
	
		// Mark the other account as unmatched in my account
/*		$myAccount = Account::get($context->getContactId(), 'contact_1_id');
		$matchedAccounts = explode(',', $myAccount->property_2);
		if (in_array($otherAccount->id, $matchedAccounts)) {
			$requests = Event::getList('request', ['account_id' => $myAccount->id], '-update_time', null);
			$accountIsMatched = false;
			foreach ($requests as $requestId => $request) {
				$requestMatchedAccounts = explode(',', $request->matched_accounts);
				if (array_key_exists($otherAccount->id, $requestMatchedAccounts)) $accountIsMatched = true;
			}
			if (!$accountIsMatched) {
				$matchedAccounts = array(); 
				foreach(explode(',', $myAccount->property_2) as $item) {
					if ($item != $otherAccount->id) $matchedAccounts[] = $item;
				}
				$myAccount->property_2 = implode(',', $matchedAccounts);
				$myAccount->update(null);
			}
		}*/
		return $this->response;
	}
	
	public function dashboardAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$account = Account::get($context->getContactId(), 'contact_1_id');
		$requests = Event::getList('request', ['status' => 'new,connected,realized,completed', 'account_id' => $account->id]);
		$locale = $this->params()->fromQuery('locale');
		
		$content = array();
		$content['description'] = Event::getDescription('request');
		$content['description']['list'] = ['property_5' => [], 'caption' => [], 'property_3' => []];
		$content['data'] = array();
		foreach ($requests as $request) $content['data'][$request->id] = $request->getProperties();

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'content' => $content,
			'locale' => $locale,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function photoUploadAction()
	{
		$context = Context::getCurrent();
		$id = $this->params()->fromRoute('id');
		$account = Account::get($id);
		if ($account) {
			$photoPath = $this->request->getFiles()->toArray()['photo_path'];
			$account->contact_1->savePhoto($photoPath);
			$account->contact_1->photo_link_id = $account->contact_1_id.'.jpg';
			$account->contact_1->update(null);
			echo $account->contact_1->photo_link_id;
		}
		return $this->response;
	}
}
