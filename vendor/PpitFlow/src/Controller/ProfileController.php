<?php
namespace PpitFlow\Controller;

use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\Term;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\AccountSource;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Event;
use PpitCore\Model\EventSource;
use PpitCore\Model\Place;
use PpitCore\Model\User;
use PpitCore\Model\UserSource;
use PpitCore\Model\UserContact;
use PpitCore\Model\UserContactSource;
use PpitCore\Model\Vcard;
use PpitCore\Model\VcardSource;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{
	public function registerAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$account_id = $this->params()->fromRoute('account_id');
		$locale = $this->params()->fromQuery('locale');
	
		// Place
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
	
		// Account
		$accountType = $context->getConfig('landing_account_type');
		$account = null;
		if ($account_id) $account = Account::get('account_id');
		else {
			$account = Account::instanciate($accountType);
		
			// pre-filled form
			$account->email = $this->params()->fromQuery('email');
			$account->n_first = $this->params()->fromQuery('n_first');
			$account->n_last = $this->params()->fromQuery('n_last');
		}
	
		// Data description
		$description = Account::getConfig($accountType);
	
		// Configuration
		$content = null;
		if ($context->getConfig('specificationMode') == 'database') {
			$config = Config::get($place_identifier.'_contact', 'identifier');
			if ($config) $content = $config->content;
		}
		if (!$content) $content = $context->getConfig('contact/'.$place_identifier);
		if (!$content) $content = $context->getConfig('contact/generic');
	
		// CSRF protection
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		
		// Process the post data after input
		$actionStatus = null;
		if ($this->request->isPost()) {
	
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->request->getPost());
			 
			if ($csrfForm->isValid()) { // CSRF check
				
				// Atomicity
				$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
				$connection->beginTransaction();
				try {
					$data = array();
					$data['status'] = 'candidate';
					$data['email'] = $this->request->getPost('email');
					$data['n_first'] = $this->request->getPost('n_first');
					$data['n_last'] = $this->request->getPost('n_last');
					$data['password'] = $this->request->getPost('password');
					$data['locale'] = $this->request->getPost('locale');
					$data['origine'] = $this->request->getPost('origine');
						
					$actionStatus = $account->loadAndAdd($data, $description);
					if ($actionStatus[0] == '206') $account = Account::get($actionStatus[1]);
					$content['data'] = $account->getProperties();
					$account->status = 'registered';
					$account->update(null);

					if (!in_array($actionStatus[0], ['200', '206'])) $connection->rollback();
					else {
	
						// Check that the user does not already exist
						$user = User::getTable()->transGet($account->email, 'username');
						if ($user) $actionStatus = ['206', 'Already exist with email address'];
						else $actionStatus = ['200', 'OK'];
					}
					if ($actionStatus[0] == '200') {

						// Check that the email address belongs to the accepted domains
						$domain = explode('@', $data['email'])[1];
						if ($context->getConfig('user/acceptedRegistrationDomain') && !in_array($domain, $context->getConfig('user/acceptedRegistrationDomain'))) {
							$actionStatus = ['401', 'Unauthorized domain'];
							$connection->rollback();
						}
							
						else {

							// Create a user account
							$user_id = $context->getSecurityAgent()->register($account->email, $account->contact_1_id, $data['password']);
							$user = User::getTable()->transGet($user_id);
							$token = $context->getSecurityAgent()->requestAuthenticationToken($user->username, false);

							// Generate a commitment if a funnel is defined and a shopping cart exists
							$funnel = null;
							if ($context->getConfig('specificationMode') == 'database') {
								$config = Config::get($place->identifier.'_funnel', 'identifier', $place->id);
								if ($config) $funnel = $config->content;
							}
							if (!$funnel) $funnel = $context->getConfig('funnel/'.$place_identifier);
							$shopping_cart = ($funnel) ? $this->params()->fromQuery('shopping_cart') : null;

							if ($shopping_cart) {
								$orderedItems = explode(',', $shopping_cart);
								foreach ($orderedItems as $item) {
									$item = explode(':', $item);
								}
								// Todo: generate an option row by item in commitment
								$product = $funnel['catalogue'][$item[0]];
								$commitment = Commitment::instanciate($funnel['type']);
								$amount = round($item[1] * $product['unit_price'], 2);
								$data = array(
									'account_id' => $account->id,
									'caption' => $product['caption'],
									'product_caption' => $product['caption'],
									'quantity' => $item[1],
									'unit_price' => $product['unit_price'],
									'amount' => $amount,
								);
								$actionStatus = $commitment->loadAndAdd($data, Commitment::getConfig('generic'));
								if ($actionStatus[0] == '200') {
									
									// Generate the term
									$term = Term::instanciate($funnel['type']);
									$data = array(
										'commitment_id' => $commitment->id,
										'caption' => $context->localize($funnel['term_caption']),
										'due_date' => date('Y-m-d'),
										'amount' => $amount,
										'means_of_payment' => 'bank_card',
									);
									$actionStatus = $term->loadAndAdd($data);
									if ($actionStatus[0] == '200') {
										$account->shopping_cart = $commitment->id;
										$account->update(null);
									}
								}
							}

							if ($actionStatus[0] == '200') $connection->commit();
							else $connection->rollback();
						}
					}
				}
				catch (\Exception $e) {
					$connection->rollback();
					$actionStatus = ['500'];
				}
			
				if ($actionStatus[0] == '200') {

					// Send the OTP by email
					$email_body = $context->localize($context->getConfig('user/messages/activation/text'));
					$link = $this->url()->fromRoute('user/v1', ['id' => $user->user_id], ['force_canonical' => true]).'?account_id='.$account->id.'&request=activate'.'&hash='.$token;
					$email_body = sprintf($email_body, $link);
					$email_title = $context->localize($context->getConfig('user/messages/activation/title'));
					Context::sendMail($user->username, $email_body, $email_title, null);
				}
			}
		}

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'accountType' => $accountType,
			'content' => $content,
			'account' => $account,
			'csrfForm' => $csrfForm,
			'actionStatus' => $actionStatus,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function requestActivationAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$locale = $this->params()->fromQuery('locale');
	
		// Place
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
	
		$email = $this->params()->fromQuery('email');
		
		// CSRF protection
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
	
		// Process the post data after input
		$actionStatus = null;
		if ($this->request->isPost()) {
	
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->request->getPost());
			 
			if ($csrfForm->isValid()) { // CSRF check
				
				$email = $this->request->getPost('email');
				$data = array();
				$data['email'] = $email;

				$vcard = Vcard::get($email, 'email');
				if (!$vcard) $actionStatus = ['400', 'Contact not found'];
				else {
					$account = Account::get($vcard->id, 'contact_1_id');
					if (!$account) $actionStatus = ['400', 'Unregistered user'];
					else {
						$userContact = UserContact::get($vcard->id, 'vcard_id');
						$user = User::getTable()->transGet($userContact->user_id);
						$token = $context->getSecurityAgent()->requestAuthenticationToken($user->username, false);
						
						// Send the OTP by email
						$email_body = $context->localize($context->getConfig('user/messages/activation/text'));
						$link = $this->url()->fromRoute('user/v1', ['id' => $user->user_id], ['force_canonical' => true]).'?account_id='.$account->id.'&request=activate'.'&hash='.$token;
						$email_body = sprintf($email_body, $link);
						$email_title = $context->localize($context->getConfig('user/messages/activation/title'));
						Context::sendMail($user->username, $email_body, $email_title, null);
						
						$actionStatus = ['200', 'OK'];
					}
				}
			}
		}

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'email' => $email,
			'csrfForm' => $csrfForm,
			'actionStatus' => $actionStatus,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function loginAction()
	{
		$context = Context::getCurrent();
		$locale = $this->params()->fromQuery('locale');
		$identity = $this->params()->fromQuery('identity');
		$redirect = $this->params()->fromQuery('redirect');
		
		// CSRF protection
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		
		$actionStatus = null;
		if ($this->request->isPost()) {
	
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->request->getPost());
			 
			if ($csrfForm->isValid()) { // CSRF check
	
				// Check if user is not revoked
				$identity = $this->request->getPost('identity');
				
				// Check that the user has an account on the current instance
				$user = User::get($identity, 'username');
				if (!$user) {
					$actionStatus = ['401', 'Unauthorized'];
		    		$this->getResponse()->setStatusCode('401');
				}
				else {
					$userContact = UserContact::getTable()->transGet($context->getInstanceId(), 'instance_id', $user->user_id, 'user_id');
					if ($userContact && $user->vcard_id != $userContact->vcard_id) {
						$user->vcard_id = $userContact->vcard_id;
						$user->update(null);
					}
/*					if (!$userContact) {
						$accountType = $context->getConfig('landing_account_type');
						$vcard = Vcard::getTable()->transGet($user->vcard_id);
						$vcard->id = null;
						$vcard->add();
						$account = Account::instanciate($accountType);
						$data = array();
						$data['status'] = 'interested';
						$data['email'] = $vcard->email;
						$rc = $account->loadAndAdd($data);
						if ($rc[0] == '206') $account = Account::get($rc[1]);
						$userContact = UserContact::instanciate();
				    	$userContact->user_id = $user->user_id;
				    	$userContact->vcard_id = $vcard->id;
				    	if ($userContact->add() != 'OK') throw new \Exception();
					}
					if ($user->vcard_id != $userContact->vcard_id) {
						$user->instance_id = $context->getInstanceId();
						$user->vcard_id = $userContact->vcard_id;
						$user->update(null);
					}*/
				}

				if (!$actionStatus) {
					$credential = $this->request->getPost('credential');
					$rc = $context->getSecurityAgent()->authenticate($identity, $credential);
					if ($rc == 'OK') {
						if ($redirect) {
							return $this->redirect()->toRoute($redirect, array(), array('query' => $this->params()->fromQuery()));
						}
						else return $this->redirect()->toRoute('home');
					}
					else if ($rc == 'Activation') {
						$actionStatus = ['401', 'Activation'];
						$this->getResponse()->setStatusCode('401');
						$this->getResponse()->setReasonPhrase('Activation');
					}
					else {
						$actionStatus = ['401', 'Unauthorized'];
			    		$this->getResponse()->setStatusCode('401');
					}
				}
			}
		}
		$view = new ViewModel(array(
			'context' => Context::getCurrent(),
			'locale' => $locale,
			'identity' => $identity,
			'csrfForm' => $csrfForm,
			'actionStatus' => $actionStatus,
		));
		$view->setTerminal(true);
		return $view;
	}
	
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
		
		$accountType = $context->getConfig('landing_account_type');
		$accounts = Account::getList($accountType, ['status' => 'active'], '+name', null);
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
			'accountType' => $accountType,
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
					
					// Exclude from the potential matching list myself and the already matched accounts
					if ($account_id != $myAccountId && (!$request->matched_accounts || !in_array($account_id, explode(',', $request->matched_accounts)))) {
						$accounts[$account_id] = $account->getProperties();
					}
				}
			}
		}
		else {
			$result = Account::getList('pbc', ['status' => 'active'], '+name', null);
			foreach ($result as $account_id => $account) {
				
				// Exclude from the potential matching list myself
				if ($account_id != $myAccountId) {
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
	
	public function importAction() {
		$context = Context::getCurrent();
		$vcards = VcardSource::getList(null, []);
		foreach ($vcards as $vcardSource) {
			// A compléter
		}
	}
}
