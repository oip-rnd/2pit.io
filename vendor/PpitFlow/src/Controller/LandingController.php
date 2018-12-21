<?php
namespace PpitFlow\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LandingController extends AbstractActionController
{
	public function template1Action()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', $context->getConfig('event_default_type'));
		$instance_caption = $context->getInstance()->caption;
		$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = Place::get($context->getPlaceId());
			$place_identifier = $place->identifier;
		}
		$shopping_cart = $this->params()->fromQuery('shopping_cart');
		
		// Content
		$content = null;
		if ($context->getConfig('specificationMode') == 'database') {
			$config = Config::get($place->identifier.'_landing', 'identifier', $place->id);
			if ($config) $content = $config->content;
		}
		if (!$content) $content = $context->getConfig('landing/'.$place_identifier);
		if (!$content) $content = $context->getConfig('landing/generic');
		
		$locale = $this->params()->fromQuery('locale');
		
		$accountType = $context->getConfig('landing_account_type');
		$account = null;
		if (!$shopping_cart) {
			if ($context->isAuthenticated()) {
				$account = Account::get($context->getContactId(), 'contact_1_id');
				$shopping_cart = $account->shopping_cart;
			}
			else {
				$funnel = null;
				if ($context->getConfig('specificationMode') == 'database') {
					$config = Config::get($place->identifier.'_funnel', 'identifier', $place->id);
					if ($config) $funnel = $config->content;
				}
				if (!$funnel) $funnel = $context->getConfig('funnel/'.$place_identifier);
				if ($funnel && array_key_exists('default_shopping_cart', $funnel)) $shopping_cart = $funnel['default_shopping_cart'];
			}
		}
		if (!$account) $account = Account::instanciate($accountType);

		// Profile form
		$profileForm = null;
		if ($context->getConfig('specificationMode') == 'database') {
			$config = Config::get($place_identifier.'_profile', 'identifier');
			if ($config) $profileForm = $config->content['form'];
		}
		if (!$profileForm) $profileForm = $context->getConfig('profile/'.$place_identifier)['form'];
		if (!$profileForm) $profileForm = $context->getConfig('profile/generic')['form'];
		$accountDescription = Account::getDescription($accountType);
		foreach ($profileForm['inputs'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $accountDescription['properties'][$inputId];
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
				if (array_key_exists('focused', $options)) $property['focused'] = $options['focused'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			if (!array_key_exists('focused', $property)) $property['focused'] = false;
			$profileForm['inputs'][$inputId] = $property;
		}
		
		// If an email is given as a parameter: Show the Login or Sign Up form depending of the account existing or not
		$panel = $this->params()->fromQuery('panel');
		$email = $this->params()->fromQuery('email');
		if ($email) {
			$vcard = Vcard::get($email, 'email');
			if ($vcard) {
				$userContact = UserContact::get($vcard->id, 'vcard_id');
				if ($userContact) $panel = 'modalLoginForm';
				else $panel = 'modalRegisterForm';
			}
			else $panel = 'modalRegisterForm';
		}
		if ($context->isAuthenticated() && !$panel) {
			if ($shopping_cart) $panel = 'payment';
		}

		if (!$locale) if ($account) $locale = $account->locale; else $locale = $context->getLocale();
		$links = $context->getConfig('public/'.$instance_caption.'/links');

		// To factorize to each main page
		$payPal = null;
		if (array_key_exists($context->getInstance()->caption, $context->getConfig()['ppitUserSettings']['safe'])) {
			$safeEnv = $context->getConfig()['ppitUserSettings']['safe'][$context->getInstance()->caption];
			if (array_key_exists('PayPal', $safeEnv)) $payPal = $safeEnv['PayPal'];
		}
		
		// Retrieve the content
		$survey = $this->params()->fromQuery('survey');
		$step = $this->params()->fromQuery('step');
		if ($survey) {
			if ($context->getConfig('specificationMode') == 'config') $content['form'] = $content['surveys'][$survey][$step];
			else $content['form'] = Config::get($place_identifier.'_survey_'.$survey, 'identifier')->content;
		}

		$viewData = array();
		$viewData['photo_link_id'] = ($account->photo_link_id) ? $account->photo_link_id : 'no-photo.png';
		if (array_key_exists('form', $content)) {
			foreach ($content['form']['inputs'] as $inputId => $options) {
				if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
				else {
					$property = $context->getConfig('core_account/'.$accountType.'/property/'.$inputId);
					if (!$property) $property = $context->getConfig('core_account/generic/property/'.$inputId);
					if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
					if (array_key_exists('class', $options)) $property['class'] = $options['class'];
					if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
					if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
				}
				if (!array_key_exists('class', $property)) $property['class'] = 'col-md-6';
				if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
				if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
				if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
				$content['form']['inputs'][$inputId] = $property;
				if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
				else $propertyId = $inputId;
	/*			if ($id) {
					if ($inputId != $propertyId) $viewData[$inputId] = (in_array($property['value'], explode(',', $account->properties[$propertyId])) ? $property['value'] : null);
					else $viewData[$inputId] = $account->properties[$propertyId];
					$queryValue = $this->params()->fromQuery($inputId);
					if ($queryValue !== null) $viewData[$inputId] = $queryValue;
				}
				else*/ $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;
			}
		}
		
		// Process the post data after input
		$message = null;
		$error = null;
		if ($this->request->isPost()) {
			$data = array();
			$data['status'] = 'interested';
			$data['place_id'] = $place->id;
			$data['callback_date'] = date('Y-m-d');
			foreach ($content['form']['inputs'] as $inputId => $property) {
//				if (!$id || $property['updatable']) {
					$viewData[$inputId] = $this->request->getPost($inputId);
					if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
					else $propertyId = $inputId;
					
					if (array_key_exists($propertyId, $data) && $data[$propertyId]) {
						if ($viewData[$inputId]) $data[$propertyId] .= ','.$viewData[$inputId];
					}
					else $data[$propertyId] = $viewData[$inputId];
//				}
			}
			$data['contact_history'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).', from '.$_SERVER['HTTP_REFERER'].', with '.$_SERVER['HTTP_USER_AGENT'].' filled landing page\'s form';

			$data['origine'] = 'subscription';
			$rc = $account->loadAndAdd($data, Account::getConfig($accountType));
			if (in_array($rc[0], ['200', '206'])) $message = 'OK';
			else $error = $rc;
		}

		// Feed the layout
		$this->layout('/layout/flow-layout');
		$this->layout()->setVariables(array(
			'context' => $context,
			'type' => $type,
			'panel' => $panel,
			'identity' => $email,
			'redirectRoute' => $this->params()->fromQuery('route'),
			'redirectParams' => '&type='.$this->params()->fromQuery('type').'&mode='.$this->params()->fromQuery('mode').'&action='.$this->params()->fromQuery('action').'&id='.$this->params()->fromQuery('id'),
			'token' => $this->params()->fromQuery('hash', null),
			'place_identifier' => $place_identifier,
			'account_id' => null,
			'accountType' => $context->getConfig('landing_account_type'),
			'header' => $content['header'],
			'intro' => $content['intro'],
			'form' => (array_key_exists('form', $content) && $content['form']) ? $content['form'] : false,
			'payment' => $context->getConfig('payment/'.$instance_caption),
			'payPal' => $payPal,
			'shopping_cart' => $shopping_cart,
			'footer' => $content['footer'],
			'locale' => $locale,
			'photo_link_id' => null,
			'profileForm' => $profileForm,
			'pageScripts' => 'ppit-flow/landing/scripts',
			'message' => ($message) ? $message : $this->params()->fromQuery('message'),
			'error' => ($error) ? $error : $this->params()->fromQuery('error'),
		));
		
		// Feed and return the view
		$view = new ViewModel(array(
			'context' => $context,
			'type' => $type,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'id' => null,
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
/*
	public function formAction()
	{
		// Context and parameters
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = Place::get($context->getPlaceId());
			$place_identifier = $place->identifier;
		}
		
		// Description
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('landing/'.$place_identifier);
			if (!$content) $content = $context->getConfig('landing/generic');
		}
		else $content = Config::get($place->identifier.'_landing', 'identifier', $place->id)->content;

		// Locale
		$locale = $this->params()->fromQuery('locale');
		if (!$locale) $locale = $context->getLocale();

		// id and account
		$id = $this->params()->fromRoute('id');
		$account_type = $context->getConfig('landing_account_type');
		if ($id) $account = Account::get($id);
		else $account = Account::instanciate($account_type);

		// Data view
		$viewData = array();
		foreach ($content['form']['inputs'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $context->getConfig('core_account/'.$account_type.'/property/'.$inputId);
				if (!$property) $property = $context->getConfig('core_account/generic/property/'.$inputId);
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if (array_key_exists('class', $options)) $property['class'] = $options['class'];
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
			}
			if (!array_key_exists('class', $property)) $property['class'] = 'col-md-6';
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			$content['form']['inputs'][$inputId] = $property;
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
			if ($id) {
				if ($inputId != $propertyId) $viewData[$inputId] = (in_array($property['value'], explode(',', $account->properties[$propertyId])) ? $property['value'] : null);
				else $viewData[$inputId] = $account->properties[$propertyId];
				$queryValue = $this->params()->fromQuery($inputId);
				if ($queryValue !== null) $viewData[$inputId] = $queryValue;
			}
			else $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;
		}

		// Process the post data after input
		$message = null;
		$error = null;
		if ($this->request->isPost()) {
			$data = array();
			$data['status'] = 'interested';
			$data['place_id'] = $place->id;
			$data['callback_date'] = date('Y-m-d');
			foreach ($content['form']['inputs'] as $inputId => $property) {
				if (!$id || $property['updatable']) {
					$viewData[$inputId] = $this->request->getPost($inputId);
					if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
					else $propertyId = $inputId;
					
					if (array_key_exists($propertyId, $data) && $data[$propertyId]) {
						if ($viewData[$inputId]) $data[$propertyId] .= ','.$viewData[$inputId];
					}
					else $data[$propertyId] = $viewData[$inputId];
				}
			}
			$data['contact_history'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).', from '.$_SERVER['HTTP_REFERER'].', with '.$_SERVER['HTTP_USER_AGENT'].' filled landing page\'s form';
			$data['origine'] = 'subscription';
			$rc = $account->loadAndAdd($data, Account::getConfig($account_type));
			if (in_array($rc[0], ['200', '206'])) $message = 'OK';
			else $error = $rc;
		}
		
		// Feed and return the view
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'id' => null,
			'content' => $content,
			'viewData' => $viewData,
			'message' => $message,
			'error' => $error,
		));
		$view->setTerminal(true);
		return $view;
	}*/
	
	public function checkoutAction()
	{
		return $this->template1Action();
	}
	
	public function testAction()
	{
		$view = new ViewModel(array());
		$view->setTerminal(true);
		return $view;
	}
}
