<?php
namespace PpitFlow\Controller;

use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\Term;
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

class FunnelController extends AbstractActionController
{
	public function subscribeAction()
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
		else $account = Account::instanciate($accountType);
	
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
					$data['status'] = 'registered';
					$data['email'] = $this->request->getPost('email');
					$data['n_first'] = $this->request->getPost('n_first');
					$data['n_last'] = $this->request->getPost('n_last');
					$data['password'] = $this->request->getPost('password');
					$data['locale'] = $this->request->getPost('locale');
					$data['origine'] = $this->request->getPost('origine');
						
					$actionStatus = $account->loadAndAdd($data, $description);
					if (!in_array($actionStatus[0], ['200', '206'])) $connection->rollback();
					else {
	
						// Check that the user does not already exist
						$user = User::getTable()->transGet($account->email, 'username');
						if ($user) {
							$actionStatus = ['206', 'Already exist with email address'];
							$connection->rollback();
						}
	
						else {
	
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
									
								// Send the OTP by email
								$email_body = $context->localize($context->getConfig('user/messages/activation/text'));
								$link = $this->url()->fromRoute('user/v1', ['id' => $user->user_id], ['force_canonical' => true]).'?account_id='.$account->id.'&request=activate'.'&hash='.$token;
								$email_body = sprintf($email_body, $link);
								$email_title = $context->localize($context->getConfig('user/messages/activation/title'));
								Context::sendMail($user->username, $email_body, $email_title, null);
	
								$actionStatus = ['200'];
								$connection->commit();
							}
						}
					}
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
	
	public function payzenAction()
    {
    	// Context and config
    	$context = Context::getCurrent();
    	$payZenConfig = $context->getConfig('ppitUserSettings')['safe'][$context->getInstance()->caption]['PayZen'];
    	
    	// Term id
    	$commitment_id = $this->params()->fromRoute('commitment_id');
    	$commitment = Commitment::get($commitment_id);
    	if (count($commitment->terms) == 1) $payment_config = 'SINGLE';
    	else {
			$payment_config = 'MULTI_EXT:';
			$first = true;
			foreach ($commitment->terms as $term) {
				if (!$first) $payment_config .= ';';
				$first = false;
				$payment_config .= substr($term->due_date, 0, 4) . substr($term->due_date, 5, 2) . substr($term->due_date, 8, 2) . '=' . $term->amount * 100;
			}
    	}
    	
    	// PayZen form date
    	$formData = array(
    		'vads_action_mode' => 'INTERACTIVE',
    		'vads_amount' => 100, //$commitment->tax_inclusive * 100,
    		'vads_capture_delay' => '0',
    		'vads_ctx_mode' => 'TEST',
    		'vads_currency' => '978',
    		'vads_page_action' => 'PAYMENT',
    		'vads_payment_config' => $payment_config,
    		'vads_return_mode' => 'POST',
    		'vads_site_id' => '88978876',
    		'vads_trans_date' => date('YmdHis'),
    		'vads_trans_id' => sprintf('%06d', $commitment_id),
    		'vads_url_return' => $this->url()->fromRoute($context->getConfig('defaultRoute'), [], ['force_canonical' => true, 'query' => ['email' => $commitment->email]]),
    		'vads_validation_mode' => '0',
    		'vads_version' => 'V2',
    	);

    	$signature = '';
    	foreach ($formData as $name => $value) $signature .= $value . '+';
    	$formData['signature'] = base64_encode(hash_hmac('sha256', $signature . $payZenConfig['key'], $payZenConfig['key'], true));    	

    	$view = new ViewModel(array(
    		'context' => $context,
    		'formData' => $formData,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function payzenReturnAction()
    {
		// Context
    	$context = Context::getCurrent();
    	$payZenConfig = $context->getConfig('ppitUserSettings')['safe'][$context->getInstance()->caption]['PayZen'];
 
    	$writer = new \Zend\Log\Writer\Stream('data/log/payzen.txt');
    	$logger = new \Zend\Log\Logger();
    	$logger->addWriter($writer);
    	$logger->info(date('Y-m-d H:i:s'));
    	 
    	// Form data
    	$form = explode('&', $this->request->getPost()->toString());
    	$formData = array();
    	foreach ($form as $var) {
    		$tuplet = explode('=', $var);
    		if (substr($tuplet[0], 0, 5) == 'vads_') $formData[$tuplet[0]] = $tuplet[1];
    		elseif ($tuplet[0] == 'signature') $receivedSignature = $tuplet[1];
    	}

    	$signature = '';
    	ksort($formData);
    	foreach ($formData as $name => $value) {
    		$logger->info($name . ' => ' . $value);
    		$signature .= $value . '+';
    	}
    	$signature = base64_encode(hash_hmac('sha256', $signature . $payZenConfig['key'], $payZenConfig['key'], true));

//    	if ($signature == $receivedSignature) {
    		if (	in_array($formData['vads_url_check_src'], ['PAY', 'BATCH_AUTO', 'RETRY']) 
    			&& 	$formData['vads_operation_type'] == 'DEBIT'
    			&&	$formData['vads_trans_status'] == 'AUTHORISED') {
	    		
		    	// Term
		    	$commitment = Commitment::get((int)$formData['vads_trans_id']);
		    	$logger->info('commitment: ' . (int)$formData['vads_trans_id'] . ', ' . $commitment->status . ', ' . $commitment->tax_inclusive);
		    	foreach ($commitment->terms as $term) {
		    		
		    		if (	!in_array($term->status, ['collected', 'registered'])
		    			&&	$term->due_date <= date('Y-m-d')
		    			&&	$term->amount == $formData['vads_effective_amount'] / 100) {

		    			$logger->info('term: ' . $term->id . ', ' . $term->status . ', ' . $term->amount . ' => Updated');
		    			$logger->info('term_id: ' . $term->id);
				    	$term->status = 'collected';
				    	$term->collection_date = $term->settlement_date = date('Y-m-d');
				    	$term->update(null);
				    	$break;
		    		}
		    		else {
		    			$logger->info('term: ' . $term->id . ', ' . $term->status . ', ' . $term->amount . ' => Skipped');
		    		}
		    	}
    		}
	    	$logger->info('status: 200');
    		$this->getResponse()->setStatusCode('200');
/*    	}
		else {
	    	$logger->info('status: 500');
			$this->getResponse()->setStatusCode('500');
		}*/
    	return $this->response;
    }
	
	public function paypalAction()
	{
		// Retrieve the context and the parameters
		$context = Context::getCurrent();
		$step = $this->params()->fromQuery('step');
		$funnel = null;
		
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
/*		if ($context->getConfig('specificationMode') == 'config') {
			$content = $context->getConfig('payment/'.$place_identifier);
			if (!$content) $content = $context->getConfig('payment/generic');
		}
		else $content = Config::get($place_identifier.'_payment', 'identifier')->content;*/
	
		$commitment = Commitment::instanciate('generic');
		$commitmentIds = $account->shopping_cart;
		$commitments = array();
		if ($commitmentIds) {
			$commitmentIds = explode(',', $commitmentIds);
			foreach ($commitmentIds as $commitment_id) {
				$commitment = Commitment::get($commitment_id);
				$commitments[$commitment_id] = $commitment;
			}
		}

		// Process the post data after input
		$actionStatus = ['200', 'OK'];
		if ($step == 'confirmed') {
				
			// Atomicity
			$connection = Account::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				// Mark the commitments as invoiced
/*				foreach ($commitments as $commitment_id => $commitment) {
					$commitment->status = 'invoiced';
					$rc = $commitment->update(null);
					if ($rc != 'OK') {
						$actionStatus = ['400', $rc];
						$connection->rollback();
					}
					else {

						// Mark the commitment's terms as collected
						$terms = Term::getList($commitment->type, ['commitment_id' => $commitment_id], '+id', null);
						foreach ($terms as $term_id => $term) {
							$term->status = 'collected';
							$term->settlement_date = $term->collection_date = date('Y-m-d');
							$rc = $term->update(null);
							if ($rc != 'OK') {
								$actionStatus = ['400', $rc];
								$connection->rollback();
							}
						}
					}
				}*/
				
				// Deliver the order
				if ($context->getConfig('specificationMode') == 'database') {
					$config = Config::get($place->identifier.'_funnel', 'identifier', $place->id);
					if ($config) $funnel = $config->content;
				}
				if (!$funnel) $funnel = $context->getConfig('funnel/'.$place_identifier);
				if (array_key_exists('orders', $funnel)) {
					foreach ($funnel['orders'] as $product_identifier => $order) {
						call_user_func($order['processor'], $account, $order['product_identifier'], $this->url());
					}
				}
				
				if ($actionStatus[0] == '200') {
					$account->shopping_cart = null;
					$rc = $account->update(null);
					if ($rc != 'OK') {
						$actionStatus = ['400', $rc];
						$connection->rollback();
					}
					else $connection->commit();
				}
			}
			catch (\Exception $e) {
				$connection->rollback();
				$actionStatus = ['500'];
			}
		}
		$this->getResponse()->setStatusCode($actionStatus[0]);
		$this->getResponse()->setReasonPhrase($actionStatus[1]);
		
		// Return the view
		$view = new ViewModel(array(
			'context' => $context,
			'locale' => $locale,
			'place_identifier' => $place_identifier,
			'funnel' => $funnel,
			'step' => $step,
			'commitments' => $commitments,
			'actionStatus' => $actionStatus,
		));
		$view->setTerminal(true);
		return $view;
	}
}
