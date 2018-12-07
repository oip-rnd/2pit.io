<?php
namespace PpitFlow\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EmailingController extends AbstractActionController
{
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
