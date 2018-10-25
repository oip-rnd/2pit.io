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

class EventController extends AbstractActionController
{
	public static function displayRank ($rank, $hasEquals) {
		if ($rank < 1) {
			throw new Exception("Value must be 1 or above");
		}
		$equalSign = ($hasEquals === True) ? "=" : "";
		switch ($rank % 10) {
			case 1: $ending = ($rank / 10) % 10 === 1 ?  "th" : "st"; break;
			case 2: $ending = ($rank / 10) % 10 === 1 ?  "th" : "nd"; break;
			case 3: $ending = ($rank / 10) % 10 === 1 ?  "th" : "rd"; break;
			default: $ending = "th";
		}
		return "Ranked " . $equalSign . " " . (string)$rank . $ending;
	}
	
	public function indexAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', $context->getConfig('event_default_type'));
		$description = Event::getDescription($type);
		$instance_caption = $context->getInstance()->caption;
		$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = Place::get($context->getPlaceId());
			$place_identifier = $place->identifier;
		}
		$accountType = $context->getConfig('landing_account_type');
		$account = Account::get($context->getContactId(), 'contact_1_id');
		$locale = $this->params()->fromQuery('locale');
		if (!$locale) if ($account) $locale = $account->locale; else $locale = $context->getLocale();

		$charter_status = null;
		if ($type == 'request') $charter_status = $account->getCharterStatus();
		$gtou_status = null;
		if ($type == 'request') $gtou_status = $account->getGtouStatus();
		
		$mode = $this->params()->fromQuery('mode', 'Public');
		$filters = array();
		foreach ($description['search']['properties'] as $propertyId => $unused) {
			$predicate = $this->params()->fromQuery($propertyId, null);
			if ($predicate !== null) $filters[$propertyId] = $predicate;
		}

		// Event content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place_identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place_identifier.'_'.$type, 'identifier')->content;
		
		// Profile form
		if ($context->getConfig('specificationMode') == 'config') {
			$profileForm = $context->getConfig('profile/'.$place_identifier)['form'];
			if (!$profileForm) $profileForm = $context->getConfig('profile/generic')['form'];
		}
		else $profileForm = Config::get($place_identifier.'_profile', 'identifier')->content['form'];
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

		$panel = $this->params()->fromQuery('panel', null);
		if ('type' == 'request' && $charter_status == 'OK' && $gtou_status == 'OK' && !$panel && (!$account->properties['completeness'] || $account->properties['completeness'] == '0_not_completed')) $panel = 'modalProfileForm';
		
		// Feed the layout
		$this->layout('/layout/flow-layout');
		$this->layout()->setVariables(array(
			'context' => $context,
			'type' => $type,
			'place_identifier' => $place_identifier,
			'account_id' => $account->id,
			'mode' => $mode,
			'panel' => $panel,
			'identity' => null,
			'redirectRoute' => $this->params()->fromQuery('route'),
			'redirectParams' => $this->params()->fromQuery('params'),
			'token' => $this->params()->fromQuery('hash', null),
			'accountType' => $accountType,
			'header' => $content['header'],
			'index' => $content['index'],
			'intro' => $content['intro'],
			'actions' => $content['actions'],
			'detail' => $content['detail'],
			'footer' => $content['footer'],
			'tooltips' => $content['tooltips'],
			'locale' => $locale,
			'photo_link_id' => ($account && $account->photo_link_id != 'no-photo.png') ? $account->photo_link_id : null,
			'profileForm' => $profileForm,
			'charter_status' => $charter_status,
			'gtou_status' => $gtou_status,
			'pageScripts' => 'ppit-flow/event/index-scripts',
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
			'account' => $account->properties,
			'content' => $content,
		));
		return $view;
	}
	
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
		if (array_key_exists('rewards', $content)) {
	
			// Rank the profiles
			$accountType = $context->getConfig('landing_account_type');
			$ranking = array();
			$cursor = Account::getList($account->type, ['status' => 'active']);
			foreach ($cursor as $anyAccountId => $anyAccount) {
				if ($anyAccount->credits) {
					foreach ($anyAccount->credits as $rowId => $value) {
						if ($rowId == 'earned') {
							$ranking[$anyAccountId] = $value;
						}
					}
				}
			}
	
			if (array_key_exists($account->id, $ranking)) {
	
				// Rank the participants and find my rank
				arsort($ranking);
				$ranks = array();
				$currentRank = 0;
				$currentWeight = 0;
				foreach($ranking as $account_id => $weight) {
					if ($currentWeight != $weight) {
						$currentRank++;
						$currentWeight = $weight;
						$ranks[$currentWeight] = 1;
					}
					else $ranks[$currentWeight]++;
					if ($ranking[$account->id] == $currentWeight) $account->credits['rank'] = $currentRank;
				}
	
				// Add a sign to indicate my rank is shared with other participant
				if ($ranks[$ranking[$account->id]] > 1) $account->credits['rank'] = '='.$account->credits['rank'];
			}
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

	public function compare($a, $b)
	{
		if ($a['rank'] == $b['rank']) {
			return 0;
		}
		return ($a['rank'] > $b['rank']) ? -1 : 1;
	}
	
	public function listAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$description = Event::getDescription($type);
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$myAccount = Account::get($context->getContactId(), 'contact_1_id');
		$locale = $this->params()->fromQuery('locale');
		$mode = $this->params()->fromQuery('mode', 'Owner');
		$filters = array();
		foreach ($description['search']['properties'] as $propertyId => $unused) {
			$predicate = $this->params()->fromQuery($propertyId, null);
			if ($predicate !== null) $filters[$propertyId] = $predicate;
		}

		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place->identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place->identifier.'_'.$type, 'identifier')->content;

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
		
		// Retrieve my requests in owner mode
		$content['data'] = array();
		if ($mode == 'Owner') {
			$requests = Event::getListV2($description, ['status' => 'new,connected,realized,completed', 'account_id' => '='.$myAccount->id]);
			foreach ($requests as $request) {
	
				$actions = array();
				$content['data'][$request->id] = $request->getProperties();
				$content['data'][$request->id]['role'] = null;

				$matchedAccounts = array();
				if ($request->matched_accounts) {
					foreach (explode(',', $request->matched_accounts) as $fullMatchedId) {
						$fullMatchedIdExpl = explode('@', $fullMatchedId); // Ids can be local or extended to other platforms
						$matchedId = $fullMatchedIdExpl[0];
						$platform = array_key_exists(1, $fullMatchedIdExpl) ? $fullMatchedIdExpl[1] : null;
						$matchingActions = array();
						$matchedAccounts[$fullMatchedId] = Account::get($matchedId)->properties;
						$matchedAccounts[$fullMatchedId]['platform'] = $platform;
						
						// Actions depending on the current matching status
						$currentMatching = $request->matching_log[$fullMatchedId];
						if (array_key_exists('action', $currentMatching) && $currentMatching['action'] == 'propose') {
							$matchingActions['accept'] = $content['actions']['Matching']['accept'];
							$matchingActions['decline'] = $content['actions']['Matching']['decline'];
						}
						elseif (!in_array($request->status, ['realized', 'completed']) && (!array_key_exists('action', $request->matching_log[$matchedId]) || $request->matching_log[$matchedId]['action'] != 'accept')) {
							$matchingActions['abandon'] = $content['actions']['Matching']['abandon'];
						}
						
						// Feedback to give
						if ($request->status == 'realized') {
							if (!array_key_exists($myAccount->id, $request->feedbacks) || !array_key_exists($matchedId, $request->feedbacks[$myAccount->id])) {
								$matchingActions['feedback'] = $content['actions']['Matching']['feedback'];
							}
						}
						
						$matchedAccounts[$fullMatchedId]['actions'] = $matchingActions;
					}
				}
				$content['data'][$request->id]['matched_accounts'] = $matchedAccounts;
				
				if (in_array($myAccount->id, explode(',', $request->matched_accounts))) $content['data'][$request->id]['amContributor'] = true;
				else $content['data'][$request->id]['amContributor'] = false;

				if ($request->status == 'new') {
					$actions['cancel'] = $content['actions']['Owner']['cancel'];
					$actions['close'] = $content['actions']['Owner']['close'];
				}
				elseif ($request->status == 'connected') {
					$actions['open'] = $content['actions']['Owner']['open'];
					$actions['complete'] = $content['actions']['Owner']['complete'];
				}
				elseif ($request->status == 'realized') {
					$requestorFeedbackGiven = true;
					$contributorFeedbackGiven = true;
				}
				elseif ($request->status == 'completed') {
					$actions['consultFeedback'] = $content['actions']['Owner']['consultFeedback'];
				}
				$content['data'][$request->id]['OwnerActions'] = $actions;
			}
		}
		
		// Retrieve my contributions in contributor mode
		elseif ($mode == 'Contributor') {
			$requests = Event::getList($type, ['status' => 'new,connected,realized,completed']);
			foreach ($requests as $request) {
				if (in_array($myAccount->id, explode(',', $request->matched_accounts))) {

					$content['data'][$request->id] = $request->getProperties();
					$content['data'][$request->id]['role'] = null;
					
					$actions = array();
					if ($request->status == 'realized') {
						if (in_array($request->matching_log[$myAccount->id]['action'], ['accept', 'receive_feedback'])) {
							$actions['feedback'] = $content['actions']['Contributor']['feedback'];
						}
					}
					elseif ($request->status == 'completed') {
						$actions['consultFeedback'] = $content['actions']['Contributor']['consultFeedback'];
					}
					$content['data'][$request->id]['ContributorActions'] = $actions;
				}
			}
		}
		
		// Retrieve the request according to the given search criteria or the current requests in no search criterion is given
		else {

			if (!$filters) $filters = ['status' => 'new,connected,realized,completed'];
			if ($type == 'request') $filters['account_status'] = 'active';
			$skills = $this->params()->fromQuery('skills');
			$requests = Event::getListV2($description, $filters, '+begin_date,+begin_time');
			$ranking = array(
				'event:status' => [['=', 'new', [], 9000], ['=', 'connected', [], 8000], ['=', 'realized', [], 7000], ['=', 'completed', [], 6000]],
				'query:skills' => [['matches', '%s', ['property_2'], 900], ['matches', '%s', ['property_1'], 800], ['matches', '%s', ['caption'], 700], ['matches', '%s', ['property_3'], 600], ['matches', '%s', ['property_7'], 500]],
				'profile:profile_tiny_4' => [['like', '%s', ['property_7'], 90]],
			);
			foreach ($requests as $request) {
				// Ranking
				$rank = 0;
				foreach ($ranking as $key => $rules) {
					$key = explode(':', $key);
					$entity = $key[0];
					$property = $key[1];
					if ($entity == 'event') $value = $request->properties[$property];
					elseif ($entity == 'query') $value = $this->params()->fromQuery($property);
					elseif ($entity == 'profile') $value = $myAccount->properties[$property];
					foreach ($rules as list($operator, $format, $parameters, $ponderation)) {
						$arguments = array();
						foreach ($parameters as $parameter) $arguments[] = $request->properties[$parameter];
						$operand = vsprintf($format, $arguments);
						if ($operator == '=' && $value == $operand) $rank += $ponderation;
						elseif ($operator == 'matches') {
							foreach (explode(',', $value) as $term) if (stripos($operand, $term) !== FALSE) $rank += $ponderation;
						}
						elseif ($operator == 'like') if (stripos($operand, $value) !== FALSE) $rank += $ponderation;
					}
				}
				
				$actions = array();
				$content['data'][$request->id] = $request->getProperties();
				$content['data'][$request->id]['rank'] = $rank;
				if ($myAccount->id == $request->account_id) $content['data'][$request->id]['role'] = 'requestor';
				elseif (in_array($myAccount->id, explode(',', $request->matched_accounts))) $content['data'][$request->id]['role'] = 'contributor';
				else $content['data'][$request->id]['role'] = null;
				if (in_array($request->status, ['new', 'connected'])) {
					if ($myAccount->id != $request->account_id && !in_array($myAccount->id, explode(',', $request->matched_accounts))) {
						$actions['propose'] = $content['actions']['Public']['propose'];
					}
					if (array_key_exists('transfer', $content['actions']['Public'])) $actions['transfer'] = $content['actions']['Public']['transfer'];
				}
				$content['data'][$request->id]['PublicActions'] = $actions;
			}
			
			if ($type == 'request') uasort($content['data'], array($this, 'compare'));
		}

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'mode' => $mode,
			'my_account_id' => $myAccount->id,
			'content' => $content,
			'locale' => $locale,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function fillAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$id = $this->params()->fromRoute('id');
		$availableSkills = $context->getConfig('matching/skills');
		$locale = $this->params()->fromQuery('locale');
		
		// Retrieve the account
		$account = Account::get($context->getContactId(), 'contact_1_id');
		$charter_status = $account->getCharterStatus();
		$gtou_status = $account->getGtouStatus();
		
		$place = Place::get($account->place_id);
		$place_identifier = $place->identifier;
		$profile = Vcard::get($account->contact_1_id);
		if (!$locale) $locale = $profile->locale;
		
		if ($id) $event = Event::get($id);
		else $event = Event::instanciate($type);

		$description = Event::getDescription($event->type);
		
		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place->identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place->identifier.'_'.$type, 'identifier')->content;
		if (!array_key_exists('options', $content['form'])) $content['form']['options'] = array();
		if (!array_key_exists('examples', $content['form']['options'])) $content['form']['options']['examples'] = false;
		
		$viewData = array();
		$viewData['account_id'] = $account->id;
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
			$data['place_id'] = $place->id;
			$data['account_id'] = $account->id;
			
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

					if (array_key_exists('account_property', $property)) $accountData[$property['account_property']] = $data[$propertyId];
				}
			}

			if ($id) $rc = $event->loadAndUpdate($data, $description['properties']);
			else $rc = $event->loadAndAdd($data, $description['properties']);

			if (in_array($rc[0], ['200'])) {
				$id = $event->id;
				$message = 'OK';
			}
			else $error = $rc[1];
		}
		
		// Feed the layout
		$this->layout('/layout/flow-layout');
		$this->layout()->setVariables(array(
			'context' => $context,
			'place_identifier' => $place_identifier,
			'panel' => $this->params()->fromQuery('panel', null),
			'token' => $this->params()->fromQuery('hash', null),
			'accountType' => $context->getConfig('landing_account_type'),
			'header' => $content['header'],
			'intro' => $content['intro'],
			'form' => $content['form'],
			'tooltips' => $content['tooltips'],
			'examples' => $content['examples'],
			'footer' => $content['footer'],
			'locale' => $locale,
			'photo_link_id' => ($account) ? $account->photo_link_id : null,
			'charter_status' => $charter_status,
			'gtou_status' => $gtou_status,
			'pageScripts' => 'ppit-flow/event/fill-scripts',
			'message' => null,
			'error' => null,
		));
		
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'type' => $type,
			'id' => $id,
			'locale' => $locale,
			'event' => $event,
			'content' => $content,
			'viewData' => $viewData,
			'message' => $message,
			'error' => $error,
			'availableSkills' => $availableSkills,
		));
		return $view;
	}

	public function detailAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$id = $this->params()->fromRoute('id');
		$availableSkills = $context->getConfig('matching/skills');
		$locale = $this->params()->fromQuery('locale');
		$message = $this->params()->fromQuery('message');
		
		// Retrieve the context account and place
		$myAccount = Account::get($context->getContactId(), 'contact_1_id');
		$charter_status = $myAccount->getCharterStatus();
		$gtou_status = $myAccount->getGtouStatus();
		$place = Place::get($myAccount->place_id);
		$place_identifier = $place->identifier;

		// Retrieve the request, the owner profile and the matched accounts
		$request = Event::get($id);
		$account = Account::get($request->account_id);
		
		// Discriminate between the mode 'requestor' (consultation of a request of mine) and the mode 'public' (requests from others)
		if ($request->account_id == $myAccount->id) $mode = 'Owner';
		else $mode = 'Public';
		
		// Retrieve the request description according to its type
		$description = Event::getDescription($request->type);
	
		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place->identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place->identifier.'_'.$type, 'identifier')->content;
		if (!array_key_exists('options', $content['detail'])) $content['detail']['options'] = array();
		
		$viewData = $request->getProperties();
	
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
				elseif (array_key_exists($propertyId, $request->properties)) $viewData[$inputId] = $request->properties[$propertyId];
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
				$actions['complete'] = $content['actions']['Owner']['complete'];
				$content['detail']['title'] = $content['detail']['title']['Owner'][$request->status];
			}
			else if ($viewData['status'] == 'connected') {
				$actions['complete'] = $content['actions']['Owner']['complete'];
				$content['detail']['title'] = $content['detail']['title']['Owner'][$request->status];
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
			}
			$content['actions']['Owner'] = $actions;
		}
		else { // Public mode
			$actions = array();
			if (!in_array($myAccount->id, explode(',', $request->matched_accounts))) {
				$content['detail']['title'] = $content['detail']['title']['Public']['new'];
				$actions['propose'] = $content['actions']['Public']['propose'];
			}
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
			}
			$content['actions']['Public'] = $actions;
		}
				
		// Matched Accounts
		foreach ($content['matched_accounts']['properties'] as $propertyId => $options) {
			if (array_key_exists('definition', $options) && $options['definition'] == 'inline') $property = $options;
			else {
				$property = $description['properties'][$propertyId];
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if (array_key_exists('labels', $options)) $property['labels'] = $options['labels'];
			}
			$content['matched_accounts']['properties'][$propertyId] = $property;
		}
		
		$matchedAccounts = array();
		if ($request->matched_accounts) {
			foreach (explode(',', $request->matched_accounts) as $matchedId) {
				$matchedAccounts[$matchedId] = Account::get($matchedId)->properties;
			}
		}
		$viewData['matched_accounts'] = $matchedAccounts;
		$viewData['matching_log'] = $request->matching_log;

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'imagePath' => 'img/'.$place_identifier.'/event_'.$type.'/',
			'id' => $id,
			'status' => $request->status,
			'mode' => $mode,
			'locale' => $locale,
			'description' => $description,
			'content' => $content,
			'viewData' => $viewData,
			'availableSkills' => $availableSkills,
		));
		$view->setTerminal(true); // Version sprint 07/09
		return $view;
	}

	public function updateAction()
	{
		$view = $this->detailAction();
		$view->setTerminal(true); // Version sprint 07/09
		return $view;
	}
	
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
	
	public function completeAction()
	{
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'event');
		$place = Place::get($context->getPlaceId());
		$id = $this->params()->fromRoute('id');
		$request = Event::get($id);
		if (!$request) {
			$this->response->setStatusCode('400');
			return $this->response;
		}
		$request->status = 'realized';
		$request->update(null);

		// Retrieve the content
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig($type.'/'.$place->identifier);
			if (!$content) $content = $context->getConfig($type.'/generic');
		}
		else $content = Config::get($place->identifier.'_'.$type, 'identifier')->content;

		// Email for feedback
		if ($request->matched_accounts && array_key_exists('emails', $content) && array_key_exists('feedback', $content['emails'])) {
			$url = $context->getServiceManager()->get('viewhelpermanager')->get('url');

			// Requestor
			$account = Account::get($request->account_id);
			$emailTitleFormat = $context->localize($content['emails']['feedback']['title']['format'], $request->locale);
			$titleArguments = array();
			foreach ($content['emails']['feedback']['title']['parameters'] as $parameter) {
				$titleArguments[] = $request->properties[$parameter];
			}
			$emailTitle = vsprintf($emailTitleFormat, $titleArguments);
			$emailBodyFormat = $context->localize($content['emails']['feedback']['body']['format'], $request->locale);
			$bodyArguments = array();
			foreach ($content['emails']['feedback']['body']['parameters'] as $parameter) {
				$bodyArguments[] = $request->properties[$parameter];
			}
			$emailBody = vsprintf($emailBodyFormat, $bodyArguments);
			Context::sendMail($request->email, $emailBody, $emailTitle);

			// Contributors
			foreach (explode(',', $request->matched_accounts) as $account_id) {
				$otherAccount = Account::get($account_id);
		
				// Email title
				$emailTitleFormat = $context->localize($content['emails']['feedback']['title']['format'], $request->locale);
				$titleArguments = array();
				foreach ($content['emails']['feedback']['title']['parameters'] as $parameter) {
					if ($parameter == 'n_first') $titleArguments[] = $otherAccount->n_first;
					else $titleArguments[] = $request->properties[$parameter];
				}
				$emailTitle = vsprintf($emailTitleFormat, $titleArguments);
					
				// Email body
				$emailBodyFormat = $context->localize($content['emails']['feedback']['body']['format'], $request->locale);
				$bodyArguments = array();
				foreach ($content['emails']['feedback']['body']['parameters'] as $parameter) {
					if ($parameter == 'n_first') $bodyArguments[] = $otherAccount->n_first;
					else $bodyArguments[] = $request->properties[$parameter];
				}
				$emailBody = vsprintf($emailBodyFormat, $bodyArguments);
					
				Context::sendMail($otherAccount->email, $emailBody, $emailTitle);
			}
		}
		
		$this->response->setStatusCode('200');
		return $this->response;
	}

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
				$url = $context->getServiceManager()->get('viewhelpermanager')->get('url');
	
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
				
				Context::sendMail($request->email.','.$account->email, $emailBody, $emailTitle);
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
		try {

			// Email
			$email = $this->getRequest()->getPost('transfer-email');
			if ($email) {
				$url = $context->getServiceManager()->get('viewhelpermanager')->get('url');
	
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
					elseif (substr($parameter, 0, 5) == 'label') $bodyArguments[] = $context->localize($description['properties'][substr($parameter, 6)]['labels']);
					elseif ($parameter == 'redirect_link') {
						$bodyArguments[] = $url('landing/template2', [], ['force_canonical' => true, 'query' => ['email' => $email, 'route' => 'flowEvent/propose', 'type' => $type, 'id' => $id]]);
					}
					else $bodyArguments[] = $request->properties[$parameter];
				}
				$emailBody = vsprintf($emailBodyFormat, $bodyArguments);

				Context::sendMail($email, $emailBody, $emailTitle, [$account->email => $account->n_first]);
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
	
	// Feedback
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
				$account->credits += $request->value;
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
			'accountType' => $context->getConfig('landing_account_type'),
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
		$event = Event::get($type, 'type', $identifier, 'identifier');
		if (!$event) {
			$this->response->setStatusCode('400');
			$this->response->setReasonPhrase('Unknown');
			return $this->response;
		}
		$account = Account::get($context->getContactId(), 'contact_1_id');
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
		$event->rewards[$account->id] = $earned;

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
}
