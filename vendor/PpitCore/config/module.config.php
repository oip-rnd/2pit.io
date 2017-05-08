<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

return array(
	'controllers' => array(
        'invokables' => array(
            'PpitCore\Controller\Community' => 'PpitCore\Controller\CommunityController',
        	'PpitCore\Controller\Credit' => 'PpitCore\Controller\CreditController',
            'PpitCore\Controller\Home' => 'PpitCore\Controller\HomeController',
        	'PpitCore\Controller\Document' => 'PpitCore\Controller\DocumentController',
        	'PpitCore\Controller\Event' => 'PpitCore\Controller\EventController',
        	'PpitCore\Controller\Interaction' => 'PpitCore\Controller\InteractionController',
        	'PpitCore\Controller\Instance' => 'PpitCore\Controller\InstanceController',
        	'PpitCore\Controller\Place' => 'PpitCore\Controller\PlaceController',
        	'PpitCore\Controller\Public' => 'PpitCore\Controller\PublicController',
        	'PpitCore\Controller\User' => 'PpitCore\Controller\UserController',
        	'PpitCore\Controller\Vcard' => 'PpitCore\Controller\VcardController',
        ),
    ),

	'console' => array(
			'router' => array(
					'routes' => array(
							'updateInstance' => array(
									'options' => array(
											'route'    => 'instance serialize <id>',
											'defaults' => array(
													'controller' => 'PpitCore\Controller\Instance',
													'action'     => 'serialize'
											)
									)
							),
							'creditUse' => array(
									'options' => array(
											'route'    => 'credit use [--live|-l] [--mailTo=]',
											'defaults' => array(
													'controller' => 'PpitCore\Controller\Credit',
													'action'     => 'use'
											)
									)
							),
							'repair' => array(
									'options' => array(
											'route'    => 'credit repair',
											'defaults' => array(
													'controller' => 'PpitCore\Controller\Credit',
													'action'     => 'repair'
											)
									)
							),
					)
			)
	),
		
    'router' => array(
        'routes' => array(
            'index' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                	'defaults' => array(
                        'controller' => 'PpitCore\Controller\Home',
                        'action'     => 'index',
                    ),
                ),
            ),
        	'home' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/home',
                	'defaults' => array(
                        '__NAMESPACE__' => 'PpitCore\Controller',
                        'controller'    => 'Home',
                        'action'        => 'index',
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
        	'community' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/community',
                    'defaults' => array(
                        'controller' => 'PpitCore\Controller\Community',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
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
	       			'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index',
	                    	'defaults' => array(
	                    		'action' => 'index',
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
	       			'dataList' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/data-list',
	                    	'defaults' => array(
	                    		'action' => 'dataList',
	                        ),
	                    ),
	                ),
	       			'update' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/update[/:id]',
		                    'constraints' => array(
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'update',
	                        ),
	                    ),
	                ),
        			'home' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/home',
        					'defaults' => array(
        							'action' => 'home',
        					),
       					),
        			),
        			'planning' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/planning[/:type]',
        					'defaults' => array(
        						'action' => 'planning',
        					),
       					),
        			),
	       			'dashboard' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/dashboard[/:type]',
        					'defaults' => array(
        						'action' => 'dashboard',
	        				),
       					),
        			),
	       			'sendMessage' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/send-message[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'sendMessage',
	                        ),
	                    ),
	                ),
	       		),
        	),
        	'document' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/document',
                    'defaults' => array(
                        'controller' => 'PpitCore\Controller\Document',
                        'action'     => 'index',
                    ),
                ),
            	'may_terminate' => true,
            		'child_routes' => array(
	        				'home' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/home',
	        								'defaults' => array(
	        										'action' => 'home',
	        								),
	        						),
	        				),
            				'index' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/index[/:parent_id]',
	        								'defaults' => array(
	        										'action' => 'index',
	        								),
	        						),
	        				),
	        				'search' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/search[/:parent_id]',
	        								'defaults' => array(
	        										'action' => 'search',
	        								),
	        						),
	        				),
	        				'list' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/list[/:parent_id]',
	        								'defaults' => array(
	        										'action' => 'list',
	        								),
	        						),
	        				),
	        				'export' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/export[/:parent_id]',
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
            				'dropboxRegister' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/dropbox-register',
            								'defaults' => array(
            										'action' => 'dropboxRegister',
            								),
            						),
            				),
            				'add' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/add[/:parent_id]',
            								'constraints' => array(
            										'parent_id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'add',
            								),
            						),
            				),
            				'update' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/update[/:id]',
            								'constraints' => array(
            										'id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'update',
            								),
            						),
            				),
            				'download' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/download[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'download',
            								),
            						),
            				),
            		),
            ),
        	'public' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/public',
                    'defaults' => array(
                        'controller' => 'PpitCore\Controller\Public',
                        'action'     => 'displayPage',
                    ),
                ),
            	'may_terminate' => true,
            		'child_routes' => array(
            				'displayBlog' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/blog[/:directory][/:name]',
            								'constraints' => array(
											        'directory' => '[a-zA-Z0-9_-]+',
											        'name' => '[a-zA-Z0-9_-]+',
            								),
	        								'defaults' => array(
	        										'action' => 'displayBlog',
	        								),
	        						),
	        				),
            				'displayPage' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '[/:directory][/:name]',
            								'constraints' => array(
											        'directory' => '[a-zA-Z0-9_-]+',
											        'name' => '[a-zA-Z0-9_-]+',
            								),
	        								'defaults' => array(
	        										'action' => 'displayPage',
	        								),
	        						),
	        				),
            				'home' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/home',
	        								'defaults' => array(
	        										'action' => 'home',
	        								),
	        						),
	        				),
            		),
        	),
        	'event' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/event',
                    'defaults' => array(
                        'controller' => 'PpitCore\Controller\Event',
                        'action'     => 'index',
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
            				'search' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/search[/:type]',
            								'defaults' => array(
            										'action' => 'search',
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
        					'get' => array(
	        						'type' => 'segment',
	        						'options' => array(
		        							'route' => '/get',
		  									'defaults' => array(
			       									'action' => 'get',
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
            										'id'     => '[0-9]*',
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
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'update',
            								),
            						),
            				),
            		),
            ),
        	'interaction' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/interaction',
                    'defaults' => array(
                        'controller' => 'PpitCore\Controller\Interaction',
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
            										'id'     => '[0-9]*',
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
            										'id'     => '[0-9]*',
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
            										'id'     => '[0-9]*',
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
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'process',
            								),
            						),
            				),
            		),
            ),
        	'instance' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/instance',
                    'defaults' => array(
                        'controller' => 'PpitCore\Controller\Instance',
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
            										'id'     => '[0-9]*',
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
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'update',
            								),
            						),
            				),
            				'accept' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/accept',
            								'defaults' => array(
            										'action' => 'accept',
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
            		),
            ),
        	'place' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/place',
                    'defaults' => array(
                        'controller' => 'PpitCore\Controller\Place',
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
		        										'id'     => '[0-9]*',
		        								),
		        								'defaults' => array(
		        										'action' => 'update',
		        								),
		        						),
		        				),
			       		),
			),
	       	'user' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/user',
                    'constraints' => array(
                    	'id'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'PpitCore\Controller\User',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
	                'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index[/:community_id]',
		                    'constraints' => array(
		                    	'community_id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	       			'search' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/search[/:community_id]',
		                    'constraints' => array(
		                    	'community_id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'search',
	                        ),
	                    ),
	                ),
	                'list' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/list[/:community_id]',
		                    'constraints' => array(
		                    	'community_id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'list',
	                        ),
	                    ),
	                ),
	       			'update' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/update[/:community_id][/:id]',
		                    'constraints' => array(
		                    	'community_id'  => '[0-9]*',
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
		                    	'id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'revoke',
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
	                'demo' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/demo[/:username]',
	                    	'defaults' => array(
	                    		'action' => 'demo',
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
		                    	'id'     => '[0-9]*',
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
		                    	'id'     => '[0-9]*',
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
		                    	'id'     => '[0-9]*',
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
		                    	'id'     => '[0-9]*',
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
		                    	'vcard_id'     => '[0-9]*',
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
	       			'getAuthenticate' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/get-authenticate',
	                    	'defaults' => array(
	                            'action' => 'getAuthenticate',
	                        ),
	                    ),
	                ),
	       			'getApplications' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/get-applications',
	                    	'defaults' => array(
	                            'action' => 'getApplications',
	                        ),
	                    ),
	                ),
	       		),
	       	),
        	'vcard' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/vcard',
                    'defaults' => array(
                        'controller' => 'PpitCore\Controller\Vcard',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
	                'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	                'search' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/search[/:community_id]',
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
	       			'listRest' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/list-rest[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'listRest',
	                        ),
	                    ),
	                ),
	       			'update' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/update[/:community_id][/:id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'update',
	                        ),
	                    ),
	                ),
	       			'photo' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/photo[/:id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    	'id'     => '[0-9]*',
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
	       			'delete' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/delete[/:id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    		'id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'delete',
	                        ),
	                    ),
	                ),
	       			'import' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/import[/:id]',
		                    'constraints' => array(
		                    	'id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'import',
	                        ),
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

				array('route' => 'community', 'roles' => array('admin')),
				array('route' => 'community/dataList', 'roles' => array('admin')),
				array('route' => 'community/delete', 'roles' => array('admin')),
				array('route' => 'community/index', 'roles' => array('admin')),
				array('route' => 'community/list', 'roles' => array('admin')),
            	array('route' => 'community/get', 'roles' => array('user')),
				array('route' => 'community/update', 'roles' => array('admin')),
				array('route' => 'community/home', 'roles' => array('user')),
				array('route' => 'community/planning', 'roles' => array('user')),
				array('route' => 'community/dashboard', 'roles' => array('user')),
				array('route' => 'community/sendMessage', 'roles' => array('admin')),

				array('route' => 'document', 'roles' => array('user')),
				array('route' => 'document/home', 'roles' => array('user')),
				array('route' => 'document/index', 'roles' => array('user')),
				array('route' => 'document/search', 'roles' => array('user')),
				array('route' => 'document/export', 'roles' => array('user')),
            	array('route' => 'document/list', 'roles' => array('user')),
				array('route' => 'document/detail', 'roles' => array('user')),
				array('route' => 'document/download', 'roles' => array('user')),
				array('route' => 'document/dropboxRegister', 'roles' => array('admin')),
				array('route' => 'document/add', 'roles' => array('user')),
				array('route' => 'document/update', 'roles' => array('user')),
				
				array('route' => 'event', 'roles' => array('admin')),
				array('route' => 'event/index', 'roles' => array('admin')),
				array('route' => 'event/search', 'roles' => array('admin')),
				array('route' => 'event/list', 'roles' => array('admin')),
				array('route' => 'event/get', 'roles' => array('admin')),
				array('route' => 'event/export', 'roles' => array('admin')),
				array('route' => 'event/synchronize', 'roles' => array('admin')),
				array('route' => 'event/detail', 'roles' => array('admin')),
				array('route' => 'event/update', 'roles' => array('admin')),

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
				array('route' => 'instance/accept', 'roles' => array('admin')),
				array('route' => 'instance/legalNotices', 'roles' => array('guest')),
				array('route' => 'instance/addImage', 'roles' => array('admin')),
				array('route' => 'instance/addLogo', 'roles' => array('admin')),

				array('route' => 'place', 'roles' => array('admin')),
				array('route' => 'place/index', 'roles' => array('admin')),
				array('route' => 'place/search', 'roles' => array('admin')),
				array('route' => 'place/detail', 'roles' => array('admin')),
				array('route' => 'place/delete', 'roles' => array('admin')),
				array('route' => 'place/export', 'roles' => array('admin')),
            	array('route' => 'place/list', 'roles' => array('admin')),
				array('route' => 'place/update', 'roles' => array('admin')),

				array('route' => 'public/displayPage', 'roles' => array('guest')),
				array('route' => 'public/displayBlog', 'roles' => array('guest')),
				array('route' => 'public/home', 'roles' => array('guest')),

				array('route' => 'user', 'roles' => array('admin')),
				array('route' => 'user/index', 'roles' => array('admin')),
				array('route' => 'user/search', 'roles' => array('admin')),
				array('route' => 'user/list', 'roles' => array('admin')),
				array('route' => 'user/update', 'roles' => array('admin')),
				array('route' => 'user/role', 'roles' => array('admin')),
				array('route' => 'user/login', 'roles' => array('guest')),
				array('route' => 'user/demo', 'roles' => array('guest')),
				array('route' => 'user/expired', 'roles' => array('guest')),
				array('route' => 'user/logout', 'roles' => array('guest')),
				array('route' => 'user/passwordRequest', 'roles' => array('admin')),
				array('route' => 'user/password', 'roles' => array('user')),
				array('route' => 'user/passwordChanged', 'roles' => array('guest')),
				array('route' => 'user/lostPassword', 'roles' => array('guest')),
				array('route' => 'user/initpassword', 'roles' => array('guest')),
				array('route' => 'user/revoke', 'roles' => array('admin')),
				array('route' => 'user/changeContact', 'roles' => array('user')),
				array('route' => 'user/delete', 'roles' => array('admin')),
				array('route' => 'user/authenticate', 'roles' => array('guest')),
				array('route' => 'user/getAuthenticate', 'roles' => array('guest')),
				array('route' => 'user/getApplications', 'roles' => array('guest')),

				array('route' => 'vcard', 'roles' => array('admin')),
				array('route' => 'vcard/add', 'roles' => array('admin')),
				array('route' => 'vcard/photo', 'roles' => array('user')),
				array('route' => 'vcard/demoMode', 'roles' => array('user')),
				array('route' => 'vcard/delete', 'roles' => array('admin')),
				array('route' => 'vcard/detail', 'roles' => array('admin')),
				array('route' => 'vcard/export', 'roles' => array('admin')),
				array('route' => 'vcard/index', 'roles' => array('admin')),
				array('route' => 'vcard/list', 'roles' => array('admin')),
				array('route' => 'vcard/listRest', 'roles' => array('admin')),
				array('route' => 'vcard/update', 'roles' => array('user')),
				array('route' => 'vcard/search', 'roles' => array('admin')),
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

	'tableColNames' => array(
			1 => 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
			'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
			'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
	),

	'ppitApplications' => array(
    		'p-pit-admin' => array(
    				'labels' => array('fr_FR' => 'P-Pit Admin', 'en_US' => '2Pit Admin'),
    				'route' => 'place',
    				'params' => array(),
					'roles' => array(
							'admin' => array(
									'show' => true,
									'labels' => array(
											'en_US' => 'Administrator',
											'fr_FR' => 'Administrateur',
									)
							),
					),
			),
	),

	'perimeters' => array(
			'p-pit-admin' => null,
	),
		
	'ppitCoreDependencies' => array(
			'core_vcard' => new \PpitCore\Model\Vcard,
	),

	'ppitContactDependencies' => array(
			'user' => new \PpitCore\Model\User,
	),
	
	'ppitCustomerDependencies' => array(
			'user' => new \PpitCore\Model\User,
	),
	'ppitUser/index' => array(
			'title' => array('en_US' => 'P-PIT Admin', 'fr_FR' => 'P-PIT Admin'),
	),

	'menus' => array(
			'p-pit-admin' => array(
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
					'document' => array(
							'route' => 'document/index',
							'params' => array(),
							'glyphicon' => 'glyphicon-file',
							'label' => array(
									'en_US' => 'Documents',
									'fr_FR' => 'Documents',
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
					'event' => array(
							'route' => 'event/index',
							'params' => array(),
							'glyphicon' => 'glyphicon-calendar',
							'label' => array(
									'en_US' => 'Events',
									'fr_FR' => 'Evènements',
							),
					),
			),
	),
		
	'styleSheet' => array(
			'navbar' => 'navbar-default navbar-fixed-top',
			'panelHeadingBackground' => '#006179',
			'panelHeadingColor' => '#FFFFFF',
	),

	'headerParams' => array(
		'background-color' => '#79CCF3',
		'shift' => 0,
		'logo' => 'p-pit.png',
		'logo-href' => "https://www.p-pit.fr",
		'logo-height' => '60',
		'advert' => 'p-pit-advert.png',
		'advert-width' => 180,
		'signature' => 'p-pit-signature.png',
		'signature-href' => "https://www.p-pit.fr",
		'signature-width' => '280px',
		'footer' => array(
				'type' => 'text',
				'value' => 'P-PIT – SAS au capital de 10 000 € - R.C.S PARIS 804 199 594 - 14, rue Charles V – 75004 PARIS',
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
			'\PpitCore\Model\Community::consumeCredits',
	),
		
	'credit' => array(
			'unlimitedCredits' => false,
	),
	'instance/index' => array(
			'title' => array('en_US' => 'P-PIT Admin', 'fr_FR' => 'P-PIT Admin'),
	),

	// Document

	'document' => array(
			'type' => array(
					'type' => 'select',
					'modalities' => array(
							'directory' => array(
								'glyphicon' => 'glyphicon-folder-close',
								'en_US' => 'Directory',
								'fr_FR' => 'Répertoire',
							),
							'link' => array(
								'glyphicon' => 'glyphicon-cloud',
								'en_US' => 'Link',
								'fr_FR' => 'Lien',
							),
							'attachment' => array(
								'glyphicon' => 'glyphicon-paperclip',
								'en_US' => 'Attachment',
								'fr_FR' => 'Pièce jointe',
							),
							'dynamic' => array(
								'glyphicon' => 'glyphicon-file',
								'en_US' => 'Dynamic',
								'fr_FR' => 'Dynamique',
							),
							'html' => array(
								'glyphicon' => 'glyphicon-file',
								'en_US' => 'HTML',
								'fr_FR' => 'HTML',
							),
					),
					'labels' => array(
							'en_US' => 'Type',
							'fr_FR' => 'Type',
					),
			),
			'name' => array(
					'type' => 'input',
					'labels' => array(
							'en_US' => 'Name',
							'fr_FR' => 'Nom',
					),
			),
			'update_time' => array(
					'type' => 'time',
					'labels' => array(
							'en_US' => 'Updated',
							'fr_FR' => 'Modifié',
					),
			),
			'url' => array(
					'type' => 'input',
					'labels' => array(
							'en_US' => 'URL',
							'fr_FR' => 'URL',
					),
			),
	),
	'document/index' => array(
			'title' => array('en_US' => 'P-PIT Document', 'fr_FR' => 'P-PIT Document'),
	),
	'document/search' => array(
			'title' => array('en_US' => 'Documents', 'fr_FR' => 'Documents'),
			'todoTitle' => array('en_US' => 'active', 'fr_FR' => 'actifs'),
			'searchTitle' => array('en_US' => 'search', 'fr_FR' => 'recherche'),
			'main' => array(
					'type' => 'select',
					'name' => 'contains',
					'update_time' => 'range',
			),
			'more' => array(),
	),
	'document/list' => array(
			'type' => 'glyphicon',
			'name' => 'text',
			'update_time' => 'text',
	),
	'document/detail' => array(
			'title' => array('en_US' => 'Document', 'fr_FR' => 'Document'),
			'displayAudit' => false,
	),
		
	// Event
	
	'event/status' => array(
			'type' => 'select',
			'modalities' => array(
					'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
			),
			'labels' => array(
					'en_US' => 'Status',
					'fr_FR' => 'Statut',
			),
	),
	'event/type' => array(
			'type' => 'select',
			'modalities' => array(
					'place' => array('en_US' => 'Place', 'fr_FR' => 'Etablissement'),
					'vcard' => array('en_US' => 'Contact', 'fr_FR' => 'Contact'),
			),
			'labels' => array(
					'en_US' => 'Type',
					'fr_FR' => 'Type',
			),
	),
	'event/identifier' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Identifier',
					'fr_FR' => 'Identifiant',
			),
	),
	'event/place_identifier' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Place code',
					'fr_FR' => 'Code établissement',
			),
	),
	'event/place_caption' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Place',
					'fr_FR' => 'Etablissement',
			),
	),
	'event/community_name' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Name',
					'fr_FR' => 'Nom',
			),
	),
	'event/caption' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Caption',
					'fr_FR' => 'Libellé',
			),
	),
	'event/description' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Description',
					'fr_FR' => 'Description',
			),
	),
	'event/begin_date' => array(
			'type' => 'date',
			'labels' => array(
					'en_US' => 'Begin date',
					'fr_FR' => 'Date début',
			),
	),
	'event/end_date' => array(
			'type' => 'date',
			'labels' => array(
					'en_US' => 'End date',
					'fr_FR' => 'Date fin',
			),
	),
	'event/value' => array(
			'type' => 'input',
			'labels' => array(
					'en_US' => 'Value',
					'fr_FR' => 'Valeur',
			),
	),
	'event/update_time' => array(
			'type' => 'datetime',
			'labels' => array(
					'en_US' => 'Update time',
					'fr_FR' => 'Heure de mise à jour',
			),
	),

	'event' => array(
			'statuses' => array(),
			'category' => array(
					'learning' => array(
							'labels' => array('en_US' => 'Learning', 'fr_FR' => 'Formation'),
							'color' => array('green' => null),
					),
			),
			'properties' => array(
					'status' => array('type' => 'specific', 'definition' => 'event/status'),
					'type' => array('type' => 'specific', 'definition' => 'event/type'),
					'identifier' => array('type' => 'specific', 'definition' => 'event/identifier'),
					'place_identifier' => array('type' => 'specific', 'definition' => 'event/place_identifier'),
					'place_caption' => array('type' => 'specific', 'definition' => 'event/place_caption'),
					'community_name' => array('type' => 'specific', 'definition' => 'event/community_name'),
					'caption' => array('type' => 'specific', 'definition' => 'event/caption'),
					'description' => array('type' => 'specific', 'definition' => 'event/description'),
					'value' => array('type' => 'specific', 'definition' => 'event/value'),
					'update_time' => array('type' => 'specific', 'definition' => 'event/update_time'),
			),
	),
	
	'event/index' => array(
			'title' => array('en_US' => 'P-Pit SynApps', 'fr_FR' => 'P-Pit SynApps'),
	),
	
	'event/search' => array(
			'title' => array('en_US' => 'Events', 'fr_FR' => 'Evènements'),
			'todoTitle' => array('en_US' => 'recent', 'fr_FR' => 'récents'),
			'searchTitle' => array('en_US' => 'search', 'fr_FR' => 'recherche'),
			'main' => array(
					'status' => 'value',
					'type' => 'value',
					'place_identifier' => 'contains',
					'community_name' => 'contains',
					'identifier' => 'contains',
					'caption' => 'contains',
					'value' => 'range',
					'update_time' => 'range',
			),
	),
	
	'event/list' => array(
			'type' => 'select',
			'identifier' => 'text',
			'caption' => 'text',
			'value' => 'number',
			'update_time' => 'time',
	),
	
	'event/detail' => array(
			'title' => array('en_US' => 'Event detail', 'fr_FR' => 'Détail de l\'évènement'),
			'displayAudit' => true,
	),
	
	'event/update' => array(
			'status' => array('mandatory' => true),
			'type' => array('mandatory' => true),
			'identifier' => array('mandatory' => true),
			'caption' => array('mandatory' => false),
			'description' => array('mandatory' => false),
			'value' => array('mandatory' => false),
	),

	'event/export' => array(
			'status' => null,
			'type' => null,
			'place_identifier' => null,
			'place_caption' => null,
			'community_name' => null,
			'identifier' => null,
			'caption' => null,
			'description' => null,
			'value' => null,
	),
		
	// Interaction

	'interaction/type/app' => array(
			'controller' => '\PpitCore\Model\App::controlInteraction',
			'processor' => '\PpitCore\Model\App::processInteraction',
	),
		
	'interaction/type/document' => array(
			'controller' => '\PpitCore\Model\Document::controlInteraction',
			'processor' => '\PpitCore\Model\Document::processInteraction',
	),

	'interaction/type/event' => array(
			'controller' => '\PpitCore\Model\Event::controlInteraction',
			'processor' => '\PpitCore\Model\Event::processInteraction',
	),
		
	'interaction/type' => array(
			'type' => 'select',
			'modalities' => array(
					'app' => array('en_US' => 'Apps', 'fr_FR' => 'Apps'),
/*					'organization' => array('en_US' => 'Organization', 'fr_FR' => 'Organisation'),
					'agent' => array('en_US' => 'Agents', 'fr_FR' => 'Agents'),
					'agentAttachment' => array('en_US' => 'Agent attachments', 'fr_FR' => 'Affectations d\'Agents'),*/
					'document' => array('en_US' => 'Documents', 'fr_FR' => 'Documents'),
					'event' => array('en_US' => 'Events', 'fr_FR' => 'Evènements'),
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
					'format' => array(
							'type' => 'select',
							'modalities' => array(
									'application/xml' => array('en_US' => 'XML', 'fr_FR' => 'XML'),
									'application/json' => array('en_US' => 'JSON', 'fr_FR' => 'JSON'),
									'text/csv' => array('en_US' => 'CSV', 'fr_FR' => 'CSV'),
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
					'format' => 'value',
					'direction' => 'value',
					'place_caption' => 'contains',
					'reference' => 'contains',
					'update_time' => 'range',
			),
	),
	
	'interaction/list' => array(
			'type' => 'select',
			'reference' => 'text',
			'update_time' => 'time',
	),
	
	'interaction/detail' => array(
			'title' => array('en_US' => 'Interaction detail', 'fr_FR' => 'Détail de l\'interaction'),
			'displayAudit' => true,
	),
	
	'interaction/update' => array(
			'status' => array('mandatory' => true),
			'type' => array('mandatory' => true),
			'format' => array('mandatory' => true),
			'direction' => array('mandatory' => false),
			'reference' => array('mandatory' => false),
			'content' => array('mandatory' => false),
			'http_status' => array('mandatory' => false),
	),

	// Place
	
	'corePlace' => array(
			'statuses' => array(),
			'properties' => array(
					'status' => array(
							'type' => 'select',
							'modalities' => array(
							),
							'labels' => array(
									'en_US' => 'Status',
									'fr_FR' => 'Statut',
							),
					),
					'identifier' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Identifier',
									'fr_FR' => 'Identifiant',
							),
					),
					'caption' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Caption',
									'fr_FR' => 'Libellé',
							),
					),
					'opening_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Opening date',
									'fr_FR' => 'Date d\'ouverture',
							),
					),
					'closing_date' => array(
							'type' => 'date',
							'labels' => array(
									'en_US' => 'Closing date',
									'fr_FR' => 'Date de fermeture',
							),
					),
					'tax_regime' => array(
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
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Banner',
									'fr_FR' => 'Bannière',
					),
							),
					'banner_href' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Banner link',
									'fr_FR' => 'Lien de la bannière',
							),
					),
					'logo_src' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Logo',
									'fr_FR' => 'Logo',
							),
					),
					'logo_width' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Width (px)',
									'fr_FR' => 'Largeur (px)',
							),
					),
					'logo_height' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Height (px)',
									'fr_FR' => 'Hauteur (px)',
							),
					),
					'logo_href' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Logo link',
									'fr_FR' => 'Lien du logo',
							),
					),
					'legal_footer' => array(
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
			'banner_src' => array('mandatory' => false),
			'banner_href' => array('mandatory' => false),
			'logo_src' => array('mandatory' => false),
			'logo_width' => array('mandatory' => false),
			'logo_height' => array('mandatory' => false),
			'logo_href' => array('mandatory' => false),
			'legal_footer' => array('mandatory' => false),
	),
);
