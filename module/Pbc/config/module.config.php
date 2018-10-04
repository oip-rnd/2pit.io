<?php
/**
 * PpitPbc V1.0 (https://github.com/p-pit/pbc)
 *
 * @link      https://github.com/p-pit/pbc
 * @copyright Copyright (c) 2018 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitPbc/blob/master/license.txt GNU-GPL license
 */

return array(
	'controllers' => array(
		'invokables' => array(
			'Pbc\Controller\PbcPublic' => 'Pbc\Controller\PbcPublicController',
			'Pbc\Controller\Mockup' => 'Pbc\Controller\MockupController',
			'Pbc\Controller\Mockup2' => 'Pbc\Controller\Mockup2Controller',
		),
	),
	
	'router' => array(
		'routes' => array(
			'pbcPublic' => array(
				'type'    => 'literal',
				'options' => array(
					'route'    => '/pbc-public',
					'defaults' => array(
						'controller' => 'Pbc\Controller\PbcPublic',
						'action'     => 'landing',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'landing' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/landing',
							'defaults' => array('action' => 'landing'),
						),
					),
				),
			),
			'mockup' => array(
				'type'    => 'literal',
				'options' => array(
					'route'    => '/mockup',
					'defaults' => array(
						'controller' => 'Pbc\Controller\Mockup',
						'action'     => 'landingPage',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'landingPage' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/landing-page',
							'defaults' => array('action' => 'landingPage'),
						),
					),
					'result' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/result',
							'defaults' => array('action' => 'result'),
						),
					),
					'result2' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/result2',
							'defaults' => array('action' => 'result2'),
						),
					),
					'result3' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/result3',
							'defaults' => array('action' => 'result3'),
						),
					),
					'file' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/file',
							'defaults' => array('action' => 'file'),
						),
					),
				),
			),
			'mockup2' => array(
				'type'    => 'literal',
				'options' => array(
					'route'    => '/mockup2',
					'defaults' => array(
						'controller' => 'Pbc\Controller\Mockup2',
						'action'     => 'landingPage',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'landingPage' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/landing-page',
							'defaults' => array('action' => 'landingPage'),
						),
					),
					'result' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/result[/:type][/:perspective]',
							'defaults' => array('action' => 'result'),
						),
					),
					'file' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/file',
							'defaults' => array('action' => 'file'),
						),
					),
					'v1' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/v1[/:type][/:perspective][/:identifier]',
							'defaults' => array('action' => 'v1'),
						),
					),
					'subscription' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/subscription',
							'defaults' => array('action' => 'subscription'),
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

				array('route' => 'pbcPublic/landing', 'roles' => array('guest')),
				
				array('route' => 'mockup/landingPage', 'roles' => array('guest')),
				array('route' => 'mockup/result', 'roles' => array('guest')),
				array('route' => 'mockup/result2', 'roles' => array('guest')),
				array('route' => 'mockup/result3', 'roles' => array('guest')),
				array('route' => 'mockup/file', 'roles' => array('guest')),

				array('route' => 'mockup2/landingPage', 'roles' => array('guest')),
				array('route' => 'mockup2/result', 'roles' => array('guest')),
				array('route' => 'mockup2/file', 'roles' => array('guest')),
				array('route' => 'mockup2/v1', 'roles' => array('guest')),
				array('route' => 'mockup2/subscription', 'roles' => array('guest')),
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
           'pbc' => __DIR__ . '/../view',
        ),
    ),
	'translator' => array(
		'locale' => 'fr_FR',
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
				'text_domain' => 'pbc'
			),
	       	array(
	            'type' => 'phpArray',
	            'base_dir' => './vendor/zendframework/zendframework/resources/languages/',
	            'pattern'  => 'fr/Zend_Validate.php',
	        ),
 		),
	),

	'pbc/skills' => array(
		'finance' => ['default' => 'Finance'],
		'infography' => ['default' => 'Infography', 'fr_FR' => 'Infographie'],
		'hr' => ['default' => 'HR', 'fr_FR' => 'RH'],
		'ui_design' => ['default' => 'UI Design'],
		'ux_design' => ['default' => 'UX Design'],
	),

	'pbc/skillColors' => array(
		'finance' => 'aqua-gradient',
		'infography' => 'purple-gradient',
		'hr' => 'peach-gradient',
		'ui_design' => 'blue-gradient',
		'ux_design' => 'aqua-gradient',
	),

	'pbc/locations' => array(
		'bucharest' => ['default' => 'Bucharest', 'fr_FR' => 'Bucarest'],
		'london' => ['default' => 'London', 'fr_FR' => 'Londres'],
		'paris_def' => ['default' => 'Paris - La Défense'],
		'paris_vdf' => ['default' => 'Paris - Val-de-Fontenay'],
	),

	'pbc/languages' => array(
		'en' => ['default' => 'English', 'fr_FR' => 'Anglais'],
		'fr' => ['default' => 'French', 'fr_FR' => 'Français'],
		'ro' => ['default' => 'Romanian', 'fr_FR' => 'Roumain'],
	),

	// Account PBC

	'core_account/pbc/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'suspect' => array('en_US' => 'Suspect (landing page)', 'fr_FR' => 'Suspect (landing page)'),
			'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
			'interested' => array('en_US' => 'Interested', 'fr_FR' => 'Intéressé'),
			'registered' => array('en_US' => 'Registered', 'fr_FR' => 'Enregistré'),
			'active' => array('en_US' => 'Active', 'fr_FR' => 'Actif'),
			'gone' => array('en_US' => 'Gone', 'fr_FR' => 'Parti'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
		'perspectives' => array(
			'contact' => array('suspect', 'new', 'registered', 'interested', 'active', 'gone'),
			'account' => array('registered', 'active'),
		),
	),

	'core_account/pbc/property/origine' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'outcoming' => array('en_US' => 'Pro bono corpo call', 'fr_FR' => 'Appel Pro bono corpo'),
			'e_mailing' => array('en_US' => 'Mailing Pro bono corpo', 'fr_FR' => 'Mailing Pro bono corpo'),
			'cooptation' => array('en_US' => 'Referal', 'fr_FR' => 'Recommendation'),
			'event' => array('en_US' => 'Event', 'fr_FR' => 'Événement'),
			'social_network' => array('en_US' => 'Jive/SBC', 'fr_FR' => 'Jive/SBC'),
//			'subscription' => array('en_US' => 'Online subscription', 'fr_FR' => 'Inscription en ligne'),
			'other' => array('en_US' => 'Other', 'fr_FR' => 'Autre'),
		),
		'labels' => array(
			'en_US' => 'Origine',
			'fr_FR' => 'Origine',
		),
	),
	
	'core_account/pbc/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'multiselect',
		'modalities' => array(
			'contributor' => ['default' => 'Contributor', 'fr_FR' => 'Contributeur'],
			'requestor' => ['default' => 'Requestor', 'fr_FR' => 'Demandeur'],
		),
		'labels' => ['default' => 'Role']
	),

	'core_account/pbc/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Matched accounts',
			'fr_FR' => 'Comptes connectés',
		),
	),
	
	'core_account/pbc/property/profile_tiny_1' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => ['default' => 'Service', 'fr_FR' => 'Service'],
	),
	
	'core_account/pbc/property/profile_tiny_2' => array(
		'definition' => 'inline',
		'type' => 'multiselect', 
		'modalities' => ['definition' => 'matching/skills'],
		'labels' => ['default' => 'Compétences par mots-clés'],
	),

	'core_account/pbc/property/profile_tiny_3' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => ['default' => 'Skills in text', 'fr_FR' => 'Compétences texte libre'],
	),

	'core_account/pbc/property/profile_tiny_4' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => ['default' => 'Location', 'fr_FR' => 'Localisation'],
	),

	'core_account/pbc/property/profile_tiny_5' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => ['default' => 'Personal catcher', 'fr_FR' => 'Accroche personnelle'],
	),
	
	'core_account/pbc/property/completeness' => array(
		'definition' => 'inline',
		'type' => 'computed',
		'modalities' => array(
			'0_not_completed' => ['default' => 'Not completed', 'fr_FR' => 'Non renseigné'],
			'1_minimum' => ['default' => 'Minimum', 'fr_FR' => 'Minimal'],
			'2_intermediary' => ['default' => 'Intermediary', 'fr_FR' => 'Intermédiaire'],
			'3_completed' => ['default' => 'Completed', 'fr_FR' => 'Complété'],
		),
		'function' => '\Pbc\Model\AccountPbc::computeCompleteness',
/*		'rules' => array(
			'3_completed' => ['profile_tiny_1' => [], 'profile_tiny_2' => [], 'profile_tiny_4' => [], 'profile_tiny_5' => []],
			'2_intermediary' => ['profile_tiny_2' => [], 'profile_tiny_5' => []],
			'1_minimum' => ['profile_tiny_2' => []],
		),*/
		'labels' => array(
			'en_US' => 'Profile completeness',
			'fr_FR' => 'Complétude du profil',
		),
	),
	
	'core_account/pbc' => array(
		'properties' => array(
			'status', 'place_id', 'contact_1_id', 'n_first', 'n_last', 'n_fn', 'email', 'tel_work', 'next_meeting_date', 'origine', 'contact_history', 'locale',
			'property_1', 'property_2',
			'profile_tiny_1', 'profile_tiny_2', 'profile_tiny_3', 'profile_tiny_4', 'profile_tiny_5',
			'json_property_1', 'json_property_2', 'json_property_3', 'json_property_4', 'json_property_5',
			'comment_1', 'comment_2', 'comment_3', 'comment_4', 'update_time',
			'completeness',
		),
		'acl' => array(
			'place_id' => array('application' => 'p-pit-admin', 'category' => 'place_id'),
		),
		'order' => 'n_fn',
	),
	
	'core_account/search/pbc' => array(
		'properties' => array(
			'place_id' => [],
			'status' => ['multiple' => true],
			'property_1' => [],
			'profile_tiny_1' => [],
			'profile_tiny_2' => [],
			'profile_tiny_3' => [],
			'profile_tiny_4' => [],
			'n_fn' => [],
			'email' => [],
			'next_meeting_date' => [],
			'origine' => [],
			'locale' => [],
		),
	),

	'core_account/list/pbc' => array(
		'properties' => array(
			'status' => array( // ['color' => ['new' => 'LightGreen', 'interested' => 'LightSalmon', 'candidate' => 'LightBlue', 'gone' => 'LightGrey']],
				'background-color' => array(
					'LightGreen' => ['status' => 'new'],
					'LightSalmon' => ['status' => 'interested'],
					'LightBlue' => ['status' => 'candidate'],
					'LightGrey' => ['status' => 'gone'],
				)
			),
			'completeness' => [],
			'n_fn' => [],
			'email' => [],
			'property_1' => [],
			'profile_tiny_1' => [],
			'profile_tiny_2' => [],
			'profile_tiny_3' => [],
			'profile_tiny_4' => [],
			'origine' => [],
			'next_meeting_date' => [],
		),
	),

	'core_account/detail/pbc' => array(
		'title' => array('en_US' => 'Contributor detail', 'fr_FR' => 'Détail du contributeur'),
		'displayAudit' => true,
		'tabs' => array(
		),
	),

	'core_account/update/pbc' => array(
		'place_id' => ['mandatory' => false],
		'status' => ['mandatory' => true],
		'n_first' => ['mandatory' => true],
		'n_last' => ['mandatory' => true],
		'email' => ['mandatory' => false],
		'tel_work' => ['mandatory' => false],
		'property_1' => ['mandatory' => false],
		'profile_tiny_1' => ['mandatory' => false],
		'profile_tiny_2' => ['mandatory' => false],
		'profile_tiny_3' => ['mandatory' => false],
		'profile_tiny_4' => ['mandatory' => false],
		'profile_tiny_5' => ['mandatory' => false],
		'property_2' => ['readonly' => true],
		'origine' => ['mandatory' => false],
		'next_meeting_date' => ['mandatory' => false],
		'contact_history' => ['mandatory' => false],
		'locale' => ['mandatory' => false],
	),

	'core_account/updateContact/pbc' => array(
	),
	
	'core_account/groupUpdate/pbc' => array(
		'status' => [],
		'next_meeting_date' => [],
	),

	'core_account/export/pbc' => array(
		'place_id' => [],
		'status' => [],
		'completeness' => [],
		'n_first' => [],
		'n_last' => [],
		'email' => [],
		'property_1' => [],
		'profile_tiny_2' => [],
//		'json_property_1' => [],
		'profile_tiny_3' => [],
		'profile_tiny_4' => [],
		'profile_tiny_5' => [],
		'origine' => [],
		'next_meeting_date' => [],
		'contact_history' => [],
		'locale' => [],
	),
	
	'core_account/pbc/property/json_property_2' => array(
		'definition' => 'inline',
		'type' => 'multiselect',
		'modalities' => array(
			'en' => ['default' => 'English', 'fr_FR' => 'Anglais'],
			'fr' => ['default' => 'French', 'fr_FR' => 'Français'],
			'ro' => ['default' => 'Romanian', 'fr_FR' => 'Roumain'],
		),
		'labels' => ['default' => 'Langues']
	),
	
	'core_account/indexCard/pbc' => array(
			'title' => array('en_US' => 'Client index card', 'fr_FR' => 'Fiche client'),
			'header' => array(
					'place_id' => null,
					'status' => null,
					'origine' => null,
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

	// Profile event
	
	'event/profile/property/category' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'hard_skill' => ['default' => 'Hard skill', 'fr_FR' => 'Compétence hard'],
			'soft_skill' => ['default' => 'Soft skill', 'fr_FR' => 'Compétence soft'],
			'inclination' => ['default' => 'Inclination', 'fr_FR' => 'Appétence'],
			'quality' => ['default' => 'Quality', 'fr_FR' => 'Qualité'],
		),
		'labels' => array(
			'en_US' => 'Category',
			'fr_FR' => 'Catégorie',
		),
	),
	
	// survey
	
	'event/survey/property/subcategory' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'steps' => ['default' => 'Steps', 'fr_FR' => 'Etapes'],
			'contributor' => ['default' => 'Contributor', 'fr_FR' => 'Contributeur'],
			'requestor' => ['default' => 'Requestor', 'fr_FR' => 'Demandeur'],
			'opinion' => ['default' => 'Opinion', 'fr_FR' => 'Avis'],
		),
		'labels' => array(
			'en_US' => 'Sub-category',
			'fr_FR' => 'Sous-catégorie',
		),
	),
	
	'event/survey/property/description' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'contributor' => ['default' => 'Contributor', 'fr_FR' => 'Contributeur'],
			'requestor' => ['default' => 'Requestor', 'fr_FR' => 'Demandeur'],
			'opinion' => ['default' => 'Opinion', 'fr_FR' => 'Avis'],
		),
		'labels' => array(
			'en_US' => 'Roles',
			'fr_FR' => 'Rôles',
		),
	),
	
	'event/survey/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Roles',
			'fr_FR' => 'Rôles',
		),
	),
	
	'event/survey/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Expected skills',
			'fr_FR' => 'Compétences attendues',
		),
	),
	
	'event/survey/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Expected informations about contributors',
			'fr_FR' => 'Informations attendues sur les contributeurs',
		),
	),
	
	'event/survey/property/property_4' => array(
		'definition' => 'inline',
		'type' => 'input',
/*		'modalities' => array(
			'per_day' => ['default' => 'Per day', 'fr_FR' => 'Par jour'],
			'per_week' => ['default' => 'Per week', 'fr_FR' => 'Par semaine'],
			'per_month' => ['default' => 'Per month', 'fr_FR' => 'Par mois'],
		),*/
		'labels' => array(
			'default' => 'Contributor\'s desire',
			'fr_FR' => 'Appétences du contributeur',
/*			'en_US' => 'Availability period',
			'fr_FR' => 'Période de disponibilité',*/
		),
	),
	
	'event/survey/property/property_5' => array(
		'definition' => 'inline',
		'type' => 'date',
/*		'modalities' => array(
			'2h' => ['default' => '1h to 2h', 'fr_FR' => '1h à 2h'],
			'half_day' => ['default' => '2h to 1/2day', 'fr_FR' => '2h à 1/2journée'],
			'full_day' => ['default' => '1 day', 'fr_FR' => '1 jour'],
			'plus1day' => ['default' => '> 1 day', 'fr_FR' => '> 1 jour'],
		),*/
		'labels' => array(
			'default' => 'Expected execution begin date',
			'fr_FR' => 'Date début d\'intervention souhaitée',
/*			'en_US' => 'Availability duration',
			'fr_FR' => 'Durée de disponibilité',*/
		),
	),
	
	'event/survey/property/property_6' => array(
		'definition' => 'inline',
		'type' => 'input',
/*		'modalities' => array(
			'2h' => ['default' => 'On timeslots of 1 to 2h', 'fr_FR' => 'Par créneaux de 1 à 2h'],
			'half_day_basis' => ['default' => 'On a half-day basis', 'fr_FR' => 'Par demi-journée'],
		),*/
		'labels' => array(
			'default' => 'Estimated contribution duration',
			'fr_FR' => 'Durée estimée de contribution',
/*			'en_US' => 'Availability frame',
			'fr_FR' => 'Plage de disponibilité',*/
		),
	),
	
	'event/survey/property/property_7' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Other frames',
			'fr_FR' => 'Autres plages',
		),
	),
	
	'event/survey/property/property_8' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'autonomy' => ['default' => 'Autonomous work', 'fr_FR' => 'Travail autonome'],
			'group' => ['default' => 'Group work', 'fr_FR' => 'Travail en groupe'],
		),
		'labels' => array(
			'en_US' => 'Autonomy vs group',
			'fr_FR' => 'Autonomie vs groupe',
		),
	),
	
	'event/survey/property/property_9' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Other intervention modes',
			'fr_FR' => 'Autres modes d\'intervention',
		),
	),
	
	'event/survey/property/property_10' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'current_week' => ['default' => '< 1 week', 'fr_FR' => '< 1 semaine'],
			'2weeks' => ['default' => 'Until 2 weeks', 'fr_FR' => 'A 2 semaines'],
			'1month' => ['default' => 'Until 1 month', 'fr_FR' => 'A 1 mois'],
		),
		'labels' => array(
			'en_US' => 'Visibility',
			'fr_FR' => 'Visibilité',
		),
	),
	
	'event/survey/property/property_11' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Other visibilities',
			'fr_FR' => 'Autres visibilités',
		),
	),
	
	'event/survey/property/property_12' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'search' => ['default' => 'By the demand', 'fr_FR' => 'Par la demande'],
			'being_found' => ['default' => 'By the offer', 'fr_FR' => 'Par l\'offre'],
		),
		'labels' => array(
			'en_US' => 'Matching modes',
			'fr_FR' => 'Modes de matching',
		),
	),
	
	'event/survey/property/property_13' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Other matching modes',
			'fr_FR' => 'Autres modes de matching',
		),
	),
	
	'event/survey/property/update_time' => array(
		'definition' => 'inline',
		'type' => 'datetime',
		'labels' => array(
			'en_US' => 'Update time',
			'fr_FR' => 'Heure de mise à jour',
		),
	),
	
	'event/survey' => array(
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
		'options' => [],
	),
	
	'event/search/survey' => array(
		'title' => array('default' => 'Answers to the survey', 'fr_FR' => 'Réponses au sondage'),
		'todoTitle' => array('default' => 'recent', 'fr_FR' => 'récents'),
		'searchTitle' => array('default' => 'search', 'fr_FR' => 'recherche'),
		'properties' => array(
			'place_id' => [],
			'n_fn' => [],
			'subcategory' => [],
		),
	),
	
	'event/list/survey' => array(
		'place_id' => [],
		'n_fn' => [],
		'subcategory' => [],
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
	),
	
	'event/detail/survey' => array(
		'title' => array('default' => 'Event detail', 'fr_FR' => 'Détail de l\'évènement'),
		'displayAudit' => true,
	),
	
	'event/update/survey' => array(
		'status' => ['mandatory' => true],
		'place_id' => [],
		'n_fn' => [],
		'subcategory' => [],
		'description' => [],
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
	),
	
	'event/export/survey' => array(
		'status' => 'A',
		'place_id' => 'B',
		'n_fn' => 'C',
		'subcategory' => 'D',
		'place_caption' => 'E',
		'property_1' => 'F',
		'property_2' => 'G',
		'property_3' => 'H',
		'property_4' => 'I',
		'property_5' => 'J',
		'property_6' => 'K',
		'property_7' => 'L',
		'property_8' => 'M',
		'property_9' => 'N',
		'property_10' => 'O',
		'property_11' => 'P',
		'property_12' => 'Q',
		'property_13' => 'R',
	),

	// Request

	'event/request/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('en_US' => 'New', 'fr_FR' => 'Nouveau'),
			'connected' => array('en_US' => 'Matching initiated', 'fr_FR' => 'Contact amorcé'),
			'realized' => array('en_US' => 'Realized', 'fr_FR' => 'Réalisé'),
			'completed' => array('en_US' => 'Completed', 'fr_FR' => 'Finalisé'),
			'canceled' => array('en_US' => 'Canceled', 'fr_FR' => 'Annulé'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
		'perspectives' => array(
			'generic' => array('new', 'connected', 'realized', 'completed', 'canceled'),
		),
	),
	
	'event/request/property/account_id' => array(
		'definition' => 'inline',
		'type' => 'select',
		'account_type' => 'pbc',
		'labels' => array(
			'en_US' => 'Owner account',
			'fr_FR' => 'Compte propriétaire',
		),
	),

	'event/request/property/account_status' => array(
		'definition' => 'core_account/pbc/property/status',
		'labels' => array(
			'en_US' => 'Account status',
			'fr_FR' => 'Statut du compte',
		),
	),
	
	'event/request/property/caption' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Request title',
			'fr_FR' => 'Titre de la demande',
		),
	),

	'event/request/property/matched_accounts' => array(
		'definition' => 'inline',
		'type' => 'multiselect',
		'account_type' => 'pbc',
		'labels' => array(
			'en_US' => 'Matched accounts',
			'fr_FR' => 'Comptes connectés',
		),
	),
	
	'event/request/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Skills',
			'fr_FR' => 'Compétences',
		),
	),
	
	'event/request/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'multiselect',
		'modalities' => ['definition' => 'matching/skills'],
		'labels' => array(
			'en_US' => 'Keyword skills',
			'fr_FR' => 'Compétences mot-clés',
		),
	),
	
	'event/request/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'information' => ['default' => 'Informations', 'fr_FR' => 'Informations'],
			'connecting' => ['default' => 'Connecting', 'fr_FR' => 'Mise en relation'],
			'expert_opinion' => ['default' => 'Expert d\'opinion', 'fr_FR' => 'Avis d’Expert'],
			'solution_building' => ['default' => 'Solution building', 'fr_FR' => 'Construction de solution'],
			'troubleshooting' => ['default' => 'Troubleshooting', 'fr_FR' => 'Dépannage'],
			'do_not_know' => ['default' => 'I don\'t know', 'fr_FR' => 'Je ne sais pas'],
		),
		'labels' => array(
			'en_US' => 'Request type',
			'fr_FR' => 'Type de demande',
		),
	),
	
	'event/request/property/property_4' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Targeted contributors',
			'fr_FR' => 'Contributeurs ciblés',
		),
	),
	
	'event/request/property/property_5' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'default' => 'Execution begin',
			'fr_FR' => 'Début d\'intervention',
		),
	),
	
	'event/request/property/property_6' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Estimated contribution duration',
			'fr_FR' => 'Durée estimée de contribution',
		),
	),
	
	'event/request/property/property_7' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Contribution location',
			'fr_FR' => 'Localisation de la contribution',
		),
	),
	
	'event/request/property/property_24' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'default' => 'Context and objectives',
			'fr_FR' => 'Contexte et objectifs',
		),
	),
	
	'event/request/property/property_25' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'default' => 'Description of the need',
			'fr_FR' => 'Détail du besoin',
		),
	),
	
	'event/request/property/property_26' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'default' => 'Other logistic constraints',
			'fr_FR' => 'Autres contraintes logistiques',
		),
	),
	
	'event/request/property/update_time' => array(
		'definition' => 'inline',
		'type' => 'datetime',
		'labels' => array(
			'en_US' => 'Update time',
			'fr_FR' => 'Heure de mise à jour',
		),
	),
	
	'event/request' => array(
		'statuses' => array(),
		'dimensions' => array(),
		'indicators' => array(),
		'properties' => array(
			'status', 'type', 'place_id', 'place_caption', 'account_id', 'n_fn', 'n_first', 'n_last', 'email', 'locale', 'category', 'subcategory', 'identifier', 'caption', 'description',
			'begin_date', 'end_date', 'day_of_week', 'day_of_month', 'exception_1', 'exception_2', 'exception_3', 'exception_4', 'begin_time', 'end_time', 'time_zone', 'location', 'latitude', 'longitude',
			'matched_accounts', 'matching_log', 'feedbacks', 'value', 'comments',
			'property_1', 'property_2', 'property_3', 'property_4', 'property_5', 'property_6', 'property_7',
			'property_24', 'property_25', 'property_26',
			'account_status', 'account_property_1', 'account_property_2', 'account_property_3', 'account_property_4', 'account_property_5', 'account_property_6', 'account_property_7', 'account_property_8', 'account_property_9', 'account_property_10', 'account_property_11', 'account_property_12', 'account_property_13', 'account_property_14', 'account_property_15', 'account_property_16',
			'update_time'
		),
		'options' => [],
	),
	
	'event/search/request' => array(
		'title' => array('default' => 'Requests', 'fr_FR' => 'Demandes'),
		'todoTitle' => array('default' => 'current', 'fr_FR' => 'en cours'),
		'searchTitle' => array('default' => 'search', 'fr_FR' => 'recherche'),
		'properties' => array(
			'status' => ['multiple' => true],
			'account_id' => [],
			'caption' => [],
			'property_2' => [],
			'property_5' => [],
			'matched_accounts' => [],
		),
	),
	
	'event/list/request' => array(
		'status' => [],
		'account_id' => [],
		'matched_accounts' => [],
		'update_time' => [],
		'caption' => [],
		'property_2' => [],
		'property_3' => [],
		'property_4' => [],
		'property_5' => [],
		'property_6' => [],
		'property_7' => [],
	),
	
	'event/update/request' => array(
		'status' => ['mandatory' => true],
		'account_id' => ['mandatory' => true],
		'caption' => [],
		'property_24' => [],
		'property_25' => [],
		'property_2' => [],
		'property_3' => [],
		'property_4' => [],
		'property_5' => [],
		'property_6' => [],
		'property_7' => [],
		'property_26' => [],
		'matched_accounts' => [],
	),
	
	'event/export/request' => array(
		'status' => 'A',
		'account_id' => 'B',
		'matched_accounts' => 'C',
		'caption' => 'D',
		'property_24' => 'E',
		'property_25' => 'F',
		'property_2' => 'G',
		'property_3' => 'H',
		'property_4' => 'I',
		'property_5' => 'J',
		'property_6' => 'K',
		'property_7' => 'L',
		'property_26' => 'M',
		'matching_log' => 'N',
		'feedbacks' => 'O',
	),
	
	// Course test

	'event/course_test/property/category' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'survey_profile' => ['default' => 'Profile file test', 'fr_FR' => 'Test fiche profil'],
			'test_request' => ['default' => 'Request test', 'fr_FR' => 'Test demande'],
		),
		'labels' => array(
			'en_US' => 'Category',
			'fr_FR' => 'Catégorie',
		),
	),
	
	'event/course_test/property/subcategory' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'steps' => ['default' => 'Steps', 'fr_FR' => 'Etapes'],
			'contributor' => ['default' => 'Contributor', 'fr_FR' => 'Contributeur'],
			'requestor' => ['default' => 'Requestor', 'fr_FR' => 'Demandeur'],
			'opinion' => ['default' => 'Opinion', 'fr_FR' => 'Avis'],
		),
		'labels' => array(
			'en_US' => 'Sub-category',
			'fr_FR' => 'Sous-catégorie',
		),
	),

	'event/course_test/property/caption' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Request title',
			'fr_FR' => 'Titre de la demande',
		),
	),
	
	'event/course_test/property/description' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'contributor' => ['default' => 'Contributor', 'fr_FR' => 'Contributeur'],
			'requestor' => ['default' => 'Requestor', 'fr_FR' => 'Demandeur'],
			'opinion' => ['default' => 'Opinion', 'fr_FR' => 'Avis'],
		),
		'labels' => array(
			'en_US' => 'Steps',
			'fr_FR' => 'Etapes',
		),
	),
	
	'event/course_test/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Skills',
			'fr_FR' => 'Compétences',
		),
	),
	
	'event/course_test/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'multiselect',
		'modalities' => ['definition' => 'matching/skills'],
		'labels' => array(
			'en_US' => 'Keyword skills',
			'fr_FR' => 'Compétences mot-clés',
		),
	),
	
	'event/course_test/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'information' => ['default' => 'Informations', 'fr_FR' => 'Informations'],
			'connecting' => ['default' => 'Connecting', 'fr_FR' => 'Mise en relation'],
			'expert_opinion' => ['default' => 'Expert d\'opinion', 'fr_FR' => 'Avis d’Expert'],
			'solution_building' => ['default' => 'Solution building', 'fr_FR' => 'Construction de solution'],
			'troubleshooting' => ['default' => 'Troubleshooting', 'fr_FR' => 'Dépannage'],
			'do_not_know' => ['default' => 'I don\'t know', 'fr_FR' => 'Je ne sais pas'],
		),
		'labels' => array(
			'en_US' => 'Request type',
			'fr_FR' => 'Type de demande',
		),
	),
	
	'event/course_test/property/property_4' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Targeted contributors',
			'fr_FR' => 'Contributeurs ciblés',
		),
	),
	
	'event/course_test/property/property_5' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'default' => 'Expected execution begin date',
			'fr_FR' => 'Date début d\'intervention souhaitée',
		),
	),
	
	'event/course_test/property/property_6' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Estimated contribution duration',
			'fr_FR' => 'Durée estimée de contribution',
		),
	),
	
	'event/course_test/property/property_7' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'default' => 'Contribution location',
			'fr_FR' => 'Localisation de la contribution',
		),
	),
	
	'event/course_test/property/property_16' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Service',
			'fr_FR' => 'Service',
		),
	),
	
	'event/course_test/property/property_17' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array(
			'en_US' => 'Location',
			'fr_FR' => 'Localisation',
		),
	),

	'event/course_test/property/property_20' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'extremely_easy' => ['default' => 'Extremely easy', 'fr_FR' => 'Extrêmement facile'],
			'very_easy' => ['default' => 'Very easy', 'fr_FR' => 'Très facile'],
			'rather_easy' => ['default' => 'Rather easy', 'fr_FR' => 'Plutôt facile'],
			'neutral' => ['default' => 'Neutral', 'fr_FR' => 'Neutre'],
			'rather_difficult' => ['default' => 'Rather difficult', 'fr_FR' => 'Assez difficile'],
			'very_difficult' => ['default' => 'Very difficult', 'fr_FR' => 'Très difficile'],
			'extremely_difficult' => ['default' => 'Extremely difficult', 'fr_FR' => 'Extrêmement difficile'],
		),
		'labels' => array(
			'en_US' => 'Opinion - Effort',
			'fr_FR' => 'Avis - Effort',
		),
	),
	
	'event/course_test/property/property_21' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Opinion - Effort (verbatim)',
			'fr_FR' => 'Avis - Effort (verbatim)',
		),
	),
	
	'event/course_test/property/property_22' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Opinion - Design',
			'fr_FR' => 'Avis - Design',
		),
	),
	
	'event/course_test/property/property_23' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'en_US' => 'Opinion - What is missing',
			'fr_FR' => 'Avis - Ce qui manque',
		),
	),

	'event/course_test/property/property_24' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'default' => 'Context and objectives',
			'fr_FR' => 'Contexte et objectifs',
		),
	),

	'event/course_test/property/property_25' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'default' => 'Description of the need',
			'fr_FR' => 'Détail du besoin',
		),
	),

	'event/course_test/property/property_26' => array(
		'definition' => 'inline',
		'type' => 'textarea',
		'labels' => array(
			'default' => 'Other logistic constraints',
			'fr_FR' => 'Autres contraintes logistiques',
		),
	),
	
	'event/course_test/property/update_time' => array(
		'definition' => 'inline',
		'type' => 'datetime',
		'labels' => array(
			'en_US' => 'Update time',
			'fr_FR' => 'Heure de mise à jour',
		),
	),
	
	'event/course_test' => array(
		'statuses' => array(),
		'dimensions' => array(),
		'indicators' => array(),
		'properties' => array(
			'status', 'type', 'place_id', 'place_caption', 'account_id', 'n_fn', 'n_first', 'n_last', 'email', 'category', 'subcategory', 'identifier', 'caption', 'description',
			'begin_date', 'end_date', 'day_of_week', 'day_of_month', 'exception_1', 'exception_2', 'exception_3', 'exception_4', 'begin_time', 'end_time', 'time_zone', 'location', 'latitude', 'longitude',
			'value', 'comments',
			'property_1', 'property_2', 'property_3', 'property_4', 'property_5', 'property_6', 'property_7', 'property_8', 'property_9', 'property_10',
			'property_11', 'property_12', 'property_13', 'property_14', 'property_15', 'property_16', 'property_17', 'property_18', 'property_19', 'property_20',
			'property_21', 'property_22', 'property_23', 'property_24', 'property_25', 'property_26', 'property_27', 'property_28', 'property_29', 'property_30',
			'account_property_1', 'account_property_2', 'account_property_3', 'account_property_4', 'account_property_5', 'account_property_6', 'account_property_7', 'account_property_8', 'account_property_9', 'account_property_10', 'account_property_11', 'account_property_12', 'account_property_13', 'account_property_14', 'account_property_15', 'account_property_16', 
			'update_time'
		),
		'options' => [],
	),
	
	'event/search/course_test' => array(
		'title' => array('default' => 'Answers to the survey', 'fr_FR' => 'Réponses au sondage'),
		'todoTitle' => array('default' => 'recent', 'fr_FR' => 'récents'),
		'searchTitle' => array('default' => 'search', 'fr_FR' => 'recherche'),
		'properties' => array(
			'status' => [],
			'email' => [],
			'category' => [],
			'subcategory' => [],
		),
	),
	
	'event/list/course_test' => array(
		'status' => [],
		'email' => [],
		'category' => [],
		'subcategory' => [],
		'update_time' => [],
		'property_1' => [],
		'property_2' => [],
		'property_3' => [],
		'property_16' => [],
		'property_17' => [],
	),
	
	'event/update/course_test' => array(
		'status' => ['mandatory' => true],
		'email' => [],
		'subcategory' => [],
		'description' => [],
		'caption' => [],
		'property_1' => [],
		'property_2' => [],
		'property_3' => [],
		'property_24' => [],
		'property_4' => [],
		'property_25' => [],
		'property_5' => [],
		'property_6' => [],
		'property_7' => [],
		'property_26' => [],
		'property_16' => [],
		'property_17' => [],
		'property_21' => [],
		'property_22' => [],
		'property_23' => [],
	),
	
	'event/export/course_test' => array(
		'status' => 'A',
		'email' => 'B',
		'subcategory' => 'C',
		'place_caption' => 'D',
		'caption' => 'E',
		'property_1' => 'F',
		'property_2' => 'G',
		'property_3' => 'H',
		'property_24' => 'I',
		'property_4' => 'J',
		'property_25' => 'K',
		'property_5' => 'L',
		'property_6' => 'M',
		'property_7' => 'N',
		'property_26' => 'O',
		'property_16' => 'P',
		'property_17' => 'Q',
		'property_21' => 'R',
		'property_22' => 'S',
		'property_23' => 'T',
	),
	
	'customization/pbc/send-message/theme_1' => '
<html>
	<style>
        @font-face {
        font-family: "League Gothic";
        src: url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.eot");
        src: url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.eot?#iefix") format("embedded-opentype"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff2") format("woff2"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff") format("woff"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.ttf") format("truetype"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.svg#league_gothicregular") format("svg");
        font-weight: normal;
        font-style: normal;
    }
	
    @media only screen and (max-width: 480px) {
        @font-face {
            font-family: "League Gothic";
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff2") format("woff2"),
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff") format("woff"),
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.ttf") format("truetype"),
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.svg#league_gothicregular") format("svg");
            font-weight: normal;
            font-style: normal;
        }
	
</style>
<table border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td>
                <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" style="font-family: arial, helvetica, sans-serif;" width="760">
                    <tbody>
                        <tr>
                            <td width="40">&nbsp;</td>
                            <td width="680">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" valign="top" width="680">
                                    <tbody>
                                        <tr>
                                            <td colspan="3" height="40">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" bgcolor="#ffffff" valign="top" width="40">&nbsp;</td>
                                            <td bgcolor="#ffffff" valign="top" width="600">
                                                <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600">
                                                    <tbody>
                                                        <tr>
                                                            <td style="line-height:24px;text-align:justify;font-size:16px; font-family: Georgia, Times New Roman, Times, serif; color:rgb(45,40,70);">
																%s
																%s
															</td>
                                                        </tr>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td align="right" bgcolor="#ffffff" valign="top" width="38">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="10">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" bgcolor="#eeeeee" colspan="3" height="15" width="682"><font style="color: rgb(51, 51, 51); font-family: arial, sans-serif; font-size: 10px; font-weight: normal;">Optimized by <a href="https://www.flowux.io">Flow UX</a></font></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="10">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="40">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
                <p>&nbsp;</p>
            </td>
        </tr>
    </tbody>
</table>
</html>
',
	
	'customization/pbc/send-message/theme_2' => '<html>%s %s</html>',
);
