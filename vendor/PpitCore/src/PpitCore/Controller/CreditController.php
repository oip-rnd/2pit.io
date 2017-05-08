<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Controller;

use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Model\Credit;
use PpitCore\Model\Instance;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Vcard;
use PpitDocument\Model\DocumentPart;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Client;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Logger;
use Zend\Log\Writer;

class CreditController extends AbstractActionController
{
	public function deleteAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);

    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the organizational unit
		$credit = Credit::get($id);
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		
    		if ($csrfForm->isValid()) {

    			// Atomicity
    			$connection = Credit::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Delete the row
					$return = $Credit->delete($credit->update_time);
					if ($return != 'OK') {
						$connection->rollback();
						$error = $return;
					}
					else {
						$connection->commit();
						$message = $return;
					}
    			}
           	    catch (\Exception $e) {
	    			$connection->rollback();
	    			throw $e;
	    		}
    		}  
    	}
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'credit' => $credit,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }

    public function useAction()
    {
    	$request = $this->getRequest();
		$context = Context::getCurrent();
		$config = $context->getConfig();
    	
    	// Make sure that we are running in a console and the user has not tricked our
    	// application into running this action from a public web server.
    	if (!$request instanceof ConsoleRequest){
    		throw new \RuntimeException('You can only use this action from a console!');
    	}
    	
    	$live = $request->getParam('live', null) || $request->getParam('l', null);
    	$mailTo = $request->getParam('mailTo', null);

    	// Log
		$logText = 'In \\PpitCore\\Controller\CreditController\\useAction {';
    	if ($live) {
	    	$writer = new Writer\Stream('data/log/console.txt');
	    	$logger = new Logger();
	    	$logger->addWriter($writer);
	    	$logger->info($logText);
    	} else print_r($logText."\n");

    	foreach ($config['creditConsumers'] as $creditConsumer) {
    		
    		// Log
    		$logText = '  Calling '.$creditConsumer.' {';
    		if ($live) $logger->info($logText);
    		else print_r($logText."\n");
    		
    		call_user_func($creditConsumer, $live, $mailTo);

    		if ($live) $logger->info('  }');
    		else print_r("  }\n");
    	}
    	if ($live) $logger->info('}');
    	else print_r("}\n");
    }

    public function repairAction()
    {
    	$select = Credit::getTable()->getSelect()->where(array('type' => 'p-pit-communities'));
    	$cursor = Credit::getTable()->transSelectWith($select);
    	foreach ($cursor as $credit) {
    		$audit = array();
    		foreach ($credit->audit as $event) {
    			$new = array();
    			$new['period'] = $event['period'];
    			if ($event['status'] == 'used' && $event['quantity'] > 0) $new['quantity'] = - (int) $event['quantity'];
    			else $new['quantity'] = $event['quantity'];
    			$new['status'] = $event['status'];
    			$new['reference'] = $event['reference'];
    			$new['time'] = $event['time'];
    			$new['n_fn'] = $event['n_fn'];
    			$new['comment'] = $event['comment'];
    			$audit[] = $new;
    		}
    		$credit->audit = $audit;
    		Credit::getTable()->transSave($credit);
    	}
    }
}
