<?php
namespace PpitCore\Controller;

use DOMPDFModule\View\Model\PdfModel;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Document;
use PpitCore\Model\DocumentPart;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Formatter\FirePhp;

class PublicController extends AbstractActionController
{
	public function displayPageAction() {
		// Retrieve the context
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());
    	$directory = $this->params()->fromRoute('directory', 0);
		$name = $this->params()->fromRoute('name', 0);
		$request = $this->getRequest();
		$fqdn = $request->getUri()->getHost();
		$template = $context->getConfig('public/'.$instance_caption.'/page/'.$directory)[$name];
		$links = $context->getConfig('public/'.$instance_caption.'/links');
		
		$this->layout('/layout/public-layout');
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'place' => $place,
				'fqdn' => $fqdn,
				'template' => $template,
				'links' => $links,
				'directory' => $directory,
				'name' => $name,
		));
    	$view->setTerminal(true);
		return $view;
	}

	public function displayBlogAction() {
	
		// Retrieve the context
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$directory = $this->params()->fromRoute('directory', 0);
		$name = $this->params()->fromRoute('name', 0);
		$request = $this->getRequest();
		$fqdn = $request->getUri()->getHost();
		$entryList = $context->getConfig('public/'.$instance_caption.'/blog');
		if ($entryList['definition'] != 'inline') $entryList = $context->getConfig($entryList['definition']);
		foreach ($entryList as $entryId => $entry) if ($entryId == $name) break;
		$links = $context->getConfig('public/'.$instance_caption.'/links');
		
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'fqdn' => $fqdn,
				'directory' => $directory,
				'name' => $name,
				'entryList' => $entryList,
				'entry' => $entry,
				'links' => $links,
		));
		$view->setTerminal(true);
		return $view;
	}
	
    public function homeAction()
    {
    	$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
    	$place = Place::get($context->getPlaceId());

    	$request = $this->getRequest();
    	$fqdn = $request->getUri()->getHost();
		$template = $context->getConfig('public/'.$instance_caption.'/home');
		$links = $context->getConfig('public/'.$instance_caption.'/links');

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'fqdn' => $fqdn,
    			'template' => $template,
				'links' => $links,
//    			'homePage' => true,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
	
    public function communityAction()
    {
    	$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
    	$identifier = $this->params()->fromRoute('identifier');
    	$place_identifier = $this->params()->fromRoute('place_identifier');
    	if ($identifier && $place_identifier) {
	    	$community = Community::get($identifier.'/'.$place_identifier, 'identifier');
    		$place = Place::get($place_identifier, 'identifier');
    		$subject_id = (int) $this->params()->fromRoute('subject_id');
    	}
    	else {
			$contact = vcard::get($context->getContactId());
			$community = Community::get(array_search(true, $contact->communities));
    		$place = Place::get($community->place_id);
    		$subject_id = (int) $contact->id;
    	}
    	if (!$place) return $this->redirect()->toRoute('home');
    	$logo = ($place->logo_src) ? $place->logo_src : '/logos/'.$context->getInstance()->caption.'/'.$context->getConfig('headerParams')['logo'];
    	$logo_height = ($place->logo_src) ? $place->logo_height : $context->getConfig('headerParams')['logo-height'];
    	$request = $this->getRequest();
    	$fqdn = $request->getUri()->getHost();
		$template = $context->getConfig('public/community/student');
		$links = $context->getConfig('public/'.$instance_caption.'/links');

    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
				'community' => $community,
    			'place' => $place,
    			'fqdn' => $fqdn,
    			'template' => $template,
				'links' => $links,
    			'robots' => 'noindex, nofollow',
    			'homePage' => true,
    			'identifier' => $identifier,
    			'place_identifier' => $place_identifier,
		    	'subject_id' => $subject_id,
    			'logo' => $logo,
    			'logo_height' => $logo_height,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
	
    public function communityPrintAction()
    {
    	return $this->communityAction();
    }
    
	// Templates
	
	public function dashboardAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());
		$links = $context->getConfig('public/'.$instance_caption.'/links');
		$view = new ViewModel(array(
			'context' => $context,
			'place' => $place,
			'links' => $links,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function pricingAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());
    	$directory = $this->params()->fromRoute('directory', 0);
		$name = $this->params()->fromRoute('name', 0);
		$request = $this->getRequest();
		$fqdn = $request->getUri()->getHost();
		$template = $context->getConfig('public/'.$instance_caption.'/page/'.$directory)[$name];
		$links = $context->getConfig('public/'.$instance_caption.'/links');
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
										'comment' => "Request: $type - ".$context->localize($template['title']),
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
    	$account->properties = $account->getProperties();
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'place' => $place,
				'fqdn' => $fqdn,
				'template' => $template,
				'links' => $links,
				'directory' => $directory,
				'name' => $name,
				'type' => $type,
				'data' => $data,
				'message' => $message,
				'account' => $account,
		));
    	$view->setTerminal(true);
		return $view;
	}
	
	public function blogPostAction()
	{
		$context = Context::getCurrent();
		$instance_caption = $context->getInstance()->caption;
		$place = Place::get($context->getPlaceId());
		$links = $context->getConfig('public/'.$instance_caption.'/links');
		$view = new ViewModel(array(
			'context' => $context,
			'place' => $place,
			'template' => [],
			'links' => $links,
		));
		$view->setTerminal(true);
		return $view;
	}
}
