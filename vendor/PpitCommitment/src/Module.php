<?php
namespace PpitCommitment;

use PpitCore\Model\GenericTable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\EventManager\EventInterface;
use Zend\Validator\AbstractValidator;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
            	Model\Commitment::class =>  function($sm) {
            		return new Model\Commitment();
            	},
            	Model\CommitmentTable::class =>  function($sm) {
                	$tableGateway = $sm->get(Model\CommitmentTableGateway::class);
                	return new GenericTable($tableGateway);
                },
                Model\CommitmentTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Model\Commitment());
                	return new TableGateway('commitment', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CommitmentMessageTable::class =>  function($sm) {
                	$tableGateway = $sm->get(Model\CommitmentMessageTableGateway::class);
                	return new GenericTable($tableGateway);
                },
                Model\CommitmentMessageTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Model\CommitmentMessage());
                	return new TableGateway('commitment_message', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CommitmentYearTable::class =>  function($sm) {
                	$tableGateway = $sm->get(Model\CommitmentYearTableGateway::class);
                	return new GenericTable($tableGateway);
                },
                Model\CommitmentYearTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Model\CommitmentYear());
                	return new TableGateway('commitment_year', $dbAdapter, null, $resultSetPrototype);
                },
                Model\PpitCommitment\Model\EventTable::class =>  function($sm) {
                	$tableGateway = $sm->get(Model\EventTableGateway::class);
                	return new GenericTable($tableGateway);
                },
                Model\EventTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Model\Event());
                	return new TableGateway('commitment_event', $dbAdapter, null, $resultSetPrototype);
                },
                Model\NotificationTable::class =>  function($sm) {
                	$tableGateway = $sm->get(Model\NotificationTableGateway::class);
                	return new Model\GenericTable($tableGateway);
                },
                Model\NotificationTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Model\Notification());
                	return new TableGateway('commitment_notification', $dbAdapter, null, $resultSetPrototype);
                },
                Model\SubscriptionTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\SubscriptionTableGateway::class);
                    return new GenericTable($tableGateway);
                },
                Model\SubscriptionTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                	$resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Subscription());
                    return new TableGateway('commitment_subscription', $dbAdapter, null, $resultSetPrototype);
                },
                Model\TermTable::class =>  function($sm) {
                    $tableGateway = $sm->get(Model\TermTableGateway::class);
                    return new GenericTable($tableGateway);
                },
                Model\TermTableGateway::class => function ($sm) {
                    $dbAdapter = $sm->get(AdapterInterface::class);
                	$resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Term());
                    return new TableGateway('commitment_term', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
