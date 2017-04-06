<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Home' => 'Application\Controller\HomeController',
        ),
    ),
	'router' => array(
        'routes' => array(
            '' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                	'defaults' => array(
                        'controller' => 'Application\Controller\Home',
                        'action'     => 'index',
                    ),
                ),
            ),
        	'home' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/home',
                	'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
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
        ),
    ),
	'bjyauthorize' => array(
		// Guard listeners to be attached to the application event manager
		'guards' => array(
			'BjyAuthorize\Guard\Route' => array(

				array('route' => '', 'roles' => array('guest')),
				array('route' => 'home', 'roles' => array('guest')),
				array('route' => 'home/index', 'roles' => array('guest')),
			)
		)
	),
	'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
            'Zend\Log\Logger' => function($sm){
                $logger = new Zend\Log\Logger;
                $writer = new Zend\Log\Writer\Stream('data/log/exception.txt');
                 
                $logger->addWriter($writer);  
                 
                return $logger;
            },
        ),
	),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
        	'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
	'translator' => array(
		'locale' => 'fr_FR',
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
			),
	       	array(
	            'type' => 'phpArray',
	            'base_dir' => './vendor/zendframework/zendframework/resources/languages/',
	            'pattern'  => 'fr/Zend_Validate.php',
	        ),
 		),
	),
	'title' => array(
			'fr_FR' => 'P-Pit',
			'en_US' => '2pit',
	),
	'menu' => array(
			'instance' => array(
					'action' => 'Instance',
					'route' => 'instance/index',
					'params' => array('type' => ''),
					'urlParams' => array(),
					'label' => array(
							'en_US' => 'Instances',
							'fr_FR' => 'Instances',
					),
			),
	),
	'defaultEntry' => 'instance',
);
