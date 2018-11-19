<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
//use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface
{
	private $routes;
	private $roles;
	
	public function onBootstrap(MvcEvent $e)
    {
    	$application   = $e->getApplication();
    	$em = $application->getEventManager();
        $sm = $em->getSharedManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($em);

        // Attach the p-pit ACL checking function
        $em->attach('route', array($this, 'checkAcl'));

	    // Load the p-pit ACL
	    $this->initAcl($e);
    }

    public function initAcl(MvcEvent $e)
    {
    	// Retrieve the config and invoke the p-pit core ACL initialization function
    	$app = $e->getApplication();
    	$serviceManager = $app->getServiceManager();
    	$config = $serviceManager->get('config');
		$securityAgent = $serviceManager->get(\PpitUser\Model\SecurityAgent::class);
//    	$securityAgent = $config['ppitUserSettings']['securityAgent'];
	    $securityAgent->initAcl($e, $config);
    }
    
    public function checkAcl(MvcEvent $e)
    {
    	// Retrieve the context as static properties in the Context class
    	Model\Context::retrieve($e);
    
    	// Delegate to p-pit core ACL checking function
    	$app = $e->getApplication();
    	$serviceManager = $app->getServiceManager();
    	$config = $serviceManager->get('config');
		$securityAgent = $serviceManager->get(\PpitUser\Model\SecurityAgent::class);
//    	$securityAgent = $config['ppitUserSettings']['securityAgent'];
    	return $securityAgent->checkAcl($e);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
            	Model\Account::class =>  function($sm) {
            		return new Model\Account();
            	},
	          	Model\AccountTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\AccountTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\AccountTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Account());
                    return new TableGateway('core_account', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CommunityTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\CommunityTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\CommunityTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Community());
                    return new TableGateway('core_community', $dbAdapter, null, $resultSetPrototype);
                },
                Model\ConfigTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\ConfigTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\ConfigTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Config());
                    return new TableGateway('core_config', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CreditTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\CreditTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\CreditTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Credit());
                    return new TableGateway('core_credit', $dbAdapter, null, $resultSetPrototype);
                },
            	Model\DocumentTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\DocumentTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\DocumentTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Document());
                    return new TableGateway('core_document', $dbAdapter, null, $resultSetPrototype);
                },
            	Model\DocumentPartTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\DocumentPartTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\DocumentPartTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\DocumentPart());
                    return new TableGateway('core_document_part', $dbAdapter, null, $resultSetPrototype);
                },
                Model\EventTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\CoreEventTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\CoreEventTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Event());
                    return new TableGateway('core_event', $dbAdapter, null, $resultSetPrototype);
                },
                Model\GenericTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\GenericTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\GenericTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Generic());
                    return new TableGateway('', $dbAdapter, null, $resultSetPrototype);
                },
            	Model\GroupAccountTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\GroupAccountTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\GroupAccountTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\GroupAccount());
                    return new TableGateway('core_group_account', $dbAdapter, null, $resultSetPrototype);
                },
                Model\InteractionTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\InteractionTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\InteractionTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Interaction());
                    return new TableGateway('core_interaction', $dbAdapter, null, $resultSetPrototype);
                },
                Model\InstanceTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\InstanceTableGateway::class);
                    return new Model\InstanceTable($tableGateway);
                },
                Model\InstanceTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Instance());
                    return new TableGateway('core_instance', $dbAdapter, null, $resultSetPrototype);
                },
                Model\PlaceTable::class => function($sm) {
                    $tableGateway = $sm->get(Model\PlaceTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\PlaceTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Place());
                    return new TableGateway('core_place', $dbAdapter, null, $resultSetPrototype);
                },
                Model\ProductTable::class => function($sm) {
                    $tableGateway = $sm->get(Model\ProductTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\ProductTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Product());
                    return new TableGateway('core_product', $dbAdapter, null, $resultSetPrototype);
                },
                Model\ProductOptionTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\ProductOptionTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\ProductOptionTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\ProductOption());
                    return new TableGateway('core_product_option', $dbAdapter, null, $resultSetPrototype);
                },
                Model\RequestTable::class => function($sm) {
                    $tableGateway = $sm->get(Model\RequestTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\RequestTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Request());
                    return new TableGateway('core_event', $dbAdapter, null, $resultSetPrototype);
                },
                Model\TokenTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\TokenTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\TokenTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Token());
                    return new TableGateway('core_user_token', $dbAdapter, null, $resultSetPrototype);
                },
            	Model\UserTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\UserTableGateway::class);
                    return new Model\UserTable($tableGateway);
                },
                Model\UserTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\User());
                    return new TableGateway('core_user', $dbAdapter, null, $resultSetPrototype);
                },
                Model\UserContactTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\UserContactTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\UserContactTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\UserContact());
                    return new TableGateway('core_user_contact', $dbAdapter, null, $resultSetPrototype);
                },
            	Model\Vcard::class =>  function($sm) {
            		return new Model\Vcard();
            	},
                Model\VcardTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\VcardTableGateway::class);
                    return new Model\GenericTable($tableGateway);
                },
                Model\VcardTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Vcard());
                    return new TableGateway('core_vcard', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
