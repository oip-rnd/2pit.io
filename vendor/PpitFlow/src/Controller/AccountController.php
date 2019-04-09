<?php
namespace PpitFlow\Controller;

use PpitContact\Model\ContactMessage;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\GroupAccount;
use PpitCore\Model\Place;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use PpitCore\ViewHelper\SsmlAccountViewHelper;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\Url;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
	/**
	 * To adapt from flowEvent to flowAccount
	 * @param unknown $account
	 * @return string
	 */
	public function displayRank($account) 
	{
		$context = Context::getCurrent();
		
		// Rank the profiles
		$ranking = array();
		$cursor = Account::getList($account->type, [/*'status' => 'active'*/], '+name', null);
		foreach ($cursor as $anyAccountId => $anyAccount) {
			if ($anyAccount->credits) {
				foreach ($anyAccount->credits as $rowId => $value) {
					if ($rowId == 'earned') {
						$ranking[$anyAccountId] = $value;
					}
				}
			}
		}

		// Rank the participants and find my rank
		arsort($ranking);
		$ranks = array();
		$currentRank = 0;
		$currentWeight = 0;
		$i = 0;
		foreach($ranking as $account_id => $weight) {
			$i++;
			if ($currentWeight != $weight) {
				$currentRank = $i;
				$currentWeight = $weight;
				$ranks[$currentWeight] = 1;
			}
			else $ranks[$currentWeight]++;
			if ($ranking[$account->id] == $currentWeight) {
				$rank = $currentRank;
			}
		}

		// Add a sign to indicate my rank is shared with other participant
		if ($ranks[$ranking[$account->id]] > 1) $equalSign = '='; else $equalSign = '';
		switch ($rank % 10) {
			case 1: $ending = ($rank / 10) % 10 === 1 ?  "th" : "st"; break;
			case 2: $ending = ($rank / 10) % 10 === 1 ?  "th" : "nd"; break;
			case 3: $ending = ($rank / 10) % 10 === 1 ?  "th" : "rd"; break;
			default: $ending = "th";
		}
		return $equalSign . " " . (string)$rank . $ending;
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'generic');
		$mode = $this->params()->fromQuery('mode', 'Public');
		$action = $this->params()->fromQuery('action'); // For detail mode
		$id = $this->params()->fromQuery('id'); // For detail mode
		$description = Account::getDescription($type);
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$profile = Account::get($context->getContactId(), 'contact_1_id');
		$locale = $this->params()->fromQuery('locale');
		if (!$locale) if ($profile) $locale = $profile->locale; else $locale = $context->getLocale();

		$charter_status = $gtou_status = null;
		if ($profile) $gtou_status = $profile->getGtouStatus();

		$filters = array();
		foreach ($description['search']['properties'] as $propertyId => $unused) {
			$predicate = $this->params()->fromQuery($propertyId, null);
			if ($predicate !== null) $filters[$propertyId] = $predicate;
		}
		
		// Account template
//		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('account/'.$place_identifier);
			if (!$content) $content = $context->getConfig('account/generic');
/*		}
		else $content = Config::get($place_identifier.'_account', 'identifier')->content;*/

		// compute ranking in gaming mode
		if ($profile && array_key_exists('rewards', $content)) {
			if (array_key_exists('earned', $profile->credits)) $profile->credits['rank'] = $this->displayRank($profile);
			$profile->properties['credits'] = $profile->credits;
		}

		// Profile form
//		if ($context->getConfig('specificationMode') == 'config') {
			$profileForm = $context->getConfig('profile/'.$place_identifier)['form'];
			if (!$profileForm) $profileForm = $context->getConfig('profile/generic')['form'];
/*		}
		else $profileForm = Config::get($place_identifier.'_profile', 'identifier')->content['form'];*/
		foreach ($profileForm['inputs'] as $inputId => $options) {
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

		// Feed the layout
		$this->layout('/layout/flow-layout');
		$this->layout()->setVariables(array(
			'context' => $context,
			'type' => $type,
			'place_identifier' => $place_identifier,
			'account_id' => ($profile) ? $profile->id : null,
			'mode' => $mode,
			'queryAction' => $action,
			'queryId' => $id,
			'panel' => $panel,
			'identity' => $email,
			'redirectRoute' => $this->params()->fromQuery('route'),
			'redirectParams' => $this->params()->fromQuery('params'),
			'token' => $this->params()->fromQuery('hash', null),
			'header' => $content['header'],
			'index' => $content['index'],
			'intro' => $content['intro'],
			'actions' => $content['actions'],
			'detail' => $content['detail'],
			'footer' => $content['footer'],
			'tooltips' => $content['tooltips'],
			'locale' => $locale,
			'photo_link_id' => ($profile && $profile->photo_link_id != 'no-photo.png') ? $profile->photo_link_id : null,
			'profileForm' => $profileForm,
			'charter_status' => $charter_status,
			'gtou_status' => $gtou_status,
			'pageScripts' => 'ppit-flow/account/index-scripts',
			'filters' => $filters,
			'message' => null,
			'error' => null,
		));
	
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'type' => $type,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'profile' => ($profile) ? $profile->properties : [],
			'content' => $content,
		));
		return $view;
	}
	
	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\View\Model\ViewModel
	 */
	public function dashboardAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type');
		$description = Event::getDescription($type);
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$account = Account::get($context->getContactId(), 'contact_1_id');
		$locale = $this->params()->fromQuery('locale');
		if (!$locale) if ($account) $locale = $account->locale; else $locale = $context->getLocale();
		
		// Event content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place_identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place_identifier.'_'.$type, 'identifier')->content;
	
		// compute ranking in gaming mode
		if (array_key_exists('rewards', $content) && array_key_exists('goal', $account->credits)) {
			if (array_key_exists('earned', $account->credits)) $account->credits['rank'] = $this->displayRank($account);
			$account->properties['credits'] = $account->credits;
		}
	
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'type' => $type,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'account' => $account->properties,
			'content' => $content,
		));
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * 
	 * @param unknown $a
	 * @param unknown $b
	 * @return number
	 */
	public function compare($a, $b)
	{
		if ($a['rank'] == $b['rank']) {
			return 0;
		}
		return ($a['rank'] > $b['rank']) ? -1 : 1;
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'generic');
		$description = Account::getDescription($type);
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$profile = Account::get($context->getContactId(), 'contact_1_id');
		$locale = $this->params()->fromQuery('locale');
		$mode = $this->params()->fromQuery('mode', 'Owner');
		$filters = array();
		foreach ($description['search']['properties'] as $propertyId => $unused) {
			$predicate = $this->params()->fromQuery($propertyId, null);
			if ($predicate !== null) $filters[$propertyId] = $predicate;
		}

		// Retrieve the content
//		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('account/'.$place->identifier);
			if (!$content) $content = $context->getConfig('account/generic');
/*		}
		else $content = Config::get($place->identifier.'_account', 'identifier')->content;*/

		// Card
		foreach ($content['card']['properties'] as $propertyId => $options) {
			if (!array_key_exists('definition', $options) || $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$propertyId];
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if (array_key_exists('labels', $options)) $property['labels'] = $options['labels'];
				if (array_key_exists('format', $options)) $property['format'] = $options['format'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			$content['card']['properties'][$propertyId] = $property;
		}
		
		if (!$filters) $filters = ['status' => 'new'];
		$content['data'] = array();

		// Retrieve my accounts in owner mode
		if ($mode == 'Owner') $filters['place_id'] = $place->id;
		$accounts = Account::getList($type, $filters);
		
		// Rank the result according to search keywords and status
		$keywords = $this->params()->fromQuery('keywords');
		$ranking = array(
			'query:keywords' => [['matches', '%s', ['property_2'], 9000], ['matches', '%s', ['property_1'], 8000], ['matches', '%s', ['name'], 7000], ['matches', '%s', ['property_3'], 6000], ['matches', '%s', ['property_7'], 5000]],
			'account:status' => [['=', 'new', [], 900], ['=', 'interested', [], 800], ['=', 'engaged', [], 700], ['=', 'active', [], 600]],
		);
		foreach ($accounts as $account) {
			// Ranking
			$rank = 0;
			foreach ($ranking as $key => $rules) {
				$key = explode(':', $key);
				$entity = $key[0];
				$property = $key[1];
				if ($entity == 'account') $value = $account->properties[$property];
				elseif ($entity == 'query') $value = $this->params()->fromQuery($property);
				elseif ($profile && $entity == 'profile') $value = $profile->properties[$property];
				foreach ($rules as list($operator, $format, $parameters, $ponderation)) {
					$arguments = array();
					foreach ($parameters as $parameter) $arguments[] = $account->properties[$parameter];
					$operand = vsprintf($format, $arguments);
					if ($operator == '=' && $value == $operand) $rank += $ponderation;
					elseif ($operator == 'matches') {
						foreach (explode(',', $value) as $term) if (stripos($operand, $term) !== FALSE) $rank += $ponderation;
					}
					elseif ($operator == 'like') if (stripos($operand, $value) !== FALSE) $rank += $ponderation;
				}
			}
			$content['data'][$account->id] = $account->getProperties();
			$content['data'][$account->id]['rank'] = $rank;

			if ($mode == 'Owner') {

				// Compute next actions depending on the current status
				$actions = array();
				foreach ($content['status'][$account->status]['nextSteps'] as $nextId) {
					if (array_key_exists($nextId, $content['actions']['Owner'])) {
						$actions[$nextId] = $content['actions']['Owner'][$nextId];
					}
				}
				$content['data'][$account->id]['OwnerActions'] = $actions;
			
				// Retrieve the related commitments
				$cursor = Commitment::getList($context->getConfig('commitment_type'), ['account_id' => $account->id]);
				foreach ($cursor as $commitment_id => $commitment) {
						
					$commitments[$commitment_id] = $commitment->getProperties();
						
					// Compute the next commitment actions depending on the current commitment status
					$commitmentActions = array();
					foreach ($content['commitments']['status'][$commiment->status]['nextSteps'] as $nextId) {
						$commitmentActions[$nextId] = $content['actions']['commitments'][$nextId];
					}
					$commitments[$commitment_id]['actions'] = $commitmentActions;
				}
				$content['data'][$account->id]['commitments'] = $commitments;
			}
			else {
				
				// Compute next actions depending on the current status
				$actions = array();
				foreach ($content['status'][$account->status]['nextSteps'] as $nextId) {
					if (array_key_exists($nextId, $content['actions']['Public'])) {
						$actions[$nextId] = $content['actions']['Public'][$nextId];
					}
				}
				$content['data'][$account->id]['PublicActions'] = $actions;
			}
		}
			
		uasort($content['data'], array($this, 'compare'));
		
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'mode' => $mode,
			'content' => $content,
			'locale' => $locale,
		));
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * Action for exporting to Excel
	 */
	public function exportAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'generic');
		$description = Account::getDescription($type);
		$params = array();
		if ($context->getPlaceId()) $params['place_id'] = $context->getPlaceId(); // Restriction on place defined according to the current user rights
		foreach ($description['search'] as $propertyId => $property) {
			if ($propertyId != 'place_id') { // Restriction on place already defined according to the current user rights
				$filter = ($this->params()->fromQuery($propertyId, null));
				if ($filter !== null) $params[$propertyId] = $filter;
			}
		}
		$limit = $this->params()->fromQuery('limit');
		$order = $this->params()->fromQuery('order', '+name');
	
		// Retrieve the list
		$accounts = Account::getList($type, $params, $order, $limit);
	
		include 'public/PHPExcel_1/Classes/PHPExcel.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/CachedObjectStorageFactory.php';
	
		$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			
		$workbook = new \PHPExcel;
		(new SsmlAccountViewHelper)->formatXls($description, $workbook, $accounts);
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
	
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Account.xlsx ');
		ob_end_clean();
		$writer->save('php://output');
		
		return $this->response;
	}
	
	/**
	 * Adapted from flowEvent to flowAccount
	 * @return \Zend\View\Model\ViewModel
	 */
	public function fillAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'generic');
		$id = $this->params()->fromRoute('id');
		$locale = $this->params()->fromQuery('locale');
		
		// Retrieve my account
		$myAccount = Account::get($context->getContactId(), 'contact_1_id');
		$charter_status = $myAccount->getCharterStatus();
		$gtou_status = $myAccount->getGtouStatus();
		
		$place = Place::get($myAccount->place_id);
		$place_identifier = $place->identifier;
		$profile = Vcard::get($myAccount->contact_1_id);
		if (!$locale) $locale = $profile->locale;
		
		if ($id) $account = Account::get($id);
		else $account = Account::instanciate($type);

		$description = Account::getDescription($account->type);
		
		// Retrieve the content
//		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('account/'.$place->identifier);
			if (!$content) $content = $context->getConfig('account/generic');
/*		}
		else $content = Config::get($place->identifier.'_account', 'identifier')->content;*/
		if (!array_key_exists('options', $content['form'])) $content['form']['options'] = array();
		if (!array_key_exists('examples', $content['form']['options'])) $content['form']['options']['examples'] = false;
		
		$viewData = array();
		$viewData['account_id'] = $myAccount->id;
		$viewData['photo_link_id'] = ($profile->photo_link_id) ? $profile->photo_link_id : 'no-photo.png';
		
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
				if ($inputId != $propertyId) $viewData[$inputId] = (in_array($property['value'], explode(',', $account->properties[$propertyId])) ? $property['value'] : null);
				elseif (array_key_exists($propertyId, $account->properties)) $viewData[$inputId] = $account->properties[$propertyId];
				$queryValue = $this->params()->fromQuery($inputId);
				if ($queryValue !== null) $viewData[$inputId] = $queryValue;
			}
			else $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;
		}
		$viewData['json_property_1'] = $account->json_property_1;
		
		// Process the post data after input
		$message = $error = null;
		if ($this->request->isPost()) {
			$data = array();
			$data['place_id'] = $place->id;
			
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
						$viewData[$propertyId] = $data[$propertyId]; // Updating the data to display in the confirmation form
					}
					elseif ($property['type'] == 'date') { // Workaround due to a bug in MDBootstrap that ignores formatSubmit
						$data[$propertyId] = ($viewData[$propertyId]) ? substr($viewData[$propertyId], 6, 4).'-'.substr($viewData[$propertyId], 3, 2).'-'.substr($viewData[$propertyId], 0, 2) : null;
					}
					else $data[$propertyId] = $viewData[$propertyId];
				}
			}

			if ($id) $rc = $account->loadAndUpdate($data);
			else $rc = $account->loadAndAdd($data);

			if (in_array($rc[0], ['200'])) {
				$id = $account->id;
				$message = 'OK';
			}
			else $error = $rc[1];
		}
		
		// Feed the layout
		$this->layout('/layout/flow-layout');
		$this->layout()->setVariables(array(
			'context' => $context,
			'id' => $id,
			'place_identifier' => $place_identifier,
			'panel' => $this->params()->fromQuery('panel', null),
			'account_id' => $myAccount->id,
			'token' => $this->params()->fromQuery('hash', null),
			'header' => $content['header'],
			'intro' => $content['intro'],
			'form' => $content['form'],
			'tooltips' => $content['tooltips'],
			'examples' => $content['examples'],
			'footer' => $content['footer'],
			'locale' => $locale,
			'photo_link_id' => ($account) ? $account->photo_link_id : null,
			'profileForm' => ['inputs' => []], // To manage via the context
			'charter_status' => $charter_status,
			'gtou_status' => $gtou_status,
			'pageScripts' => 'ppit-flow/account/fill-scripts',
			'message' => null,
			'error' => null,
		));
		
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'type' => $type,
			'id' => $id,
			'locale' => $locale,
			'account' => $account,
			'content' => $content,
			'viewData' => $viewData,
			'message' => $message,
			'error' => $error,
		));
		return $view;
	}

	/**
	 * 
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function captureAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$id = $this->params()->fromRoute('id');
		if (!$id) {
			$this->getResponse()->setStatusCode('400');
			$this->getResponse()->setReasonPhrase('No id is provided for the account to update');
			return $this->getResponse();
		}

		// Retrieve the account to update
		$account = Account::get($id);
		if (!$account) {
			$this->getResponse()->setStatusCode('400');
			$this->getResponse()->setReasonPhrase('The account does not exist');
			return $this->getResponse();
		}
		
		if ($this->request->isPost()) {
			
			// Retrieve the key-value pairs to update in the account's json_property_1
			$data = array('json_property_1' => $account->json_property_1);
			$pairs = json_decode($this->request->getContent(), true)['content'];
			foreach ($pairs as $key => $value) {
				$key = substr($key, 5);
				$data['json_property_1'][$key] = $value;
			}
			$rc = $account->loadAndUpdate($data);
			if ($rc[0] != '200') {
				$this->getResponse()->setStatusCode($rc[0]);
				return $this->getResponse();
			}
		}
		return $this->response;
	}
	
	/**
	 * Adapted from flowEvent to flowAccount
	 * @return \Zend\View\Model\ViewModel
	 */
	public function detailAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'generic');
		$id = $this->params()->fromRoute('id');
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$locale = $this->params()->fromQuery('locale');
		$message = $this->params()->fromQuery('message');
		
		// Retrieve the context account and place
		$myAccount = Account::get($context->getContactId(), 'contact_1_id');
		if ($myAccount) {
			$charter_status = $myAccount->getCharterStatus();
			$gtou_status = $myAccount->getGtouStatus();
			$place = Place::get($myAccount->place_id);
			$place_identifier = $place->identifier;
		}

		// Retrieve the request, the owner profile and the matched accounts
		$account = Account::get($id);

		// Discriminate between the mode 'requestor' (consultation of an account of mine) and the mode 'public' (public accounts)
		if ($account->place_id == $place->id) $mode = 'Owner';
		else $mode = 'Public';
		
		// Retrieve the account description according to its type
		$description = Account::getDescription($account->type);
	
		// Retrieve the content
//		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('account/'.$place->identifier);
			if (!$content) $content = $context->getConfig('account/generic');
/*		}
		else $content = Config::get($place->identifier.'_'.$type, 'identifier')->content;*/
		if (!array_key_exists('options', $content['detail'])) $content['detail']['options'] = array();
		
		$viewData = $account->getProperties();
	
		// Form
		foreach ($content['detail']['properties'] as $inputId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$inputId];
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if (array_key_exists('mandatory', $options)) $property['mandatory'] = $options['mandatory'];
				if (array_key_exists('updatable', $options)) $property['updatable'] = $options['updatable'];
				if (array_key_exists('rows', $options)) $property['rows'] = $options['rows'];
				if (array_key_exists('class', $options)) $property['class'] = $options['class'];
				if (array_key_exists('labels', $options)) $property['labels'] = $options['labels'];
				if (array_key_exists('format', $options)) $property['format'] = $options['format'];
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
			if (!array_key_exists('format', $property)) $property['format'] = null;
			$content['detail']['properties'][$inputId] = $property;
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
			if ($id) {
				if ($inputId != $propertyId) $viewData[$inputId] = (in_array($property['value'], explode(',', $request->properties[$propertyId])) ? $property['value'] : null);
				elseif (array_key_exists($propertyId, $account->properties)) $viewData[$inputId] = $account->properties[$propertyId];
				$queryValue = $this->params()->fromQuery($inputId);
				if ($queryValue !== null) $viewData[$inputId] = $queryValue;
			}
			else $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;
		}

		if ($mode == 'Owner') {
			$actions = array();
			if ($viewData['status'] == 'new') {
				$actions['update'] = $content['actions']['Owner']['update'];
				$actions['cancel'] = $content['actions']['Owner']['cancel'];
//				$actions['complete'] = $content['actions']['Owner']['complete'];
				$content['detail']['title'] = $content['detail']['title']['Owner'][$account->status];
			}
/*			else if ($viewData['status'] == 'connected') {
				$actions['complete'] = $content['actions']['Owner']['complete'];
				$content['detail']['title'] = $content['detail']['title']['Owner'][$account->status];
			}
			else if ($viewData['status'] == 'realized') {
				$requestorFeedbackGiven = true;
				$contributorFeedbackGiven = true;
				if (!array_key_exists($request->account_id, $request->feedbacks)) $content['detail']['title'] = $content['detail']['title']['Owner']['requestor_feedback'];
				else $content['detail']['title'] = $content['detail']['title']['Owner']['contributor_feedback'];
			}
			else if ($viewData['status'] == 'completed') {
				$content['detail']['title'] = $content['detail']['title']['Owner'][$request->status];
				$actions['consultFeedback'] = $content['actions']['Public']['consultFeedback'];
			}*/
			$content['actions']['Owner'] = $actions;
		}
		else { // Public mode
			$actions = array();
//			if (!$myAccount || !in_array($myAccount->id, explode(',', $request->matched_accounts))) {
				$content['detail']['title'] = $content['detail']['title']['Public']['new'];
//				$actions['propose'] = $content['actions']['Public']['propose'];
/*			}
			else {
				if ($request->status == 'realized') {
					if ($request->matching_log[$myAccount->id]['action'] == 'accept') {
						$content['detail']['title'] = $content['detail']['title']['Public']['contributor_feedback'];
						$actions['feedback'] = $content['actions']['Public']['feedback'];
					}
					elseif ($request->matching_log[$myAccount->id]['action'] == 'give_feedback') {
						$content['detail']['title'] = $content['detail']['title']['Public']['requestor_feedback'];
					}
					elseif ($request->matching_log[$myAccount->id]['action'] == 'receive_feedback') {
						$content['detail']['title'] = $content['detail']['title']['Public']['contributor_feedback'];
						$actions['feedback'] = $content['actions']['Public']['feedback'];
					}
				}
				elseif ($request->status == 'completed') {
					$content['detail']['title'] = $content['detail']['title']['Public']['completed'];
					$actions['consultFeedback'] = $content['actions']['Public']['consultFeedback'];
				}
				else {
					$content['detail']['title'] = $content['detail']['title']['Public']['linked'];
				}
			}*/
			$content['actions']['Public'] = $actions;
		}
				
		// Matched Accounts
/*		foreach ($content['matched_accounts']['properties'] as $propertyId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$propertyId];
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if (array_key_exists('labels', $options)) $property['labels'] = $options['labels'];
			}
			$content['matched_accounts']['properties'][$propertyId] = $property;
		}*/
		
		$matchedAccounts = array();
/*		if ($request->matched_accounts) {
			foreach (explode(',', $request->matched_accounts) as $matchedId) {
				$account = Account::get($matchedId);
				if ($account) $matchedAccounts[$matchedId] = $account->properties;
			}
		}*/
		$viewData['matched_accounts'] = $matchedAccounts;
//		$viewData['matching_log'] = $request->matching_log;

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'imagePath' => 'img/'.$place_identifier.'/event_'.$type.'/',
			'id' => $id,
			'status' => $account->status,
			'mode' => $mode,
			'locale' => $locale,
			'description' => $description,
			'content' => $content,
			'viewData' => $viewData,
		));
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\View\Model\ViewModel
	 */
	public function updateAction()
	{
		$view = $this->detailAction();
		$view->setTerminal(true); // Version sprint 07/09
		return $view;
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
	 */
	public function cancelAction()
	{
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$id = $this->params()->fromRoute('id');
		$request = Event::get($id);
		if (!$request) {
			$this->response->setStatusCode('400');
			return $this->response;
		}
		if ($this->request->isPost()) {
			$request->status = 'canceled';
			$request->update(null);
		}
		$view = $this->detailAction();
		$view->setTerminal(true); // Version sprint 07/09
		return $view;
	}
	
	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function contactAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$id = $this->params()->fromRoute('id');
		$request = Event::get($id);
		$locale = $this->params()->fromQuery('locale');

		// Retrieve the accounts to link this request with
		$otherAccounts = explode(',', $this->params()->fromQuery('accounts'));
		
		// Atomicity
		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {

			// Mark the other account as matched in the request
			if ($request->matched_accounts) $matchedAccounts = explode(',', $request->matched_accounts);
			else $matchedAccounts = array();
			foreach ($otherAccounts as $account_id) {
				if (!in_array($account_id, $matchedAccounts)) $matchedAccounts[] = $account_id;
				$request->matched_accounts = implode(',', $matchedAccounts);
				$entry = array(
					'user_id' => $context->getUserId(),
					'n_fn' => $context->getFormatedName(),
					'action' => 'request/contact',
				);
				if (array_key_exists($account_id, $request->matching_log)) {
					$request->matching_log[$account_id]['log'][date('Y-m-d H:i:s')] = $entry;
				}
				else {
					$request->matching_log[$account_id] = array(
						'action' => 'contact',
						'date' => Date('Y-m-d'),
						'log' => array(date('Y-m-d H:i:s') => $entry),
					);
				}
			}
			$rc = $request->update(null);
			if ($rc != 'OK') {
				$connection->rollback();
				$this->response->setStatusCode('500');
				return $this->response;
			}

			// Mark the other account as matched in the owner's account
			$account = Account::get($request->account_id);
	
			if ($account->property_2) $matchedAccounts = explode(',', $account->property_2);
			else $matchedAccounts = array();
			foreach ($otherAccounts as $account_id) {
				if (!in_array($account_id, $matchedAccounts)) $matchedAccounts[] = $account_id;
				$account->property_2 = implode(',', $matchedAccounts);
				$rc = $account->update(null);
				if ($rc != 'OK') {
					$connection->rollback();
					$this->response->setStatusCode('500');
					return $this->response;
				}
			}
			$connection->commit();
			$this->response->setStatusCode('200');
		}
		catch (\Exception $e) {
			$connection->rollback();
			$this->response->setStatusCode('500');
		}
		return $this->response;
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function abandonAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$id = $this->params()->fromRoute('id');
		$request = Event::get($id);
		$locale = $this->params()->fromQuery('locale');
		
		// Atomicity
		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {
			
			// Retrieve the accounts to abandon
			$otherAccounts = explode(',', $this->params()->fromQuery('accounts'));
		
			// Mark the other account as unmatched in the request
			if ($request->matched_accounts) $matchedAccounts = explode(',', $request->matched_accounts);
			else $matchedAccounts = array();
			$keys = array();
			foreach ($matchedAccounts as $item) $keys[$item] = null;
			foreach ($otherAccounts as $account_id) {
				if (array_key_exists($account_id, $keys)) unset($keys[$account_id]);
				$matchedAccounts = array();
				foreach ($keys as $item => $unused) $matchedAccounts[] = $item;
				$request->matched_accounts = implode(',', $matchedAccounts);
				$entry = array(
					'user_id' => $context->getUserId(),
					'n_fn' => $context->getFormatedName(),
					'action' => 'request/abandon',
				);
				$request->matching_log[$account_id]['log'][date('Y-m-d H:i:s')] = $entry;
			}
			$rc = $request->update(null);
			if ($rc != 'OK') {
				$connection->rollback();
				$this->response->setStatusCode('500');
				return $this->response;
			}
	
			// Mark the other account as unmatched in my account
/*			$account = Account::get($request->account_id);
			if ($account->property_2) {
				$matchedAccounts = explode(',', $account->property_2);
				foreach ($otherAccounts as $other_id) {
					if (in_array($other_id, $matchedAccounts)) {
						$requests = Event::getList('request', ['account_id' => $account->id], '-update_time', null);
						$accountIsMatched = false;
						foreach ($requests as $reqId => $req) {
							if ($reqId != $id) { // Previously processed
								$requestMatchedAccounts = explode(',', $req->matched_accounts);
								if (array_key_exists($other_id, $requestMatchedAccounts)) $accountIsMatched = true;
							}
						}
						if (!$accountIsMatched) {
							$matchedAccounts = array();
							foreach(explode(',', $account->property_2) as $item) {
								if ($item != $other_id) $matchedAccounts[] = $item;
							}
							$account->property_2 = implode(',', $matchedAccounts);
							$rc = $account->update(null);
							if ($rc != 'OK') {
								$connection->rollback();
								$this->response->setStatusCode('500');
								return $this->response;
							}
						}
					}
				}
			}*/
			$connection->commit();
			$this->response->setStatusCode('200');
		}
		catch (\Exception $e) {
			$connection->rollback();
			$this->response->setStatusCode('500');
		}
		return $this->response;
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function acceptAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$id = $this->params()->fromRoute('id');
		$request = Event::get($id);
		$locale = $this->params()->fromQuery('locale');
	
		// Atomicity
		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {
				
			// Retrieve the accounts to abandon
			$otherAccounts = explode(',', $this->params()->fromQuery('accounts'));
	
			// Change the account status in the request matching log
			if ($request->matched_accounts) $matchedAccounts = explode(',', $request->matched_accounts);
			else $matchedAccounts = array();
			foreach ($otherAccounts as $account_id) {
				$request->matched_accounts = implode(',', $matchedAccounts);
				$entry = array(
					'user_id' => $context->getUserId(),
					'n_fn' => $context->getFormatedName(),
					'action' => 'request/accept',
				);
				$request->matching_log[$account_id]['log'][date('Y-m-d H:i:s')] = $entry;
				$request->matching_log[$account_id]['action'] = 'accept';
				$request->matching_log[$account_id]['date'] = Date('Y-m-d');
			}
					
			// Update the event status
			$request->status = 'connected';
			$rc = $request->update(null);
			if ($rc != 'OK') {
				$connection->rollback();
				$this->response->setStatusCode('500');
				return $this->response;
			}

			$connection->commit();
			$this->response->setStatusCode('200');
		}
		catch (\Exception $e) {
			$connection->rollback();
			$this->response->setStatusCode('500');
		}
		return $this->response;
	}
	
	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function declineAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$id = $this->params()->fromRoute('id');
		$request = Event::get($id);
		$locale = $this->params()->fromQuery('locale');
		
		// Atomicity
		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {
			
			// Retrieve the accounts to abandon
			$otherAccounts = explode(',', $this->params()->fromQuery('accounts'));
		
			// Mark the other account as unmatched in the request
			if ($request->matched_accounts) $matchedAccounts = explode(',', $request->matched_accounts);
			else $matchedAccounts = array();
			$keys = array();
			foreach ($matchedAccounts as $item) $keys[$item] = null;
			foreach ($otherAccounts as $account_id) {
				if (array_key_exists($account_id, $keys)) unset($keys[$account_id]);
				$matchedAccounts = array();
				foreach ($keys as $item => $unused) $matchedAccounts[] = $item;
				$request->matched_accounts = implode(',', $matchedAccounts);
				$entry = array(
					'user_id' => $context->getUserId(),
					'n_fn' => $context->getFormatedName(),
					'action' => 'request/decline',
				);
				$request->matching_log[$account_id]['log'][date('Y-m-d H:i:s')] = $entry;
				$request->matching_log[$account_id]['action'] = 'decline';
				$request->matching_log[$account_id]['date'] = Date('Y-m-d');
			}
			$rc = $request->update(null);
			if ($rc != 'OK') {
				$connection->rollback();
				$this->response->setStatusCode('500');
				return $this->response;
			}
	
			// Mark the other account as unmatched in my account
/*			$account = Account::get($request->account_id);
			if ($account->property_2) {
				$matchedAccounts = explode(',', $account->property_2);
				foreach ($otherAccounts as $other_id) {
					if (in_array($other_id, $matchedAccounts)) {
						$requests = Event::getList('request', ['account_id' => $account->id], '-update_time', null);
						$accountIsMatched = false;
						foreach ($requests as $reqId => $req) {
							if ($reqId != $id) { // Previously processed
								$requestMatchedAccounts = explode(',', $req->matched_accounts);
								if (array_key_exists($other_id, $requestMatchedAccounts)) $accountIsMatched = true;
							}
						}
						if (!$accountIsMatched) {
							$matchedAccounts = array();
							foreach(explode(',', $account->property_2) as $item) {
								if ($item != $other_id) $matchedAccounts[] = $item;
							}
							$account->property_2 = implode(',', $matchedAccounts);
							$rc = $account->update(null);
							if ($rc != 'OK') {
								$connection->rollback();
								$this->response->setStatusCode('500');
								return $this->response;
							}
						}
					}
				}
			}*/
			$connection->commit();
			$this->response->setStatusCode('200');
		}
		catch (\Exception $e) {
			$connection->rollback();
			$this->response->setStatusCode('500');
		}
		return $this->response;
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function closeAction()
	{
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$id = $this->params()->fromRoute('id');
		$request = Event::get($id);
		if (!$request) {
			$this->response->setStatusCode('400');
			return $this->response;
		}
		$request->status = 'connected';
		$request->update(null);
	
		$this->response->setStatusCode('200');
		return $this->response;
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function openAction()
	{
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$id = $this->params()->fromRoute('id');
		$request = Event::get($id);
		if (!$request) {
			$this->response->setStatusCode('400');
			return $this->response;
		}
		$request->status = 'new';
		$request->update(null);
	
		$this->response->setStatusCode('200');
		return $this->response;
	}
	
	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function completeAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$accountType = $context->getConfig('event_account_type')[$type];
		$description = Event::getConfigProperties($type);
		$accountConfigProperties = Account::getConfig($accountType);
		$place = Place::get($context->getPlaceId());
		$id = $this->params()->fromRoute('id');

		// Retrieve the event to complete
		$event = Event::get($id);
		if (!$event) {
			$this->response->setStatusCode('400');
			return $this->response;
		}

		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place->identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place->identifier.'_'.$type, 'identifier')->content;

		// Atomicity
		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {
			
			// Update the event status
			$data = array();
			$data['status'] = 'realized';
			$rc = $event->loadAndUpdate($data, $description);
			if ($rc[0] != '200') {
				$connection->rollback();
				$this->response->setStatusCode('500');
				$this->response->setReasonPhrase($rc[1]);
				return $this->response;
			}

			// Rewarding and email for feedback
			if ($event->matched_accounts && array_key_exists('emails', $content) && array_key_exists('feedback', $content['emails'])) {
	
				// Retrieve the requestor
				$account = Account::get($event->account_id);
				$accountToUpdate = false;
				$accountCredits = $account->credits;
				
				// Email
				$emailCc = [$context->getConfig('mailAdmin') => $context->getConfig('nameAdmin')];
				$emailTitleFormat = $context->localize($content['emails']['feedback']['title']['format'], $event->locale);
				$titleArguments = array();
				foreach ($content['emails']['feedback']['title']['parameters'] as $parameter) {
					$titleArguments[] = $event->properties[$parameter];
				}
				$emailTitle = vsprintf($emailTitleFormat, $titleArguments);
				$emailBodyFormat = $context->localize($content['emails']['feedback']['body']['format'], $event->locale);
				$bodyArguments = array();
				foreach ($content['emails']['feedback']['body']['parameters'] as $parameter) {
					$bodyArguments[] = $event->properties[$parameter];
				}
				$emailBody = vsprintf($emailBodyFormat, $bodyArguments);
				Context::sendMail($event->email, $emailBody, $emailTitle, $emailCc);
	
				// Reward and email each contributor
				foreach (explode(',', $event->matched_accounts) as $account_id) {
					$otherAccount = Account::get($account_id);
					
					// Rewarding
					if ($event->value) {
						$accountToUpdate = true;
						$accountCredits['spent'] += $event->value;
						
						$data = array();
						$data['credits'] = $otherAccount->credits;
						$data['credits']['earned'] += $event->value;
						$rc = $otherAccount->loadAndUpdate($data, $accountConfigProperties);

						if ($rc[0] != '200') {
							$connection->rollback();
							$this->response->setStatusCode('500');
							$this->response->setReasonPhrase($rc[1]);
							return $this->response;
						}
					}
			
					// Email title
					$emailCc = [$context->getConfig('mailAdmin') => $context->getConfig('nameAdmin')];
					$emailTitleFormat = $context->localize($content['emails']['feedback']['title']['format'], $event->locale);
					$titleArguments = array();
					foreach ($content['emails']['feedback']['title']['parameters'] as $parameter) {
						if ($parameter == 'n_first') $titleArguments[] = $otherAccount->n_first;
						else $titleArguments[] = $event->properties[$parameter];
					}
					$emailTitle = vsprintf($emailTitleFormat, $titleArguments);
						
					// Email body
					$emailBodyFormat = $context->localize($content['emails']['feedback']['body']['format'], $event->locale);
					$bodyArguments = array();
					foreach ($content['emails']['feedback']['body']['parameters'] as $parameter) {
						if ($parameter == 'n_first') $bodyArguments[] = $otherAccount->n_first;
						else $bodyArguments[] = $event->properties[$parameter];
					}
					$emailBody = vsprintf($emailBodyFormat, $bodyArguments);
						
					Context::sendMail($otherAccount->email, $emailBody, $emailTitle, $emailCc);
				}
				
				if ($accountToUpdate) {
					
					// Reward the requestor
					$data = array();
					$data['credits'] = $accountCredits;
					$rc = $account->loadAndUpdate($data, $accountConfigProperties);
					if ($rc[0] != '200') {
						$connection->rollback();
						$this->response->setStatusCode('500');
						$this->response->setReasonPhrase($rc[1]);
						return $this->response;
					}
				}
				$connection->commit();
			}
		}
		catch (\Exception $e) {
			$connection->rollback();
			$this->response->setStatusCode('500');
			$this->response->setReasonPhrase('Exception: '.$e);
			return $this->response;
		}
		
		$this->response->setStatusCode('200');
		return $this->response;
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function proposeAction()
	{
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$place = Place::get($context->getPlaceId());
		$id = $this->params()->fromRoute('id');
		$account_id = $this->params()->fromQuery('account_id');
		if ($account_id) $account = Account::get($account_id);
		else {
			$account = Account::get($context->getContactId(), 'contact_1_id');
			$account_id = $account->id;
		}

		$request = Event::get($id);
		if (!$request) {
			$this->response->setStatusCode('400');
			$this->response->setReasonPhrase('Unknown');
			return $this->response;
		}

		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place->identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place->identifier.'_'.$type, 'identifier')->content;
		
		// Atomicity
		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {

			// Mark my account as matched in the request
			if ($request->matched_accounts) $matchedAccounts = explode(',', $request->matched_accounts);
			else $matchedAccounts = array();
			if (!in_array($account_id, $matchedAccounts)) $matchedAccounts[] = $account_id;
			$request->matched_accounts = implode(',', $matchedAccounts);
			$entry = array(
				'user_id' => $context->getUserId(),
				'n_fn' => $context->getFormatedName(),
				'action' => 'request/propose',
			);
			if (array_key_exists($account_id, $request->matching_log)) {
				$request->matching_log[$account_id]['log'][date('Y-m-d H:i:s')] = $entry;
			}
			else {
				$request->matching_log[$account_id] = ['log' => [date('Y-m-d H:i:s') => $entry]];
			}
			$request->matching_log[$account_id]['action'] = 'propose';
			$request->matching_log[$account_id]['date'] = Date('Y-m-d');

			// Mark the request as connected
			$rc = $request->update(null);
			if ($rc != 'OK') {
				$connection->rollback();
				$this->response->setStatusCode('500');
				$this->response->setReasonPhrase('event->update:'.$rc);
				return $this->response;
			}

			// Mark the other account as matched in the owner's account
			$ownerAccount = Account::get($request->account_id);
			if ($ownerAccount) {
				if ($ownerAccount->property_2) $matchedAccounts = explode(',', $ownerAccount->property_2);
				else $matchedAccounts = array();
				if (!in_array($account_id, $matchedAccounts)) $matchedAccounts[] = $account_id;
				$ownerAccount->property_2 = implode(',', $matchedAccounts);
				$rc = $ownerAccount->update(null);
				if ($rc != 'OK') {
					$connection->rollback();
					$this->response->setStatusCode('500');
					$this->response->setReasonPhrase('account->update:'.$rc);
					return $this->response;
				}
			}

			// Email
			if (array_key_exists('emails', $content) && array_key_exists('matching', $content['emails'])) {
				$emailCc = [$context->getConfig('mailAdmin') => $context->getConfig('nameAdmin')];
	
				// Email title
				$emailTitleFormat = $context->localize($content['emails']['matching']['title']['format'], $request->locale);
				$titleArguments = array();
				foreach ($content['emails']['matching']['title']['parameters'] as $parameter) {
					if ($parameter == 'contributor_n_first') $titleArguments[] = $account->n_first;
					else $titleArguments[] = $request->properties[$parameter];
				}
				$emailTitle = vsprintf($emailTitleFormat, $titleArguments);
				
				// Email body
				$emailBodyFormat = $context->localize($content['emails']['matching']['body']['format'], $request->locale);
				$bodyArguments = array();
				foreach ($content['emails']['matching']['body']['parameters'] as $parameter) {
					if ($parameter == 'contributor_n_first') $bodyArguments[] = $account->n_first;
					else $bodyArguments[] = $request->properties[$parameter];
				}
				$emailBody = vsprintf($emailBodyFormat, $bodyArguments);
				
				Context::sendMail($request->email.','.$account->email, $emailBody, $emailTitle, $emailCc);
			}

			// Commit the update
			$connection->commit();
			$this->response->setStatusCode('200');
			return $this->response;
		}
		catch (\Exception $e) {
			$connection->rollback();
			$this->response->setStatusCode('500');
			$this->response->setReasonPhrase('Exception: '.$e);
			return $this->response;
		}
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function transferAction()
	{
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$place = Place::get($context->getPlaceId());
		$id = $this->params()->fromRoute('id');
	
		$account = Account::get($context->getContactId(), 'contact_1_id');
		
		$request = Event::get($id);
		if (!$request) {
			$this->response->setStatusCode('400');
			$this->response->setReasonPhrase('Unknown');
			return $this->response;
		}
	
		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place->identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place->identifier.'_'.$type, 'identifier')->content;
		$description = \PpitCore\Model\Event::getDescription($type);
		
		// Atomicity
		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
//		try {

			// Email
			$email = $this->getRequest()->getPost('transfer-email');
			if ($email) {
				$emailCc = [$context->getConfig('mailAdmin') => $context->getConfig('nameAdmin')];
				
				// Email title
				$emailTitleFormat = $context->localize($content['emails']['transfer']['title']['format'], $request->locale);
				$titleArguments = array();
				foreach ($content['emails']['transfer']['title']['parameters'] as $parameter) {
					if ($parameter == 'referrer_n_first') $titleArguments[] = $referrer->n_first;
					elseif ($parameter == 'contributor_n_first') $titleArguments[] = $account->n_first;
					else $titleArguments[] = $request->properties[$parameter];
				}
				$emailTitle = vsprintf($emailTitleFormat, $titleArguments);
					
				// Email body
				$emailBodyFormat = $context->localize($content['emails']['transfer']['body']['format'], $request->locale);
				$bodyArguments = array();
				foreach ($content['emails']['transfer']['body']['parameters'] as $parameter) {
					if ($parameter == 'referrer_n_first') $bodyArguments[] = $account->n_first;
					elseif (substr($parameter, 0, 5) == 'label') $bodyArguments[] = $context->localize($description['properties'][substr($parameter, 6)]['labels'], $request->locale);
					elseif ($parameter == 'redirect_link') {
						$bodyArguments[] = $this->url()->fromRoute('landing/template2', [], ['force_canonical' => true, 'query' => ['email' => $email, 'route' => 'flowEvent/index', 'type' => $type, 'mode' => 'detail', 'action' => 'propose', 'id' => $id]]);
					}
					else $bodyArguments[] = $request->properties[$parameter];
				}
				$emailBody = vsprintf($emailBodyFormat, $bodyArguments);

				Context::sendMail($email, $emailBody, $emailTitle, [$account->email => $account->n_first], $emailCc);
			}
	
			// Commit the update
			$connection->commit();
			$this->response->setStatusCode('200');
			return $this->response;
/*		}
		catch (\Exception $e) {
			$connection->rollback();
			$this->response->setStatusCode('500');
			$this->response->setReasonPhrase('Exception: '.$e);
			return $this->response;
		}*/
	}
	
	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
	 */
	public function feedbackAction()
	{
		// Retrieve the context and parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$id = $this->params()->fromRoute('id');
		$locale = $this->params()->fromQuery('locale');
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$account = Account::get($context->getContactId(), 'contact_1_id');
		$contributor_id = $this->params()->fromQuery('contributor');
		$request = Event::get($id);
		if (!$request) {
			$this->response->setStatusCode('400');
			return $this->response;
		}
		$feedback = (array_key_exists($account->id, $request->feedbacks)) ? $request->feedbacks[$account->id] : array();

		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place->identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place->identifier.'_'.$type, 'identifier')->content;

		// Discriminate between the mode 'requestor' (consultation of a request of mine) and the mode 'public' (requests from others)
		if ($request->account_id == $account->id) $mode = 'requestor';
		else $mode = 'public';
		
		$viewData = array();
		
		// Form
		$inputs = array();
		foreach ($content['feedback']['inputs'] as $inputId => $options) {
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
			if ($property['type'] == 'html' && array_key_exists('params', $property)) {
				$arguments = array();
				foreach ($property['params'] as $paramId) $arguments[$paramId] = $request->properties[$paramId];
				$text = array();
				foreach ($property['text'] as $locale => $localized) $text[$locale] = vsprintf($localized, $arguments);
				$property['text'] = $text;
			}
			if (array_key_exists('repository', $property)) $property['repository'] = $context->getConfig($property['repository']);
			if (!array_key_exists('mandatory', $property)) $property['mandatory'] = false;
			if (!array_key_exists('updatable', $property)) $property['updatable'] = true;
			if (!array_key_exists('placeholder', $property)) $property['placeholder'] = null;
  			if (!array_key_exists('mode', $property) || $property['mode'] == $mode) {
				$inputs[$inputId] = $property;
  			}
			if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
			else $propertyId = $inputId;
			if ($id && array_key_exists($propertyId, $feedback)) $viewData[$inputId] = $feedback[$propertyId];
			else $viewData[$inputId] = (array_key_exists('default', $property)) ? $property['default'] : null;
		}
		$content['feedback']['inputs'] = $inputs;

		if ($mode == 'requestor') {
			$content['feedback']['title'] = $content['feedback']['title']['requestor']['new'];
		}
		else { // Public mode
			$content['feedback']['title'] = $content['feedback']['title']['contributor']['new'];
		}
		
		// Process the post data after input
		$message = $error = null;
		if ($this->request->isPost()) {
					
			// Atomicity
			$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
//			try {
		
				if ($mode == 'requestor') {

					// The contributor received a feedback 
					$entry = array(
						'user_id' => $context->getUserId(),
						'n_fn' => $context->getFormatedName(),
						'action' => 'request/feedback',
					);
					if (array_key_exists($contributor_id, $request->matching_log)) {
						$request->matching_log[$contributor_id]['log'][date('Y-m-d H:i:s')] = $entry;
					}
					else {
						$request->matching_log[$contributor_id] = ['log' => [date('Y-m-d H:i:s') => $entry]];
					}
					$request->matching_log[$contributor_id]['action'] = 'receive_feedback';
					$request->matching_log[$contributor_id]['date'] = Date('Y-m-d');

					// Log the feedback
					if (!array_key_exists($account->id, $request->feedbacks)) $request->feedbacks[$account->id] = array();
					$request->feedbacks[$account->id][$contributor_id] = array(
						'private_comment' => $this->getRequest()->getPost('private_comment'),
						'platform_benefit' => $this->getRequest()->getPost('platform_benefit'),
						'platform_satisfaction' => $this->getRequest()->getPost('platform_satisfaction'),
						'platform_accessibility' => $this->getRequest()->getPost('platform_accessibility'),
						'platform_comment' => $this->getRequest()->getPost('platform_comment'),
						'community_comment' => $this->getRequest()->getPost('community_comment')
					);
				}
				else {
				
					// The contributor gave a feedback 
					$entry = array(
						'user_id' => $context->getUserId(),
						'n_fn' => $context->getFormatedName(),
						'action' => 'request/feedback',
					);
					if (array_key_exists($account->id, $request->matching_log)) {
						$request->matching_log[$account->id]['log'][date('Y-m-d H:i:s')] = $entry;
					}
					else {
						$request->matching_log[$account->id] = ['log' => [date('Y-m-d H:i:s') => $entry]];
					}
					$request->matching_log[$account->id]['action'] = 'give_feedback';
					$request->matching_log[$account->id]['date'] = Date('Y-m-d');

					// Log the feedback
					$request->feedbacks[$account->id] = array(
						$request->account_id => array(
							'private_comment' => $this->getRequest()->getPost('private_comment'),
							'platform_benefit' => $this->getRequest()->getPost('platform_benefit'),
							'platform_satisfaction' => $this->getRequest()->getPost('platform_satisfaction'),
							'platform_accessibility' => $this->getRequest()->getPost('platform_accessibility'),
							'platform_comment' => $this->getRequest()->getPost('platform_comment'),
							'community_comment' => $this->getRequest()->getPost('community_comment')
						),
					);
				}
				
				// Mark the request as completed if all the stakeholders (requestor and contributors gave their feedback)
				$completed = true;
				if (!array_key_exists($request->account_id, $request->feedbacks)) $completed = false;
				foreach (explode(',', $request->matched_accounts) as $otherAccountId) {
					if (!array_key_exists($otherAccountId, $request->feedbacks)) $completed = false;
				}
				if ($completed) $request->status = 'completed';
				
				$rc = $request->update(null);
				if ($rc != 'OK') {
					$connection->rollback();
					$error = $rc;
				}

				// Credit the feedback giver with the credit value associated to this event
//				$account->credits += $request->value;
				$account->update(null);

				$connection->commit();
				$message = 'OK';
/*			}
			catch (\Exception $e) {
				$connection->rollback();
				$error = 'technical';
			}*/
		}
		
		// Feed the layout
		$this->layout('/layout/flow-layout');
		$this->layout()->setVariables(array(
			'context' => $context,
			'place_identifier' => $place_identifier,
			'panel' => $this->params()->fromQuery('panel', null),
			'token' => $this->params()->fromQuery('hash', null),
			'header' => $content['header'],
			'intro' => $content['intro'],
			'form' => $content['feedback'],
			'footer' => $content['footer'],
			'locale' => $locale,
			'charter_status' => $charter_status = $account->getCharterStatus(),
			'gtou_status' => $account->getGtouStatus(),
			'photo_link_id' => $account->photo_link_id,
			'pageScripts' => 'ppit-flow/request/feedback-scripts',
			'tooltips' => $content['tooltips'],
			'message' => $message,
			'error' => $error,
		));
		
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'type' => $type,
			'id' => $id,
			'contributor_id' => $contributor_id,
			'locale' => $locale,
			'event' => $request,
			'content' => $content,
			'viewData' => $viewData,
			'message' => $message,
			'error' => $error,
		));
		return $view;
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\View\Model\ViewModel
	 */
	public function consultFeedbackAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$id = $this->params()->fromRoute('id');
		$request = Event::get($id);
		$myAccount = Account::get($context->getContactId(), 'contact_1_id');
		$locale = $this->params()->fromQuery('locale');
				
		// Retrieve the accounts for the feedback givers
		$myReceivedFeedbacks = array();
		foreach ($request->feedbacks as $giver_id => $receivers) {
			foreach ($receivers as $receiver_id => $feedback) {
				if ($receiver_id == $myAccount->id) {
					$giver = Account::get($giver_id);
					$myReceivedFeedbacks[] = array(
						'n_fn' => $giver->n_fn,
						'text' => $feedback['private_comment'],
					);
				}
			}
		}

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'id' => $id,
			'locale' => $locale,
			'request' => $request,
			'myReceivedFeedbacks' => $myReceivedFeedbacks,
		));
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function signOutAction()
	{
		// Retrieve the context and parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		if (!$this->request->isPost()) {
			$this->response->setStatusCode('400');
			$this->response->setReasonPhrase('POST');
			return $this->response;
		}
		$identifier = trim($this->request->getPost('identifier'));
		if (!$identifier) {
			$this->response->setStatusCode('400');
			$this->response->setReasonPhrase('POST');
			return $this->response;
		}
		$account = Account::get($context->getContactId(), 'contact_1_id');
//		$event = Event::get($type, 'type', $identifier, 'identifier');
		$events = Event::getList($type, ['identifier' => $identifier]);
		$event = null;
		foreach ($events as $row) {
			if (in_array($account->id, explode(',', $row->matched_accounts))) {
				$event = $row;
				break;
			}
		}
		if (!$event) {
			$this->response->setStatusCode('400');
			$this->response->setReasonPhrase('Unknown');
			return $this->response;
		}
/*		if (!in_array($account->id, explode(',', $event->matched_accounts))) {
			$this->response->setStatusCode('401');
			$this->response->setReasonPhrase('Unregistered');
			echo 'My account: '.$account->id.' Event: '.$event->id.' Matched accounts: '.$event->matched_accounts;
			return $this->response;
		}*/
		if (array_key_exists($account->id, $event->rewards)) {
			$this->response->setStatusCode('401');
			$this->response->setReasonPhrase('Duplicate');
			return $this->response;
		}
		$credits = $account->credits;
		$earned = array_key_exists('earned', $credits) ? $credits['earned'] : 0;
		$earned += $event->value;
		$credits['earned'] = $earned;
		$account->credits = $credits;
//		$event->rewards[$account->id] = $earned;
		$event->rewards[$account->id] = date('Y-m-d H:i:s');
		
		$connection = Event::getTable()->getAdapter()->getDriver()->getConnection();
		$connection->beginTransaction();
		try {
			$event->update(null);
			$account->update(null);
			$connection->commit();
			echo (int)$event->value;
			$this->response->setStatusCode('200');
			return $this->response;
		}
		catch (\Exception $e) {
			$connection->rollback();
			$this->response->setStatusCode('500');
		}
	}
	
	/**
	 * To adapt from flowEvent to flowAccount
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function repairAction()
	{
/*		$context = Context::getCurrent();
		$account = Account::get($context->getContactId(), 'contact_1_id');

		// Rank the profiles
		$accountType = $context->getConfig($account->type);
		$ranking = array();
		$cursor = Account::getList($account->type, [], '+name', null);
		foreach ($cursor as $anyAccountId => $anyAccount) {
			if ($anyAccount->credits) {
				foreach ($anyAccount->credits as $rowId => $value) {
					if ($rowId == 'earned') {
						$ranking[$anyAccountId] = $value;
					}
				}
			}
		}
		
		// Rank the participants and find my rank
		arsort($ranking);
		$ranks = array();
		$currentRank = 0;
		$currentWeight = 0;
		$i = 0;
		foreach($ranking as $account_id => $weight) {
			$i++;
			if ($currentWeight != $weight) {
				$currentRank = $i;
				$currentWeight = $weight;
				$ranks[$currentWeight] = 1;
			}
			else $ranks[$currentWeight]++;
			if ($ranking[$account->id] == $currentWeight) {
				$rank = $currentRank;
			}
		}
		
		// Add a sign to indicate my rank is shared with other participant
		if ($ranks[$ranking[$account->id]] > 1) $equalSign = '='; else $equalSign = '';
		switch ($rank % 10) {
			case 1: $ending = ($rank / 10) % 10 === 1 ?  "th" : "st"; break;
			case 2: $ending = ($rank / 10) % 10 === 1 ?  "th" : "nd"; break;
			case 3: $ending = ($rank / 10) % 10 === 1 ?  "th" : "rd"; break;
			default: $ending = "th";
		}
var_dump($rank);
		// Add a sign to indicate my rank is shared with other participant
		if ($ranks[$ranking[$account->id]] > 1) $equalSign = '='; else $equalSign = '';
		switch ($rank % 10) {
			case 1: $ending = ($rank / 10) % 10 === 1 ?  "th" : "st"; break;
			case 2: $ending = ($rank / 10) % 10 === 1 ?  "th" : "nd"; break;
			case 3: $ending = ($rank / 10) % 10 === 1 ?  "th" : "rd"; break;
			default: $ending = "th";
		}
		echo $equalSign . " " . (string)$rank . $ending;

		$creditSum = 0;
		$accounts = Account::getList('pbc', [], '+name', null);
		$computed = array();
		foreach ($accounts as $account) $computed[$account->id] = 1;
		foreach (Event::getList('event', [], '-update_time', null) as $eventId => $event) {
			foreach ($event->rewards as $accountId => $unused) $computed[$accountId] += $event->value;
		}
		foreach ($accounts as $account) {
			if ($computed[$account->id] != $account->credits['earned']) {
				echo $account->id.': earned: '.$account->credits['earned'].', computed: '.$computed[$account->id]."\n";
				$account->credits['earned'] = $computed[$account->id];
//				$account->update(null);
			}
			$creditSum += $account->credits['earned'];
		}
		echo "Credit sum: ".$creditSum;*/
		
		VcardSource::instanciate()->add();
		UserSource::instanciate()->add();
		UserContactSource::instanciate()->add();
		AccountSource::instanciate()->add();
		EventSource::instanciate()->add();
		
		echo "Done\n";
		return $this->response;
	}
}
