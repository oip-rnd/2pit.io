<?php

return array(
    'controllers' => array(
        'invokables' => array(
        ),
    ),
 
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
	    ),
    ),
	'bjyauthorize' => array(
		// Guard listeners to be attached to the application event manager
		'guards' => array(
			'BjyAuthorize\Guard\Route' => array(
			)
		)
	),
	__NAMESPACE__ => array(
			'options' => array(
					'routes' => array(
/*							'ppit-user' => 'ppit-user',
							'ppit-user-login' => 'zfcuser/login',
							'home' => 'home',
							'home-login' => 'zfcuser/login'*/
					)
			)
	),
		
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',       // On défini notre doctype
        'not_found_template'       => 'error/404',   // On indique la page 404
        'exception_template'       => 'error/index', // On indique la page en cas d'exception
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            'ppit-user' => __DIR__ . '/../view',
        ),
    ),

	'translator' => array(
		'locale' => 'fr_FR',
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
				'text_domain' => 'ppit-user'
			),
	       	array(
	            'type' => 'phpArray',
	            'base_dir' => './vendor/zendframework/zendframework/resources/languages/',
	            'pattern'  => 'fr/Zend_Validate.php',
	        ),
 		),
	),
);
