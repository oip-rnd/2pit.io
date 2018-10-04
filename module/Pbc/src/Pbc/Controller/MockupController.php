<?php
namespace Pbc\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Formatter\FirePhp;

class MockupController extends AbstractActionController
{
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
			$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = Place::get($context->getPlaceId());
			$place_identifier = $place->identifier;
		}
		$links = $context->getConfig('public/'.$instance_caption.'/links');

		if ($context->getConfig('specificationMode') == 'config') $content = $context->getConfig('profile/'.$place_identifier);
		else $content = Config::get($place_identifier.'_profile', 'identifier')->content;
		$locale = $this->params()->fromQuery('locale');
		
		$token = null;
		$id = $this->params()->fromRoute('id');
		$account = null;
		if ($id) {
			$account = Account::get($id);
			$token = $this->params()->fromQuery('hash', null);
	    	if ($token != $account->authentication_token) return $this->redirect()->toRoute('landing/template2', ['place_identifier' => $place_identifier]);
		}
		elseif ($context->isAuthenticated()) {
			$account = Account::get($context->getContactId(), 'contact_1_id');
		}
		else $account = Account::instanciate($context->getConfig('landing_account_type'));

		if (!$locale) if ($account) $locale = $account->locale; else $locale = $context->getLocale();
		$links = $context->getConfig('public/'.$instance_caption.'/links');

		$viewData = array();
		$viewData['photo_link_id'] = ($account->photo_link_id) ? $account->photo_link_id : 'no-photo.png';
		
		// Skills
		$skills = ($this->params()->fromQuery('skills')) ? explode(',', $this->params()->fromQuery('skills'))  : [];
		$skillsText = implode(', ', array_map("\Pbc\Controller\MockupController::localize", $skills));

		// Locations
		$locations = ($this->params()->fromQuery('locations')) ? explode(',', $this->params()->fromQuery('locations'))  : [];
		$locationsText = implode(', ', array_map("\Pbc\Controller\MockupController::localize", $locations));
		
		$accounts = array(
			['n_first' => 'John', 'n_last' => 'Doe', 'json_property_1' => ['ui_design' => 'expert', 'infography' => 'base'], 'photo_link_id' => 'https://mdbootstrap.com/img/Photos/Avatars/img%20(9).jpg', 'featured' => [['type' => 'desire', 'text' => 'Aimerait aider une startup interne']]],
			['n_first' => 'IT4Girls', 'n_last' => 'Aston', 'json_property_1' => [], 'photo_link_id' => 'https://mdbootstrap.com/img/Photos/Avatars/img%20(20).jpg', 'featured' => [['type' => 'project', 'image' => 'https://mdbootstrap.com/img/Photos/Others/images/86.jpg', 'text' => 'IT4Girls féminise le coding']]],
			['n_first' => 'Maria', 'n_last' => 'Kate', 'json_property_1' => ['finance' => 'expert'], 'photo_link_id' => 'https://mdbootstrap.com/img/Photos/Avatars/img%20(10).jpg', 'featured' => [['type' => 'request', 'text' => 'Coaching accueil alternants']]],
			['n_first' => 'William', 'n_last' => 'Shakespeare', 'json_property_1' => ['hr' => 'expert', 'ux_design' => 'base'], 'photo_link_id' => 'https://www.probonocorpo.com/photos/no-photo.png', 'featured' => [['type' => 'desire', 'text' => 'Souhaite développer son réseau']/*,['type' => 'request', 'text' => 'Assistance process IT']*/]],
		);
		
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'place' => $place,
			'place_identifier' => $place_identifier,
			'id' => $id,
			'photo_link_id' => $account->photo_link_id,
			'token' => $token,
			'links' => $links,
			'viewData' => $viewData,
			'skills' => $skills,
			'skillsText' => $skillsText,
			'locations' => $locations,
			'locationsText' => $locationsText,
			'accounts' => $accounts,
			'content' => $content,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function result2Action()
	{
		return $this->resultAction();
	}

	public function result3Action()
	{
		$view = $this->resultAction();
		$view->accounts = array(
			['n_first' => 'John', 'n_last' => 'Doe', 'json_property_1' => ['ui_design' => 'expert', 'infography' => 'base'], 'photo_link_id' => 'https://mdbootstrap.com/img/Photos/Avatars/img%20(9).jpg', 'featured' => [['type' => 'request', 'text' => 'Qui peut me conseiller pour rédiger mes CGU ?']]],
			['n_first' => 'Projet IT4Girls', 'n_last' => '', 'json_property_1' => [], 'photo_link_id' => 'https://mdbootstrap.com/img/Photos/Avatars/img%20(20).jpg', 'featured' => [['type' => 'project', 'image' => 'https://mdbootstrap.com/img/Photos/Others/images/86.jpg', 'text' => 'Je recrute des formatrices Scratch pour cours enfants']]],
			['n_first' => 'Maria', 'n_last' => 'Kate', 'json_property_1' => ['finance' => 'expert'], 'photo_link_id' => 'https://mdbootstrap.com/img/Photos/Avatars/img%20(10).jpg', 'featured' => [['type' => 'request', 'text' => 'Je cherche un coach pour les journées d\'accueil alternant']]],
			['n_first' => 'John', 'n_last' => 'Doe', 'json_property_1' => ['hr' => 'expert', 'ux_design' => 'base'], 'photo_link_id' => 'https://mdbootstrap.com/img/Photos/Avatars/img%20(9).jpg', 'featured' => [/*['type' => 'desire', 'text' => 'Souhaite développer son réseau'],*/['type' => 'request', 'text' => 'J\'ai besoin d\'un IT Angel pour faire avancer mon projet infra']]],
		);
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
}
