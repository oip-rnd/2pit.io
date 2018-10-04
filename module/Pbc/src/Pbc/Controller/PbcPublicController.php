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

class PbcPublicController extends AbstractActionController
{
	public function landingAction()
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
