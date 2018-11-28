<?php
namespace PpitCore\Controller;

use PpitCore\Model\Context;
use PpitCore\Model\Instance;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class HomeController extends AbstractActionController
{
    public function indexAction()
    {
    	$context = Context::getCurrent();
		$instance_caption = $this->params()->fromRoute('instance_caption', null);
		if ($instance_caption) Context::$instance = Instance::get($instance_caption, 'caption');
		if (!$context->isAuthenticated()) {
			if ($context->getInstance()->home_page) return $this->redirect()->toRoute($context->getInstance()->home_page);
			else return $this->redirect()->toRoute('user/login', array('instance_caption' => ($instance_caption) ? $instance_caption : $context->getInstance()->caption));
		}

		$applications = $context->getApplications();
		foreach ($applications as $applicationId => $application) {
			if ($application['isCurrent']) {
				$entry = $context->getConfig('menus/'.$applicationId)['entries'][$application['default']];
				return $this->redirect()->toRoute($entry['route'], $entry['params']);
			}
		}
		return $this->redirect()->toRoute($context->getConfig()['defaultRoute']);
    }
}
