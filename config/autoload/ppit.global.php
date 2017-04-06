<?php
use Zend\Session\Container;

return array(
	'defaultRoute' => 'interaction',
	'mailProtocol' => 'Sendmail', // Should be 'SendMail' or 'Smtp'. No email sent if null
	'mailAdmin' => 'postmaster@p-pit.fr',
	'nameAdmin' => 'P-PIT',
	'mailTo' => null, // Overrides the real email if not NULL (for test purposes)
	'isTraceActive' => true,
	'isDemoModeActive' => true,
	'isDemoAccountUpdatable' => false,
	'documentPart/currentEthicalCharter' => array('id' => 1726),
	'documentPart/currentTerms' => array('id' => 1725),
    'ppitModules' => array(
    	'PpitCore',
    ),
	'ppitAccountingSettings' => array(
			'accountingFolderId' => 327,
	),
	'Ugap' => array(
			'OSIReceivedDirectory' => 'data/OSI/received/',
			'OSISavedDirectory' => 'data/OSI/saved/',
	),
	'ppitCommitment/P-Pit' => array(
			'commitmentListMessage' => array(
					'user' => 'postmaster@p-pit.fr',
					'url' => 'http://localhost/~bruno/p-pit.fr/public/commitment-message/commitment-list',
			),
			'commitmentGetMessage' => array(
					'user' => 'postmaster@p-pit.fr',
					'url' => 'http://localhost/~bruno/p-pit.fr/public/commitment-message/commitment-get',
			),
			'commitmentPostMessage' => array(
					'user' => 'postmaster@p-pit.fr',
					'url' => 'http://localhost/~bruno/p-pit.fr/public/commitment-message/commitment-post',
			),
			'invoiceGetMessage' => array(
					'user' => 'postmaster@p-pit.fr',
					'url' => 'http://localhost/~bruno/p-pit.fr/public/commitment-message/invoice-get',
			),
			'xmlUblInvoiceMessage' => array(
				'edi' => 'ugap',
				'user' => 'P-Pit',
				'url' => 'http://localhost/~bruno/p-pit.fr/public/commitment-response/test-receive',
			),
	),
	'ppit-payment' => array(
		'pathfile' => '/var/webaffaires/payment/param/pathfile',
		'path_bin' => '/var/webaffaires/payment/bin/glibc-2.5-42/',
	),
	'ppitContactSettings' =>array(
		'photoRootLink' => 5,
		'smsCost' => 0.1,
		'roles' => array(
				'accountant' => array(
						'labels' => array(
								'en_US' => 'Accountant',
								'fr_FR' => 'Comptable',
						),
				),
				'admin' => array(
						'labels' => array(
								'en_US' => 'Administrator',
								'fr_FR' => 'Administrateur',
						),
				),
				'accountant' => array(
						'labels' => array(
								'en_US' => 'Accountant',
								'fr_FR' => 'Comptable',
						),
				),
				'business_owner' => array(
						'labels' => array(
								'en_US' => 'Business owner',
								'fr_FR' => 'Responsable opérationnel',
						),
				),
				'sales_manager' => array(
						'labels' => array(
								'en_US' => 'Sales manager',
								'fr_FR' => 'Responsable commercial',
						),
				),
		),
		'functions' => array(
				'order' => 'Order',
		),
	),

	'ppitCoreSettings' => array(
		'defaultInstanceId' => 1,
		'defaultPlaceId' => 4,
		'appName' => 'p-pit',
		'domainName' => 'localhost', // Used in links inside emails
		'isTraceActive' => true,
		'maxUploadSize' => 2048000,
		'compressGifPngToJpg' => false,
		'formExpiration' => 1800, // Duration in seconds of a form validity (against CSRF)
		'specificationMode' => 'database', // 'config' vs 'database'
	),

	'ppitMasterDataSettings' => array(
		'defaultOrgUnitTypes' => array('Direction', 'Division', 'Site'),

		'defaultRoles' => array(
				'accountant' => 'Accountant',
				'admin' => 'Administrator',
				'agent' => 'Agent',
				'approver' => 'Approver',
				'operational-responsible' => 'Operational responsible',
				'responsible' => 'Responsible',
		),

		'defaultProductProperties' => array(
				'country' => array(
						'type' => 'select',
						'label' => 'Country',
						'modalities' => array('France'),
				),
		),
		'placeProperties' => array(
				'nb_people' => array(
						'type' => 'number',
						'label' => 'Nb people',
						'min' => 0,
						'max' => 99999,
				),
				'surface' => array(
						'type' => 'number',
						'label' => 'Surface',
						'min' => 0,
						'max' => 99999,
				),
				'nb_floors' => array(
						'type' => 'number',
						'label' => 'Nb floors',
						'min' => 0,
						'max' => 999,
				),
				'opening_hours' => array(
						'type' => 'input',
						'label' => 'Opening hours',
				),
				'parking' => array(
						'type' => 'input',
						'label' => 'Parking',
				),
				'delivery_access' => array(
						'type' => 'textarea',
						'label' => 'Accès livraison',
						'maxLength' => 2047,
				),
				'logistic_constraints' => array(
						'type' => 'textarea',
						'label' => 'Logistic constraints',
						'maxLength' => 2047,
				),
		),
		'taxRegimes' => array(
				'1' => 'France métropolitaine',
		),
/*		'priceCategories' => array(
				'1' => 'France métropolitaine',
				'2' => 'Martinique',
				'3' => 'Mayotte',
				'4' => 'Guyane',
				'5' => 'Réunion',
		),
		'vatRates' => array(
				'1' => '0.2',
				'2' => '0.085',
				'3' => '0',
				'4' => '0',
				'5' => '0.085',
		)*/
	),
	'ppitOrderSettings' => array(
			'recyclingMode' => true,
			'defaultDeliverySla' => 1,
			'retractionTimeLimit' => 7, // In calendar days
			'responseMessage' => array(
				'edi' => 'ugap',
				'user' => 'XEROX',
				'url' => 'http://localhost/~bruno/p-pit.fr/public/order-response/test-receive',
			),
			'shipmentMessage' => array(
				'edi' => 'ugap',
				'user' => 'XEROX',
				'url' => 'http://localhost/~bruno/p-pit.fr/public/order-response/test-receive',
			),
			'invoiceMessage' => array(
				'edi' => 'ugap',
				'user' => 'XEROX',
				'url' => 'http://localhost/~bruno/p-pit.fr/public/order-response/test-receive',
			),
	),
	'ppitEquipmentSettings' => array(
		'areaProperties' => array(
				'nb_people' => array(
						'type' => 'number',
						'label' => 'Nb people',
						'min' => 0,
						'max' => 99999,
				),
				'surface' => array(
						'type' => 'number',
						'label' => 'Surface',
						'min' => 0,
						'max' => 99999,
				),
				'front_back' => array(
						'type' => 'checkbox',
						'label' => 'Front office',
						'min' => 0,
						'max' => 999,
				),
				'consumption_1' => array(
						'type' => 'number',
						'label' => 'Consumption 1',
						'min' => 0,
						'max' => 999999,
				),
				'consumption_2' => array(
						'type' => 'number',
						'label' => 'Consumption 2',
						'min' => 0,
						'max' => 999999,
				),
		),
	),

	'ppitSalesSettings' => array(
			'defaultHomePageLayout' => array(
					'contact_us',
					'jumbotron',
					'front_product_1',
					'legal_notices',
			),
	),

	'ppitStudies' => array(
			'sportOption' => true,
			'currentSchoolYear' => 2015,
			'preferedProperties' => array(
	    			'n_fn' => null,
	    			'center_name' => null,
	    			'school_year' => null,
	    			'emergency_phone_1' => null,
	    			'emergency_phone_2' => null,
	    			'emergency_email' => null,
	    			'sport' => null,
	    			'category' => null,
	    			'class' => null,
	    			'specialty' => null,
	    			'boarding_school' => null,
	    			'adr_city' => null,
	    			'adr_state' => null,
	    			'adr_country' => null,
	    			'sex' => null,
			    	'birth_date' => null,
	    	),
			'sports' => array(
					'basketball' => array(
							'labels' =>array(
									'en_US' => 'Basketball',
									'fr_FR' => 'Basketball',
							),
					),
					'football' => array(
							'labels' =>array(
									'en_US' => 'Soccer',
									'fr_FR' => 'Football',
							),
					),
					'golf' => array(
							'labels' =>array(
									'en_US' => 'Golf',
									'fr_FR' => 'Golf',
							),
					),
					'horse-riding' => array(
							'labels' =>array(
									'en_US' => 'Horse-riding',
									'fr_FR' => 'Equitation',
							),
					),
					'tennis' => array(
							'labels' =>array(
									'en_US' => 'Tennis',
									'fr_FR' => 'Tennis',
							),
					),
			),
			'status' => array(
					'new' => array(
							'labels' => array(
									'en_US' => 'New',
									'fr_FR' => 'Nouveau',
							)
					),
					'confirmed' => array(
							'labels' => array(
									'en_US' => 'Confirmed',
									'fr_FR' => 'Confirmé',
							)
					),
					'registered' => array(
							'labels' => array(
									'en_US' => 'Registered',
									'fr_FR' => 'Enregistré',
							)
					),
			),
			'classes' => array('6e', '5e', '4e', '3e', '2nde', '1ère', 'Term.'),
			'specialties' => array('S', 'ES', 'STMG'),
			'boardingSchool' => array('Interne', 'Externe', 'Week-end', 'Dimanche'),
			'maritalStatuses' => array('mariés', 'divorcés', 'séparés', 'célibataire', 'veuf/veuve'),
	),

	'ppitSupportSettings' => array(
			'recyclingMode' => true,
			'incidentTypes' => array(
					'production' => array(
							'labels' => array(
									'en_US' => 'Production',
									'fr_FR' => 'Production',
							)
					),
			),
			'incidentActions' => array(
					'manage' => array(
						'responsible' => array('business_owner'),
						'currentStatuses' => array('new'),
						'nextStatus' => 'managed',
						'labels' => array(
							'en_US' => 'Manage',
							'fr_FR' => 'Prendre en compte',
						),
					),
					'reject' => array(
						'responsible' => array('business_owner'),
						'currentStatuses' => array('new'),
						'nextStatus' => 'rejected',
						'labels' => array(
							'en_US' => 'Reject',
							'fr_FR' => 'Rejeter',
						),
					),
					'complete' => array(
						'responsible' => array('helpdesk'),
						'currentStatuses' => array('pending'),
						'nextStatus' => 'managed',
						'labels' => array(
							'en_US' => 'Complete',
							'fr_FR' => 'Compléter',
						),
					),
					'close' => array(
						'responsible' => array('helpdesk'),
						'currentStatuses' => array('new', 'managed', 'rejected', 'pending'),
						'nextStatus' => 'closed',
						'labels' => array(
							'en_US' => 'Close',
							'fr_FR' => 'Clôturer',
						),
					),
			),
			'incidentStatus' => array(
					'new' => array(
							'labels' => array(
									'en_US' => 'New',
									'fr_FR' => 'Nouveau',
							),
							'responsible' => array('business_owner'),
							'actionText' => array(
									'en_US' => 'Hello,
We inform you that an incident have been submitted and requires your care. To proceed, please follow this links: %s.
',
									'fr_FR' => 'Bonjour,
Nous vous informons qu\'unincident a été émis et doit être pris en charge. Pour ce faire, veuillez suivre ce lien: %s.
',
							),
							'informed' => array('helpdesk'),
							'actionTitle' => array(
									'en_US' => 'New incident',
									'fr_FR' => 'Nouvel incident',
							),
					),
					'managed' => array(
							'labels' => array(
									'en_US' => 'Managed',
									'fr_FR' => 'Pris en charge',
							),
							'informed' => array('helpdesk'),
							'informationTitle' => array(
									'en_US' => 'Managed incident',
									'fr_FR' => 'Incident pris en charge',
							),
							'informationText' => array(
									'en_US' => 'Hello,
We inform you that the management of the incident with reference %s is effective. For more details, please follow this links: %s.
',
									'fr_FR' => 'Bonjour,
Nous vous informons que l\'incident référencé sous le code %s est effectivement pris en charge. Pour plus de détails, veuillez suivre ce lien: %s.
',
							),
					),
					'rejected' => array(
							'labels' => array(
									'en_US' => 'Rejected',
									'fr_FR' => 'Rejeté',
							),
							'informed' => array('helpdesk'),
							'informationTitle' => array(
									'en_US' => 'Rejected incident',
									'fr_FR' => 'Incident rejeté',
							),
							'informationText' => array(
									'en_US' => 'Hello,
We inform you that the incident with reference %s has been rejected. For more details, please follow this links: %s.
',
									'fr_FR' => 'Bonjour,
Nous vous informons que l\'incident référence sous le code %s a été rejeté. Pour plus de détails, veuillez suivre ce lien: %s.
',
							),
					),
					'pending' => array(
							'labels' => array(
									'en_US' => 'Pending',
									'fr_FR' => 'En suspens',
							),
							'responsible' => array('helpdesk'),
							'actionTitle' => array(
									'en_US' => 'Pending incident',
									'fr_FR' => 'Incident en suspens',
							),
							'actionText' => array(
									'en_US' => 'Hello,
Your action is required about the incident with reference %s which is pending. For more details, please follow this links: %s.
',
									'fr_FR' => 'Bonjour,
Une action de votre part est demandée concernant l\'incident référencé sous le code %s, en suspens. Pour plus de détails, veuillez suivre ce lien: %s.
',
							),
					),
					'solved' => array(
							'labels' => array(
									'en_US' => 'Solved',
									'fr_FR' => 'Solutionné',
							),
							'responsible' => array('helpdesk'),
							'actionTitle' => array(
									'en_US' => 'Solved incident',
									'fr_FR' => 'Incident résolu',
							),
							'actionText' => array(
									'en_US' => 'Hello,
We inform you that the incident with reference %s has been solved. Your confirmation of return to normal is required. To proceed, please follow this links: %s.
',
									'fr_FR' => 'Bonjour,
Nous vous informons que l\'incident référencé sous le code %s a été résolu. Votre confirmation d\'un retour à la normale est requise. Pour ce faire, veuillez suivre ce lien: %s.
',
							),
					),
					'closed' => array(
							'labels' => array(
									'en_US' => 'Closed',
									'fr_FR' => 'Clôturé',
							),
							'informed' => array('business_owner', 'helpdesk'),
							'informationTitle' => array(
									'en_US' => 'Closed incident',
									'fr_FR' => 'Incident Clôturé',
							),
							'informationText' => array(
									'en_US' => 'Hello,
We inform you that the incident with reference %s has been closed. For more details, please follow this links: %s.
',
									'fr_FR' => 'Bonjour,
Nous vous informons que l\'incident référencé sous le code %s a été clôturé. Pour plus de détails, veuillez suivre ce lien: %s.
',
							),
					),
			),
			'incidentProperties' => array(
			),
			'incidentImportMaxRows' => 100,
			'incidentImportProperties' => array(
			),
			'supportInboxServer' => '{smtp.2pit.io:993/imap/ssl}',
			'supportInboxUser' => 'support@p-pit.fr',
			'supportInboxPassword' => '?',
	),

	'ppitUserSettings' => array(
		'securityAgent' => new \PpitUser\Model\SecurityAgent,
		'strongPassword' => false,
		'tokenValidity' => 5,
		'locales' => array('fr_FR' => 'fr_FR'),
		'checkAcl' => true,
		'safe' => array(
				'p-pit' => array(
					'ugapprod' => 'ugapprod',
					'ugaptest' => 'ugaptest',
					'postmaster@p-pit.fr' => 'Admin2016',
				),
				'P-Pit' => array(
					'ugapprod' => 'ugapprod',
					'ugaptest' => 'ugaptest',
					'postmaster@p-pit.fr' => 'Admin2016',
				),
				'FM Sports' => array(
					'postmaster@fmsportetudes.net' => 'Admin2016',	
				),
				'ugap' => array(
					'XEROX' => 'ugap_XEROX',
					'P-Pit' => 'ugap_P-Pit',
				),
		),
		'roleTree' => array(
			'guest' => array(),
			'user' => array('guest'),
			'responsible' => array('user'),
			'accountant' => array('user'),
			'approver' => array('user'),
			'admin' => array('user'),
			'trainer' => array('user'),
		),
		'roles' => array(
			'guest' => 'Guest',
			'user' => 'User',
			'accountant' => 'Accountant',
			'admin' => 'Administrator',
			'approver' => 'Approver',
			'trainer' => 'Trainer',
		),

		// Deprecated
		'community_roles' => array(
			'admin' => 'Administrator',
/*			'customer_admin' => 'Customer administrator',
			'customer_approver' => 'Customer approver',
			'customer_responsible' => 'Customer responsible',*/
		),
		'aclReturnRoutes' => array(
				'Customer site' => 'customerSite/index',
				'Customer division' => 'customerDivision/index',
		),
		'messages' => array(
				'addTitle' => array(
						'en_US' => 'Your credentials',
						'fr_FR' => 'Vos identifiants',
				),
				'addText' => array(
						'en_US' => 'Welcome in P-PIT portal,
In order to set your password for your identifier: %s, please follow this link: %s',
						'fr_FR' => 'Bienvenue sur le portail P-PIT,
Afin de définir votre mot de passe pour votre identifiant : %s, veuillez suivre ce lien : %s',
				),
				'passwordChangedTitle' => array(
						'en_US' => 'Password changed',
						'fr_FR' => 'Mot de passe modifié',
				),
				'passwordChangedText' => array(
						'en_US' => 'Please note that your password for access to P-PIT portal have been modified. If you did not make this action, please contact your administrator.',
						'fr_FR' => 'Veuillez noter que votre mot de passe d\'accès au portail P-PIT a été modifié. Si ce n\'est pas vous qui avez modifié votre mot de passe, veuillez contacter votre administrateur.',
				)
		),
	),
);