<?php
namespace PpitFlow\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Event;
use PpitCore\Model\Place;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EmailingController extends AbstractActionController
{
	public function compare($a, $b)
	{
		if ($a['rank'] == $b['rank']) {
			return 0;
		}
		return ($a['rank'] > $b['rank']) ? -1 : 1;
	}
	
	public function requestListAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'request');
		$description = Event::getDescription($type);
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		$myAccount = Account::get($context->getContactId(), 'contact_1_id');
		$locale = $this->params()->fromQuery('locale', 'fr_FR');
		$mode = 'Public';
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
				elseif ($myAccount && $entity == 'profile') $value = $myAccount->properties[$property];
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
			if ($myAccount && $myAccount->id == $request->account_id) $content['data'][$request->id]['role'] = 'requestor';
			elseif (in_array($myAccount && $myAccount->id, explode(',', $request->matched_accounts))) $content['data'][$request->id]['role'] = 'contributor';
			else $content['data'][$request->id]['role'] = null;
			if (in_array($request->status, ['new', 'connected'])) {
				if ($myAccount && $myAccount->id != $request->account_id && !in_array($myAccount->id, explode(',', $request->matched_accounts))) {
					$actions['propose'] = $content['actions']['Public']['propose'];
				}
				if (array_key_exists('transfer', $content['actions']['Public'])) $actions['transfer'] = $content['actions']['Public']['transfer'];
			}
			$content['data'][$request->id]['PublicActions'] = $actions;
		}
			
		if ($type == 'request') uasort($content['data'], array($this, 'compare'));
	
		$view = new ViewModel(array(
			'context' => $context,
			'mode' => $mode,
			'contentDescription' => $content,
			'locale' => $locale,
			'requestUri' => $this->request->getRequestUri(),
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function generateAction()
	{
		$context = Context::getCurrent();
		$client = new Client(
			$this->url()->fromRoute('probonocorpo/newsletter', [], ['force_canonical' => true]),
			array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
		);
		$client->setMethod('GET');
		echo $client->send()->getBody();
		return $this->response;
	}

	public function serializeAction() {
		$context = Context::getCurrent();
		
		// Parameters
		$place_identifier = $this->params()->fromRoute('place_identifier');
		$place = Place::get($place_identifier, 'identifier');
		$category = $this->params()->fromQuery('category');
		$config = Config::get($place_identifier.'_'.$category, 'identifier', $place->id);
		if (!$config) $config = Config::instanciate($place->id, $place_identifier.'_'.$category);
		$data['content'] = $context->getConfig($category.'/'.$place_identifier);
		foreach ($data['content'] as $emailContentId => &$emailContent) {
			if (array_key_exists('route', $emailContent)) {
				$client = new Client(
					$this->url()->fromRoute($emailContent['route'], ['query' => ['mode' => 'personnalized']], ['force_canonical' => true]),
					array('adapter' => 'Zend\Http\Client\Adapter\Curl', 'maxredirects' => 0, 'timeout' => 30)
				);
				$client->setMethod('GET');
				$emailContent['text'] = $client->send()->getBody();
				$emailContent['params'] = array();
			}
			echo $emailContent['text'];
		}
		print_r($config->loadAndUpdate($data));
		echo json_encode($data['content'], JSON_PRETTY_PRINT);
		return $this->response;
	}
}
