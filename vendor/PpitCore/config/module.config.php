<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */
namespace PpitCore;

use Zend\Router\Http\Literal;
use Zend\ServiceManager\Factory\InvokableFactory;

return array(

	'controllers' => array(
		'factories' => array(
			Controller\AccountController::class => InvokableFactory::class,
			Controller\ConfigController::class => InvokableFactory::class,
			Controller\CreditController::class => InvokableFactory::class,
			Controller\HomeController::class => InvokableFactory::class,
			Controller\DocumentController::class => InvokableFactory::class,
			Controller\EventController::class => InvokableFactory::class,
			Controller\InteractionController::class => InvokableFactory::class,
			Controller\InstanceController::class => InvokableFactory::class,
			Controller\PlaceController::class => InvokableFactory::class,
			Controller\ProductController::class => InvokableFactory::class,
			Controller\ProductOptionController::class => InvokableFactory::class,
			Controller\RequestController::class => InvokableFactory::class,
			Controller\UserController::class => InvokableFactory::class,
			Controller\VcardController::class => InvokableFactory::class,
		),
	),
		
	'router' => array(
		'routes' => array(
			'index' => array(
				'type' => Literal::class,
				'options' => array(
					'route'    => '/',
					'defaults' => array(
						'controller' => Controller\HomeController::class,
						'action' => 'index',
					),
				),
			),
			'account' => array(
				'type'    => Literal::class,
				'options' => array(
					'route'    => '/account',
					'defaults' => array(
						'controller' => Controller\AccountController::class,
						'action'     => 'list',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'index' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index[/:entry][/:type][/:app]',
							'defaults' => array(
								'action' => 'index',
							),
						),
					),
					'indexAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index-alt[/:entry][/:type][/:entryId]',
							'defaults' => array(
								'action' => 'indexAlt',
							),
						),
					),
/*					'contactIndex' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/contact-index[/:entry][/:type][/:app]',
							'defaults' => array(
								'action' => 'contactIndex',
							),
						),
					),*/
					'search' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search[/:entry][/:type]',
							'defaults' => array(
								'action' => 'search',
							),
						),
					),
					'searchAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search-alt[/:entry][/:type]',
							'defaults' => array(
								'action' => 'searchAlt',
							),
						),
					),
					'eventAccountSearch' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/event-account-search[/:entry][/:type]',
							'defaults' => array(
								'action' => 'eventAccountSearch',
							),
						),
					),
					'list' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list[/:entry][/:type]',
							'defaults' => array(
								'action' => 'list',
							),
						),
					),
					'listAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list-alt[/:entry][/:type]',
							'defaults' => array(
								'action' => 'listAlt',
							),
						),
					),
					'export' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/export[/:entry][/:type]',
							'defaults' => array(
								'action' => 'export',
							),
						),
					),
					'eventAccountList' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/event-account-list[/:entry][/:type]',
							'defaults' => array(
								'action' => 'eventAccountList',
							),
						),
					),
					'exportCsv' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/export-csv[/:entry][/:type]',
							'defaults' => array(
								'action' => 'exportCsv',
							),
						),
					),
					'group' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/group[/:type]',
							'defaults' => array(
								'action' => 'group',
							),
						),
					),
					'groupAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/group-alt[/:type]',
							'defaults' => array(
								'action' => 'groupAlt',
							),
						),
					),
					'addToGroup' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/addToGroup[/:type]',
							'defaults' => array(
								'action' => 'addToGroup',
							),
						),
					),
					'sendMessage' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/send-message[/:type]',
							'defaults' => array(
								'action' => 'sendMessage',
							),
						),
					),
					'dropboxLink' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/dropbox-link[/:document]',
							'defaults' => array(
								'action' => 'dropboxLink',
							),
						),
					),
					'detail' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/detail[/:type][/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'detail',
							),
						),
					),
					'detailAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/detail-alt[/:type][/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'detailAlt',
							),
						),
					),
					'getAvailability' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/get-availability[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'getAvailability',
							),
						),
					),
					'update' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update[/:id][/:type]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'update',
							),
						),
					),
					'updateAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update-alt[/:id][/:type]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'updateAlt',
		        			),
						),
					),
					'updateUser' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update-user[/:type][/:id][/:act]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'update-user',
							),
						),
					),
					'passwordRequest' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/password-request',
							'defaults' => array(
								'action' => 'passwordRequest',
							),
						),
					),
					'updateContact' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update-contact[/:type][/:contactNumber][/:id][/:act]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'updateContact',
							),
						),
					),
					'updateContactAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update-contact-alt[/:type][/:contactNumber][/:id][/:act]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'updateContactAlt',
							),
						),
					),
					'indexCard' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index-card[/:id][/:type]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'indexCard',
							),
						),
					),
					'fbgetleads' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/fbgetleads[/:type]',
							'defaults' => array(
								'action' => 'fbgetleads',
							),
						),
					),
					'v1' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/v1[/:type][/:perspective][/:id]',
							'defaults' => array(
								'action' => 'v1',
							),
						),
					),
					'repair' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/repair',
							'defaults' => array(
								'action' => 'repair',
							),
						),
					),
				),
			),
			'home' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/home',
					'defaults' => array(
						'controller' => Controller\HomeController::class,
						'action' => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'index' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index[/:instance_caption]',
							'defaults' => array(
								'action' => 'index',
							),
						),
					),
				),
			),
			'document' => array(
				'type'    => Literal::class,
				'options' => array(
					'route' => '/document',
					'defaults' => array(
						'controller' => Controller\DocumentController::class,
						'action'     => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'download' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/download[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'download',
							),
						),
					),
					'update' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update[/:identifier]',
							'defaults' => array(
								'action' => 'update',
							),
						),
					),
				),
			),
			'event' => array(
				'type'    => Literal::class,
				'options' => array(
					'route' => '/event',
					'defaults' => array(
						'controller' => Controller\EventController::class,
						'action'     => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'v1' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/v1[/:type][/:id]',
							'defaults' => array(
								'action' => 'v1',
							),
						),
					),
					'index' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index[/:type][/:app][/:category]',
							'defaults' => array(
								'action' => 'index',
							),
						),
					),
					'indexAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index-alt[/:type][/:app][/:category]',
							'defaults' => array(
								'action' => 'indexAlt',
							),
						),
					),
					'calendar' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/calendar[/:type][/:category][/:entryId]',
							'defaults' => array(
								'action' => 'calendar',
							),
						),
					),
					'search' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search[/:type]',
							'defaults' => array(
								'action' => 'search',
							),
						),
					),
					'searchAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search-alt[/:type]',
							'defaults' => array(
								'action' => 'searchAlt',
							),
						),
					),
					'list' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list[/:type]',
							'defaults' => array(
								'action' => 'list',
							),
						),
					),
					'listAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list-alt[/:type]',
							'defaults' => array(
								'action' => 'listAlt',
							),
						),
					),
					'listV2' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list-v2[/:type]',
							'defaults' => array(
								'action' => 'listV2',
							),
						),
					),
					'distribute' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/distribute[/:type]',
							'defaults' => array(
								'action' => 'distribute',
							),
						),
					),
					'mapPlanning' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/map-planning[/:type]',
							'defaults' => array(
								'action' => 'mapPlanning',
							),
						),
					),
					'planning' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/planning[/:type][/:category]',
							'defaults' => array(
								'action' => 'planning',
							),
						),
					),
					'concurrencies' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/concurrencies[/:type][/:category]',
							'defaults' => array(
								'action' => 'concurrencies',
							),
						),
					),
					'export' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/export[/:type]',
							'defaults' => array(
								'action' => 'export',
							),
						),
					),
					'synchronize' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/synchronize[/:type]',
							'defaults' => array(
								'action' => 'synchronize',
							),
						),
					),
					'detail' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/detail[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'detail',
							),
						),
					),
					'detailAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/detail-alt[/:type][/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'detailAlt',
							),
						),
					),
					'update' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update[/:id][/:type]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'update',
							),
						),
					),
					'updateAlt' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update-alt[/:id][/:type]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'updateAlt',
							),
						),
					),
				),
            ),
			'interaction' => array(
				'type'    => Literal::class,
				'options' => array(
					'route' => '/interaction',
					'defaults' => array(
						'controller' => Controller\InteractionController::class,
						'action' => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'index' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index',
							'defaults' => array(
								'action' => 'index',
							),
						),
					),
					'search' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search',
							'defaults' => array(
								'action' => 'search',
							),
						),
					),
					'list' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list',
							'defaults' => array(
								'action' => 'list',
							),
						),
					),
					'export' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/export',
							'defaults' => array(
								'action' => 'export',
							),
						),
					),
					'detail' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/detail[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'detail',
							),
						),
					),
					'update' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update[/:id][/:act]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'update',
							),
						),
					),
					'send' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/send[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'send',
							),
						),
					),
					'receive' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/receive[/:type]',
							'defaults' => array(
								'action' => 'receive',
							),
						),
					),
					'process' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/process[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'process',
							),
						),
					),
				),
			),
			'instance' => array(
				'type'    => Literal::class,
				'options' => array(
					'route' => '/instance',
					'defaults' => array(
						'controller' => Controller\InstanceController::class,
						'action'     => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'index' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index',
							'defaults' => array(
								'action' => 'index',
							),
						),
					),
					'search' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search',
								'defaults' => array(
									'action' => 'search',
							),
						),
					),
					'list' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list',
							'defaults' => array(
								'action' => 'list',
							),
						),
					),
					'export' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/export',
							'defaults' => array(
								'action' => 'export',
							),
						),
					),
					'detail' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/detail[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'detail',
							),
						),
					),
					'update' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update[/:id][/:act]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'update',
							),
						),
					),
					'try' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/try',
							'defaults' => array(
								'action' => 'try',
							),
						),
					),
					'tryAdd' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/try-add',
							'defaults' => array(
								'action' => 'tryAdd',
							),
						),
					),
/*					'accept' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/accept',
							'defaults' => array(
								'action' => 'accept',
							),
						),
					),*/
					'charter' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/charter',
							'defaults' => array(
								'action' => 'charter',
							),
						),
					),
					'validateCharter' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/validate-charter',
							'defaults' => array(
								'action' => 'validateCharter',
							),
						),
					),
					'generalTermsOfUse' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/general-terms-of-use',
							'defaults' => array(
								'action' => 'generalTermsOfUse',
							),
						),
					),
					'validateGeneralTermsOfUse' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/validate-general-terms-of-use',
							'defaults' => array(
								'action' => 'validateGeneralTermsOfUse',
							),
						),
					),
					'legalNotices' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/legal-notices',
							'defaults' => array(
								'action' => 'legalNotices',
							),
						),
					),
					'addImage' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/add-image',
							'defaults' => array(
								'action' => 'addImage',
							),
						),
					),
					'addLogo' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/add-logo',
							'defaults' => array(
								'action' => 'addLogo',
							),
						),
					),
					'serialize' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/serialize',
							'defaults' => array(
								'action' => 'serialize',
							),
						),
					),
					'v1' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/v1[/:id]',
							'defaults' => array(
								'action' => 'v1',
							),
						),
					),
            	),
            ),
			'place' => array(
				'type' => Literal::class,
				'options' => array(
					'route'    => '/place',
					'defaults' => array(
						'controller' => Controller\PlaceController::class,
						'action' => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'index' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index',
							'defaults' => array(
								'action' => 'index',
							),
						),
					),
					'search' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search',
							'defaults' => array(
								'action' => 'search',
							),
						),
					),
					'list' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list',
							'defaults' => array(
								'action' => 'list',
							),
						),
					),
					'export' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/export',
							'defaults' => array(
								'action' => 'export',
							),
						),
					),
					'detail' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/detail[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'detail',
							),
						),
					),
					'update' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update[/:id][/:act]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'update',
							),
						),
					),
					'admin' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/admin[/:place_id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'admin',
							),
						),
					),
					'v1' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/v1[/:id]',
							'defaults' => array(
								'action' => 'v1',
							),
						),
					),
					'serialize' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/serialize[/:place_identifier]',
							'defaults' => array(
								'action' => 'serialize',
							),
						),
					),
				),
			),
			'product' => array(
				'type'    => Literal::class,
				'options' => array(
					'route' => '/product',
					'defaults' => array(
						'controller' => Controller\ProductController::class,
						'action' => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'index' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index[/:type]',
							'defaults' => array(
								'action' => 'index',
							),
						),
					),
					'indexV2' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index-v2[/:type][/:entryId]',
							'defaults' => array(
								'action' => 'indexV2',
							),
						),
					),
					'criteria' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/criteria[/:instance_caption][/:type]',
							'defaults' => array(
								'action' => 'criteria',
							),
						),
					),
					'search' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search[/:type]',
							'defaults' => array(
								'action' => 'search',
							),
						),
					),
					'searchV2' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search-v2[/:type]',
							'defaults' => array(
								'action' => 'searchV2',
							),
						),
					),
					'list' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list[/:type]',
							'defaults' => array(
								'action' => 'list',
							),
						),
					),
					'listV2' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list-v2[/:type]',
							'defaults' => array(
								'action' => 'listV2',
							),
						),
					),
					'serviceList' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/service-list',
							'defaults' => array(
								'action' => 'serviceList',
							),
						),
					),
					'detail' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/detail[/:type][/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'detail',
							),
						),
					),
					'detailV2' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/detail-v2[/:type][/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'detailV2',
							),
						),
					),
					'dataList' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/data-list[/:product_category_id]',
							'constraints' => array(
								'product_category_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'dataList',
							),
						),
					),
					'update' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update[/:type][/:id][/:act]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'update',
							),
						),
					),
					'updateV2' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update-v2[/:type][/:id][/:act]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'updateV2',
							),
						),
					),
					'matrix' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/matrix[/:product_category_id][/:id]',
							'constraints' => array(
								'product_category_id' => '[0-9]*',
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'matrix',
							),
						),
					),
					'export' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/export[/:type]',
							'defaults' => array(
								'action' => 'export',
							),
						),
					),
					'delete' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/delete[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'delete',
							),
						),
					),
					'v1' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/v1[/:type][/:id]',
							'defaults' => array(
								'action' => 'v1',
							),
						),
					),
				),
			),
			'productOption' => array(
				'type' => Literal::class,
				'options' => array(
					'route' => '/product-option',
					'defaults' => array(
						'controller' => Controller\ProductOptionController::class,
						'action' => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'index' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index[/:product_id]',
							'constraints' => array(
								'product_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'index',
							),
						),
					),
					'list' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list[/:type][/:product_id]',
							'constraints' => array(
								'product_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'list',
							),
						),
					),
					'listV2' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list-v2[/:type][/:product_id]',
							'constraints' => array(
								'product_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'listV2',
							),
						),
					),
					'export' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/export[/:type][/:product_id]',
							'constraints' => array(
								'product_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'export',
							),
						),
					),
					'update' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update[/:type][/:product_id][/:id][/:act]',
							'constraints' => array(
								'product_id' => '[0-9]*',
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'update',
							),
						),
					),
					'updateV2' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update-v2[/:type][/:product_id][/:id][/:act]',
							'constraints' => array(
								'product_id' => '[0-9]*',
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'updateV2',
							),
						),
					),
				),
			),
			'user' => array(
				'type'    => Literal::class,
				'options' => array(
					'route' => '/user',
					'constraints' => array(
						'id' => '[0-9]*',
					),
					'defaults' => array(
						'controller' => Controller\UserController::class,
						'action'     => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'index' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/index[/:app]',
							'defaults' => array(
								'action' => 'index',
							),
						),
					),
					'search' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/search[/:app]',
							'constraints' => array(
								'community_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'search',
							),
						),
					),
					'list' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/list[/:app]',
							'constraints' => array(
								'community_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'list',
							),
						),
					),
					'export' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/export[/:app]',
							'constraints' => array(
								'community_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'export',
							),
						),
					),
					'update' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/update[/:app][/:id]',
							'constraints' => array(
								'community_id' => '[0-9]*',
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'update',
							),
						),
					),
					'revoke' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/revoke[/:id]',
								'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'revoke',
							),
						),
					),
					'batchReactivate' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/batch-reactivate',
							'defaults' => array(
								'action' => 'batchReactivate',
							),
						),
					),
					'login' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/login[/:instance_caption]',
							'defaults' => array(
								'action' => 'login',
							),
						),
					),
					'googleLogin' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/google-login',
							'defaults' => array(
								'action' => 'googleLogin',
							),
						),
					),
					'demo' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/demo[/:username]',
							'defaults' => array(
								'action' => 'demo',
							),
						),
					),
					'maintainSession' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/maintain-session',
							'defaults' => array(
								'action' => 'maintainSession',
							),
						),
					),
					'expired' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/expired[/:instance_id]',
							'constraints' => array(
								'instance_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'expired',
							),
						),
					),
					'logout' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/logout[/:instance_caption]',
							'defaults' => array(
								'action' => 'logout',
							),
						),
					),
					'passwordRequest' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/password-request[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'passwordRequest',
							),
						),
					),
					'generatePassword' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/generate-password[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'generatePassword',
							),
						),
					),
					'lostPassword' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/lost-password[/:instance_caption]',
							'defaults' => array(
								'action' => 'lostPassword',
							),
						),
					),
					'password' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/password[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'password',
							),
						),
					),
					'passwordChanged' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/password-changed[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'passwordChanged',
							),
						),
					),
					'initpassword' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/initpassword[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'initpassword',
							),
						),
					),
					'delete' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/delete[/:id]',
							'constraints' => array(
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'delete',
							),
						),
					),
					'changeContact' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/change-contact[/:vcard_id]',
							'constraints' => array(
								'vcard_id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'changeContact',
							),
						),
					),
					'authenticate' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/authenticate',
							'defaults' => array(
								'action' => 'authenticate',
							),
						),
					),
					'v1' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/v1[/:id]',
							'defaults' => array(
								'action' => 'v1',
							)
						)
					),
					'fbwebhook' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/fbwebhook',
							'defaults' => array(
								'action' => 'fbwebhook',
							)
						)
					),
					'fbpageaccess' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/fbpageaccess',
							'defaults' => array(
								'action' => 'fbpageaccess',
							)
						)
					)
				),
			),
			'vcard' => array(
				'type'    => Literal::class,
				'options' => array(
					'route' => '/vcard',
					'defaults' => array(
						'controller' => Controller\VcardController::class,
						'action' => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'photo' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/photo[/:id]',
							'constraints' => array(
								'community_id' => '[0-9]*',
								'id' => '[0-9]*',
							),
							'defaults' => array(
								'action' => 'photo',
							),
						),
					),
					'demoMode' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/demo-mode',
							'defaults' => array(
								'action' => 'demoMode',
							),
						),
					),
					'v1' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/v1[/:id]',
							'defaults' => array(
								'action' => 'v1',
							),
						),
					),
					'dataRecovery' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/data-recovery',
							'defaults' => array(
								'action' => 'dataRecovery',
							),
						),
					),
				),
			),
			'config' => array(
				'type' => Literal::class,
				'options' => array(
					'route' => '/config',
					'defaults' => array(
						'controller' => Controller\ConfigController::class,
						'action' => 'v1',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'v1' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/v1[/:identifier]',
							'defaults' => array('action' => 'v1'),
						),
					),
					'serialize' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/serialize[/:place_identifier]',
							'defaults' => array('action' => 'serialize'),
						),
					),
				),
			),
		),
	),

	'bjyauthorize' => array(
		// Guard listeners to be attached to the application event manager
		'guards' => array(
			'BjyAuthorize\Guard\Route' => array(

				array('route' => 'index', 'roles' => array('guest')),
				array('route' => 'home', 'roles' => array('guest')),
				array('route' => 'home/index', 'roles' => array('guest')),

				array('route' => 'account', 'roles' => array('operational_management', 'sales_manager')),
				array('route' => 'account/index', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/indexAlt', 'roles' => array('operational_management', 'sales_manager', 'manager')),
//				array('route' => 'account/contactIndex', 'roles' => array('operational_management', 'sales_manager')),
				array('route' => 'account/search', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/searchAlt', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/group', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/groupAlt', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/addToGroup', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/sendMessage', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/dropboxLink', 'roles' => array('guest')),
				array('route' => 'account/detail', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/detailAlt', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/getAvailability', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/eventAccountList', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/eventAccountSearch', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/export', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/exportCsv', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/list', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/listAlt', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/update', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/updateAlt', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/updateUser', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/passwordRequest', 'roles' => array('admin')),
				array('route' => 'account/updateContact', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/updateContactAlt', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/indexCard', 'roles' => array('operational_management', 'sales_manager', 'manager')),
				array('route' => 'account/fbgetleads', 'roles' => array('guest')),
				array('route' => 'account/v1', 'roles' => array('guest')),
				array('route' => 'account/repair', 'roles' => array('admin')),

				array('route' => 'config/serialize', 'roles' => array('admin')),
				array('route' => 'config/v1', 'roles' => array('guest')),
				
				array('route' => 'document', 'roles' => array('user')),
				array('route' => 'document/download', 'roles' => array('user')),
				array('route' => 'document/update', 'roles' => array('admin')),
				
				array('route' => 'event', 'roles' => array('user')),
				array('route' => 'event/v1', 'roles' => array('guest')),
				array('route' => 'event/index', 'roles' => array('user')),
				array('route' => 'event/indexAlt', 'roles' => array('user')),
				array('route' => 'event/calendar', 'roles' => array('user')),
				array('route' => 'event/search', 'roles' => array('user')),
				array('route' => 'event/searchAlt', 'roles' => array('user')),
				array('route' => 'event/list', 'roles' => array('user')),
				array('route' => 'event/listAlt', 'roles' => array('user')),
				array('route' => 'event/listV2', 'roles' => array('user')),
				array('route' => 'event/distribute', 'roles' => array('user')),
				array('route' => 'event/mapPlanning', 'roles' => array('user')),
				array('route' => 'event/planning', 'roles' => array('user')),
				array('route' => 'event/concurrencies', 'roles' => array('user')),
				array('route' => 'event/export', 'roles' => array('user')),
				array('route' => 'event/synchronize', 'roles' => array('user')),
				array('route' => 'event/detail', 'roles' => array('user')),
				array('route' => 'event/detailAlt', 'roles' => array('user')),
				array('route' => 'event/update', 'roles' => array('user')),
				array('route' => 'event/updateAlt', 'roles' => array('user')),
				
				array('route' => 'interaction', 'roles' => array('admin')),
				array('route' => 'interaction/index', 'roles' => array('admin')),
				array('route' => 'interaction/search', 'roles' => array('admin')),
				array('route' => 'interaction/list', 'roles' => array('admin')),
				array('route' => 'interaction/export', 'roles' => array('admin')),
				array('route' => 'interaction/detail', 'roles' => array('admin')),
				array('route' => 'interaction/update', 'roles' => array('admin')),
				array('route' => 'interaction/send', 'roles' => array('admin')),
				array('route' => 'interaction/receive', 'roles' => array('guest')),
				array('route' => 'interaction/process', 'roles' => array('admin')),
						
				array('route' => 'instance', 'roles' => array('admin')),
				array('route' => 'instance/index', 'roles' => array('admin')),
				array('route' => 'instance/search', 'roles' => array('admin')),
				array('route' => 'instance/list', 'roles' => array('admin')),
				array('route' => 'instance/export', 'roles' => array('admin')),
				array('route' => 'instance/detail', 'roles' => array('admin')),
				array('route' => 'instance/update', 'roles' => array('admin')),
				array('route' => 'instance/try', 'roles' => array('guest')),
				array('route' => 'instance/tryAdd', 'roles' => array('guest')),
//				array('route' => 'instance/accept', 'roles' => array('admin')),
				array('route' => 'instance/charter', 'roles' => array('guest')),
				array('route' => 'instance/validateCharter', 'roles' => array('user')),
				array('route' => 'instance/generalTermsOfUse', 'roles' => array('guest')),
				array('route' => 'instance/validateGeneralTermsOfUse', 'roles' => array('user')),
				array('route' => 'instance/legalNotices', 'roles' => array('guest')),
				array('route' => 'instance/addImage', 'roles' => array('admin')),
				array('route' => 'instance/addLogo', 'roles' => array('admin')),
				array('route' => 'instance/serialize', 'roles' => array('admin')),
				array('route' => 'instance/v1', 'roles' => array('guest')),
				
				array('route' => 'place', 'roles' => array('admin')),
				array('route' => 'place/index', 'roles' => array('admin')),
				array('route' => 'place/search', 'roles' => array('admin')),
				array('route' => 'place/detail', 'roles' => array('admin')),
				array('route' => 'place/delete', 'roles' => array('admin')),
				array('route' => 'place/export', 'roles' => array('admin')),
            	array('route' => 'place/list', 'roles' => array('admin')),
				array('route' => 'place/update', 'roles' => array('admin')),
				array('route' => 'place/admin', 'roles' => array('admin')),
				array('route' => 'place/v1', 'roles' => array('guest')),
				array('route' => 'place/serialize', 'roles' => array('admin')),
				
				// Product
				array('route' => 'product', 'roles' => array('sales_manager')),
				array('route' => 'product/index', 'roles' => array('sales_manager')),
				array('route' => 'product/indexV2', 'roles' => array('sales_manager')),
				array('route' => 'product/list', 'roles' => array('sales_manager')),
				array('route' => 'product/listV2', 'roles' => array('sales_manager')),
				array('route' => 'product/export', 'roles' => array('sales_manager')),
				array('route' => 'product/criteria', 'roles' => array('guest')),
				array('route' => 'product/serviceList', 'roles' => array('guest')),
				array('route' => 'product/search', 'roles' => array('sales_manager')),
				array('route' => 'product/searchV2', 'roles' => array('sales_manager')),
				array('route' => 'product/detail', 'roles' => array('sales_manager')),
				array('route' => 'product/detailV2', 'roles' => array('sales_manager')),
				array('route' => 'product/update', 'roles' => array('sales_manager')),
				array('route' => 'product/updateV2', 'roles' => array('sales_manager')),
				array('route' => 'product/matrix', 'roles' => array('admin')),
				array('route' => 'product/delete', 'roles' => array('admin')),
				array('route' => 'product/v1', 'roles' => array('guest')),
				
				// Product option
				array('route' => 'productOption', 'roles' => array('sales_manager')),
				array('route' => 'productOption/index', 'roles' => array('sales_manager')),
				array('route' => 'productOption/list', 'roles' => array('sales_manager')),
				array('route' => 'productOption/listV2', 'roles' => array('sales_manager')),
				array('route' => 'productOption/export', 'roles' => array('sales_manager')),
				array('route' => 'productOption/update', 'roles' => array('sales_manager')),
				array('route' => 'productOption/updateV2', 'roles' => array('sales_manager')),
				
				array('route' => 'user', 'roles' => array('admin', 'manager')),
				array('route' => 'user/index', 'roles' => array('admin', 'manager')),
				array('route' => 'user/search', 'roles' => array('admin', 'manager')),
				array('route' => 'user/list', 'roles' => array('admin', 'manager')),
				array('route' => 'user/export', 'roles' => array('admin', 'manager')),
				array('route' => 'user/update', 'roles' => array('admin', 'manager')),
				array('route' => 'user/role', 'roles' => array('admin')),
				array('route' => 'user/login', 'roles' => array('guest')),
				array('route' => 'user/googleLogin', 'roles' => array('guest')),
				array('route' => 'user/demo', 'roles' => array('guest')),
				array('route' => 'user/maintainSession', 'roles' => array('user')),
				array('route' => 'user/expired', 'roles' => array('guest')),
				array('route' => 'user/logout', 'roles' => array('guest')),
				array('route' => 'user/passwordRequest', 'roles' => array('admin', 'manager')),
				array('route' => 'user/generatePassword', 'roles' => array('admin', 'manager')),
				array('route' => 'user/password', 'roles' => array('user')),
				array('route' => 'user/passwordChanged', 'roles' => array('guest')),
				array('route' => 'user/lostPassword', 'roles' => array('guest')),
				array('route' => 'user/initpassword', 'roles' => array('guest')),
				array('route' => 'user/revoke', 'roles' => array('admin', 'manager')),
				array('route' => 'user/batchReactivate', 'roles' => array('guest')),
				array('route' => 'user/changeContact', 'roles' => array('user')),
				array('route' => 'user/delete', 'roles' => array('admin', 'manager')),
				array('route' => 'user/authenticate', 'roles' => array('guest')),
				array('route' => 'user/v1', 'roles' => array('guest')),
				array('route' => 'user/fbwebhook', 'roles' => array('guest')),
				array('route' => 'user/fbpageaccess', 'roles' => array('guest')),
				
				array('route' => 'vcard/photo', 'roles' => array('user')),
				array('route' => 'vcard/demoMode', 'roles' => array('user')),
				array('route' => 'vcard/dataRecovery', 'roles' => array('admin')),
				array('route' => 'vcard/v1', 'roles' => array('guest')),
			)
		)
	),
		
	'view_manager' => array(
		'strategies' => array(
			'ViewJsonStrategy',
		),
		'display_not_found_reason' => true,
		'display_exceptions'       => true,
		'doctype'                  => 'HTML5',       // On défini notre doctype
		'not_found_template'       => 'error/404',   // On indique la page 404
		'exception_template'       => 'error/index', // On indique la page en cas d'exception
		'template_map' => array(
			'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
		),
		'template_path_stack' => array(
			'ppit-core' => __DIR__ . '/../view',
		),
	),

	'translator' => array(
		'locale' => 'fr_FR',
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
				'text_domain' => 'ppit-core'
			),
			array(
				'type' => 'phpArray',
				'base_dir' => './vendor/zendframework/zendframework/resources/languages/',
				'pattern'  => 'fr/Zend_Validate.php',
			),
		),
	),

	'defaultRoute' => 'landing/template2',
	
	// Network of 2pit instances reachable from the local instance (the default simulates a group skill marketplace)
	'instance/network' => array(
		'group.2pit.io' => array('caption' => ['default' => 'Group skill Marketplace', 'fr_FR' => 'Plateforme de compétences groupe']),
	),
	
	'instance/charter' => array(
		'default' => 'To be completed',
	),

	'instance/last_charter_time' => '2018-08-09 00:00:00',
	
	'instance/general_terms_of_use' => array(
			'default' => '
<h4 class="dark-grey-text text-uppercase text-center mb-3">Terms of Use 2pit.io</h4>
		
<h5 class="dark-grey-text font-weight-bold mb-3">Legal notice</h5>
		
<p class="dark-grey-text">This site is published by 2pit.io.</p>
<p class="dark-grey-text">
Headquarter: 25 rue du Faubourg du Temple - Bâtiment C - 75010 PARIS - FRANCE<br>
Legal Representative & Publication director: M. Bruno LARTILLOT<br>
Host: OVH 59820 Gravelines for 2pit.io
</p>
<p class="dark-grey-text">Contact : <a href="mailto:contact@2pit.io?subject=2pit.io - Give a title for your request">contact@2pit.io</a></p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">1 Purpose of the platform</h5>
		
<p class="dark-grey-text">2pit.io provides small businesses and professional individuals with a complete application to help operating the business. The purpose of this platform is to illustrate the features proposed in the 2pit.io distribution. Even the present General Terms of Use can be reused and adapted to your instance based on the 2pit.io distribution.</p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">2 Rules of use</h5>
		
<p class="dark-grey-text">Each user is responsible of his/her content and data loaded on the platform. Users are thus bound to a duty of confidentiality on the information they hold and manipulate. They will have to ensure that the information declared on the platform does not violate legal rules. They must also respect the other users on the platform. Then, they are asked to communicate in a cordial and no rude way and especially:</p>
<ol class="dark-grey-text">
<li>Not to put online inappropriate contents including pornographic, pedophile, racist, xenophobic, defamatory content, which infringes human respect and its dignity, inciting the commission of an offence or a crime, contrary to public order and moral laws.</li>
<li>Not to commit reprehensible acts under the applicable law, in particular with regards to intellectual property and third-party rights, including personality rights. In this respect, Users must not upload third-party works or third-party representative works (photos, texts etc.) for which they do not hold the necessary rights of use or operating authorizations, or to reproduce trademarks for which they are not authorized depositaries and for which they do not hold rights of use.</li>
<li>Do not put online political, trade-union, religious contents. Do not communicate information relating to private life of a third party without its writing agreement. Regarding the profile picture if it is provided, it must be centered on the user face and adapted to professional environment.</li>
</ol>
<p class="dark-grey-text">Data put online by users are susceptible to be moderated by the platform administrator and be removed whatever the reason, without the need to justify.</p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">3 Protection of confidential information</h5>
		
<p class="dark-grey-text font-weight-bold mb-3">Collection</p>
<p class="dark-grey-text">As part of the use of the platform, 2pit.io is required to collect personal data about you as a processing manager.</p>
		
<p class="dark-grey-text font-weight-bold mb-3">Recipients</p>
<p class="dark-grey-text">The data collected on the platform is for the platform administrator. Data stored on the platform will not be subject to any external communication except to third parties authorized under a legal or regulatory provision.</p>
		
<p class="dark-grey-text font-weight-bold mb-3">Duration of retention</p>
<p class="dark-grey-text">Unless disposition provided by law or regulation, personal data used for administrative management purposes are kept only for the duration necessary for the purposes of the working and/or business relationship, within the time limit periods in effect.</p>
		
<p class="dark-grey-text font-weight-bold mb-3">Processing safety</p>
<p class="dark-grey-text">2pit.io takes the appropriate physical, technical and organizational measures to ensure the security and confidentiality of personal data, in particular to protect them against loss, accidental destruction, tampering, and unauthorized access.</p>
		
<p class="dark-grey-text font-weight-bold mb-3">Application of the local regulations on the transfer of personal data outside the user’s Economic Area</p>
<p class="dark-grey-text">For a given user, no personal data transfer outside his/her Economic Area will be carried out as part of the use of the 2pit.io platform.</p>

<p class="dark-grey-text font-weight-bold mb-3">Exercise of rights</p>
<p class="dark-grey-text">The employee shall, under the conditions laid down in the applicable rules, have the following rights:</p>
<ol class="dark-grey-text">
<li>Access to the personal data collected concerning him/her;</li>
<li>Rectification or updating of inaccurate, incomplete or obsolete data;</li>
<li>Erasure;</li>
<li>Limitation;</li>
<li>Opposition for legitimate reasons;</li>
<li>Portability.</li>
</ol>
<p class="dark-grey-text">These rights can be exercised by e-mail at the following e-mail address: <a href="mailto:contact@2pit.io?subject=2pit.io - Exercise of rights regarding personal data">contact@2pit.io</a></p>

<p class="dark-grey-text font-weight-bold mb-3">Informations</p>
<p class="dark-grey-text">For any question relating to the protection of their personal data, employees may contact the 2pit.io Data Protection officer at the address <a href="mailto:"contact@2pit.io?subject=2pit.io - Give a title for your request">contact@2pit.io</a>. Employees also have the possibility to submit a complaint to the supervisory authority in charge of data protection.</p>

<h5 class="dark-grey-text font-weight-bold mb-3">5 Modification of Terms of Use</h5>
<p class="dark-grey-text">The user is invited to take regular notice of these Terms of Use, which can be changed at any time.</p>

<h5 class="dark-grey-text font-weight-bold mb-3">6 Applicable law / Dispute</h5>
<p class="dark-grey-text">The present general conditions are subject to French law.</p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">7 Suspension or closure of access to the platform</h5>
<p class="dark-grey-text">2pit.io reserves the option of shutting down access to the 2pit.io platform at any time and for any reason without having to justify it or suspend a user’s access to the platform, in case of non-compliance with the general conditions of use. If a user suspends his/her subscription to the 2pit.io offer, his/her account will be disabled. His/her profile will be kept during a maximum period of one year and can be deleted on his/her request.</p>

<h5 class="dark-grey-text font-weight-bold mb-3">8 Miscellaneous</h5>
<p class="dark-grey-text">Requests for support or further information on the use of the platform should be sent to the following e-mail address: <a href="mailto:contact@2pit.io?subject=2pit.io - Give a title for your request">contact@2pit.io</a>. Elsewhere, if the user notices content that should not be displayed on 2pit.io for any reason, including because it does not comply with current laws, please contact the 2pit.io support at <a href="mailto:contact@2pit.io?subject=2pit.io - SUSPICIOUS CONTENT">contact@2pit.io</a> with the topic “SUSPICIOUS CONTENT".</p>
',
			'fr_FR' => '
<h4 class="dark-grey-text text-uppercase text-center mb-3">Conditions Générales d’Utilisation (CGU) de 2pit.io</h4>
		
<h5 class="dark-grey-text font-weight-bold mb-3">Mentions légales</h5>
		
<p class="dark-grey-text">Ce site est publié par 2pit.io</p>
<p class="dark-grey-text">
25 rue du Faubourg du Temple - Bâtiment C - 75010 PARIS<br>
Représentant légal et Directeur de la publication : M. Bruno LARTILLOT<br>
Hébergeur : OVH 59820 Gravelines pour 2pit.io
</p>
<p class="dark-grey-text">Contact: <a href="mailto:contact@2pit.io?subject=2pit.io - Donnez un titre à votre demande">contact@2pit.io</a></p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">Objet de la plateforme</h5>
		
<p class="dark-grey-text">2pit.io procure aux petites entreprises et professionnels une application complète et gratuite pour les aider à exécuter leur métier. L’objet de cette plateforme est d’illustrer les fonctionnalités proposées dans la distribution open-source 2pit.io. Les présentes Conditions Générales d’Utilisation peuvent elles-même être réutilisées et adaptées à votre instance basée sur la distribution 2pit.io.</p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">2 Règles d’utilisation</h5>
		
<p class="dark-grey-text">Chaque utilisateur est responsable du contenu et des données saisis et chargés sur la plateforme. Les utilisateurs sont ainsi tenus à un devoir de confidentialité sur les informations qu’ils détiennent et manipulent. Ils devront veiller à ce que les informations déclarées sur la plateforme ne violent pas la réglementation. Ils doivent également respecter les autres utilisateurs de la plateforme. Ainsi, il leur est demandé de communiquer de façon cordiale et non grossière et notamment :</p>
<ol class="dark-grey-text">
<li>Ne pas mettre en ligne des contenus inappropriés notamment à caractère pornographique, pédophile, raciste, xénophobe, diffamatoire, portant atteinte au respect de la personne humaine et à sa dignité, incitant à la commission d’un délit ou d’un crime, contraires à l’ordre public ou aux bonnes mœurs.</li>
<li>Ne pas commettre des actes répréhensibles au regard des lois applicables, notamment en ce qui concerne la propriété intellectuelle et les droits de tiers, dont les droits de la personnalité. Par exemple, les utilisateurs ne doivent pas mettre en ligne des œuvres de tiers et/ou représentant des tiers (photos, textes etc.) pour lesquelles ils ne détiennent pas les droits d’utilisation ou les autorisations d’exploitation nécessaires, ou reproduire des marques dont ils ne sont pas dépositaires et pour lesquelles ils ne détiennent pas de droits d’utilisation.</li>
<li>Ne pas mettre en ligne de contenu lié à une activité d’ordre politique, syndical ou religieux. Ne doivent pas être communiquées les informations relatives à la vie privée d’un tiers sans son accord écrit. S’agissant de la photo du profil si elle est renseignée, elle doit être centrée sur le visage de l’utilisateur et adaptée à l’environnement professionnel.</li>
</ol>

<p class="dark-grey-text">Les données mises en ligne par les utilisateurs sont susceptibles d’être modérées par l’administrateur de la plateforme et d’être supprimées pour quelque raison que ce soit, sans nécessité de le justifier.</p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">3 Protection des données à caractère personnel</h5>
		
<p class="dark-grey-text font-weight-bold mb-3">Collecte</p>
<p class="dark-grey-text">Dans le cadre de l’utilisation de la plateforme, 2pit.io est amenée à collecter des données personnelles vous concernant en qualité de responsable de traitement.</p>
		
<p class="dark-grey-text font-weight-bold mb-3">Destinataires</p>
<p class="dark-grey-text">Les données sont destinées à l’administrateur de la plateforme. Les données enregistrées sur la plateforme ne feront l’objet d’aucune communication extérieure sauf aux tiers autorisés en vertu d’une disposition légale ou réglementaire.</p>
		
<p class="dark-grey-text font-weight-bold mb-3">Durée de conservation</p>
<p class="dark-grey-text">Sauf dispositions législatives ou réglementaires contraires, les données à caractère personnel utilisées aux fins de gestion administrative sont conservées uniquement pendant la durée nécessaire à l’accomplissement des finalités relatives aux relations de travail et/ou d’affaires, dans la limite des délais de prescription en vigueur.</p>
		
<p class="dark-grey-text font-weight-bold mb-3">Sécurité des traitements</p>
<p class="dark-grey-text">2pit.io prend les mesures physiques, techniques et organisationnelles appropriées pour assurer la sécurité et la confidentialité des données à caractère personnel, en vue notamment de les protéger contre toute perte, destruction accidentelle, altération, et accès non autorisés.</p>
		
<p class="dark-grey-text font-weight-bold mb-3">Application des réglementations locales en matière de transferts de données à caractère personnel en dehors de l\'Espace Économique propre à chaque utilisateur.</p>
<p class="dark-grey-text">Aucun transfert de données à caractères personnel en dehors de l’Espace Economique dont relève l’utilisateur ne sera effectué dans le cadre de l’utilisation de la plateforme 2pit.io.</p>
		
<p class="dark-grey-text font-weight-bold mb-3">Exercice des droits</p>
<p class="dark-grey-text">Le collaborateur dispose, dans les conditions prévues par la réglementation applicable, des droits suivants :</p>
<ol class="dark-grey-text">
<li>Accès aux données à caractère personnel collectées le concernant ;</li>
<li>Rectification ou de mise à jour des données inexactes, incomplètes ou périmées ;</li>
<li>Effacement ;</li>
<li>Limitation ;</li>
<li>Opposition pour motifs légitimes ;</li>
<li>Portabilité.</li>
</ol>
<p class="dark-grey-text">Ces droits peuvent être exercés par messagerie électronique à l’adresse mail suivante : <a href="mailto:contact@2pit.io?subject=2pit.io - Exercice des droits relatifs aux données personnelles">contact@2pit.io</a></p>
		
<p class="dark-grey-text font-weight-bold mb-3">Informations</p>
<p class="dark-grey-text">Pour toute question relative à la protection de leurs données personnelles, les collaborateurs peuvent s’adresser au Délégué à la Protection des Données de 2pit.io à l’adresse <a href="mailto:contact@2pit.io?subject=2pit.io - Question relative aux données personnelles">contact@2pit.io</a>. Les collaborateurs ont également la possibilité d’introduire une réclamation auprès de l’autorité de contrôle en charge de la protection des données.</p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">5 Modification des conditions d’utilisation</h5>
<p class="dark-grey-text">L’Utilisateur est invité à prendre régulièrement connaissance des présentes CGU, qui peuvent être modifiées à tout moment.</p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">6 Loi applicable / litiges</h5>
<p class="dark-grey-text">Les présentes conditions générales sont soumises au droit français.</p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">7 Suspension ou Fermeture de l’accès à la Plateforme</h5>
<p class="dark-grey-text">P-Pit SAS se réserve la possibilité de fermer l’accès à la plateforme 2pit.io à tout moment et pour quelque raison que ce soit sans avoir à le justifier ou de suspendre l’accès d’un utilisateur à la plateforme, notamment en cas de non-respect des conditions générales d’utilisation. Si un Utilisateur interrompt sa souscription à l’offre 2pit.io, son compte sera désactivé. Son profil sera conservé durant une période maximale de 1 (un) an et pourra être supprimé à sa demande.</p>
		
<h5 class="dark-grey-text font-weight-bold mb-3">8 Divers</h5>
<p class="dark-grey-text">Les demandes de support ou d’informations complémentaires sur l’utilisation de la plateforme, doivent être envoyées à l’adresse mail suivante : <a href="mailto:contact@2pit.io?subject=2pit.io - Donnez un titre à votre demande">contact@2pit.io</a>. Par ailleurs, si l’utilisateur constate un contenu qui ne devrait pas être présent sur MyCTO pour quelque raison que ce soit, y compris parce qu’il n’est pas conforme aux réglementations en vigueur, il est invité à contacter sans délai <a href="mailto:contact@2pit.io?subject=2pit.io - CONTENU SUSPECT">contact@2pit.io</a> avec le sujet « CONTENU SUSPECT ».</p>
',
		),

	'instance/last_terms_of_use_time' => '2018-08-09 00:00:00',
	
	'mailTo' => 'contact@probonocorpo.com', // Deprecated
	
	'ppit_roles' => array(
		'guest' => [],
		'user' => [],
		'super_admin' => array(
			'route' => 'place',
			'show' => true,
			'labels' => array(
				'en_US' => 'Super administrator',
				'fr_FR' => 'Super administrateur',
			)
		),
		'admin' => array(
			'route' => 'place',
			'show' => true,
			'labels' => array(
				'en_US' => 'Administrator',
				'fr_FR' => 'Administrateur',
			)
		),
		'dpo' => array(
			'route' => 'place',
			'show' => true,
			'labels' => array(
				'en_US' => 'Data Privacy Officer',
				'fr_FR' => 'Responsable des données privées',
			)
		),
		'manager' => array(
			'show' => true,
			'default' => true,
			'labels' => array(
				'en_US' => 'Head of training',
				'fr_FR' => 'Responsable pédagogique',
			),
		),
		'sales_manager' => array(
			'show' => true,
			'labels' => array(
				'en_US' => 'Sales manager',
				'fr_FR' => 'Gestion commerciale',
			),
		),
		'operational_management' => array(
			'show' => true,
			'default' => true,
			'labels' => array(
				'en_US' => 'Operational management',
				'fr_FR' => 'Gestion des opérations',
			),
		),
	),

	'manageable_roles' => ['admin', 'manager', 'sales_manager', 'operational_management', 'dpo'],
	
	'ppitApplications' => array(
		'synapps' => array(
			'labels' => array('fr_FR' => 'SynApps', 'en_US' => 'SynApps'),
			'default' => 'contact',
			'defaultRole' => 'operational_management',
		),
		'p-pit-admin' => array(
			'labels' => array('fr_FR' => 'P-Pit Admin', 'en_US' => '2Pit Admin'),
			'default' => 'user',
			'defaultRole' => 'operational_management',
		),
	),

	'perimeters' => array(
		'p-pit-admin' => array(),
	),

	'ppitCoreDependencies' => array(
		'core_account' => new \PpitCore\Model\Account,
		'core_vcard' => new \PpitCore\Model\Vcard,
	),
/*
	'ppitUser/index' => array(
		'title' => array('en_US' => 'P-PIT Admin', 'fr_FR' => 'P-PIT Admin'),
	),*/

	'menus/p-pit-admin' => array(
		'entries' => array(
			'place' => array(
				'route' => 'place/index',
				'params' => array(),
				'label' => array(
					'en_US' => 'Places',
					'fr_FR' => 'Etablissements',
				),
			),
			'user' => array(
				'route' => 'user/index',
				'params' => array(),
				'glyphicon' => 'glyphicon-user',
				'urlParams' => array(),
				'label' => array(
					'en_US' => 'Users',
					'fr_FR' => 'Utilisateurs',
				),
			),
			'interaction' => array(
				'route' => 'interaction/index',
				'params' => array(),
				'glyphicon' => 'glyphicon-transfer',
				'label' => array(
					'en_US' => 'Interactions',
					'fr_FR' => 'Interactions',
				),
			),
			'admin' => array(
				'route' => 'place/admin',
				'params' => array(),
				'urlParams' => array('app' => 'p-pit-studies'),
				'glyphicon' => 'glyphicon-cog',
				'label' => array(
					'en_US' => 'Admin',
					'fr_FR' => 'Admin',
				),
			),
		),
		'labels' => array(
			'default' => '2pit Admin',
			'fr_FR' => 'P-Pit Admin',
		),
	),

	'menus/synapps' => array(
		'entries' => array(
			'suspect' => array(
				'route' => 'account/indexAlt',
				'params' => array('entry' => 'contact', 'type' => 'pbc', 'entryId' => 'suspect'),
				'glyphicon' => 'glyphicon-user',
				'fa' => 'far fa-address-card fa-lg',
				'label' => array(
					'en_US' => 'All the contacts',
					'fr_FR' => 'Tous contacts',
				),
			),
			'contact' => array(
				'route' => 'account/indexAlt',
				'params' => array('entry' => 'account', 'type' => 'pbc', 'entryId' => 'contact'),
				'glyphicon' => 'glyphicon-user',
				'fa' => 'far fa-address-card fa-lg',
				'label' => array(
					'en_US' => 'Active',
					'fr_FR' => 'Actifs',
				),
			),
			'account' => array(
				'route' => 'account/indexAlt',
				'params' => array('entry' => 'group', 'type' => 'group', 'entryId' => 'account'),
				'glyphicon' => 'glyphicon-user',
				'label' => array(
					'en_US' => 'Groups',
					'fr_FR' => 'Groupes',
				),
			),
			'request' => array(
				'route' => 'event/indexAlt',
				'params' => array('type' => 'request', 'app' => 'synapps'),
				'label' => array(
					'en_US' => 'Requests',
					'fr_FR' => 'Demandes',
				),
			),
			'event' => array(
				'route' => 'event/indexAlt',
				'params' => array('type' => 'event', 'app' => 'synapps'),
				'label' => array(
					'en_US' => 'Events',
					'fr_FR' => 'Événements',
				),
			),
			'calendar' => array(
				'route' => 'event/calendar',
				'params' => array('type' => 'planning', 'app' => 'synapps'),
				'label' => array(
					'en_US' => 'Calendar',
					'fr_FR' => 'Calendrier',
				),
			),
			'email' => array(
				'route' => 'event/index',
				'params' => array('entry' => 'email', 'type' => 'email', 'app' => 'synapps'),
				'glyphicon' => 'glyphicon-send',
				'fa' => 'far fa-paper-plane fa-lg',
				'label' => array(
					'en_US' => 'Emails',
					'fr_FR' => 'Emails',
				),
			),
		),
		'labels' => array(
			'default' => 'Synapps',
			'fr_FR' => 'Synapps',
		),
	),
/*
	'admin/p-pit-admin' => array(
		'place',
	),

	'calendar/p-pit-admin' => array(
	),*/

	'styleSheet' => array(
		'navbar' => 'navbar-default navbar-fixed-top',
		'panelHeadingBackground' => '#006179',
		'panelHeadingColor' => '#FFFFFF',
	),

	'bootstrap4' => array(
		'body' => 'fixed-sn indigo-skin',
		'navbar' => 'navbar-toggleable-md navbar-dark background-primary indigo lighten-2',
		'footer' => 'indigo lighten-3 center-on-small-only',
		'logo' => '2pit.png',
		'logo-height' => '20',
	),

	'headerParams' => array(
		'background-color' => '#79CCF3',
		'shift' => 0,
		'logo' => '2pit.png',
		'logo-href' => "https://www.p-pit.fr",
		'logo-height' => '60',
		'advert' => 'p-pit-advert.png',
		'advert-width' => 180,
		'signature' => null,
/*		'signature-href' => "https://www.p-pit.fr",
		'signature-width' => '280px',*/
		'footer' => array(
			'type' => 'text',
			'value' => 'P-Pit – SAS au capital de xxx € - R.C.S PARIS xxx xxx xxx - ...',
		),
	),

	'legalHoliday' => array(
		'fr_FR' => array(
			'2016-01-01', '2016-05-05', '2016-05-08', '2016-07-14', '2016-08-15', '2016-11-01', '2016-11-11', '2016-12-25',
			'2017-04-17', '2017-05-01', '2017-05-08', '2017-05-25', '2017-06-05', '2017-07-14', '2017-08-15', '2017-11-01', '2017-12-25',
		),
		'en_US' => array(),
	),
		
	'creditConsumers' => array(
//		'\Model\Community::consumeCredits',
	),
		
	'credit' => array(
		'unlimitedCredits' => false,
	),
	'instance/index' => array(
		'title' => array('en_US' => 'P-PIT Admin', 'fr_FR' => 'P-PIT Admin'),
	),

	'accepted_tags_in_database' => '<h1><h2><h3><h4><h5><p><a><img><br><hr><span><div><em><strong>',

	// Vcard: Deprecated => To be removed from updateContact

	'vcard/properties' => array(
		'n_title' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Title',
				'fr_FR' => 'Civilité',
			),
		),
		'n_first' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'First name',
				'fr_FR' => 'Prénom',
			),
		),
		'n_last' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Last name',
				'fr_FR' => 'Nom',
			),
		),
		'n_fn' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Name',
				'fr_FR' => 'Nom',
			),
		),
		'tel_work' => array(
			'definition' => 'inline',
			'type' => 'phone',
			'labels' => array(
				'en_US' => 'Phone',
				'fr_FR' => 'Téléphone',
			),
		),
		'tel_cell' => array(
			'definition' => 'inline',
			'type' => 'phone',
			'labels' => array(
				'en_US' => 'Cellular',
				'fr_FR' => 'Mobile',
			),
		),
		'email' => array(
			'definition' => 'inline',
			'type' => 'email',
			'labels' => array(
				'en_US' => 'Email',
				'fr_FR' => 'Email',
			),
		),
		'adr_street' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Address - street',
				'fr_FR' => 'Adresse - rue',
			),
		),
		'adr_extended' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Address - extended',
				'fr_FR' => 'Adresse - complément',
			),
		),
		'adr_post_office_box' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Address - post office box',
				'fr_FR' => 'Adresse - boîte postale',
			),
		),
		'adr_zip' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Address - ZIP',
				'fr_FR' => 'Adresse - Code postal',
			),
		),
		'adr_city' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Address - city',
				'fr_FR' => 'Adresse - ville',
			),
		),
		'adr_state' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Address - state',
				'fr_FR' => 'Adresse - état',
			),
		),
		'adr_country' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'en_US' => 'Address - country',
				'fr_FR' => 'Adresse - pays',
			),
		),
		'locale' => array(
			'definition' => 'inline',
			'type' => 'select',
			'modalities' => array(
				'en_US' => array('en_US' => 'en_US', 'fr_FR' => 'en_US'),
				'fr_FR' => array('en_US' => 'fr_FR', 'fr_FR' => 'fr_FR'),
			),
			'labels' => array(
				'en_US' => 'Locale',
				'fr_FR' => 'Traduction',
			),
		),
	),

	// Profile
	
	'profile/generic/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
			'minimum' => array('en_US' => 'Minimum', 'fr_FR' => 'Minimal'),
			'intermediary' => array('en_US' => 'Intermediary', 'fr_FR' => 'Intermédiaire'),
			'complete' => array('en_US' => 'Complete', 'fr_FR' => 'Complet'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
	),
	
	// Account

	'core_account/generic/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
//			'suspect' => array('en_US' => 'Suspect (landing page)', 'fr_FR' => 'Suspect (landing page)'),
			'interested' => array('en_US' => 'Interested', 'fr_FR' => 'Intéressé'),
//			'candidate' => array('en_US' => 'Condidate', 'fr_FR' => 'Candidat'),
			'active' => array('en_US' => 'Active', 'fr_FR' => 'Actif'),
			'gone' => array('en_US' => 'Gone', 'fr_FR' => 'Parti'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
		'perspectives' => array(
//			'suspect' => array('suspect'),
//			'contact' => array('new', 'interested', 'candidate', 'gone'),
			'account' => array('new', 'interested', 'active', 'gone'),
		),
	),
	
	'core_account/generic/property/place_id' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'2pit' => array('fr_FR' => 'P-PIT', 'en_US' => '2PIT'),
		),
		'labels' => array(
			'en_US' => 'Place',
			'fr_FR' => 'Établissement',
		),
	),

	'core_account/generic/property/identifier' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Identifier',
			'fr_FR' => 'Identifiant',
		),
	),

	'core_account/generic/property/name' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Name',
			'fr_FR' => 'Dénomination',
		),
	),
	
	'core_account/generic/property/photo_link_id' => array(
		'definition' => 'inline',
		'type' => 'photo',
		'labels' => array(
			'en_US' => '',
			'fr_FR' => '',
		),
	),
	
	'core_account/generic/property/basket' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'p1' => array('en_US' => 'P1', 'fr_FR' => 'P1'),
			'p2' => array('en_US' => 'P2', 'fr_FR' => 'P2'),
			'p3' => array('en_US' => 'P3', 'fr_FR' => 'P3'),
		),
		'labels' => array(
			'en_US' => 'Priority',
			'fr_FR' => 'Priorité',
		),
	),
	
	'core_account/generic/property/contact_1_id' => array(
		'definition' => 'inline',
		'type' => 'photo',
		'labels' => array(
			'en_US' => '',
			'fr_FR' => '',
		),
	),
	
	'core_account/generic/property/contact_1_status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'main' => array('fr_FR' => 'Principal', 'en_US' => 'Main'),
			'invoice' => array('fr_FR' => 'Invoicing', 'en_US' => 'Facturation'),
		),
		'labels' => array(
			'en_US' => 'Contact role',
			'fr_FR' => 'Rôle du contact',
		),
	),
	
	'core_account/generic/property/org' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Enterprise',
			'fr_FR' => 'Entreprise',
		),
	),
	
	'core_account/generic/property/title_1' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'en_US' => 'CONTACT IDENTIFICATION',
			'fr_FR' => 'IDENTIFICATION DU CONTACT',
		),
	),
	
	'core_account/generic/property/n_title' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Title',
			'fr_FR' => 'Titre',
		),
	),
	
	'core_account/generic/property/n_first' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'First name',
			'fr_FR' => 'Prénom',
		),
	),
	
	'core_account/generic/property/n_last' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Last name',
			'fr_FR' => 'Nom',
		),
	),
	
	'core_account/generic/property/n_fn' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Formatted name',
			'fr_FR' => 'Nom formaté',
		),
	),
	
	'core_account/generic/property/email' => array(
		'definition' => 'inline',
		'type' => 'email',
		'labels' => array(
			'en_US' => 'Email',
			'fr_FR' => 'Email',
		),
	),
	
	'core_account/generic/property/tel_work' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Phone',
			'fr_FR' => 'Téléphone',
		),
	),
	
	'core_account/generic/property/tel_cell' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Cellular',
			'fr_FR' => 'Mobile',
		),
	),
	
	'core_account/generic/property/adr_street' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Address',
			'fr_FR' => 'Adresse',
		),
	),
	
	'core_account/generic/property/adr_extended' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Complement',
			'fr_FR' => 'Complément',
		),
	),
	
	'core_account/generic/property/adr_post_office_box' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Post office box',
			'fr_FR' => 'Boîte postale',
		),
	),
	
	'core_account/generic/property/adr_zip' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Zip code',
			'fr_FR' => 'Code postal',
		),
	),
	
	'core_account/generic/property/adr_city' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'City',
			'fr_FR' => 'Ville',
		),
	),
	
	'core_account/generic/property/adr_state' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'State',
			'fr_FR' => 'Etat',
		),
	),
	
	'core_account/generic/property/adr_country' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Country',
			'fr_FR' => 'Pays',
		),
	),
	
	'core_account/generic/property/birth_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'Birth date',
			'fr_FR' => 'Date de naissance',
		),
	),
	
	'core_account/generic/property/gender' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'f' => array('en_US' => 'Female', 'fr_FR' => 'Femme'),
			'm' => array('en_US' => 'Male', 'fr_FR' => 'Homme'),
		),
		'labels' => array(
			'en_US' => 'Gender',
			'fr_FR' => 'Genre',
		),
	),
	
	'core_account/generic/property/nationality' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Nationality',
			'fr_FR' => 'Nationalité',
		),
	),

	'core_account/generic/property/locale' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'en_US' => ['default' => 'en_US'],
			'fr_FR' => ['default' => 'fr_FR'],
		),
		'labels' => array(
			'en_US' => 'Language',
			'fr_FR' => 'Langue',
		),
	),

	'core_account/generic/property/invoice_n_title' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - Title',
			'fr_FR' => 'Facturation - Titre',
		),
	),
	
	'core_account/generic/property/invoice_n_first' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - First name',
			'fr_FR' => 'Facturation - Prénom',
		),
	),
	'core_account/generic/property/invoice_n_last' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - Last name',
			'fr_FR' => 'Facturation - Nom',
		),
	),
	'core_account/generic/property/invoice_email' => array(
		'definition' => 'inline',
		'type' => 'email',
		'labels' => array(
			'en_US' => 'Invoicing - Email',
			'fr_FR' => 'Facturation - Email',
		),
	),
	'core_account/generic/property/invoice_tel_work' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Invoicing - Phone',
			'fr_FR' => 'Facturation - Téléphone',
		),
	),
	'core_account/generic/property/invoice_tel_cell' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Invoicing - Cellular',
			'fr_FR' => 'Facturation - Mobile',
		),
	),
	'core_account/generic/property/invoice_adr_street' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - Address',
			'fr_FR' => 'Facturation - Adresse',
		),
	),
	'core_account/generic/property/invoice_adr_extended' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - Complement',
			'fr_FR' => 'Facturation - Complément',
		),
	),
	'core_account/generic/property/invoice_adr_post_office_box' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - Post office box',
			'fr_FR' => 'Facturation - Boîte postale',
		),
	),
	'core_account/generic/property/invoice_adr_zip' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - Zip code',
			'fr_FR' => 'Facturation - Code postal',
		),
	),
	'core_account/generic/property/invoice_adr_city' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - City',
			'fr_FR' => 'Facturation - Ville',
		),
	),
	'core_account/generic/property/invoice_adr_state' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - State',
			'fr_FR' => 'Facturation - Etat',
		),
	),
	'core_account/generic/property/invoice_adr_country' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Invoicing - Country',
			'fr_FR' => 'Facturation - Pays',
		),
	),
	'core_account/generic/property/invoice_gender' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'f' => array('en_US' => 'Female', 'fr_FR' => 'Femme'),
			'm' => array('en_US' => 'Male', 'fr_FR' => 'Homme'),
		),
		'labels' => array(
			'en_US' => 'Gender',
			'fr_FR' => 'Genre',
		),
	),
	
	'core_account/generic/property/contact_2_id' => array('definition' => 'core_account/generic/property/contact_1_id'),
	'core_account/generic/property/contact_2_status' => array('definition' => 'core_account/generic/property/contact_1_status'),
	'core_account/generic/property/n_title_2' => array('definition' => 'core_account/generic/property/n_title'),
	'core_account/generic/property/n_first_2' => array('definition' => 'core_account/generic/property/n_first'),
	'core_account/generic/property/n_last_2' => array('definition' => 'core_account/generic/property/n_last'),
	'core_account/generic/property/n_fn_2' => array('definition' => 'core_account/generic/property/n_fn'),
	'core_account/generic/property/email_2' => array('definition' => 'core_account/generic/property/email'),
	'core_account/generic/property/tel_work_2' => array('definition' => 'core_account/generic/property/tel_work'),
	'core_account/generic/property/tel_cell_2' => array('definition' => 'core_account/generic/property/tel_cell'),
	'core_account/generic/property/adr_street_2' => array('definition' => 'core_account/generic/property/adr_street'),
	'core_account/generic/property/adr_extended_2' => array('definition' => 'core_account/generic/property/adr_extended'),
	'core_account/generic/property/adr_post_office_box_2' => array('definition' => 'core_account/generic/property/adr_post_office_box'),
	'core_account/generic/property/adr_zip_2' => array('definition' => 'core_account/generic/property/adr_zip'),
	'core_account/generic/property/adr_city_2' => array('definition' => 'core_account/generic/property/adr_city'),
	'core_account/generic/property/adr_state_2' => array('definition' => 'core_account/generic/property/adr_state'),
	'core_account/generic/property/adr_country_2' => array('definition' => 'core_account/generic/property/adr_country'),

	'core_account/generic/property/contact_3_id' => array('definition' => 'core_account/generic/property/contact_1_id'),
	'core_account/generic/property/contact_3_status' => array('definition' => 'core_account/generic/property/contact_1_status'),
	'core_account/generic/property/n_title_3' => array('definition' => 'core_account/generic/property/n_title'),
	'core_account/generic/property/n_first_3' => array('definition' => 'core_account/generic/property/n_first'),
	'core_account/generic/property/n_last_3' => array('definition' => 'core_account/generic/property/n_last'),
	'core_account/generic/property/n_fn_3' => array('definition' => 'core_account/generic/property/n_fn'),
	'core_account/generic/property/email_3' => array('definition' => 'core_account/generic/property/email'),
	'core_account/generic/property/tel_work_3' => array('definition' => 'core_account/generic/property/tel_work'),
	'core_account/generic/property/tel_cell_3' => array('definition' => 'core_account/generic/property/tel_cell'),
	'core_account/generic/property/adr_street_3' => array('definition' => 'core_account/generic/property/adr_street'),
	'core_account/generic/property/adr_extended_3' => array('definition' => 'core_account/generic/property/adr_extended'),
	'core_account/generic/property/adr_post_office_box_3' => array('definition' => 'core_account/generic/property/adr_post_office_box'),
	'core_account/generic/property/adr_zip_3' => array('definition' => 'core_account/generic/property/adr_zip'),
	'core_account/generic/property/adr_city_3' => array('definition' => 'core_account/generic/property/adr_city'),
	'core_account/generic/property/adr_state_3' => array('definition' => 'core_account/generic/property/adr_state'),
	'core_account/generic/property/adr_country_3' => array('definition' => 'core_account/generic/property/adr_country'),

	'core_account/generic/property/contact_4_id' => array('definition' => 'core_account/generic/property/contact_1_id'),
	'core_account/generic/property/contact_4_status' => array('definition' => 'core_account/generic/property/contact_1_status'),
	'core_account/generic/property/n_title_4' => array('definition' => 'core_account/generic/property/n_title'),
	'core_account/generic/property/n_first_4' => array('definition' => 'core_account/generic/property/n_first'),
	'core_account/generic/property/n_last_4' => array('definition' => 'core_account/generic/property/n_last'),
	'core_account/generic/property/n_fn_4' => array('definition' => 'core_account/generic/property/n_fn'),
	'core_account/generic/property/email_4' => array('definition' => 'core_account/generic/property/email'),
	'core_account/generic/property/tel_work_4' => array('definition' => 'core_account/generic/property/tel_work'),
	'core_account/generic/property/tel_cell_4' => array('definition' => 'core_account/generic/property/tel_cell'),
	'core_account/generic/property/adr_street_4' => array('definition' => 'core_account/generic/property/adr_street'),
	'core_account/generic/property/adr_extended_4' => array('definition' => 'core_account/generic/property/adr_extended'),
	'core_account/generic/property/adr_post_office_box_4' => array('definition' => 'core_account/generic/property/adr_post_office_box'),
	'core_account/generic/property/adr_zip_4' => array('definition' => 'core_account/generic/property/adr_zip'),
	'core_account/generic/property/adr_city_4' => array('definition' => 'core_account/generic/property/adr_city'),
	'core_account/generic/property/adr_state_4' => array('definition' => 'core_account/generic/property/adr_state'),
	'core_account/generic/property/adr_country_4' => array('definition' => 'core_account/generic/property/adr_country'),

	'core_account/generic/property/contact_5_id' => array('definition' => 'core_account/generic/property/contact_1_id'),
	'core_account/generic/property/contact_5_status' => array('definition' => 'core_account/generic/property/contact_1_status'),
	'core_account/generic/property/n_title_5' => array('definition' => 'core_account/generic/property/n_title'),
	'core_account/generic/property/n_first_5' => array('definition' => 'core_account/generic/property/n_first'),
	'core_account/generic/property/n_last_5' => array('definition' => 'core_account/generic/property/n_last'),
	'core_account/generic/property/n_fn_5' => array('definition' => 'core_account/generic/property/n_fn'),
	'core_account/generic/property/email_5' => array('definition' => 'core_account/generic/property/email'),
	'core_account/generic/property/tel_work_5' => array('definition' => 'core_account/generic/property/tel_work'),
	'core_account/generic/property/tel_cell_5' => array('definition' => 'core_account/generic/property/tel_cell'),
	'core_account/generic/property/adr_street_5' => array('definition' => 'core_account/generic/property/adr_street'),
	'core_account/generic/property/adr_extended_5' => array('definition' => 'core_account/generic/property/adr_extended'),
	'core_account/generic/property/adr_post_office_box_5' => array('definition' => 'core_account/generic/property/adr_post_office_box'),
	'core_account/generic/property/adr_zip_5' => array('definition' => 'core_account/generic/property/adr_zip'),
	'core_account/generic/property/adr_city_5' => array('definition' => 'core_account/generic/property/adr_city'),
	'core_account/generic/property/adr_state_5' => array('definition' => 'core_account/generic/property/adr_state'),
	'core_account/generic/property/adr_country_5' => array('definition' => 'core_account/generic/property/adr_country'),

	'core_account/generic/property/groups' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array( // Dynamicaly loaded
		),
		'labels' => array(
			'en_US' => 'Groups',
			'fr_FR' => 'Groupes',
		),
	),
	
	'core_account/generic/property/opening_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => '1st contact date',
			'fr_FR' => 'Date 1er contact',
		),
	),
	
	'core_account/generic/property/closing_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'Closing date',
			'fr_FR' => 'Date de fermeture',
		),
	),
	
	'core_account/generic/property/callback_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'Callback date',
			'fr_FR' => 'Date de rappel',
		),
	),

	'core_account/generic/property/first_activation_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'First activation date',
			'fr_FR' => 'Date 1ère activation',
		),
	),

	'core_account/generic/property/date_1' => array(
		'definition' => 'inline',
		'type' => 'date',
		'dependency' => ['property' => 'status', 'values' => ['suspect']],
		'labels' => array(
			'en_US' => 'Suspect status date',
			'fr_FR' => 'Date statut Suspect',
		),
	),
	
	'core_account/generic/property/date_2' => array(
		'definition' => 'inline',
		'type' => 'date',
		'dependency' => ['property' => 'status', 'values' => ['interested']],
		'labels' => array(
			'en_US' => 'Interested status date',
			'fr_FR' => 'Date statut Intéressé',
		),
	),
	
	'core_account/generic/property/date_3' => array(
		'definition' => 'inline',
		'type' => 'date',
		'dependency' => ['property' => 'status', 'values' => ['candidate']],
		'labels' => array(
			'en_US' => 'Candidate status date',
			'fr_FR' => 'Date statut Candidat',
		),
	),
	
	'core_account/generic/property/date_4' => array(
		'definition' => 'inline',
		'type' => 'date',
		'dependency' => ['property' => 'status', 'values' => ['active']],
		'labels' => array(
			'en_US' => 'Active status date',
			'fr_FR' => 'Date statut Actif',
		),
	),
	
	'core_account/generic/property/date_5' => array(
		'definition' => 'inline',
		'type' => 'date',
		'dependency' => ['property' => 'status', 'values' => ['gone']],
		'labels' => array(
			'en_US' => 'Gone status date',
			'fr_FR' => 'Date statut Parti',
		),
	),
	
	'core_account/generic/property/next_meeting_date' => array(
		'definition' => 'inline',
		'type' => 'datetime',
		'labels' => array(
			'en_US' => 'Next meeting_date',
			'fr_FR' => 'Prochain rendez-vous',
		),
	),
	
	'core_account/generic/property/next_meeting_confirmed' => array(
		'definition' => 'inline',
		'type' => 'datetime',
		'labels' => array(
			'en_US' => 'Meeting confirmed',
			'fr_FR' => 'Rendez-vous confirmé',
		),
	),
	
	'core_account/generic/property/priority' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'p1' => array('en_US' => 'Priority 1', 'fr_FR' => 'Priorité 1'),
			'p2' => array('en_US' => 'Priority 2', 'fr_FR' => 'Priorité 2'),
			'p3' => array('en_US' => 'Priority 3', 'fr_FR' => 'Priorité 3'),
		),
		'labels' => array(
			'en_US' => 'Priority',
			'fr_FR' => 'Priorité',
		),
	),
	
	'core_account/generic/property/origine' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'subscription' => array('en_US' => 'Online subscription', 'fr_FR' => 'Inscription en ligne'),
			'cooptation' => array('en_US' => 'Cooptation', 'fr_FR' => 'Cooptation'),
			'file' => array('en_US' => 'File', 'fr_FR' => 'Fichier'),
			'e_mailing' => array('en_US' => 'e-mailing', 'fr_FR' => 'e-mailing'),
			'facebook' => array('default' => 'Facebook'),
			'agency' => array('default' => 'Agence'),
		),
		'labels' => array(
			'en_US' => 'Origine',
			'fr_FR' => 'Origine',
		),
	),
	
	'core_account/generic/property/title_2' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'en_US' => 'COMMENTS',
			'fr_FR' => 'COMMENTAIRES',
		),
	),
	
	'core_account/generic/property/contact_history' => array(
		'definition' => 'inline',
		'type' => 'log',
		'labels' => array(
			'en_US' => 'Comment',
			'fr_FR' => 'Commentaire',
		),
	),

	'core_account/generic/property/availability' => array(
		'definition' => 'inline',
		'type' => 'computed',
		'labels' => array(
			'en_US' => 'Availability',
			'fr_FR' => 'Disponibilité',
		),
	),
	
	'core_account/generic/property/availability_begin' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'Availability begin',
			'fr_FR' => 'Début de disponibilité',
		),
	),
	
	'core_account/generic/property/availability_end' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'Availability end',
			'fr_FR' => 'Fin de disponibilité',
		),
	),
	
	'core_account/generic/property/availability_exceptions' => array(
		'definition' => 'inline',
		'type' => 'structure',
		'max_occurences' => 10,
		'fields' => array(
			'begin_date' => array(
				'type' => 'date',
				'mandatory' => true,
				'labels' => array('en_US' => 'Begin date', 'fr_FR' => 'Date de début'),
			),
			'end_date' => array(
				'type' => 'date',
				'mandatory' => true,
				'labels' => array('en_US' => 'End date', 'fr_FR' => 'Date de fin'),
			),
		),
		'labels' => array(
				'en_US' => 'Unavailability slots',
				'fr_FR' => 'Plages d\'indisponibilité',
		),
	),
	
	'core_account/generic/property/availability_constraints' => array(
		'definition' => 'inline',
		'type' => 'structure',
		'max_occurences' => 1,
		'fields' => array(
			'sunday' => array(
				'type' => 'select',
				'mandatory' => false,
				'modalities' => array(
					'day' => ['default' => 'Day', 'fr_FR' => 'Journée'],
					'morning' => ['default' => 'Morning', 'fr_FR' => 'Matin'],
					'afternoon' => ['default' => 'Afternoon', 'fr_FR' => 'Après-midi'],
					'evening' => ['default' => 'Evening', 'fr_FR' => 'Soirée'],
				),
				'labels' => array('en_US' => 'Sunday', 'fr_FR' => 'Dimanche'),
			),
			'monday' => array(
				'type' => 'select',
				'mandatory' => false,
				'modalities' => array(
					'day' => ['default' => 'Day', 'fr_FR' => 'Journée'],
					'morning' => ['default' => 'Morning', 'fr_FR' => 'Matin'],
					'afternoon' => ['default' => 'Afternoon', 'fr_FR' => 'Après-midi'],
					'evening' => ['default' => 'Evening', 'fr_FR' => 'Soirée'],
				),
				'labels' => array('en_US' => 'Monday', 'fr_FR' => 'Lundi'),
			),
			'tuesday' => array(
				'type' => 'select',
				'mandatory' => false,
				'modalities' => array(
					'day' => ['default' => 'Day', 'fr_FR' => 'Journée'],
					'morning' => ['default' => 'Morning', 'fr_FR' => 'Matin'],
					'afternoon' => ['default' => 'Afternoon', 'fr_FR' => 'Après-midi'],
					'evening' => ['default' => 'Evening', 'fr_FR' => 'Soirée'],
				),
				'labels' => array('en_US' => 'Tuesday', 'fr_FR' => 'Mardi'),
			),
			'wednesday' => array(
				'type' => 'select',
				'mandatory' => false,
				'modalities' => array(
					'day' => ['default' => 'Day', 'fr_FR' => 'Journée'],
					'morning' => ['default' => 'Morning', 'fr_FR' => 'Matin'],
					'afternoon' => ['default' => 'Afternoon', 'fr_FR' => 'Après-midi'],
					'evening' => ['default' => 'Evening', 'fr_FR' => 'Soirée'],
				),
				'labels' => array('en_US' => 'Wednesday', 'fr_FR' => 'Mercredi'),
			),
			'thursday' => array(
				'type' => 'select',
				'mandatory' => false,
				'modalities' => array(
					'day' => ['default' => 'Day', 'fr_FR' => 'Journée'],
					'morning' => ['default' => 'Morning', 'fr_FR' => 'Matin'],
					'afternoon' => ['default' => 'Afternoon', 'fr_FR' => 'Après-midi'],
					'evening' => ['default' => 'Evening', 'fr_FR' => 'Soirée'],
				),
				'labels' => array('en_US' => 'Thursday', 'fr_FR' => 'Jeudi'),
			),
			'friday' => array(
				'type' => 'select',
				'mandatory' => false,
				'modalities' => array(
					'day' => ['default' => 'Day', 'fr_FR' => 'Journée'],
					'morning' => ['default' => 'Morning', 'fr_FR' => 'Matin'],
					'afternoon' => ['default' => 'Afternoon', 'fr_FR' => 'Après-midi'],
					'evening' => ['default' => 'Evening', 'fr_FR' => 'Soirée'],
				),
				'labels' => array('en_US' => 'Friday', 'fr_FR' => 'Vendredi'),
			),
			'saturday' => array(
				'type' => 'select',
				'mandatory' => false,
				'modalities' => array(
					'day' => ['default' => 'Day', 'fr_FR' => 'Journée'],
					'morning' => ['default' => 'Morning', 'fr_FR' => 'Matin'],
					'afternoon' => ['default' => 'Afternoon', 'fr_FR' => 'Après-midi'],
					'evening' => ['default' => 'Evening', 'fr_FR' => 'Soirée'],
				),
				'labels' => array('en_US' => 'Saturday', 'fr_FR' => 'Samedi'),
			),
		),
		'labels' => array(
				'en_US' => 'Days of availability',
				'fr_FR' => 'Jours de disponibilité',
		),
	),

	'core_account/generic/property/credits' => array(
		'definition' => 'inline',
		'type' => 'key_value',
		'labels' => array(
			'en_US' => 'Credits',
			'fr_FR' => 'Crédits',
		),
	),
	
	'core_account/generic/property/default_means_of_payment' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'bank_card' => array('fr_FR' => 'CB', 'en_US' => 'Bank card'),
			'transfer' => array('fr_FR' => 'Virement', 'en_US' => 'Transfer'),
			'direct_debit' => array('fr_FR' => 'Prélèvement', 'en_US' => 'Direct debit'),
			'check' => array('fr_FR' => 'Chèque', 'en_US' => 'Check'),
			'cash' => array('fr_FR' => 'Espèces', 'en_US' => 'Cash'),
		),
		'labels' => array(
			'en_US' => 'Default means of payment',
			'fr_FR' => 'Mode de règlement par défaut',
		),
	),
	
	'core_account/generic/property/transfer_order_id' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'SEPA Unique mandat reference',
			'fr_FR' => 'Référence unique du mandat SEPA',
		),
	),

	'core_account/generic/property/transfer_order_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'SEPA mandat signature date',
			'fr_FR' => 'Date de signature du mandat SEPA',
		),
	),

	'core_account/generic/property/bank_identifier' => array(
		'definition' => 'inline',
		'type' => 'input',
		'private' => true,
		'labels' => array(
			'en_US' => 'IBAN',
			'fr_FR' => 'IBAN',
		),
	),
	
	'core_account/generic/property/notification_time' => array(
		'definition' => 'inline',
		'type' => 'datetime',
		'labels' => array(
			'en_US' => 'Last notification time',
			'fr_FR' => 'Heure de dernière notification',
		),
	),
	
	'core_account/generic/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 1',
			'fr_FR' => 'Champs libre 1',
		),
	),
	
	'core_account/generic/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 2',
			'fr_FR' => 'Champs libre 2',
		),
	),
	
	'core_account/generic/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 3',
			'fr_FR' => 'Champs libre 3',
		),
	),
	
	'core_account/generic/property/property_4' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 4',
			'fr_FR' => 'Champs libre 4',
		),
	),
	
	'core_account/generic/property/property_5' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 5',
			'fr_FR' => 'Champs libre 5',
		),
	),
	
	'core_account/generic/property/property_6' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 6',
			'fr_FR' => 'Champs libre 6',
		),
	),
	
	'core_account/generic/property/property_7' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 7',
			'fr_FR' => 'Champs libre 7',
		),
	),
	
	'core_account/generic/property/property_8' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 8',
			'fr_FR' => 'Champs libre 8',
		),
	),
	
	'core_account/generic/property/property_9' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 9',
			'fr_FR' => 'Champs libre 9',
		),
	),
	
	'core_account/generic/property/property_10' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 10',
			'fr_FR' => 'Champs libre 10',
		),
	),
	
	'core_account/generic/property/property_11' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 11',
			'fr_FR' => 'Champs libre 11',
		),
	),
	
	'core_account/generic/property/property_12' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 12',
			'fr_FR' => 'Champs libre 12',
		),
	),
	
	'core_account/generic/property/property_13' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 13',
			'fr_FR' => 'Champs libre 13',
		),
	),
	
	'core_account/generic/property/property_14' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 14',
			'fr_FR' => 'Champs libre 14',
		),
	),
	
	'core_account/generic/property/property_15' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 15',
			'fr_FR' => 'Champs libre 15',
		),
	),
	
	'core_account/generic/property/property_16' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Free field 16',
			'fr_FR' => 'Champs libre 16',
		),
	),

	'core_account/generic/property/property_17' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Free property 17',
			'fr_FR' => 'Propriété libre 17',
		),
	),
	
	'core_account/generic/property/property_18' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Free property 18',
			'fr_FR' => 'Propriété libre 18',
		),
	),
	
	'core_account/generic/property/property_19' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Free property 19',
			'fr_FR' => 'Propriété libre 19',
		),
	),
	
	'core_account/generic/property/property_20' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Free property 20',
			'fr_FR' => 'Propriété libre 20',
		),
	),
	
	'core_account/generic/property/json_property_1' => array(
		'definition' => 'inline',
		'type' => 'key_value',
		'labels' => array(
			'en_US' => 'Data (1)',
			'fr_FR' => 'Données (1)',
		),
	),
	
	'core_account/generic/property/json_property_2' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Data (2)',
			'fr_FR' => 'Données (2)',
		),
	),
	
	'core_account/generic/property/json_property_3' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Data (3)',
			'fr_FR' => 'Données (3)',
		),
	),
	
	'core_account/generic/property/json_property_4' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Data (4)',
			'fr_FR' => 'Données (4)',
		),
	),
	
	'core_account/generic/property/json_property_5' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Data (5)',
			'fr_FR' => 'Données (5)',
		),
	),
	
	'core_account/generic/property/comment_1' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Comments (1)',
			'fr_FR' => 'Commentaires (1)',
		),
		'max_length' => 65535,
	),
	
	'core_account/generic/property/comment_2' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Comments (2)',
			'fr_FR' => 'Commentaires (2)',
		),
		'max_length' => 65535,
	),
	
	'core_account/generic/property/comment_3' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Comments (3)',
			'fr_FR' => 'Commentaires (3)',
		),
		'max_length' => 65535,
	),
	
	'core_account/generic/property/comment_4' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Comments (4)',
			'fr_FR' => 'Commentaires (4)',
		),
		'max_length' => 65535,
	),
	
	'core_account/generic/property/update_time' => array(
		'definition' => 'inline',
		'type' => 'datetime',
		'labels' => array(
			'en_US' => 'Update time',
			'fr_FR' => 'Heure de mise à jour',
		),
	),
	
	'core_account/generic' => array(
		'properties' => array(
			'title_1', 'title_2', 'status', 'place_id', 'identifier', 'name', 'photo_link_id', 'basket', 'contact_1_id', 'contact_1_status', 'org',
			'date_1', 'date_2', 'date_3', 'date_4', 'date_5',
			'n_title', 'n_first', 'n_last', 'email', 'tel_work', 'tel_cell', 'locale', 
			'adr_street', 'adr_extended', 'adr_post_office_box', 'adr_zip', 'adr_city', 'adr_state', 'adr_country', 'birth_date', 'locale',
			'contact_2_id', 'contact_2_status', 'n_title_2', 'n_first_2', 'n_last_2', 'email_2', 'tel_work_2', 'tel_cell_2',
			'adr_street_2', 'adr_extended_2', 'adr_post_office_box_2', 'adr_zip_2', 'adr_city_2', 'adr_state_2', 'adr_country_2',
			'contact_3_id', 'contact_3_status', 'n_title_3', 'n_first_3', 'n_last_3', 'email_3', 'tel_work_3', 'tel_cell_3',
			'adr_street_3', 'adr_extended_3', 'adr_post_office_box_3', 'adr_zip_3', 'adr_city_3', 'adr_state_3', 'adr_country_3',
			'contact_4_id', 'contact_4_status', 'n_title_4', 'n_first_4', 'n_last_4', 'email_4', 'tel_work_4', 'tel_cell_4', 
			'adr_street_4', 'adr_extended_4', 'adr_post_office_box_4', 'adr_zip_4', 'adr_city_4', 'adr_state_4', 'adr_country_4',
			'contact_5_id', 'contact_5_status', 'n_title_5', 'n_first_5', 'n_last_5', 'email_5', 'tel_work_5', 'tel_cell_5',
			'adr_street_5', 'adr_extended_5', 'adr_post_office_box_5', 'adr_zip_5', 'adr_city_5', 'adr_state_5', 'adr_country_5',
			'groups',
			'opening_date', 'closing_date', 'callback_date', 'first_activation_date', 'next_meeting_date', 'next_meeting_confirmed', 'priority', 'origine', 'contact_history', 'notification_time',
			'default_means_of_payment', 'transfer_order_id', 'transfer_order_date', 'bank_identifier',
			'property_1', 'property_2', 'property_3', 'property_4', 'property_5', 'property_6', 'property_7', 'property_8', 
			'property_9', 'property_10', 'property_11', 'property_12', 'property_13', 'property_14', 'property_15', 'property_16', 'property_17', 'property_18', 'property_19', 'property_20',
			'json_property_1', 'json_property_2', 'json_property_3', 'json_property_4', 'json_property_5',
			'comment_1', 'comment_2', 'comment_3', 'comment_4', 'update_time'
		),
		'acl' => array(
			'place_id' => array('application' => 'p-pit-admin', 'category' => 'place_id'),
		),
		'order' => 'name',
	),

	'core_account/todo/generic/contact' => array(
		'filters' => array(
			'status' => ['new', 'interested'],
			'next_meeting_date' => ['*'],
		),
		'order' => ['status' => 'DESC', 'callback_date' => 'ASC'],
	),
	
	'core_account/index/generic' => array(
			'title' => array('en_US' => 'Contacts', 'fr_FR' => 'Contacts'),
	),
	
	'core_account/search/generic' => array(
			'title' => array('en_US' => 'Contacts', 'fr_FR' => 'Contacts'),
			'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
			'properties' => array(
//					'place_id' => [],
					'status' => ['multiple' => true],
					'name' => [],
					'email' => [],
					'opening_date' => [],
					'callback_date' => [],
					'first_activation_date' => [],
					'next_meeting_date' => [],
					'basket' => [],
					'origine' => [],
					'property_1' => [],
					'property_2' => [],
			),
	),

	'core_account/event_account_search/generic' => array(
		'properties' => array(
			'status' => [],
			'n_fn' => [],
//			'groups' => [],
		),
	),
	
	'core_account/list/generic' => array(
			'properties' => array(
				'status' => array(
					'background-color' => array(
						'LightGreen' => ['status' => 'new'],
						'LightSalmon' => ['status' => 'interested'],
						'LightBlue' => ['status' => 'candidate'],
						'LightSalmon' => ['status' => 'answer'],
						'LightGrey' => ['status' => 'gone'],
					)
				),
				'name' => [],
				'email' => [],
				'opening_date' => [],
				'callback_date' => [],
				'first_activation_date' => [],
				'next_meeting_date' => [],
				'priority' => [],
				'origine' => [],
				'place_id' => [],
				'update_time' => [],
			),
	),

	'core_account/event_account_list/generic' => array(
		'properties' => array(
			'n_fn' => [],
		),
	),
	
	'core_account/detail/generic' => array(
			'title' => array('en_US' => 'Contact detail', 'fr_FR' => 'Détail du contact'),
			'displayAudit' => true,
			'tabs' => array(
					'contact_1' => array(
							'definition' => 'inline',
							'route' => 'account/update',
							'params' => array('type' => ''),
							'labels' => array('en_US' => 'Main contact', 'fr_FR' => 'Contact principal'),
					),
					'contact_2' => array(
							'definition' => 'inline',
							'route' => 'account/updateContact',
							'params' => array('type' => '', 'contactNumber' => 2),
							'labels' => array('en_US' => 'Invoicing', 'fr_FR' => 'Contact facturation'),
					),
			),
	),
	
	'core_account/update/generic' => array(
			'place_id' => ['mandatory' => false],
			'status' => ['mandatory' => true],
			'identifier' => ['readonly' => true],
			'name' => ['mandatory' => false],
			'basket' => ['mandatory' => false],
			'opening_date' => ['mandatory' => false],
			'callback_date' => ['mandatory' => false],
			'first_activation_date' => ['mandatory' => false],
			'next_meeting_date' => ['mandatory' => false],
			'origine' => ['mandatory' => false],
			'title_1' => [],
			'n_title' => ['mandatory' => false],
			'n_first' => ['mandatory' => false],
			'n_last' => ['mandatory' => true],
			'email' => ['mandatory' => false],
			'tel_work' => ['mandatory' => false],
			'tel_cell' => ['mandatory' => false],
			'adr_street' => ['mandatory' => false],
			'adr_zip' => ['mandatory' => false],
			'adr_city' => ['mandatory' => false],
			'title_2' => [],
			'groups' => ['readonly' => true],
			'property_1' => ['mandatory' => false],
			'property_2' => ['mandatory' => false],
			'default_means_of_payment' => array('mandatory' => false),
/*			'transfer_order_id' => array('mandatory' => false), 
			'transfer_order_date' => array('mandatory' => false), 
			'bank_identifier' => array('mandatory' => false),*/
			'comment_1' => ['mandatory' => false],
			'comment_2' => ['mandatory' => false],
			'contact_history' => ['mandatory' => false],
			'locale' => ['mandatory' => false],
	),
	
	'core_account/updateContact/generic' => array(
			'n_title' => array('mandatory' => false),
			'n_first' => array('mandatory' => false),
			'n_last' => array('mandatory' => false),
			'tel_work' => array('mandatory' => false),
			'tel_cell' => array('mandatory' => false),
			'email' => array('mandatory' => false),
			'adr_street' => array('mandatory' => false),
			'adr_extended' => array('mandatory' => false),
			'adr_zip' => array('mandatory' => false),
			'adr_post_office_box' => array('mandatory' => false),
			'adr_city' => array('mandatory' => false),
			'adr_state' => array('mandatory' => false),
			'adr_country' => array('mandatory' => false),
			'locale' => array('mandatory' => false),
	),
	
	'core_account/groupUpdate/generic' => array(
			'status' => array('mandatory' => true),
			'callback_date' => array('mandatory' => false),
			'first_activation_date' => array('mandatory' => false),
	),

	'core_account/export/generic' => array(
			'status' => [],
//			'place_id' => [],
//			'identifier' => [],
			'name' => [],
//			'basket' => [],
			'opening_date' => [],
			'callback_date' => [],
/*			'first_activation_date' => [],
			'next_meeting_date' => [],
			'next_meeting_confirmed' => [],
			'priority' => [],*/
			'origine' => [],
/*			'n_title' => [],
			'n_first' => [],
			'n_last' => [],*/
			'email' => [],
/*			'property_1' => [],
			'property_2' => [],
			'comment_1' => ['mandatory' => false],
			'comment_2' => ['mandatory' => false],
			'tel_work' => [],*/
			'tel_cell' => [],
			'adr_street' => [],
			'adr_zip' => [],
			'adr_city' => [],
/*	
			'n_title_2' => [],
			'n_first_2' => [],
			'n_last_2' => [],
			'email_2' => [],
			'tel_work_2' => [],
			'tel_cell_2' => [],*/

/*			'default_means_of_payment' => [],
			'transfer_order_id' => [],
			'transfer_order_date' => [],
			'bank_identifier' => [],*/
		
//			'contact_history' => [],
//			'notification_time' => [],
			'locale' => [],
			'json_property_1' => [],
	),

	'core_account/indexCard/generic' => array(
			'title' => array('en_US' => 'Client index card', 'fr_FR' => 'Fiche client'),
			'header' => array(
					'place_id' => null,
					'status' => null,
					'origine' => null,
			),
			'1st-column' => array(
					'title' => 'title_1',
					'rows' => array(
							'n_title' => array('mandatory' => true),
							'n_first' => array('mandatory' => true),
							'n_last' => array('mandatory' => true),
							'email' => array('mandatory' => false),
							'property_1' => array('mandatory' => false),
							'property_2' => array('mandatory' => false),
							'comment_1' => ['mandatory' => false],
							'comment_2' => ['mandatory' => false],
							'tel_work' => array('mandatory' => false),
							'tel_cell' => array('mandatory' => false),
							'adr_street' => array('mandatory' => false),
							'adr_extended' => array('mandatory' => false),
							'adr_post_office_box' => array('mandatory' => false),
							'adr_zip' => array('mandatory' => false),
							'adr_city' => array('mandatory' => false),
							'adr_state' => array('mandatory' => false),
							'adr_country' => array('mandatory' => false),
							'locale' => array('mandatory' => false),
					),
			),
			'2nd-column' => array(
					'title' => 'title_2',
					'rows' => array(
					),
			),
			'pdfDetailStyle' => '
<style>
table.note-report {
	font-size: 1em;
	border: 1px solid gray;
}
table.note-report th {
	color: #FFF;
	font-weight: bold;
	text-align: center;
	vertical-align: center;
	border: 1px solid gray;
	background-color: #006169;
}
				
table.note-report td {
	color: #666;
	border: 1px solid gray;
}
</style>
',
	),

	// Groups
	
	'core_account/group/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
			'active' => array('en_US' => 'Registered', 'fr_FR' => 'Inscrit'),
			'gone' => array('en_US' => 'Gone', 'fr_FR' => 'Parti'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
		'perspectives' => array(
			'group' => array('new', 'active', 'gone'),
		),
		'mandatory' => true,
	),
	
	'core_account/group/property/place_id' => array('definition' => 'core_account/generic/property/place_id'),
	'core_account/group/property/identifier' => array('definition' => 'core_account/generic/property/identifier'),
	'core_account/group/property/name' => array('definition' => 'core_account/generic/property/name'),

	'core_account/group' => array(
		'properties' => array(
			'status', 'place_id', 'identifier', 'name',
		),
		'acl' => array(
			'place_id' => array('application' => 'p-pit-admin', 'category' => 'place_id'),
		),
		'order' => 'name',
	),
	'core_account/index/generic' => array(
		'title' => array('en_US' => 'Groups', 'fr_FR' => 'Groupes'),
	),
	'core_account/search/group' => array(
		'title' => array('en_US' => 'Groups', 'fr_FR' => 'Groupes'),
		'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
		'properties' => array(
			'place_id' => [],
			'status' => [],
			'name' => [],
		),
	),
	
	'core_account/list/group' => array(
		'properties' => array(
			'status' => [],
			'name' => [],
			'place_id' => [],
		),
	),
	
	'core_account/detail/group' => array(
		'title' => array('en_US' => 'Detail', 'fr_FR' => 'Détail'),
		'displayAudit' => true,
		'tabs' => array(
		),
	),
	
	'core_account/update/group' => array(
		'place_id' => ['mandatory' => false],
		'status' => ['mandatory' => true],
		'name' => ['mandatory' => false],
	),
	
	'core_account/updateContact/group' => array(),
	
	'core_account/groupUpdate/group' => array(
		'status' => array('mandatory' => true),
	),
	
	'core_account/export/group' => array(
		'status' => [],
		'place_id' => [],
		'name' => [],
	),
	
	'core_account/indexCard/group' => array(
		'title' => array('en_US' => 'Group index card', 'fr_FR' => 'Fiche groupe'),
		'header' => array(
			'place_id' => null,
			'status' => null,
			'name' => null,
		),
		'1st-column' => array(
			'title' => null,
			'rows' => array(
			),
		),
		'2nd-column' => array(
			'title' => 'title_2',
			'rows' => array(
			),
		),
		'pdfDetailStyle' => '
<style>
table.note-report {
	font-size: 1em;
	border: 1px solid gray;
}
table.note-report th {
	color: #FFF;
	font-weight: bold;
	text-align: center;
	vertical-align: center;
	border: 1px solid gray;
	background-color: #006169;
}
	
table.note-report td {
	color: #666;
	border: 1px solid gray;
}
</style>
',
	),
	
	// group <-> account link

	'group_account/generic/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('en_US' => 'Active', 'fr_FR' => 'Actif'),
			'inactive' => array('en_US' => 'Inactive', 'fr_FR' => 'Inactif'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
	),

	'group_account/generic/property/place_id' => array(
		'definition' => 'inline',
		'type' => 'dynamic',
		'modalities' => array(
		),
		'labels' => array(
			'en_US' => 'Center',
			'fr_FR' => 'Centre',
		),
	),

	'group_account/generic/property/group_id' => array(
		'definition' => 'inline',
		'type' => 'dynamic',
		'modalities' => array(
		),
		'labels' => array(
			'en_US' => 'Group',
			'fr_FR' => 'Groupe',
		),
	),
	
	'group_account/generic/property/group_name' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Group name',
			'fr_FR' => 'Nom du groupe',
		),
	),

	'group_account/generic/property/account_id' => array(
		'definition' => 'inline',
		'type' => 'dynamic',
		'modalities' => array(
		),
		'labels' => array(
			'en_US' => 'Account',
			'fr_FR' => 'Compte',
		),
	),
	
	'group_account/generic/property/n_fn' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Formatted name',
			'fr_FR' => 'Nom formaté',
		),
	),
	
	'group_account/generic/property/email' => array(
		'definition' => 'inline',
		'type' => 'email',
		'labels' => array(
			'en_US' => 'Email',
			'fr_FR' => 'Email',
		),
	),
	
	'group_account/generic/property/tel_work' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Phone',
			'fr_FR' => 'Téléphone',
		),
	),
	
	'group_account/generic/property/tel_cell' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Cellular',
			'fr_FR' => 'Mobile',
		),
	),
	
	'group_account/view/generic' => array(
	),
	
	'group_account/search/generic' => array(
	),

	'group_account/list/generic' => array(
	),

	'group_account/update/generic' => array(
	),

	'group_account/export/generic' => array(
	),

	// Agent
	
	'core_account/agent/property/title_1' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'default' => 'IDENTIFICATION',
			'fr_FR' => 'IDENTIFICATION',
		),
	),
	'core_account/agent/property/title_2' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'default' => 'PLANIFICATION',
			'fr_FR' => 'PLANIFICATION',
		),
	),
	'core_account/agent/property/title_3' => array(
		'definition' => 'inline',
		'type' => 'title',
		'labels' => array(
			'en_US' => 'COMMENTS',
			'fr_FR' => 'COMMENTAIRES',
		),
	),
	
	'core_account/agent/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'active' => array('en_US' => 'Active', 'fr_FR' => 'Actif'),
			'inactive' => array('en_US' => 'Inactive', 'fr_FR' => 'Inactif'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
		'perspectives' => array(
			'account' => array('active', 'inactive'),
		),
	),
	
	'core_account/agent' => array(
		'properties' => array(
			'title_1', 'title_2', 'title_3', 'status', 'place_id', 'identifier', 'photo_link_id',
			'contact_1_id', 'contact_1_status', 'org', 'n_title', 'n_first', 'n_last', 'n_fn', 'email', 'tel_work', 'tel_cell',
			'adr_street', 'adr_extended', 'adr_post_office_box', 'adr_zip', 'adr_city', 'adr_state', 'adr_country',
			'availability', 'availability_begin', 'availability_end', 'availability_exceptions', 'availability_constraints',
			'contact_history',
		),
		'acl' => array(
			'place_id' => array('application' => 'p-pit-admin', 'category' => 'place_id'),
		),
		'order' => 'n_fn',
	),
	
	'core_account/index/agent' => array(
		'title' => array('en_US' => 'Team', 'fr_FR' => 'Équipe'),
	),
	'core_account/search/agent' => array(
		'title' => array('en_US' => 'Team', 'fr_FR' => 'Équipe'),
		'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
		'properties' => array(
			'place_id' => ['multiple' => true],
			'status' => ['multiple' => true],
			'n_fn' => [],
			'availability' => [],
		),
	),

	'core_account/list/agent' => array(
		'properties' => array(
			'status' => [],
			'n_fn' => [],
			'email' => [],
			'tel_work' => [],
			'tel_cell' => [],
			'place_id' => [],
		),
	),

	'core_account/event_account_list/agent' => array(
		'properties' => array(
			'n_fn' => [],
		),
	),
	
	'core_account/detail/agent' => array(
		'title' => array('en_US' => 'Detail', 'fr_FR' => 'Détail'),
		'displayAudit' => true,
		'tabs' => array(
			'contact_1' => array(
				'definition' => 'inline',
				'route' => 'account/update',
				'params' => array('type' => ''),
				'labels' => array('en_US' => 'Contact data', 'fr_FR' => 'Données de contact'),
			),
		),
	),

	'core_account/update/agent' => array(
		'place_id' => ['mandatory' => false],
		'status' => ['mandatory' => true],
		'title_1' => [],
		'n_title' => ['mandatory' => false],
		'n_first' => ['mandatory' => true],
		'n_last' => ['mandatory' => true],
		'email' => ['mandatory' => false],
		'tel_work' => ['mandatory' => false],
		'tel_cell' => ['mandatory' => false],
		'adr_street' => ['mandatory' => false],
		'adr_zip' => ['mandatory' => false],
		'adr_city' => ['mandatory' => false],
		'title_2' => ['mandatory' => false],
		'availability_begin' => ['mandatory' => false],
		'availability_end' => ['mandatory' => false],
		'availability_constraints' => ['mandatory' => false],
		'availability_exceptions' => ['mandatory' => false],
		'title_3' => ['mandatory' => false],
		'contact_history' => ['mandatory' => false],
	),

	'core_account/updateContact/agent' => array(
	),
	
	'core_account/groupUpdate/agent' => array(
		'status' => array('mandatory' => true),
	),
	
	'core_account/export/agent' => array(
		'status' => [],
		'place_id' => [],
		'n_title' => [],
		'n_first' => [],
		'n_last' => [],
		'email' => [],
		'tel_work' => [],
		'tel_cell' => [],
		'adr_street' => [],
		'adr_zip' => [],
		'adr_city' => [],
		'availability_begin' => [],
		'availability_end' => [],
		'availability_constraints' => [],
		'availability_exceptions' => [],
		'contact_history' => [],
	),
	
	'core_account/indexCard/agent' => array(
		'title' => array('en_US' => 'Agent index card', 'fr_FR' => 'Fiche agent'),
		'header' => array(
			'place_id' => null,
			'status' => null,
		),
		'1st-column' => array(
			'title' => 'title_1',
			'rows' => array(
				'n_title' => [],
				'n_first' => [],
				'n_last' => [],
				'email' => [],
				'tel_work' => [],
				'tel_cell' => [],
				'adr_street' => [],
				'adr_extended' => [],
				'adr_post_office_box' => [],
				'adr_zip' => [],
				'adr_city' => [],
				'adr_state' => [],
				'adr_country' => [],
			),
		),
		'2nd-column' => array(
			'title' => 'title_2',
			'rows' => array(
			),
		),
		'pdfDetailStyle' => '
<style>
table.note-report {
	font-size: 1em;
	border: 1px solid gray;
}
table.note-report th {
	color: #FFF;
	font-weight: bold;
	text-align: center;
	vertical-align: center;
	border: 1px solid gray;
	background-color: #006169;
}
	
table.note-report td {
	color: #666;
	border: 1px solid gray;
}
</style>
',
	),
	
	// Event

	'event/type' => array(
		'type' => 'select',
		'modalities' => array(
			'test_note' => array('en_US' => 'Global result', 'fr_FR' => 'Résultat global'),
			'test_detail' => array('en_US' => 'Detailed results', 'fr_FR' => 'Résultats détaillés'),
		),
		'default' => 'test_note',
		'labels' => array(
			'en_US' => 'Type',
			'fr_FR' => 'Type',
		),
	),
	
	'event/generic/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
				'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
				'completed' => array('en_US' => 'Completed', 'fr_FR' => 'Complété'),
				'scheduled' => array('en_US' => 'Scheduled', 'fr_FR' => 'Planifié'),
		),
		'labels' => array(
				'en_US' => 'Status',
				'fr_FR' => 'Statut',
		),
		'perspectives' => array(
			'generic' => array('', 'new', 'scheduled'),
		),
	),
	
	'event/generic/property/type' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
				'place' => array('en_US' => 'Place', 'fr_FR' => 'Etablissement'),
				'generic' => array('en_US' => 'Generic', 'fr_FR' => 'Générique'),
				'planning' => array('en_US' => 'Planning', 'fr_FR' => 'Planning'),
		),
		'labels' => array(
				'en_US' => 'Type',
				'fr_FR' => 'Type',
		),
	),
	
	'event/generic/property/identifier' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Identifier',
				'fr_FR' => 'Identifiant',
		),
	),
	
	'event/generic/property/place_id' => array(
		'definition' => 'inline',
		'type' => 'select',
		'labels' => array(
				'en_US' => 'Place',
				'fr_FR' => 'Etablissement',
		),
	),
	
	'event/generic/property/place_identifier' => array(
		'definition' => 'inline',
		'type' => 'select',
		'labels' => array(
				'en_US' => 'Place code',
				'fr_FR' => 'Code établissement',
		),
	),

	'event/generic/property/account_id' => array(
		'definition' => 'inline',
		'type' => 'select',
		'account_type' => 'generic',
		'labels' => array(
			'en_US' => 'Owner account',
			'fr_FR' => 'Compte propriétaire',
		),
	),

	'event/generic/property/groups' => array(
		'definition' => 'inline',
		'type' => 'multiselect',
		'account_type' => 'generic',
		'labels' => array(
			'en_US' => 'Group',
			'fr_FR' => 'Groupe',
		),
	),
	
	'event/generic/property/place_caption' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Place',
				'fr_FR' => 'Etablissement',
		),
	),

	'event/generic/property/n_fn' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Formatted name',
				'fr_FR' => 'Nom formaté',
		),
	),

	'event/generic/property/n_first' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'First name',
			'fr_FR' => 'Prénom',
		),
	),

	'event/generic/property/n_last' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Last name',
			'fr_FR' => 'Nom usuel',
		),
	),

	'event/generic/property/email' => array(
		'definition' => 'inline',
		'type' => 'email',
		'labels' => array(
			'en_US' => 'Email',
			'fr_FR' => 'Email',
		),
	),

	'event/generic/property/tel_work' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Phone',
			'fr_FR' => 'Téléphone',
		),
	),
	
	'event/generic/property/tel_cell' => array(
		'definition' => 'inline',
		'type' => 'phone',
		'labels' => array(
			'en_US' => 'Cellular',
			'fr_FR' => 'Mobile',
		),
	),
	
	'event/generic/property/locale' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'en_US' => 'en_US',
			'fr_FR' => 'fr_FR',
		),
		'labels' => array(
			'default' => 'Locale',
			'fr_FR' => 'Lieu',
		),
	),
	
	'event/generic/property/category' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Category',
				'fr_FR' => 'Catégorie',
		),
	),
	
	'event/generic/property/subcategory' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Subcategory',
				'fr_FR' => 'Sous-catégorie',
		),
	),
	
	'event/generic/property/caption' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Caption',
				'fr_FR' => 'Libellé',
		),
	),

	'event/generic/property/description' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Description',
				'fr_FR' => 'Description',
		),
	),

	'event/generic/property/begin_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
				'en_US' => 'Begin date',
				'fr_FR' => 'Date début',
		),
	),
	
	'event/generic/property/end_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
				'en_US' => 'End date',
				'fr_FR' => 'Date fin',
		),
	),
	
	'event/generic/property/day_of_week' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
				'1' => array('en_US' => 'Monday', 'fr_FR' => 'Lundi'),
				'2' => array('en_US' => 'Tuesday', 'fr_FR' => 'Mardi'),
				'3' => array('en_US' => 'Wednesday', 'fr_FR' => 'Mercredi'),
				'4' => array('en_US' => 'Thursday', 'fr_FR' => 'Jeudi'),
				'5' => array('en_US' => 'Friday', 'fr_FR' => 'Vendredi'),
				'6' => array('en_US' => 'Saturday', 'fr_FR' => 'Samedi'),
				'7' => array('en_US' => 'Sunday', 'fr_FR' => 'Dimanche'),
		),
		'labels' => array(
				'en_US' => 'Day of week',
				'fr_FR' => 'Jour de la semaine',
		),
	),
	
	'event/generic/property/day_of_month' => array(
		'definition' => 'inline',
		'type' => 'number',
		'minValue' => 1,
		'maxValue' => 31,
		'labels' => array(
				'en_US' => 'Day of month',
				'fr_FR' => 'Jour du mois',
		),
	),
	
	'event/generic/property/exception_1' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
				'en_US' => 'Exception 1',
				'fr_FR' => 'Exception 1',
		),
	),
	
	'event/generic/property/exception_2' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
				'en_US' => 'Exception 2',
				'fr_FR' => 'Exception 2',
		),
	),
	
	'event/generic/property/exception_3' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
				'en_US' => 'Exception 3',
				'fr_FR' => 'Exception 3',
		),
	),
	
	'event/generic/property/exception_4' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
				'en_US' => 'Exception 4',
				'fr_FR' => 'Exception 4',
		),
	),
	
	'event/generic/property/begin_time' => array(
		'definition' => 'inline',
		'type' => 'time',
		'labels' => array(
				'en_US' => 'Time',
				'fr_FR' => 'Heure',
		),
	),
	
	'event/generic/property/end_time' => array(
		'definition' => 'inline',
		'type' => 'time',
		'labels' => array(
				'en_US' => 'End time',
				'fr_FR' => 'Heure de fin',
		),
	),
	
	'event/generic/property/time_zone' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Time zone',
				'fr_FR' => 'Fuseau horaire',
		),
	),
	
	'event/generic/property/location' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Location',
				'fr_FR' => 'Lieu',
		),
	),
	
	'event/generic/property/latitude' => array(
		'definition' => 'inline',
		'type' => 'number',
		'minValue' => -90,
		'maxValue' => 90,
		'labels' => array(
				'en_US' => 'Latitude',
				'fr_FR' => 'Latitude',
		),
	),
	
	'event/generic/property/longitude' => array(
		'definition' => 'inline',
		'type' => 'number',
		'minValue' => 0,
		'maxValue' => 359.5999,
		'labels' => array(
				'en_US' => 'Longitude',
				'fr_FR' => 'Longitude',
		),
	),

	'event/generic/property/matched_accounts' => array(
		'definition' => 'inline',
		'type' => 'multiselect',
		'account_type' => 'generic',
		'labels' => array(
			'en_US' => 'Matched accounts',
			'fr_FR' => 'Comptes connectés',
		),
	),

	'event/generic/property/matching_log' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Matching log',
			'fr_FR' => 'Historique de matching',
		),
	),

	'event/generic/property/rewards' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Rewards',
			'fr_FR' => 'Rewards',
		),
	),
	
	'event/generic/property/feedbacks' => array(
		'definition' => 'inline',
		'type' => 'log',
		'items' => array(
			'update_time' => ['default' => 'Date and time', 'fr_FR' => 'Date et heure'],
			'account_id' => ['default' => 'interlocutor', 'fr_FR' => 'Interlocuteur'],
			'private_comment' => ['default' => 'Private - Comment', 'fr_FR' => 'Private - Commentaire'],
			'platform_benefit' => ['default' => 'Platform - Benefit', 'fr_FR' => 'Plateforme - Bénéfice'],
			'platform_satisfaction' => ['default' => 'Platform - Satisfaction', 'fr_FR' => 'Plateforme - Satisfaction'],
			'platform_accessibility' => ['default' => 'Platform - Accessibility', 'fr_FR' => 'Plateforme - Accessibilité'],
			'platform_comment' => ['default' => 'Platform - Comment', 'fr_FR' => 'Plateforme - Commentaire'],
			'community_comment' => ['default' => 'Community - Comment', 'fr_FR' => 'Communauté - Commentaire'],
		),
		'labels' => array(
			'en_US' => 'Feedbacks',
			'fr_FR' => 'Feedbacks',
		),
	),
	
	'event/generic/property/value' => array(
		'definition' => 'inline',
		'type' => 'number',
		'minValue' => 0,
		'maxValue' => 1000000000,
		'labels' => array(
				'en_US' => 'Value',
				'fr_FR' => 'Valeur',
		),
	),
	
	'event/generic/property/comments' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
				'en_US' => 'Comments',
				'fr_FR' => 'Commentaires',
		),
	),
	
	'event/generic/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'A',
				'fr_FR' => 'A',
		),
	),
	
	'event/generic/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'B',
				'fr_FR' => 'B',
		),
	),
	
	'event/generic/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'C',
				'fr_FR' => 'C',
		),
	),
	
	'event/generic/property/property_4' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'D',
				'fr_FR' => 'D',
		),
	),
	
	'event/generic/property/property_5' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'E',
				'fr_FR' => 'E',
		),
	),
	
	'event/generic/property/property_6' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'F',
				'fr_FR' => 'F',
		),
	),
	
	'event/generic/property/property_7' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'G',
				'fr_FR' => 'G',
		),
	),
	
	'event/generic/property/property_8' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'H',
				'fr_FR' => 'H',
		),
	),
	
	'event/generic/property/property_9' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'I',
				'fr_FR' => 'I',
		),
	),
	
	'event/generic/property/property_10' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'J',
				'fr_FR' => 'J',
		),
	),
	
	'event/generic/property/property_11' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'K',
				'fr_FR' => 'K',
		),
	),
	
	'event/generic/property/property_12' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'L',
				'fr_FR' => 'L',
		),
	),
	
	'event/generic/property/property_13' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'M',
				'fr_FR' => 'M',
		),
	),
	
	'event/generic/property/property_14' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'N',
				'fr_FR' => 'N',
		),
	),
	
	'event/generic/property/property_15' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'O',
				'fr_FR' => 'O',
		),
	),
	
	'event/generic/property/property_16' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'P',
				'fr_FR' => 'P',
		),
	),
	
	'event/generic/property/property_17' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Q',
				'fr_FR' => 'Q',
		),
	),
	
	'event/generic/property/property_18' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'R',
				'fr_FR' => 'R',
		),
	),

	'event/generic/property/property_19' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'S',
				'fr_FR' => 'S',
		),
	),
	
	'event/generic/property/property_20' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'T',
				'fr_FR' => 'T',
		),
	),
	
	'event/generic/property/property_21' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'U',
				'fr_FR' => 'U',
		),
	),
	
	'event/generic/property/property_22' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'V',
				'fr_FR' => 'V',
		),
	),
	
	'event/generic/property/property_23' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'W',
				'fr_FR' => 'W',
		),
	),
	
	'event/generic/property/property_24' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'X',
				'fr_FR' => 'X',
		),
	),
	
	'event/generic/property/property_25' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Y',
				'fr_FR' => 'Y',
		),
	),
	
	'event/generic/property/property_26' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'Z',
				'fr_FR' => 'Z',
		),
	),
	
	'event/generic/property/property_27' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'AA',
				'fr_FR' => 'AA',
		),
	),

	'event/generic/property/property_28' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'AB',
				'fr_FR' => 'AB',
		),
	),
	
	'event/generic/property/property_29' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'AC',
				'fr_FR' => 'AC',
		),
	),
	
	'event/generic/property/property_30' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
				'en_US' => 'AD',
				'fr_FR' => 'AD',
		),
	),

	'event/generic/property/account_status' => array(
		'definition' => 'core_account/generic/property/status',
		'labels' => array(
			'default' => 'Account status',
			'fr_FR' => 'Statut du compte',
		),
	),
	
	'event/generic/property/account_property_1' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AE',
		),
	),

	'event/generic/property/account_property_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AF',
		),
	),

	'event/generic/property/account_property_3' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AG',
		),
	),

	'event/generic/property/account_property_4' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AH',
		),
	),

	'event/generic/property/account_property_5' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AE',
		),
	),

	'event/generic/property/account_property_6' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AI',
		),
	),

	'event/generic/property/account_property_7' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AJ',
		),
	),

	'event/generic/property/account_property_8' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AK',
		),
	),

	'event/generic/property/account_property_9' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AL',
		),
	),

	'event/generic/property/account_property_10' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AM',
		),
	),

	'event/generic/property/account_property_11' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AN',
		),
	),

	'event/generic/property/account_property_12' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AO',
		),
	),

	'event/generic/property/account_property_13' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AP',
		),
	),

	'event/generic/property/account_property_14' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AQ',
		),
	),

	'event/generic/property/account_property_15' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AR',
		),
	),

	'event/generic/property/account_property_16' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'AS',
		),
	),
	
	'event/generic/property/update_time' => array(
		'definition' => 'inline',
		'type' => 'datetime',
		'labels' => array(
			'default' => 'AE',
		),
	),
	
	'event/generic' => array(
		'statuses' => array(),
		'dimensions' => array(),
		'indicators' => array(),
		'properties' => array(
			'status', 'type', 'place_id', 'place_caption', 'account_id', 'groups', 'n_fn', 'n_first', 'n_last', 'email', 'category', 'subcategory', 'identifier', 'caption', 'description',
			'begin_date', 'end_date', 'day_of_week', 'day_of_month', 'exception_1', 'exception_2', 'exception_3', 'exception_4', 'begin_time', 'end_time', 'time_zone', 'location', 'latitude', 'longitude', 
			'matched_accounts', 'matching_log', 'feedbacks', 'value', 'comments',
			'property_1', 'property_2', 'property_3', 'property_4', 'property_5', 'property_6', 'property_7', 'property_8', 'property_9', 'property_10',
			'property_11', 'property_12', 'property_13', 'property_14', 'property_15', 'property_16', 'property_17', 'property_18', 'property_19', 'property_20',
			'property_21', 'property_22', 'property_23', 'property_24', 'property_25', 'property_26', 'property_27', 'property_28', 'property_29', 'property_30',
			'account_status', 'account_property_1', 'account_property_2', 'account_property_3', 'account_property_4', 'account_property_5', 'account_property_6', 'account_property_7', 'account_property_8', 'account_property_9', 'account_property_10', 'account_property_11', 'account_property_12', 'account_property_13', 'account_property_14', 'account_property_15', 'account_property_16', 
			'update_time'
		),
		'options' => [],
	),
	
	'event/index/generic' => array(
		'title' => array('en_US' => 'P-Pit SynApps', 'fr_FR' => 'P-Pit SynApps'),
	),
	
	'event/search/generic' => array(
		'title' => array('default' => 'Events', 'fr_FR' => 'Evènements'),
		'todoTitle' => array('default' => 'recent', 'fr_FR' => 'récents'),
		'searchTitle' => array('default' => 'search', 'fr_FR' => 'recherche'),
		'properties' => array(
			'place_id' => [],
			'account_id' => [],
			'identifier' => [],
			'value' => [],
			'day_of_week' => [],
			'begin_date' => [],
			'begin_time' => [],
			'update_time' => [],
		),
	),
	
	'event/list/generic' => array(
		'place_id' => [],
		'n_fn' => [],
		'category' => [],
		'identifier' => [],
		'caption' => [],
		'value' => [],
		'update_time' => [],
		'property_1' => [],
		'property_2' => [],
		'property_3' => [],
		'property_4' => [],
		'property_5' => [],
		'property_6' => [],
		'property_7' => [],
		'property_8' => [],
		'property_9' => [],
		'property_10' => [],
		'property_11' => [],
		'property_12' => [],
		'property_13' => [],
		'property_14' => [],
		'property_15' => [],
		'property_16' => [],
		'property_17' => [],
		'property_18' => [],
		'property_19' => [],
		'property_20' => [],
		'property_21' => [],
		'property_22' => [],
		'property_23' => [],
		'property_24' => [],
		'property_25' => [],
		'property_26' => [],
		'begin_date' => [],
		'end_date' => [],
		'day_of_week' => [],
		'day_of_month' => [],
		'begin_time' => [],
		'end_time' => [],
		'time_zone' => [],
		'location' => [],
		'latitude' => [],
		'longitude' => [],
		'comments' => [],
	),
	
	'event/detail/generic' => array(
		'title' => array('default' => 'Event detail', 'fr_FR' => 'Détail de l\'évènement'),
		'displayAudit' => true,
	),
	
	'event/format/generic' => [
		'mask' => '%s (%s)',
		'params' => [
			'caption' => [],
			'location' => [],
		],
	],
	
	'event/update/generic' => array(
		'status' => ['mandatory' => true],
		'type' => ['mandatory' => true],
		'place_id' => ['mandatory' => false],
		'account_id' => ['mandatory' => false],
		'category' => ['mandatory' => false],
		'subcategory' => ['mandatory' => false],
		'identifier' => ['mandatory' => true],
		'caption' => ['mandatory' => false],
		'description' => ['mandatory' => false],
		'day_of_week' => array('mandatory' => false),
		'begin_date' => array('mandatory' => false),
		'begin_time' => array('mandatory' => false),
		'end_date' => array('mandatory' => false),
		'end_time' => array('mandatory' => false),
		'exception_1' => array('mandatory' => false),
		'exception_2' => array('mandatory' => false),
		'exception_3' => array('mandatory' => false),
		'exception_4' => array('mandatory' => false),
		'value' => ['mandatory' => false],
		'property_1' => ['mandatory' => false],
		'property_2' => ['mandatory' => false],
		'property_3' => ['mandatory' => false],
		'property_4' => ['mandatory' => false],
		'property_5' => ['mandatory' => false],
		'property_6' => ['mandatory' => false],
		'property_7' => ['mandatory' => false],
		'property_8' => ['mandatory' => false],
		'property_9' => ['mandatory' => false],
		'property_10' => ['mandatory' => false],
		'property_11' => ['mandatory' => false],
		'property_12' => ['mandatory' => false],
		'property_13' => ['mandatory' => false],
		'property_14' => ['mandatory' => false],
		'property_15' => ['mandatory' => false],
		'property_16' => ['mandatory' => false],
		'property_17' => ['mandatory' => false],
		'property_18' => ['mandatory' => false],
		'property_19' => ['mandatory' => false],
		'property_20' => ['mandatory' => false],
		'property_21' => ['mandatory' => false],
		'property_22' => ['mandatory' => false],
		'property_23' => ['mandatory' => false],
		'property_24' => ['mandatory' => false],
		'property_25' => ['mandatory' => false],
		'property_26' => ['mandatory' => false],
	),
	
	'event/export/generic' => array(
		'status' => 'A',
		'type' => 'B',
		'place_id' => 'C',
		'n_fn' => 'D',
		'category' => 'E',
		'subcategory' => 'F',
		'place_caption' => 'G',
		'identifier' => 'H',
		'caption' => 'I',
		'description' => 'J',
		'value' => 'K',
		'property_1' => 'L',
		'property_2' => 'M',
		'property_3' => 'N',
		'property_4' => 'O',
		'property_5' => 'P',
		'property_6' => 'Q',
		'property_7' => 'R',
		'property_8' => 'S',
		'property_9' => 'T',
		'property_10' => 'U',
		'property_11' => 'V',
		'property_12' => 'W',
		'property_13' => 'X',
		'property_14' => 'Y',
		'property_15' => 'Z',
		'property_16' => 'AA',
		'property_17' => 'AB',
		'property_18' => 'AC',
		'property_19' => 'AD',
		'property_20' => 'AE',
		'property_21' => 'AF',
		'property_22' => 'AG',
		'property_23' => 'AH',
		'property_24' => 'AI',
		'property_25' => 'AJ',
		'property_26' => 'AK',
	),
	
	// Planning

	'planningMap/types' => ['generic'],
	
	'planningMap/generic' => [
		'periods' => [
/*			[
				'begin' => '2018-09-15',
				'end' => '2019-06-30',
				'exceptions' => [['begin_date' => '2018-12-20', 'end_date' => '2019-01-06']],
				'slots' => [
					1 => [['begin_time' => '09:00', 'end' => '13:00'], ['begin_time' => '14:00', 'end' => '18:00']],
					2 => [['begin_time' => '09:00', 'end' => '13:00'], ['begin_time' => '14:00', 'end' => '18:00']],
					3 => [['begin_time' => '09:00', 'end' => '13:00'], ['begin_time' => '14:00', 'end' => '17:00']],
					4 => [['begin_time' => '09:00', 'end' => '13:00'], ['begin_time' => '14:00', 'end' => '18:00']],
					5 => [['begin_time' => '09:00', 'end' => '13:00'], ['begin_time' => '14:00', 'end' => '16:00']],
				],
			],*/
		],
		'labels' => ['default' => 'Generic', 'fr_FR' => 'Générique'],
	],

	'event/planning/property/category' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'business' => ['default' => 'Business', 'fr_FR' => 'Commercial'],
			'team' => ['default' => 'Individual', 'fr_FR' => 'Individuel'],
		),
		'labels' => array(
			'en_US' => 'Category',
			'fr_FR' => 'Catégorie',
		),
	),
	
	'event/planning/property/account_id' => array(
		'definition' => 'inline',
		'type' => 'select',
		'account_type' => 'agent',
		'labels' => array(
			'en_US' => 'Responsible',
			'fr_FR' => 'Responsable',
		),
	),

	'event/planning/property/begin_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'Date',
			'fr_FR' => 'Date',
		),
	),
	
	'event/planning' => array(
		'statuses' => array(),
		'dimensions' => array(),
		'indicators' => array(),
		'properties' => array(
			'status', 'type', 'place_id', 'place_caption', 'account_id', 'category', 'subcategory', 'identifier', 'caption', 'description',
			'begin_date', 'end_date', 'day_of_week', 'day_of_month', 'exception_1', 'exception_2', 'exception_3', 'exception_4', 'begin_time', 'end_time', 'time_zone', 'location', 'latitude', 'longitude',
			'value', 'comments',
			'update_time',
		),
		'options' => ['calendar' => true, 'account_type' => 'agent'],
	),
	
	'event/index/planning' => array(
		'title' => array('default' => 'Planning', 'fr_FR' => 'Planning'),
	),
	
	'event/search/planning' => array(
		'title' => array('default' => 'Planning', 'fr_FR' => 'Planning'),
		'todoTitle' => array('default' => 'recent', 'fr_FR' => 'récents'),
		'searchTitle' => array('default' => 'search', 'fr_FR' => 'recherche'),
		'properties' => array(
			'place_id' => [],
			'category' => [],
			'account_id' => [],
			'caption' => [],
			'day_of_week' => [],
			'begin_date' => [],
			'begin_time' => [],
			'location' => [],
		),
	),
	
	'event/list/planning' => array(
		'place_id' => [],
		'account_id' => [],
		'category' => [],
		'caption' => [],
		'begin_date' => [],
//		'end_date' => [],
//		'day_of_week' => [],
//		'day_of_month' => [],
		'begin_time' => [],
//		'end_time' => [],
		'location' => [],
		'update_time' => [],
	),
	
	'event/detail/planning' => array(
		'title' => array('default' => 'Event detail', 'fr_FR' => 'Détail de l\'évènement'),
		'displayAudit' => true,
	),
	
	'event/update/planning' => array(
		'status' => ['mandatory' => true],
		'place_id' => [],
		'account_id' => [],
		'category' => [],
		'caption' => [],
		'description' => [],
		'begin_date' => array('mandatory' => false),
		'begin_time' => array('mandatory' => false),
		'end_time' => array('mandatory' => false),
		'location' => array('mandatory' => false),
	),
	
	'event/export/planning' => array(
		'status' => 'A',
		'type' => 'B',
		'place_id' => 'C',
		'account_id' => 'D',
		'category' => 'E',
		'caption' => 'F',
		'description' => 'G',
		'begin_date' => 'H',
		'begin_time' => 'I',
		'location' => 'J',
	),

	// Planification (event typed "calendar")

	'event/calendar/property/account_id' => array(
		'definition' => 'inline',
		'type' => 'select',
		'account_type' => 'teacher',
		'labels' => array(
			'en_US' => 'Coach',
			'fr_FR' => 'Animateur',
		),
	),
	
	'event/calendar' => array(
		'statuses' => array(),
		'dimensions' => array(),
		'indicators' => array(),
		'properties' => array(
			'status', 'type', 'place_id', 'place_caption', 'account_id', 'groups', 'category', 'subcategory', 'identifier', 'caption', 'description',
			'begin_date', 'end_date', 'day_of_week', 'day_of_month', 'exception_1', 'exception_2', 'exception_3', 'exception_4', 'begin_time', 'end_time', 'time_zone', 'location', 'latitude', 'longitude',
			'value', 'comments',
			'update_time',
		),
		'options' => ['calendar' => true, 'account_type' => 'teacher'],
	),

	'event/format/calendar' => array(
		'mask' => '%s%s - %s (%s)',
		'params' => [
			'caption' => [],
			'property_3' => [],
			'n_fn' => [],
			'location' => [],
		],
	),
/*	
	'event/update/calendar' => array(
		'status' => ['mandatory' => true],
//		'category' => ['mandatory' => true],
		'groups' => ['mandatory' => true],
		'place_id' => [],
		'account_id' => [],
		'caption' => [],
		'description' => [],
		'day_of_week' => array('mandatory' => false),
		'begin_date' => array('mandatory' => false),
		'end_date' => array('mandatory' => false),
		'begin_time' => array('mandatory' => false),
		'end_time' => array('mandatory' => false),
		'location' => array('mandatory' => false),
	),*/
	
	
	// Emails
	
	'event/email/property/description' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'HTML',
			'fr_FR' => 'HTML',
		),
	),
	
	'event/email/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'to',
			'fr_FR' => 'A',
		),
	),
	
	'event/email/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Cc',
			'fr_FR' => 'Cc',
		),
	),
	
	'event/email/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Cci',
			'fr_FR' => 'Cci',
		),
	),
	
	'event/email/property/property_4' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Subject',
			'fr_FR' => 'Objet',
		),
	),
	
	'event/email/property/property_5' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'From',
			'fr_FR' => 'De',
		),
	),
	
	'event/email' => array(
		'statuses' => array(),
		'dimensions' => array(),
		'indicators' => array(),
		'properties' => array(
			'status', 'type', 'place_id', 'place_caption', 'n_fn', 'n_first', 'n_last', 'email', 'category', 'subcategory', 'identifier', 'caption', 'description',
			'begin_date', 'end_date', 'day_of_week', 'day_of_month', 'exception_1', 'exception_2', 'exception_3', 'exception_4', 'begin_time', 'end_time', 'time_zone', 'location', 'latitude', 'longitude',
			'value', 'comments',
			'property_1', 'property_2', 'property_3', 'property_4', 'property_5', 'property_6', 'property_7', 'property_8', 'property_9', 'property_10',
			'property_11', 'property_12', 'property_13', 'property_14', 'property_15', 'property_16', 'property_17', 'property_18', 'property_19', 'property_20',
			'property_21', 'property_22', 'property_23', 'property_24', 'property_25', 'property_26', 'property_27', 'property_28', 'property_29', 'property_30',
			'update_time'
		),
		'options' => ['readonly' => true],
	),
	
	'event/search/email' => array(
		'title' => array('default' => 'Events', 'fr_FR' => 'Emails'),
		'todoTitle' => array('default' => 'recent', 'fr_FR' => 'récents'),
		'searchTitle' => array('default' => 'search', 'fr_FR' => 'recherche'),
		'properties' => array(
			'place_id' => [],
			'n_fn' => [],
			'property_1' => [],
			'property_4' => [],
			'category' => [],
			'update_time' => [],
		),
	),

	'event/list/email' => array(
		'place_id' => [],
		'n_fn' => [],
		'category' => [],
		'property_1' => [],
		'property_2' => [],
		'property_3' => [],
		'property_4' => [],
		'property_5' => [],
		'update_time' => [],
	),
	
	'event/update/email' => array(
		'place_id' => [],
		'n_fn' => [],
		'category' => [],
		'description' => [],
		'property_1' => [],
		'property_2' => [],
		'property_3' => [],
		'property_4' => [],
		'property_5' => [],
	),
	
	// Interaction
/*
	'interaction/type/app' => array(
			'controller' => '\Model\App::controlInteraction',
			'processor' => '\Model\App::processInteraction',
	),

	'interaction/type/document' => array(
			'controller' => '\Model\Document::controlInteraction',
			'processor' => '\Model\Document::processInteraction',
	),

	'interaction/type/config' => array(
			'controller' => '\Model\Config::controlInteraction',
			'processor' => '\Model\Config::processInteraction',
	),*/

	'interaction/type/event' => array(
		'controller' => '\PpitCore\Model\Event::controlInteraction',
		'processor' => '\PpitCore\Model\Event::processInteraction',
	),
	
	'interaction/type' => array(
			'type' => 'select',
			'modalities' => array(
					'application' => array('en_US' => 'Apps', 'fr_FR' => 'Apps'),
					'config' => array('en_US' => 'Config', 'fr_FR' => 'Config'),
					'event' => array('en_US' => 'Events', 'fr_FR' => 'Evènements'),
					'web_service' => array('en_US' => 'Web service', 'fr_FR' => 'Web service'),
			),
			'labels' => array(
					'en_US' => 'Type',
					'fr_FR' => 'Type',
			),
	),
		
	'interaction' => array(
			'statuses' => array(),
			'properties' => array(
					'status' => array(
							'type' => 'select',
							'modalities' => array(
									'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
									'processed' => array('en_US' => 'Processed', 'fr_FR' => 'Exécuté'),
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'type' => array('type' => 'specific', 'definition' => 'interaction/type'),
					'category' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Category',
									'fr_FR' => 'Catégorie',
							),
					),
					'format' => array(
							'type' => 'select',
							'modalities' => array(
									'application/xml' => array('en_US' => 'XML', 'fr_FR' => 'XML'),
									'application/json' => array('en_US' => 'JSON', 'fr_FR' => 'JSON'),
									'text/csv' => array('en_US' => 'CSV', 'fr_FR' => 'CSV'),
									'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => array('en_US' => 'Excel', 'fr_FR' => 'Excel'),
							),
							'labels' => array(
									'en_US' => 'Format',
									'fr_FR' => 'Format',
							),
					),
					'direction' => array(
							'type' => 'select',
							'modalities' => array(
									'input' => array('en_US' => 'Input', 'fr_FR' => 'Entrant'),
									'output' => array('en_US' => 'Output', 'fr_FR' => 'Sortant'),
							),
							'labels' => array(
									'en_US' => 'Direction',
									'fr_FR' => 'Sens',
							),
					),
					'place_caption' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Place',
									'fr_FR' => 'Etablissement',
							),
					),
					'reference' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Reference',
									'fr_FR' => 'Référence',
							),
					),
					'content' => array(
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Content',
									'fr_FR' => 'Contenu',
							),
					),
					'file' => array(
							'type' => 'file',
							'labels' => array(
									'en_US' => 'Upload a file',
									'fr_FR' => 'Télécharger un fichier',
							),
					),
					'http_status' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Return code',
									'fr_FR' => 'Code retour',
							),
					),
					'property_1' => array('type' => 'specific', 'definition' => 'interaction/property_1'),
					'property_2' => array('type' => 'specific', 'definition' => 'interaction/property_2'),
					'property_3' => array('type' => 'specific', 'definition' => 'interaction/property_3'),
					'property_4' => array('type' => 'specific', 'definition' => 'interaction/property_4'),
					'property_5' => array('type' => 'specific', 'definition' => 'interaction/property_5'),
					'property_6' => array('type' => 'specific', 'definition' => 'interaction/property_6'),
					'property_7' => array('type' => 'specific', 'definition' => 'interaction/property_7'),
					'property_8' => array('type' => 'specific', 'definition' => 'interaction/property_8'),
					'property_9' => array('type' => 'specific', 'definition' => 'interaction/property_9'),
					'property_10' => array('type' => 'specific', 'definition' => 'interaction/property_10'),
					'property_11' => array('type' => 'specific', 'definition' => 'interaction/property_11'),
					'property_12' => array('type' => 'specific', 'definition' => 'interaction/property_12'),
					'property_13' => array('type' => 'specific', 'definition' => 'interaction/property_13'),
					'property_14' => array('type' => 'specific', 'definition' => 'interaction/property_14'),
					'property_15' => array('type' => 'specific', 'definition' => 'interaction/property_15'),
					'property_16' => array('type' => 'specific', 'definition' => 'interaction/property_16'),
					'property_17' => array('type' => 'specific', 'definition' => 'interaction/property_17'),
					'property_18' => array('type' => 'specific', 'definition' => 'interaction/property_18'),
					'property_19' => array('type' => 'specific', 'definition' => 'interaction/property_19'),
					'property_20' => array('type' => 'specific', 'definition' => 'interaction/property_20'),
					'property_21' => array('type' => 'specific', 'definition' => 'interaction/property_21'),
					'property_22' => array('type' => 'specific', 'definition' => 'interaction/property_22'),
					'property_23' => array('type' => 'specific', 'definition' => 'interaction/property_23'),
					'property_24' => array('type' => 'specific', 'definition' => 'interaction/property_24'),
					'property_25' => array('type' => 'specific', 'definition' => 'interaction/property_25'),
					'property_26' => array('type' => 'specific', 'definition' => 'interaction/property_26'),
					'property_27' => array('type' => 'specific', 'definition' => 'interaction/property_27'),
					'property_28' => array('type' => 'specific', 'definition' => 'interaction/property_28'),
					'property_29' => array('type' => 'specific', 'definition' => 'interaction/property_29'),
					'property_30' => array('type' => 'specific', 'definition' => 'interaction/property_30'),
					'update_time' => array(
							'type' => 'datetime',
							'labels' => array(
									'en_US' => 'Update time',
									'fr_FR' => 'Heure de mise à jour',
							),
					),
			),
	),
	
	'interaction/index' => array(
			'title' => array('en_US' => 'P-Pit SynApps', 'fr_FR' => 'P-Pit SynApps'),
	),
	
	'interaction/search' => array(
			'title' => array('en_US' => 'Interactions', 'fr_FR' => 'Interactions'),
			'todoTitle' => array('en_US' => 'recent', 'fr_FR' => 'récentes'),
			'searchTitle' => array('en_US' => 'search', 'fr_FR' => 'recherche'),
			'main' => array(
					'status' => 'value',
					'type' => 'value',
					'category' => 'contains',
					'format' => 'value',
					'direction' => 'value',
					'place_caption' => 'contains',
					'reference' => 'contains',
					'update_time' => 'range',
			),
	),
	
	'interaction/list' => array(
			'type' => 'select',
			'category' => 'text',
			'update_time' => 'time',
	),
	
	'interaction/detail' => array(
			'title' => array('en_US' => 'Interaction detail', 'fr_FR' => 'Détail de l\'interaction'),
			'displayAudit' => true,
	),
	
	'interaction/update' => array(
			'status' => array('mandatory' => true),
			'type' => array('mandatory' => true),
			'category' => array('mandatory' => false),
			'format' => array('mandatory' => true),
			'direction' => array('mandatory' => false),
			'reference' => array('mandatory' => false),
			'content' => array('mandatory' => false),
			'file' => array('mandatory' => false),
			'http_status' => array('mandatory' => false),
	),

		'request/type' => array(
				'type' => 'select',
				'modalities' => array(
						'generic' => array('en_US' => 'Generic request', 'fr_FR' => 'Demande générique'),
				),
				'labels' => array(
						'en_US' => 'Type',
						'fr_FR' => 'Type',
				),
		),

		'request/generic/status' => array(
				'type' => 'select',
				'modalities' => array(
						'to-execute' => array('en_US' => 'To execute', 'fr_FR' => 'A exécuter'),
						'executed' => array('en_US' => 'Executed', 'fr_FR' => 'Exécuté'),
						'closed' => array('en_US' => 'Closed', 'fr_FR' => 'Clôturé'),
						'canceled' => array('en_US' => 'Canceled', 'fr_FR' => 'Annulé'),
				),
				'labels' => array(
						'en_US' => 'Status',
						'fr_FR' => 'Statut',
				),
		),
		
		'request/generic/caption' => array(
				'type' => 'input',
				'labels' => array(
						'en_US' => 'Title',
						'fr_FR' => 'Titre',
				),
		),
		
		'request/generic/category' => array(
				'type' => 'select',
				'modalities' => array(
						'general' => array('en_US' => 'General request', 'fr_FR' => 'Demande générale'),
						'place-creation' => array('en_US' => 'Place creation', 'fr_FR' => 'Création de centre/établissement'),
						'place-logo' => array('en_US' => 'Place logo', 'fr_FR' => 'Logo centre/établissement'),
						'place-footage' => array('en_US' => 'Place footer notice', 'fr_FR' => 'Mention bas de page centre/établissement'),
						'place-deletion' => array('en_US' => 'Place deletion', 'fr_FR' => 'Suppression de centre/établissement'),
						'account-creation' => array('en_US' => 'User account creation', 'fr_FR' => 'Création de compte utilisateur'),
						'account-password' => array('en_US' => 'User password generation', 'fr_FR' => 'Génération de mote de passe utilisateur'),
						'account-unblocking' => array('en_US' => 'User account unblocking', 'fr_FR' => 'Déblocage de compte utilisateur'),
						'account-deletion' => array('en_US' => 'User account deletion', 'fr_FR' => 'Suppression de compte utilisateur'),
				),
				'labels' => array(
						'en_US' => 'Category',
						'fr_FR' => 'Catégorie',
				),
		),

		'request/generic/subcategory' => array(
				'type' => 'select',
				'modalities' => array(
				),
				'labels' => array(
						'en_US' => 'Subcategory',
						'fr_FR' => 'Sous-catégorie',
				),
		),

		'request/generic/comments' => array(
				'type' => 'log',
				'labels' => array(
						'en_US' => 'Description',
						'fr_FR' => 'Description',
				),
		),
		
		'request/generic' => array(
				'properties' => array(
						'status' => array('definition' => 'request/generic/status'),
						'type' => array('definition' => 'request/type'),
						'place_id' => array('definition' => 'event/place_id'),
						'place_identifier' => array('definition' => 'event/place_identifier'),
						'place_caption' => array('definition' => 'event/place_caption'),
						'n_fn' => array('definition' => 'event/n_fn'),
						'category' => array('definition' => 'request/generic/category'),
						'subcategory' => array('definition' => 'request/generic/subcategory'),
						'caption' => array('definition' => 'request/generic/caption'),
						'begin_date' => array('definition' => 'event/begin_date'),
						'end_date' => array('definition' => 'event/end_date'),
						'comments' => array('definition' => 'request/generic/comments'),
						'update_time' => array('definition' => 'event/update_time'),
				),
		),
		
		'request/index/generic' => array(
				'definition' => 'inline',
				'title' => array('en_US' => 'P-Pit SynApps', 'fr_FR' => 'P-Pit SynApps'),
		),
		
		'request/search/generic' => array(
				'definition' => 'inline',
				'title' => array('en_US' => 'Requests', 'fr_FR' => 'Demandes'),
				'todoTitle' => array('en_US' => 'current', 'fr_FR' => 'en cours'),
				'searchTitle' => array('en_US' => 'search', 'fr_FR' => 'recherche'),
				'properties' => array(
						'status' => null,
						'type' => null,
						'place_id' => null,
						'n_fn' => null,
						'category' => null,
						'subcategory' => null,
						'caption' => null,
						'update_time' => null,
				),
		),
		
		'request/list/generic' => array(
				'definition' => 'inline',
				'properties'=> array(
						'status' => array(),
						'type' => array(),
						'place_id' => array(),
						'n_fn' => array(),
						'category' => array(),
						'subcategory' => array(),
						'caption' => array(),
				),
		),
		
		'request/detail/generic' => array(
				'definition' => 'inline',
				'title' => array('en_US' => 'Request file', 'fr_FR' => 'Fiche demande'),
				'displayAudit' => true,
		),
		
		'request/update/generic' => array(
				'definition' => 'inline',
				'properties' => array(
						'status' => array('mandatory' => true),
						'place_id' => array('mandatory' => true),
						'category' => array('mandatory' => true),
						'subcategory' => array('mandatory' => false),
						'caption' => array('mandatory' => true),
						'begin_date' => array('mandatory' => true),
						'end_date' => array('mandatory' => false),
						'comments' => array('mandatory' => false),
				),
		),

	// Place
	
	'corePlace' => array(
			'statuses' => array(),
			'properties' => array(
					'status' => array(
							'definition' => 'inline',
							'type' => 'select',
							'modalities' => array(
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'identifier' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Identifier',
									'fr_FR' => 'Identifiant',
							),
					),
					'caption' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'opening_date' => array(
							'definition' => 'inline',
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Opening date',
									'fr_FR' => 'Date d\'ouverture',
							),
					),
					'closing_date' => array(
							'definition' => 'inline',
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Closing date',
									'fr_FR' => 'Date de fermeture',
							),
					),
					'tax_regime' => array(
							'definition' => 'inline',
							'type' => 'select',
							'modalities' => array(
									'1' => array('en_US' => 'Metropolitan France', 'fr_FR' => 'France métropolitaine'),
							),
							'default' => '1',
							'labels' => array(
									'en_US' => 'Tax regime',
									'fr_FR' => 'Régime fiscal',
							),
					),
					'banner_src' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Banner',
									'fr_FR' => 'Bannière',
					),
							),
					'banner_width' => array(
							'definition' => 'inline',
							'type' => 'number',
							'minValue' => 0,
							'maxValue' => 180,
							'labels' => array(
									'en_US' => 'Banner width (px)',
									'fr_FR' => 'Largeur de la bannière (px)',
							),
					),
					'logo_src' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Logo',
									'fr_FR' => 'Logo',
							),
					),
					'logo_width' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Width (px)',
									'fr_FR' => 'Largeur (px)',
							),
					),
					'logo_height' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Height (px)',
									'fr_FR' => 'Hauteur (px)',
							),
					),
					'logo_href' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Logo link',
									'fr_FR' => 'Lien du logo',
							),
					),
					'legal_footer' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Legal notices at footer',
									'fr_FR' => 'Mentions légales en pied de page',
							),
					),
			),
	),

	'corePlace/index' => array(
			'title' => array('en_US' => 'P-Pit Admin', 'fr_FR' => 'P-Pit Admin'),
	),
	
	'corePlace/search' => array(
			'title' => array('en_US' => 'Places', 'fr_FR' => 'Etablissements'),
			'todoTitle' => array('en_US' => 'open', 'fr_FR' => 'ouverts'),
			'searchTitle' => array('en_US' => 'search', 'fr_FR' => 'recherche'),
			'main' => array(
					'identifier' => 'contains',
					'caption' => 'contains',
			),
	),

	'corePlace/list' => array(
			'identifier' => 'text',
			'caption' => 'text',
			'opening_date' => 'date',
			'closing_date' => 'date',
	),

	'corePlace/detail' => array(
			'title' => array('en_US' => 'Place detail', 'fr_FR' => 'Détail de l\'établissement'),
			'displayAudit' => true,
	),

	'corePlace/update' => array(
			'identifier' => array('mandatory' => true),
			'caption' => array('mandatory' => true),
			'opening_date' => array('mandatory' => true),
			'closing_date' => array('mandatory' => false),
			'tax_regime' => array('mandatory' => true),
			'legal_footer' => array('mandatory' => false),
			'logo_src' => array('mandatory' => false),
			'logo_width' => array('mandatory' => false),
			'logo_height' => array('mandatory' => false),
			'logo_href' => array('mandatory' => false),
			'banner_src' => array('mandatory' => false),
			'banner_width' => array('mandatory' => false),
	),

	// Product
	
	'core_product/generic/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
			'unavailable' => array('en_US' => 'Unavailable', 'fr_FR' => 'Non disponible'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
	),
	
	'core_product/generic/property/place_id' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Center',
			'fr_FR' => 'Centre',
		),
	),
	
	'core_product/generic/property/identifier' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Identifier',
			'fr_FR' => 'Identifiant',
		),
	),
	
	'core_product/generic/property/caption' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Caption',
			'fr_FR' => 'Libellé',
		),
	),

	'core_product/generic/property/description' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Caption',
			'fr_FR' => 'Libellé',
		),
	),

	'core_product/generic/property/variants' => array(
		'definition' => 'inline',
		'type' => 'key_value',
		'labels' => array(
			'en_US' => 'Variants',
			'fr_FR' => 'Variantes',
		),
	),

	'core_product/generic/property/tax_1_share' => array(
		'definition' => 'inline',
		'type' => 'number',
		'labels' => array(
			'en_US' => 'Tax 1 share',
			'fr_FR' => 'Part de TVA standard',
		),
	),

	'core_product/generic/property/tax_2_share' => array(
		'definition' => 'inline',
		'type' => 'number',
		'labels' => array(
			'en_US' => 'Tax 2 share',
			'fr_FR' => 'Part de TVA intermédiaire',
		),
	),

	'core_product/generic/property/tax_3_share' => array(
		'definition' => 'inline',
		'type' => 'number',
		'labels' => array(
			'en_US' => 'Tax 3 share',
			'fr_FR' => 'Part de TVA réduite',
		),
	),
	
	'core_product/search/generic' => array(
			'title' => array('en_US' => 'Products', 'fr_FR' => 'Produits'),
			'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
			'properties' => array(
					'place_id' => [],
					'status' => [],
					'identifier' => [],
					'caption' => [],
			),
	),

	'core_product/export/generic' => array(
		'properties' => array(
//			'place_id' => 'A',
			'status' => 'B',
			'identifier' => 'C',
			'caption' => 'D',
		),
	),
	
	// Deprecated
	'ppitProduct' => array(
		'properties' => array(),
		'variants' => array(),
		'criteria' => array(),
	),
	
	'ppitProduct/index' => array(
		'title' => array('en_US' => 'P-PIT Sales', 'fr_FR' => 'P-PIT Ventes'),
	),
	
	'ppitProduct/search' => array(),
	
	'ppitProduct/list' => array(),
	
	'ppitProduct/update' => array(),
	
	// Product option
	
	'productOption/generic/property/category' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'' => ['default' => 'Undefined option', 'fr_FR' => 'Option indéfinie'],
			'other' => ['default' => 'Other option', 'fr_FR' => 'Option autre'],
		),
	),
	
	// User
	'coreUser' => array(
			'properties' => array(
					'status' => array(
							'definition' => 'inline',
							'type' => 'select',
							'modalities' => array(
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'place_id' => array(
							'definition' => 'inline',
							'type' => 'select',
							'modalities' => array(
									'2pit' => array('fr_FR' => 'P-PIT', 'en_US' => '2PIT'),
							),
							'labels' => array(
									'en_US' => 'Center',
									'fr_FR' => 'Centre',
							),
					),
					'username' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Identifier',
									'fr_FR' => 'Identifiant',
							),
					),
					'n_title' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Title',
									'fr_FR' => 'Titre',
							),
					),
					'n_last' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Last name',
									'fr_FR' => 'Nom usuel',
							),
					),
					'n_first' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'First name',
									'fr_FR' => 'Prénom',
							),
					),
					'n_fn' => array(
							'definition' => 'inline',
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Formatted name',
									'fr_FR' => 'Nom formaté',
							),
					),
					'email' => array(
							'definition' => 'inline',
							'type' => 'email',
							'labels' => array(
									'en_US' => 'Email',
									'fr_FR' => 'Email',
							),
					),
					'roles' => array(
							'definition' => 'inline',
							'type' => 'json',
							'labels' => array(
									'en_US' => 'Roles',
									'fr_FR' => 'Roles',
							),
					),
					'applications' => array(
							'definition' => 'inline',
							'type' => 'json',
							'labels' => array(
									'en_US' => 'Applications',
									'fr_FR' => 'Applications',
							),
					),
					'perimeters' => array(
							'definition' => 'inline',
							'type' => 'json',
							'labels' => array(
									'en_US' => 'Perimeters',
									'fr_FR' => 'Perimeters',
							),
					),
					'requires_notifications' => array(
							'definition' => 'inline',
							'type' => 'json',
							'labels' => array(
									'en_US' => 'Accept notifications',
									'fr_FR' => 'Accepte les notifications',
							),
					),
					'locale' => array(
							'definition' => 'inline',
							'type' => 'select',
							'modalities' => array(
									'en_US' => array('en_US' => 'en_US', 'fr_FR' => 'en_US'),
									'fr_FR' => array('en_US' => 'fr_FR', 'fr_FR' => 'fr_FR'),
					),
					'labels' => array(
									'en_US' => 'Accept notifications',
									'fr_FR' => 'Accepte les notifications',
							),
					),
			),
	),
	
	'coreUser/index' => array(
			'title' => array('en_US' => 'P-Pit Admin', 'fr_FR' => 'P-Pit Admin'),
	),
	
	'coreUser/search' => array(
			'title' => array('en_US' => 'Users', 'fr_FR' => 'Users'),
			'todoTitle' => array('en_US' => 'revoked', 'fr_FR' => 'actifs'),
			'searchTitle' => array('en_US' => 'search', 'fr_FR' => 'active'),
			'main' => array(
					'place_id' => 'select',
					'username' => 'contains',
					'n_fn' => 'contains',
			),
	),
	
	'coreUser/list' => array(
			'place_id' => 'text',
			'username' => 'text',
			'n_fn' => 'text',
	),
	
	'coreUser/detail' => array(
			'title' => array('en_US' => 'User detail', 'fr_FR' => 'Fiche utilisateur'),
			'displayAudit' => true,
	),
/*	
	'corePlace/update' => array(
			'username' => array('mandatory' => true),
			'title' => array('mandatory' => false),
			'last_name' => array('mandatory' => true),
			'first_name' => array('mandatory' => true),
			'email' => array('mandatory' => true),
			'applications' => array('mandatory' => false),
			'perimeters' => array('mandatory' => false),
			'requires_notifications' => array('mandatory' => false),
			'locale' => array('mandatory' => false),
	),*/

	'coreUser/export' => array(
			'n_fn' => null,
			'username' => null,
			'email' => null,
	),

	'user/messages/activation/title' => array(
		'en_US' => 'Your account activation',
		'fr_FR' => 'Activation de votre compte',
	),

	'user/acceptedRegistrationDomain' => null,
	
	'user/messages/activation/text' => array(
		'en_US' => '
Hello,
Almost done for being able to connect to your account, by following this link : %s
See you soon on the platform!
',
		'fr_FR' => '
Bonjour,
Plus qu\'une étape pour pouvoir vous connecter à votre compte, en suivant ce lien : %s
A bientôt sur la plateforme !
',
	),
	
	'user/messages/lost_password/title' => array(
		'default' => 'Your request for password reinitialization',
		'fr_FR' => 'Votre demande de réinitiailisation de mot de passe',
	),
	
	'user/messages/lost_password/text' => array(
		'default' => '
Hello,
You lost your password ? Not a problem.
Just follow this link for defining a new password for your account : %s
Please note that this link is temporary
See you soon on the platform!
',
		'fr_FR' => '
Bonjour,
Vous avez perdu votre mot de passe ? Pas de souci.
En suivant ce lien vous pourrez définir un nouveau mot de passe pour votre compte : %s
A bientôt sur la plateforme !
',
	),
	
	// coreVcard

	'coreVcard/n_title' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Title',
					'fr_FR' => 'Titre',
			),
	),
	'coreVcard/n_last' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Last name',
					'fr_FR' => 'Nom',
			),
	),
	'coreVcard/n_first' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'First name',
					'fr_FR' => 'Prénom',
			),
	),
	'coreVcard/n_fn' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Formatted name',
					'fr_FR' => 'Nom formaté',
			),
	),
	'coreVcard/email' => array(
			'type' => 'email',
			'labels' => array(
					'en_US' => 'Email',
					'fr_FR' => 'Email',
			),
	),
	'coreVcard/tel_cell' => array(
			'type' => 'phone',
			'labels' => array(
					'en_US' => 'Cellular',
					'fr_FR' => 'Téléphone mobile',
			),
	),

	'demo' => array(
			'event/search/title' => array(
					'en_US' => '
<h4>Event list</h4>
<p>As a default, all the new events are presented in the list.</p>
<p>As soon as a criterion below is specified, the list switch in search mode.</p>
',
					'fr_FR' => '
<h4>Liste des évènements</h4>
<p>Par défaut, tous les nouveaux évènements sont présentés dans la liste.</p>
<p>Dès lors qu\'un des critères ci-dessous est spécifié, le mode de recherche est automatiquement activé.</p>
',
			),
			'event/search' => array(
					'en_US' => '
<h4>Search button</h4>
<p>The search button refresh the list filtered according to the criteria below.</p>
',
					'fr_FR' => '
<h4>Bouton de recherche</h4>
<p>Le bouton de recherche rafraichit la liste filtrée sur les critères ci-dessous.</p>
',
			),
			'event/search/x' => array(
					'en_US' => '
<h4>Return in default mode</h4>
<p>The <code>x</code> button reinitializes all the search criteria and reset the list filtered on new events.</p>
',
					'fr_FR' => '
<h4>Retour au mode par défaut</h4>
<p>Le bouton <code>x</code> réinitialise tous les critères de recherche et ré-affiche la liste filtrée sur les nouveaux évènements.</p>
',
			),
			'event/search/export' => array(
					'en_US' => '
<h4>List export</h4>
<p>The list can be exported to Excel as it is presented: defaulting list or list resulting of a multi-criteria search.</p>
',
					'fr_FR' => '
<h4>Export de la liste</h4>
<p>La liste peut être exportée sous Excel telle que présentée : liste par défaut ou liste résultant d\'une recherche multi-critère.</p>
',
			),
			'event/list/ordering' => array(
					'en_US' => '
<h4>Ordering</h4>
<p>The list can be sorted according to each column in ascending or descending order.</p>
',
					'fr_FR' => '
<h4>Classement</h4>
<p>La liste peut être triée selon chaque colonne en ordre ascendant ou descendant.</p>
',
			),
			'event/list/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un évènement</h4>
<p>Le bouton + permet l\'ajout d\un nouvel évènement.</p>
					',
			),
			'event/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un évènement</h4>
<p>Lors de la création d\'un évènement les données principales sont renseignées.</p>
	<ul>
	</ul>
					',
			),
			'event/group' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Mode tableur</h4>
<p>Ce bouton permet d\'activer le mode de saisie tableur.</p>
	<ul>
	</ul>
					',
			),
			'event/list/detail' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Détail d\'un évènement</h4>
<p>Le bouton zoom permet d\'accéder au détail d\'un évènement.</p>
					',
			),
			'event/update' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Gestion des données de l\'évènement</h4>
<p>L\'accès au détail d\'un évènement permet de consulter et éventuellement en rectifier les données.</p>
					',
			),
			'product/search/title' => array(
					'en_US' => '
<h4>Catalogue</h4>
<p>As a default, all the products that are marked available are presented in the list.</p>
<p>As soon as a criterion below is specified, the list switch in search mode.</p>
',
					'fr_FR' => '
<h4>Catalogue</h4>
<p>Par défaut, tous les produits marqués comme disponibles sont présentés dans la liste.</p>
<p>Dès lors qu\'un des critères ci-dessous est spécifié, le mode de recherche est automatiquement activé.</p>
',
			),
			'product/search/x' => array(
					'en_US' => '
<h4>Return in default mode</h4>
<p>The <code>x</code> button reinitializes all the search criteria and reset the list filtered on available products.</p>
',
					'fr_FR' => '
<h4>Retour au mode par défaut</h4>
<p>Le bouton <code>x</code> réinitialise tous les critères de recherche et ré-affiche la liste filtrée sur les produits disponibles.</p>
',
			),
			'product/search/export' => array(
					'en_US' => '
<h4>List export</h4>
<p>The list can be exported to Excel as it is presented: defaulting list or list resulting of a multi-criteria search.</p>
',
					'fr_FR' => '
<h4>Export de la liste</h4>
<p>La liste peut être exportée sous Excel telle que présentée : liste par défaut ou liste résultant d\'une recherche multi-critère.</p>
',
			),
			'product/list/ordering' => array(
					'en_US' => '
<h4>Ordering</h4>
<p>The list can be sorted according to each column in ascending or descending order.</p>
',
					'fr_FR' => '
<h4>Classement</h4>
<p>La liste peut être triée selon chaque colonne en ordre ascendant ou descendant.</p>
',
			),
			'product/list/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un produit</h4>
<p>Le bouton + permet l\'ajout d\un nouveau produit.</p>
					',
			),
			'product/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un produit</h4>
<p>Lors de la création d\'un produit les données principales sont renseignées.</p>
	<ul>
		<li>Type (souscription à une offre, prestation spécifique...)</li>
		<li>Marque, identification et description</li>
		<li>Disponibilité</li>
		<li>Prix et répartition du prix selon les différents régimes de TVA (standard, intermédiaire, réduit)</li>
	</ul>
					',
			),
			'product/list/detail' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Détail d\'un produit</h4>
<p>Le bouton zoom permet d\'accéder au détail d\'un produit et aux options associées.</p>
					',
			),
			'product/update' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Gestion des attributs du produit</h4>
<p>L\'accès au détail d\'un produit permet de consulter et éventuellement en rectifier les données, ainsi que de gérer les options associées au produit.</p>
					',
			),
			'productOption/list/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'une option</h4>
<p>Les options peuvent être globales (à un bon de commande) ou associée à un produit particulier.</p>
					',
			),
	),
);
