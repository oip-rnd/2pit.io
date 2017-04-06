<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore;

use PpitCore\Model\Agent;
use PpitCore\Model\AgentAttachment;
use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Credit;
use PpitCore\Model\Generic;
use PpitCore\Model\GenericTable;
use PpitCore\Model\Interaction;
use PpitCore\Model\Instance;
use PpitCore\Model\InstanceTable;
use PpitCore\Model\OrgUnit;
use PpitCore\Model\OrgUnitContact;
use PpitCore\Model\Place;
use PpitCore\Model\Template;
use PpitCore\Model\User;
use PpitCore\Model\UserTable;
use PpitCore\Model\Token;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module //implements AutoloaderProviderInterface, ConfigProviderInterface
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
        $securityAgent = $config['ppitUserSettings']['securityAgent'];
	    $securityAgent->initAcl($e, $config);
    }
    
    public function checkAcl(MvcEvent $e)
    {
    	// Retrieve the context as static properties in the Context class
    	Context::retrieve($e);
    
    	// Delegate to p-pit core ACL checking function
    	$app = $e->getApplication();
    	$serviceManager = $app->getServiceManager();
    	$config = $serviceManager->get('config');
        $securityAgent = $config['ppitUserSettings']['securityAgent'];
    	return $securityAgent->checkAcl($e);
    }
	
	public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
 	          	'PpitCore\Model\AgentTable' =>  function($sm) {
                    $tableGateway = $sm->get('AgentTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'AgentTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Agent());
                    return new TableGateway('core_agent', $dbAdapter, null, $resultSetPrototype);
                },
 	          	'PpitCore\Model\AgentAttachentTable' =>  function($sm) {
                    $tableGateway = $sm->get('AgentAttachmentTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'AgentAttachmentTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AgentAttachment());
                    return new TableGateway('core_agent_attachment', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCore\Model\CommunityTable' =>  function($sm) {
                    $tableGateway = $sm->get('CommunityTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'CommunityTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Community());
                    return new TableGateway('core_community', $dbAdapter, null, $resultSetPrototype);
                },
            	'PpitCore\Model\CreditTable' =>  function($sm) {
                    $tableGateway = $sm->get('CreditTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'CreditTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Credit());
                    return new TableGateway('core_credit', $dbAdapter, null, $resultSetPrototype);
                },
            	'PpitCore\Model\GenericTable' =>  function($sm) {
                    $tableGateway = $sm->get('GenericTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'GenericTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Generic());
                    return new TableGateway('', $dbAdapter, null, $resultSetPrototype);
                },
            	'PpitCore\Model\InteractionTable' =>  function($sm) {
                    $tableGateway = $sm->get('InteractionTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'InteractionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Interaction());
                    return new TableGateway('core_interaction', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCore\Model\InstanceTable' =>  function($sm) {
                    $tableGateway = $sm->get('InstanceTableGateway');
                    $table = new InstanceTable($tableGateway);
                    return $table;
                },
                'InstanceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Instance());
                    return new TableGateway('core_instance', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCore\Model\OrgUnitTable' => function($sm) {
                    $tableGateway = $sm->get('OrgUnitTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'OrgUnitTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OrgUnit());
                    return new TableGateway('core_org_unit', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCore\Model\OrgUnitContactTable' => function($sm) {
                    $tableGateway = $sm->get('OrgUnitContactTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'OrgUnitContactTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OrgUnitContact());
                    return new TableGateway('core_org_unit_contact', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCore\Model\PlaceTable' => function($sm) {
                    $tableGateway = $sm->get('PlaceTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'PlaceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Place());
                    return new TableGateway('core_place', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCore\Model\TemplateTable' => function($sm) {
                    $tableGateway = $sm->get('TemplateTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'TemplateTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Template());
                    return new TableGateway('core_template', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCore\Model\TokenTable' =>  function($sm) {
                    $tableGateway = $sm->get('TokenTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'TokenTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Token());
                    return new TableGateway('user_token', $dbAdapter, null, $resultSetPrototype);
                },
                'CoreTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Token());
                    return new TableGateway('user_token', $dbAdapter, null, $resultSetPrototype);
                },
            	'PpitCore\Model\UserTable' =>  function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCore\Model\UserContactTable' =>  function($sm) {
                    $tableGateway = $sm->get('UserContactTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'UserContactTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UserContact());
                    return new TableGateway('user_contact', $dbAdapter, null, $resultSetPrototype);
                },
                'PpitCore\Model\VcardTable' =>  function($sm) {
                    $tableGateway = $sm->get('VcardTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'VcardTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Vcard());
                    return new TableGateway('core_vcard', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
