<?php

return array(
	'specifications' => array(
			'credit' => array(
					'unlimitedCredits' => true,
			),
			'styleSheet' => array(
					'navbar' => 'navbar-default navbar-fixed-top',
					'panelHeadingBackground' => '#006179',
					'panelHeadingColor' => '#FFFFFF',
			),
			'bootstrap4' => array(
				'logo' => 'PBC-logo-web-fleur.png',
				'logo-height' => '40',
			),
			'headerParams' => array(
				'background-color' => '#79CCF3',
				'shift' => 0,
					'anchor' => array(
							'type' => 'route',
							'route' => 'public/home',
							'params' => [],
					),
					'logo' => 'PBC-logo-fleur-texte.png',
					'logo-height' => 40,
					'logo-href' => 'https://www.probonocorpo.com',
					'signature' => null,
					'advert' => 'probonocorpo.png',
					'advert-width' => 40,
				'self-powered' => true,
			),

		'menus/synapps' => array(
			'entries' => array(
				'contact' => array(
					'route' => 'account/indexAlt',
					'params' => array('entry' => 'contact', 'type' => 'pbc', 'app' => 'synapps'),
					'glyphicon' => 'glyphicon-user',
					'label' => array(
						'default' => 'All the contacts',
						'fr_FR' => 'Tous contacts',
					),
				),
				'account' => array(
					'route' => 'account/indexAlt',
					'params' => array('entry' => 'account', 'type' => 'pbc', 'app' => 'synapps'),
					'glyphicon' => 'glyphicon-user',
					'label' => array(
						'default' => 'Active',
						'fr_FR' => 'Actifs',
					),
				),
				'group' => array(
					'route' => 'account/indexAlt',
					'params' => array('entry' => 'group', 'type' => 'group', 'app' => 'synapps'),
					'glyphicon' => 'glyphicon-user',
					'label' => array(
						'default' => 'Groups',
						'fr_FR' => 'Groupes',
					),
				),
				'request' => array(
					'route' => 'event/indexAlt',
					'params' => array('type' => 'request', 'category' => 'request', 'app' => 'synapps'),
					'label' => array(
						'en_US' => 'Requests',
						'fr_FR' => 'Demandes',
					),
				),
				'survey_profile' => array(
					'route' => 'event/indexAlt',
					'params' => array('type' => 'course_test', 'category' => 'survey_profile', 'app' => 'synapps'),
					'label' => array(
						'en_US' => 'Course tests',
						'fr_FR' => 'Tests parcours',
					),
				),
				'ux_design' => array(
					'route' => 'event/indexAlt',
					'params' => array('type' => 'survey', 'category' => 'ux_design', 'app' => 'synapps'),
					'label' => array(
						'en_US' => 'Interviews',
						'fr_FR' => 'Interviews',
					),
				),
				'email' => array(
					'route' => 'event/index',
					'params' => array('type' => 'email', 'app' => 'synapps'),
					'label' => array(
						'en_US' => 'Emails',
						'fr_FR' => 'Emails',
					),
				),
			),
			'labels' => array(
				'default' => 'Back-office',
			),
		),
		
		'manageable_roles' => ['admin', 'sales_manager'],
		
		'landing_account_type' => 'pbc',
		
		'core_account/generic/property/property_1' => array(
			'mandatory' => false,
			'definition' => 'inline',
			'type' => 'select',
			'multiple' => true,
			'modalities' => array(
				'contributor' => ['default' => 'Contributor', 'fr_FR' => 'Contributeur'],
				'requestor' => ['default' => 'Requestor', 'fr_FR' => 'Demandeur'],
			),
			'labels' => ['default' => 'Role', 'fr_FR' => 'Rôle'],
		),

		'core_account/generic/property/property_2' => array(
			'mandatory' => false,
			'definition' => 'inline',
			'type' => 'select',
			'multiple' => true,
			'modalities' => array(
				'available' => ['default' => 'Available for a phone call', 'fr_FR' => 'Disponible pour un point téléphonique'],
			),
			'labels' => ['default' => 'Availability', 'fr_FR' => 'Disponibilité'],
		),
		
		'core_account/generic/property/comment_1' => array(
			'definition' => 'inline',
			'type' => 'textarea',
			'labels' => array(
				'default' => 'Offered skills',
				'fr_FR' => 'Compétences offertes',
			),
			'max_length' => 65535,
		),
		
		'core_account/generic/property/comment_2' => array(
			'definition' => 'inline',
			'type' => 'textarea',
			'labels' => array(
				'default' => 'Requested skills',
				'fr_FR' => 'Compétences demandées',
			),
			'max_length' => 65535,
		),
		
		'core_account/sendMessage' => array(
/*			'templates' => array(
				'emailing_survey_isc' => array('definition' => 'emailing_isc/probonocorpo'),
				'emailing_survey_intrapreneurs' => array('definition' => 'emailing_intrapreneurs/probonocorpo'),
				'emailing_reminder_suspects' => array('definition' => 'emailing_reminder_suspects/probonocorpo'),
				'emailing_adopt1projet' => array('definition' => 'emailing_adopt1projet/probonocorpo'),
			),*/
			'themes' => array(
				'theme_1' => array('definition' => 'customization/pbc/send-message/theme_1'),
				'theme_2' => array('definition' => 'customization/pbc/send-message/theme_2'),
			),
			'signature' => array(
				'definition' => 'inline',
				'body' => array(
					'default' => '
<hr>
<div><a href="https://www.probonocorpo.com"><img src="http://img.probonocorpo.com/PBC-logo-fleur-texte.png" width="300" height="79" alt="Probono corpo logo" /></a></div>
<br />The <strong>Probono corpo</strong> team
<br />Bruno, Daniel, Gis&egrave;le, Nicole
<br /><a href="https://sbc.safe.socgen/groups/pro-bono-corpo">go/probono</a>
<br /><a href="mailto:probonocorpo.par@socgen.com">probonocorpo.par@socgen.com</a>
					',
					'fr_FR' => '
<hr>
<div><a href="https://www.probonocorpo.com"><img src="http://img.probonocorpo.com/PBC-logo-fleur-texte.png" width="300" height="79" alt="Probono corpo logo" /></a></div>
<br />L\'&Eacute;quipe <strong>Probono corpo</strong>
<br />Bruno, Daniel, Gis&egrave;le, Nicole
<br /><a href="https://sbc.safe.socgen/groups/pro-bono-corpo">go/probono</a>
<br /><a href="mailto:probonocorpo.par@socgen.com">probonocorpo.par@socgen.com</a>
',
					),
			),
		),

		'event/event/property/matched_accounts' => array(
			'definition' => 'inline',
			'type' => 'multiselect',
			'account_type' => 'pbc',
			'labels' => array(
				'en_US' => 'Matched accounts',
				'fr_FR' => 'Comptes connectés',
			),
		),
		
		'flow/tests' => array(
			'test_request' => 'test_request/probonocorpo',
			'survey_profile' => 'survey_profile/probonocorpo',
		),
		
		'mailTo' => 'contact@probonocorpo.com', // Deprecated
/*		'core_account/mailTo' => array( // Overrides the target emails if not NULL (for test purposes or manual email sending)
			'contact@probonocorpo.com' => 'ProBonoCorpo',
			'probonocorpo.par@socgen.com' => 'ProBonoCorpo',
		),*/
	),
);
