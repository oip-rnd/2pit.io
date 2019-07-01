<?php
namespace PpitFlow\Controller;

use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\Term;
use PpitContact\Model\ContactMessage;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Config;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use PpitCore\Model\User;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CatalogueController extends AbstractActionController
{
	public static function getContent($place_identifier)
	{
		$context = Context::getCurrent();
		
		$content = null;
		if ($context->getConfig('specificationMode') == 'database') {
			$config = Config::get($place_identifier.'_catalogue', 'identifier');
			if ($config) $content = $config->content;
		}
		if (!$content) {		
			$content = $context->getConfig('catalogue/'.$place_identifier);
			if (!$content) $content = $context->getConfig('catalogue/generic');
		}
		
		foreach ($content['complete']['recipient_properties'] as $propertyId => &$property) {
			if ($property['definition'] != 'inline') {
				$definition = $context->getConfig($property['definition']);
				foreach ($definition as $itemId => $item) $property[$itemId] = $item;
			}
		}
		return $content;
	}
	
	public function notifyNew($content, $account, $place, $property_1)
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the template
		$template = $content['contact']['email_template'];
	
		// Generate the email
		$data = array();
		$basePath = $this->getRequest()->getUri()->getPath();
		$data['name'] = $account->name;
		$data['property_1'] = $property_1;
		$data['email'] = $account->email;
		$data['tel_cell'] = $account->tel_cell;
		$data['type'] = 'email';
		$data['to'][$place->support_email] = null;
		$data['from_mail'] = $place->support_email;
		$data['from_name'] = $place->caption;
		$data['subject'] = $template['subject'];
		$arguments = array();
		foreach ($template['subject']['params'] as $param) $arguments[] = $data[$param];
		$data['subject'] = vsprintf($context->localize($data['subject']['text']), $arguments);
	
		$data['body'] = $context->localize($template['body']['text']);
		$arguments = array();
		foreach ($template['body']['params'] as $param) $arguments[] = $data[$param];
		$data['body'] = vsprintf($data['body'], $arguments);
	
		if ($place && array_key_exists('core_account/sendMessage', $place->config)) $signature = $place->config['core_account/sendMessage']['signature'];
		else $signature = $context->getConfig('core_account/sendMessage')['signature'];
		if ($signature['definition'] != 'inline') {
			$signature = $context->getConfig($signature['definition']);
		}
		$data['body'] .= $context->localize($signature['body']);
	
		return $data;
	}
	
	public function indexAction()
	{
		// Retrieve context and parameters
		$context = Context::getCurrent();

		$type = $this->params()->fromRoute('type', 'generic');
		$place_identifier = $this->params()->fromRoute('place_identifier', $context->getPlace()->identifier);
		$rates = $context->getConfig('catalogue/product/rates');
		$content = CatalogueController::getContent($place_identifier);

		// Account and commitments
		$profile = $context->getProfile();
		$commitment_id = $this->params()->fromQuery('commitment_id');
		$commitment = null;
		if ($profile) {
			$commitments = Commitment::getList($type, ['account_id' => $profile->id], 'id', 'desc', 'search');
			if (!$commitment_id) {
				foreach ($commitments as $commitment) {
					if ($commitment->status == 'new') {
						$commitment_id = $commitment->id;
						break;
					}
				}
			}
		}
		else {
			$profile = Account::instanciate($type);
			$commitments = [];
		}
		
		// Authentication
		$panel = $this->params()->fromQuery('panel');
		$email = $this->params()->fromQuery('email');
		$error = $this->params()->fromQuery('error');
		$message = $this->params()->fromQuery('message');
		$redirect = $this->params()->fromQuery('redirect', 'home');
		if ($email && !$context->isAuthenticated()) {
			$vcard = Vcard::get($email, 'email');
			$profile->email = $email;
			if ($vcard) {
				$userContact = UserContact::get($vcard->id, 'vcard_id');
				if ($userContact) $panel = 'modalLoginForm';
				$profile->n_first = $vcard->n_first;
				$profile->n_last = $vcard->n_last;
			}
			else {
				$profile->n_first = $this->params()->fromQuery('n_first');
				$profile->n_last = $this->params()->fromQuery('n_last');
			}
			if ($panel != 'modalLoginForm') {
				$panel = 'modalRegisterForm';
			}
		}
		elseif ($commitment_id && $context->isAuthenticated()) {
			$commitment = Commitment::get($commitment_id);
			$panel = 'modalCompleteForm';
		}

		$view = new ViewModel(array(
			'context' => $context,
			'type' => $type,
			'place_identifier' => $place_identifier,
			'content' => $content,
			'rates' => $rates,
			'profile' => $profile,
			'commitments' => $commitments,
			'commitment_id' => $commitment_id,
			'commitment' => $commitment,
			'requestUri' => $this->request->getRequestUri(),
			'viewController' => 'ppit-flow/view-controller/catalogue-scripts.phtml',
			
			'token' => $this->params()->fromQuery('hash', null),
			'panel' => $panel,
			'email' => $email,
			'redirect' => $redirect,
			'message' => $message,
			'error' => $error,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function subscribeAction()
	{
		// Retrieve the context, the content and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'generic');
		$place_identifier = $this->params()->fromRoute('place_identifier');
		$rates = $context->getConfig('catalogue/product/rates');
		$content = CatalogueController::getContent($place_identifier);
		$product = $this->params()->fromQuery('product');
	
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'content' => $content,
			'product' => $product,
			'rates' => $rates,
			'actionStatus' => null,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function contactAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'generic');
		$place_identifier = $this->params()->fromRoute('place_identifier');
		$content = CatalogueController::getContent($place_identifier);
		$account = $context->getProfile();
		if (!$account) $account = Account::instanciate($type);
	
		// Retrieve the request content
		$cart = json_decode($this->request->getPost('cart'), true);
	
		// CSRF protection
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
	
		// Process the post data after input
		$actionStatus = null;
		if ($this->request->getPost('complete')) {
	
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->request->getPost());
	
			if ($csrfForm->isValid()) { // CSRF check
	
				// Atomicity
				$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
				$connection->beginTransaction();
				try {
						
					// Create the account if no exists for the user
					if (!$account->id) {
						$data = array();
						$data['place_id'] = $context->getPlaceId();
						$data['status'] = 'interested';
						$data['n_first'] = $this->request->getPost('n_first');
						$data['n_last'] = $this->request->getPost('n_last');
						$data['email'] = $this->request->getPost('email');
						$data['tel_cell'] = $this->request->getPost('tel_cell');
						$data['property_1'] = $this->request->getPost('property_1');
						$data['callback_date'] = date('Y-m-d');
						$actionStatus = $account->loadAndAdd($data);
						if ($actionStatus[0] == '206') $account = Account::get($actionStatus[1]);
					}
					if (in_array($actionStatus[0], ['200', '206'])) {

						// Notify the back-office of the new contact
						$place = $context->getPlace();
						$data = $this->notifyNew($content, $account, $place, $this->request->getPost('property_1'));
						$mail = ContactMessage::instanciate();
						$mail->type = 'email';
						if ($mail->loadData($data) != 'OK') throw new \Exception('View error');
						$rc = $mail->add();
						if ($rc != 'OK') {
							$connection->rollback();
							$actionStatus = ['500'];
						}
						else $connection->commit();
					}
					else $connection->rollback();
				}
				catch (\Exception $e) {
					$connection->rollback();
					$actionStatus = ['500'];
				}
			}
		}
	
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'content' => $content,
			'account' => $account,
			'cart' => $cart,
			'csrfForm' => $csrfForm,
			'actionStatus' => $actionStatus,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function calculateAction()
	{
		// Context and parameters
		$context = Context::getCurrent();
		$place_identifier = $this->params()->fromRoute('place_identifier');
		$rates = $context->getConfig('catalogue/product/rates');

		$subscription_amount = 0;
		$subscriptions = array();
		
		// Retrieve the request content
		$content = json_decode($this->request->getContent($place_identifier), true);
		
		// Retrieve the quantities of subscribed products, compute the products amount and log the subscriptions
		foreach ($content['products'] as $productId => &$row) {
			$quantity = $row['quantity'];
			$product = $rates['variants'][$productId];
			$unit_price = $product['unit_price'];
			$amount = 0;
			for ($j = 0; $j < $quantity; $j++) {
				$amount += $unit_price;
				$subscriptions[] = $unit_price;
			}
			$row['caption'] = $product['caption'];
			$row['unit_price'] = $unit_price;
			$row['amount'] = $amount;
			$subscription_amount += $amount;
		}
		$content['subscription_amount'] = $subscription_amount;
		
		// Retrieve the quentities of subscribed options and add the options amount
		$options_amount = 0;
		foreach ($content['options'] as $optionId => &$row) {
			$quantity = $row['quantity'];
			$option = $rates['options'][$optionId];
			$unit_price = $option['unit_price'];
			$amount = $unit_price * $quantity;
			$row['caption'] = $option['caption'];
			$row['unit_price'] = $unit_price;
			$row['amount'] = $amount;
			$options_amount += $amount;
		}

		$content['options_amount'] = $options_amount;
		$content['including_options_amount'] = $subscription_amount + $options_amount;

		// Order the subscriptions by ascending amount and give the progressive discounts per subscription
		$discount_amount = 0;
		sort($subscriptions);
		reset($rates['discounts']['multiple_subscription']['progressiveness']);
		$discount_rate = 0;
		$content['discounts'] = array();
		foreach ($subscriptions as $unit_price) {
			$discount = round(- $unit_price * $discount_rate, 2);
			if ($discount != 0) {
				$content['discounts'][] = ['caption' => $rates['discounts']['multiple_subscription']['caption'], 'basis' => $unit_price, 'rate' => $discount_rate, 'amount' => $discount];
				$discount_amount += $discount;
			}
			$next = next($rates['discounts']['multiple_subscription']['progressiveness']);
			if ($next !== false) $discount_rate = $next;
		}
		$content['discount_amount'] = $discount_amount;

		$content['total_amount'] = $subscription_amount + $options_amount + $discount_amount;

		// Return the content augmented with the computed amount and discounts in JSON format
		$this->response->setContent(json_encode($content, JSON_PRETTY_PRINT));
		return $this->response;
	}

	public function cartAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$type = $this->params()->fromRoute('type', 'generic');
		$place_identifier = $this->params()->fromRoute('place_identifier');
		$rates = $context->getConfig('catalogue/product/rates');
		$content = CatalogueController::getContent($place_identifier);
		$account = $context->getProfile();
		if (!$account) $account = Account::instanciate($type);
		$commitment_id = null;

		// Retrieve the request content
		$cart = json_decode($this->request->getPost('cart'), true);

		// CSRF protection
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
	
		// Process the post data after input
		$actionStatus = null;
		if ($this->request->getPost('complete')) {
	
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->request->getPost());
			 
			if ($csrfForm->isValid()) { // CSRF check

				// Atomicity
				$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
				$connection->beginTransaction();
				try {
					
					// Create the account if no exists for the user
					if (!$account->id) {
						$data = array();
						$data['status'] = 'candidate';
						$data['n_first'] = $this->request->getPost('n_first');
						$data['n_last'] = $this->request->getPost('n_last');
						$data['email'] = $this->request->getPost('email');
						$data['tel_cell'] = $this->request->getPost('tel_cell');
						$data['adr_street'] = $this->request->getPost('adr_street');
						$data['adr_extended'] = $this->request->getPost('adr_extended');
						$data['adr_zip'] = $this->request->getPost('adr_zip');
						$data['adr_city'] = $this->request->getPost('adr_city');
//						$data['property_1'] = 'tennis';
						$actionStatus = $account->loadAndAdd($data);
						if ($actionStatus[0] == '206') $account = Account::get($actionStatus[1]);
					}
					
					// Generate a commitment
					$commitment = Commitment::instanciate($type);
					$options = array();
					$start = '9999-12-31';
					foreach ($cart['products'] as $productId => $row) {
						if ($row['start'] < $start) $start = $row['start'];
						$options[] = array(
							'identifier' => $productId,
							'caption' => $context->localize($row['caption']) . ' - ' . $row['description'],
							'unit_price' => $row['unit_price'],
							'quantity' => $row['quantity'],
							'amount' => $row['amount'],
						);
					}
					foreach ($cart['options'] as $optionId => $row) {
						$options[] = array(
							'identifier' => $optionId,
							'caption' => $context->localize($row['caption']),
							'unit_price' => $row['unit_price'],
							'quantity' => $row['quantity'],
							'amount' => $row['amount'],
						);
					}
					if (array_key_exists('discounts', $cart)) {
						foreach ($cart['discounts'] as $row) {
							$options[] = array(
								'identifier' => 'multiple_subscription_discount',
								'caption' => $context->localize($rates['discounts']['multiple_subscription']['caption']),
								'unit_price' => - $row['basis'],
								'quantity' => $row['rate'],
								'amount' => - $row['amount'],
							);
						}
					}
					$data = array(
						'account_id' => $account->id,
						'year' => date('Y'),
						'caption' => sprintf($context->localize($content['commitment']['caption']['labels']), $context->decodeDate(date('Y-m-d'))),
						'options' => $options,
					);
					$actionStatus = $commitment->loadAndAdd($data, Commitment::getConfig('generic'));
					if ($actionStatus[0] == '200') {
							
						// Generate the terms
						$startDate = date('Y-m-d', strtotime($start.'- 15 days'));
						
						if ($startDate < date('Y-m-d')) {

							// One global term if the course start date - 15 days is before current date
							$term = Term::instanciate($type, $commitment->id);
							$data = array(
								'caption' => $context->localize($content['commitment']['terms']['whole']['caption']),
								'due_date' => date('Y-m-d'),
								'amount' => $commitment->tax_inclusive,
								'means_of_payment' => 'bank_card',
							);
							$actionStatus = $term->loadAndAdd($data);
						}
						else {

							foreach ($content['commitment']['terms']['scheduled'] as $termDescription) {
	
								$termAmount = round($commitment->tax_inclusive * $termDescription['share'], 2);
								if ($termDescription['type'] == 'deposit') $termDate = date('Y-m-d');
								else $termDate = max($startDate, date('Y-m-d', strtotime($start . $termDescription['days'] . ' days')));
								$term = Term::instanciate($type, $commitment->id);
								$data = array(
									'caption' => $context->localize($termDescription['caption']),
									'due_date' => $termDate,
									'amount' => $termAmount,
									'means_of_payment' => 'bank_card',
								);
								$actionStatus = $term->loadAndAdd($data);
							}
						}
						if ($actionStatus[0] == '200') {
							$account->shopping_cart = $commitment->id;
							$account->update(null);
						}
					}
		
					if ($actionStatus[0] == '200') {
						$connection->commit();
						$commitment_id = $commitment->id;
					}
					else $connection->rollback();
				}
				catch (\Exception $e) {
					$connection->rollback();
					$actionStatus = ['500'];
				}
			}
		}

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'content' => $content,
			'account' => $account,
			'cart' => $cart,
			'commitment_id' => $commitment_id,
			'csrfForm' => $csrfForm,
			'actionStatus' => $actionStatus,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function completeAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$place_identifier = $this->params()->fromRoute('place_identifier');
		$locale = $this->params()->fromQuery('locale');
		$content = CatalogueController::getContent($place_identifier);
		$account = Account::get($context->getContactId(), 'contact_1_id');
		$commitment_id = $this->params()->fromRoute('commitment_id');
		$commitment = Commitment::get($commitment_id);
		if (!$commitment || $commitment->account_id != $account->id) {
			$this->getResponse()->setStatusCode('401'); // Unauthorized
			return $this->response;
		}

		// CSRF protection
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
	
		// Process the post data after input
		$actionStatus = null;
		if ($this->request->isPost()) {
	
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($this->request->getPost());
			 
			if (!$csrfForm->isValid()) {
				$this->getResponse()->setStatusCode('401'); // Unauthorized
				return $this->response;
			}

			// Atomicity
			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {

				$first = true; 
				foreach ($commitment->options as &$option) {
				    if (array_key_exists($option['identifier'], $context->getConfig('catalogue/product/rates')['variants'])) {
						for ($i = 0; $i < $option['quantity']; $i++) {
	
							foreach ($content['complete']['recipient_properties'] as $property_id => $property) {
								
								$value = $this->request->getPost('complete-' . $property_id . '-' . $option['identifier'] . '-' . $i);
								if ($property['type'] == 'date') $value = $context->decodeDate($value);
								if (array_key_exists('mask', $property)) $value = sprintf($property['mask'], $value);
								$commitment->description .= $value . ', ';
							}
						}
				    }
				}
				$commitment->status = 'approved';
				$commitment->update(null);
				$connection->commit();
				$actionStatus = ['200'];
			}
			catch (\Exception $e) {
				$connection->rollback();
				$actionStatus = ['500'];
			}
		}

		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'content' => $content,
			'locale' => $locale,
			'account' => $account,
			'commitment' => $commitment,
			'csrfForm' => $csrfForm,
			'actionStatus' => $actionStatus,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function invoiceListAction()
	{
		// Retrieve context and parameters
		$context = Context::getCurrent();
		$place_identifier = $this->params()->fromRoute('place_identifier');
		$content = CatalogueController::getContent($place_identifier);
		$account_id = $this->params()->fromRoute('account_id');

		// Account and commitments
		$account = Account::get($account_id);
		if ($account) $commitments = Commitment::getList($account->type, ['account_id' => $account->id], 'id', 'desc', 'search');		
		else $commitments = [];
		
		$view = new ViewModel(array(
			'context' => $context,
			'content' => $content,
			'account' => $account,
			'commitments' => $commitments,
		));
		$view->setTerminal(true);
		return $view;
	}
}
