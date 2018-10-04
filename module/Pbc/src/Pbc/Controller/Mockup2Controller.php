<?php
namespace Pbc\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Formatter\FirePhp;

class Mockup2Controller extends AbstractActionController
{
	public $accounts = array(
		1 => array(
			'id' => 1,
			'n_first' => 'Anna',
			'n_last' => 'Aston',
			'photo_link_id' => 'img%20(20).jpg',
			'property_1' => 'bucharest',
			'json_property_1' => ['finance' => 'expert'],
			'json_property_2' => ['ro', 'fr'],
		),
		2 => array(
			'id' => 2,
			'n_first' => 'John',
			'n_last' => 'Doe',
			'photo_link_id' => 'img%20(9).jpg',
			'property_1' => 'london',
			'json_property_1' => ['infography' => 'base'],
			'json_property_2' => ['en'],
		),
		3 => array(
			'id' => 3,
			'n_first' => 'Maria',
			'n_last' => 'Kate',
			'photo_link_id' => 'img%20(10).jpg',
			'property_1' => 'paris_def',
			'json_property_1' => ['ui_design' => 'base'],
			'json_property_2' => ['fr', 'en'],
		),
		4 => array(
			'id' => 4,
			'n_first' => 'Anna',
			'n_last' => 'Deynah',
			'photo_link_id' => 'img%20(26).jpg',
			'property_1' => 'paris_vdf',
			'json_property_1' => ['hr' => 'expert'],
			'json_property_2' => ['fr'],
		),
		5 => array(
			'id' => 5,
			'n_first' => 'Abbey',
			'n_last' => 'Clark',
			'photo_link_id' => 'img%20(31).jpg',
			'property_1' => 'bucharest',
			'json_property_1' => ['ux_design' => 'base'],
			'json_property_2' => ['ro', 'en'],
		),
		6 => array(
			'id' => 6,
			'n_first' => 'Blake',
			'n_last' => 'Dabney',
			'photo_link_id' => 'img%20(4).jpg',
			'property_1' => 'london',
			'json_property_1' => ['finance' => 'expert', 'infography' => 'base'],
			'json_property_2' => ['en'],
		),
		7 => array(
			'id' => 7,
			'n_first' => 'Andrea',
			'n_last' => 'Clay',
			'photo_link_id' => 'img%20(6).jpg',
			'property_1' => 'paris_def',
			'json_property_1' => ['infography' => 'base', 'ui_design' => 'base'],
			'json_property_2' => ['fr'],
		),
		8 => array(
			'id' => 8,
			'n_first' => 'Cami',
			'n_last' => 'Gosse',
			'photo_link_id' => 'img%20(7).jpg',
			'property_1' => 'paris_vdf',
			'json_property_1' => ['finance' => 'expert', 'hr' => 'base'],
			'json_property_2' => ['fr', 'en'],
		),
		9 => array(
			'id' => 9,
			'n_first' => 'Bobby',
			'n_last' => 'Haley',
			'photo_link_id' => 'img%20(8).jpg',
			'property_1' => 'bucharest',
			'json_property_1' => ['finance' => 'expert', 'infography' => 'base', 'ui_design' => 'base'],
			'json_property_2' => ['ro', 'fr'],
		),
		10 => array(
			'id' => 10,
			'n_first' => 'Elisa',
			'n_last' => 'Janson',
			'photo_link_id' => 'img%20(10).jpg',
			'property_1' => 'london',
			'json_property_1' => ['hr' => 'expert', 'ui_design' => 'base', 'ux_design' => 'base'],
			'json_property_2' => ['en'],
		),
		11 => array(
			'id' => 11,
			'n_first' => 'Robert',
			'n_last' => 'Jacobs',
			'photo_link_id' => 'img%20(9).jpg',
			'property_1' => 'paris_def',
			'json_property_1' => ['ux_design' => 'base', 'ui_design' => 'base'],
			'json_property_2' => ['fr', 'en'],
		),
	);

	public static function getDescription($type) {
		$context = Context::getCurrent();
		$description = array();
		$description['properties'] = array();
		$description['list'] = $context->getConfig('core_account/list/'.$type);
		foreach($context->getConfig('core_account/'.$type)['properties'] as $propertyId) {
			$property = $context->getConfig('core_account/'.$type.'/property/'.$propertyId);
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			$description['properties'][$propertyId] = $property;
		}
		return $description;
	}

	public function landingPageAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());
		$links = $context->getConfig('public/'.$instance_caption.'/links');
		
		$view = new ViewModel(array(
			'context' => $context,
			'place' => $place,
			'links' => $links,
			'counters' => [5, 3, 2, 2, 0],
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public static function localize($x) { 
		$context = Context::getCurrent();
		return (array_key_exists($x, $context->getConfig('pbc/skills'))) ? $context->localize($context->getConfig('pbc/skills')[$x]) : null;
	}
	
	public function resultAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());
		$links = $context->getConfig('public/'.$instance_caption.'/links');

		// Parameters
		$type = $this->params()->fromRoute('type', 'pbc');
		$perspective = $this->params()->fromRoute('perspective', 'account');

		// Skills
		$skills = ($this->params()->fromQuery('skills')) ? explode(',', $this->params()->fromQuery('skills'))  : [];
		$skillsText = implode(', ', array_map("\Pbc\Controller\MockupController::localize", $skills));

		// Available skills
		$availableSkills = array();
		foreach ($context->getConfig('pbc/skills') as $skillId => $skill) $availableSkills[$skillId] = $context->localize($skill);

		// Locations
		$locations = ($this->params()->fromQuery('locations')) ? explode(',', $this->params()->fromQuery('locations'))  : [];
		$locationsText = implode(', ', array_map("\Pbc\Controller\MockupController::localize", $locations));

		// Available locations
		$availableLocations = array();
		foreach ($context->getConfig('pbc/locations') as $locationId => $location) $availableLocations[$locationId] = $context->localize($location);

		// Languages
		$languages = ($this->params()->fromQuery('languages')) ? explode(',', $this->params()->fromQuery('languages'))  : [];
		$languagesText = implode(', ', array_map("\Pbc\Controller\MockupController::localize", $languages));

		// Available languages
		$availableLanguages = array();
		foreach ($context->getConfig('pbc/languages') as $languageId => $language) $availableLanguages[$languageId] = $context->localize($language);
		
		$view = new ViewModel(array(
			'context' => $context,
			'place' => $place,
			'links' => $links,
			'type' => $type,
			'perspective' => $perspective,
			'skills' => $skills,
			'skillsText' => $skillsText,
			'availableSkills' => $availableSkills,
			'locations' => $locations,
			'locationsText' => $locationsText,
			'availableLocations' => $availableLocations,
			'languages' => $languages,
			'languagesText' => $languagesText,
			'availableLanguages' => $availableLanguages,
			'accounts' => $this->accounts,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function fileAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());
		$links = $context->getConfig('public/'.$instance_caption.'/links');
		
		$accounts = array(
			array(
				'n_first' => 'Anna',
				'n_last' => 'Aston',
				'json_property_1' => ['finance' => 'expert', 'infography' => 'base'],
				'photo_link_id' => 'img%20(20).jpg',
				'location' => 'london',
				'language' => 'en',
				'availability' => ['Lundi après 16h - Jeudi matin 1 sem. / 2', 'En congés en Juillet'],
			),
		);
		
		$view = new ViewModel(array(
			'context' => $context,
			'place' => $place,
			'links' => $links,
			'accounts' => $accounts,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function v1Action()
	{
		$context = Context::getCurrent();
		
		// Parameters
		$type = $this->params()->fromRoute('type');
		$perspective = $this->params()->fromRoute('perspective');
		$identifier = $this->params()->fromRoute('identifier');

		$content = array(
			'description' => Account::getDescription($type),
		);
		
		// Get
		if ($this->request->isGet()) {
			if ($identifier) {
		
				// Direct access mode
				$account = Account::get($identifier, 'identifier');
				if (!$account) {
					$this->getResponse()->setStatusCode('400');
					return $this->getResponse();
				}
				$content = $account->getProperties();
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
				$statusDef = $content['description']['properties']['status'];
				if ($statusDef['definition'] != 'inline') $statusDef = $context->getConfig($statusDef['definition']);
				if (!array_key_exists('status', $filters)) $filters['status'] = implode(',', $statusDef['perspectives'][$perspective]);
				$accounts = Account::getList($type, $filters, $order, $limit, null, $page, $per_page);
				$content['data'] = array();
				foreach ($accounts as $account) $content['data'][$account->id] = $account->getProperties();
			}
		}

		// Old
/*		$content = array(
			'description' => $context->getConfig('core_account/pbc'),
			'data' => array(
			),
		);*/

		$skills = $this->params()->fromQuery('skills');
		if ($skills) $skills = explode(',', $skills);
		else $skills = [];
		
		$locations = $this->params()->fromQuery('locations');
		if ($locations) $locations = explode(',', $locations);
		else $locations = [];
		
		$languages = $this->params()->fromQuery('languages');
		if ($languages) $languages = explode(',', $languages);
		else $languages = [];
/*		
		foreach ($this->accounts as $account) {
			if (!$skills) $skill = true;
			else {
				$skill = false;
				foreach ($skills as $skillId) {
					if (array_key_exists($skillId, $account['json_property_1'])) {
						$skill = true;
						break;
					}
				}
			}
			if (!$languages) $language = true;
			else {
				$language = false;
				foreach ($languages as $languageId) {
					if (in_array($languageId, $account['json_property_2'])) {
						$language = true;
						break;
					}
				}
			}
			if (!$locations) $location = true;
			else {
				$location = false;
				foreach ($locations as $locationId) {
					if ($locationId == $account['location']) {
						$location = true;
						break;
					}
				}
			}
			if ($skill && $language && $location) $content['data'][$account['id']] = $account;
		}*/
		
		$view = new ViewModel(array(
			'context' => $context,
			'content' => $content,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function subscriptionAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());

		$content = array(
			'description' => $context->getConfig('core_account/pbc'),
			'accounts' => [],
			'data' => [],
		);
		
		$contributors = $this->params()->fromQuery('contributors');
		if ($contributors) $contributors = explode(',', $contributors);
		else $contributors = [];
		$i = 0;
		foreach ($contributors as $contributorId) {
			if ($i++ < 3) {
				$account = Account::get($contributorId);
				$content['accounts'][$contributorId] = $account->getProperties();
			}
		}

		$request = $this->getRequest();
		$fqdn = $request->getUri()->getHost();
		$type = 'business';

		$email = $request->getPost('email');
		$account = null;
		if ($email) {
			$contact = Vcard::get($email, 'email');
			if ($contact) $account = Account::get($contact->id, 'contact_1_id');
			else $contact = Vcard::instanciate();
		}
		else $contact = Vcard::instanciate();
		if (!$account) {
			$account = Account::instanciate($type);
			$account->place_id = $place->id;
			$account->opening_date = date('Y-m-d');
			$account->origine = 'subscription';
		}

		$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
		$message = null;
		$data = array();
		$data['n_title'] = $request->getPost('n_title');
		$data['n_first'] = $request->getPost('n_first');
		$data['n_last'] = $request->getPost('n_last');
		$data['email'] = $request->getPost('email');
		$data['tel_work'] = $request->getPost('tel_work');
		$data['name'] = $request->getPost('name');
		$data['adr_street'] = $request->getPost('adr_street');
		$data['adr_zip'] = $request->getPost('adr_zip');
		$data['adr_city'] = $request->getPost('adr_city');
		$data['property_2'] = 'p-pit-commitment';
		$data['property_4'] = $request->getPost('property_4');
		$data['property_5'] = $request->getPost('property_5');

    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		if ($csrfForm->isValid()) {

				if ($contact->loadData($data) == 'OK') {
					if ($account->loadData($type, $data) == 'OK') {
						if (!$account->name) $account->name = $contact->n_fn;

						// Atomically save
						$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
						$connection->beginTransaction();
						try {
							if (!$contact->id) {
								$rc = $contact->add();
								if ($rc != 'OK') $error = $rc;
							}

							if (!$error) {
								$account->contact_1_id = $contact->id;
								$account->contact_1_status = 'main';
								if (!$account->callback_date || $account->callback_date > date('Y-m-d')) $account->callback_date = date('Y-m-d');
								$account->contact_history[] = array(
										'time' => date('Y-m-d H:i:s'),
										'n_fn' => $contact->email,
										'comment' => "Request: ",
								);
								if ($account->id) $rc = $account->update(null);
								else $rc = $account->add();
								if ($rc != 'OK') {
									$error = $rc;
									$connection->rollback();
								}
								$id = $account->id;
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
					}
				}
    		}
    	}
    	$content['data'] = $data;
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'place' => $place,
				'fqdn' => $fqdn,
				'content' => $content,
				'message' => $message,
		));
    	$view->setTerminal(true);
		return $view;
	}
}
