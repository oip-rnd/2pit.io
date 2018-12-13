<?php
namespace PpitFlow\Controller;

use PpitCommitment\Model\Commitment;
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

class PaymentController extends AbstractActionController
{
	public function addAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		
		// Data description
		$description = Account::getDescription($context->getConfig('landing_account_type'));
		
		// Place
		$place = Place::get($context->getPlaceId());
		$place_identifier = $place->identifier;
		
		// Account
		$account = Account::get($context->getContactId(), 'contact_1_id');
		
		// Locale
		$locale = $this->params()->fromQuery('locale');
	
		// Configuration
		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('payment/'.$place_identifier);
			if (!$content) $content = $context->getConfig('payment/generic');
		}
		else $content = Config::get($place_identifier.'_payment', 'identifier')->content;
	
		$commitment = Commitment::instanciate('generic');

		// Process the post data after input
		$message = $error = null;
		if ($this->request->isPost()) {
			$data = array(
				'account_id' => $account->id,
				'caption' => $content['caption'],
				'quantity' => $content['quantity'],
				'unit_price' => $content['unit_price'],
			);
			foreach ($content['form']['inputs'] as $inputId => $property) {
				if (array_key_exists('property_id', $property)) $propertyId = $property['property_id'];
				else $propertyId = $inputId;
				if ($property['updatable']) {
					if ($property['type'] == 'checkbox') {
						if (array_key_exists($propertyId, $data) && $data[$propertyId]) {
							if ($viewData[$inputId]) $data[$propertyId] .= ','.$viewData[$inputId];
						}
						else $data[$propertyId] = $viewData[$inputId];
					}
					elseif ($property['type'] == 'date') { // Workaround due to a bug in MDBootstrap that ignores formatSubmit
						$data[$propertyId] = substr($viewData[$propertyId], 6, 4).'-'.substr($viewData[$propertyId], 3, 2).'-'.substr($viewData[$propertyId], 0, 2);
					}
					else $data[$propertyId] = $viewData[$propertyId];
				}
			}
	
			$rc = $commitment->loadAndAdd($data, Commitment::getConfig('generic'));
			if (in_array($rc[0], ['200', '206'])) $message = 'OK';
			else $error = $rc;
		}
	
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'content' => $content,
			'message' => $message,
			'error' => $error,
		));
		$view->setTerminal(true);
		return $view;
	}
}
