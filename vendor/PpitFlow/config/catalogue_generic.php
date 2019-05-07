<?php
	
define('CATALOGUE_GENERIC', [
	'header' => array(
		'title' => ['default' => '2pit.io - Catalogue', 'fr_FR' => '2pit.io - Catalogue'],
		'description' => ['default' => 'Subscribe online to the 2pit.io products', 'fr_FR' => 'Souscrivez en ligne au produits 2pit.io'],
		'style' => array(
			'navbar' => 'background-color: transparent;',
			'topNavCollapse' => 'background-color: #ffffff;',
		),
		'navbar' => array(
			'class' => 'navbar navbar-expand-lg fixed-top scrolling-navbar navbar-black',
			'account' => true,
			'collapse' => false,
		),
		'logo' => array(
			'href' => 'home',
			'params' => [],
			'src' => '/logos/2pit.io/carre80.png',
			'height' => 40,
			'alt' => '2pit.io logo',
		),
		'intro_height' => '65%',
		'background_image' => array(
			'mask' => null,
			'src' => ['default' => '/img/2pit.io/notes-coul.png'],
			'class' => 'img-fluid',
			'alt' => 'A coffee and the bill',
		),
	),

	'card' => [
		'properties' => [
			'logo' => ['definition' => 'inline', 'type' => 'img', 'labels' => ['default' => '']],
			'description' => ['definition' => 'inline', 'type' => 'html', 'labels' => ['default' => 'Module', 'fr_FR' => 'Module']],
			'space' => ['type' => 'space', 'labels' => ['default' => '']],
			'solo' => ['definition' => 'inline', 'type' => 'number', 'labels' => ['default' => 'Solo', 'fr_FR' => 'Solo'], 'format' => ['default' => '%s € /month', 'fr_FR' => '%s € /mois']],
			'team' => ['definition' => 'inline', 'type' => 'number', 'labels' => ['default' => 'Team', 'fr_FR' => 'Équipe'], 'format' => ['default' => '%s € /month', 'fr_FR' => '%s € /mois']],
		],
	],
	
	'inputs' => [
		'solo' => ['definition' => 'inline', 'type' => 'radio', 'propertyId' => 'size', 'value' => 0, 'attributes' => 'checked', 'labels' => ['default' => 'Solo'], 'fr_FR' => 'Solo'],
		'team' => ['definition' => 'inline', 'type' => 'radio', 'propertyId' => 'size', 'value' => 1, 'labels' => ['default' => 'Team', 'fr_FR' => 'Équipe']],
		'annual' => ['definition' => 'inline', 'type' => 'radio', 'propertyId' => 'periodicity', 'value' => 0, 'attributes' => 'checked', 'labels' => ['default' => 'Annual subscription'], 'fr_FR' => 'Souscription annuelle'],
		'monthly' => ['definition' => 'inline', 'type' => 'radio', 'propertyId' => 'periodicity', 'value' => 1, 'labels' => ['default' => 'Monthly subscription', 'fr_FR' => 'Souscription mensuelle']],
		'p_pit_learning' => ['definition' => 'inline', 'type' => 'product', 'variants' => ['p_pit_learning_solo_annual', 'p_pit_learning_solo_monthly', 'p_pit_learning_team_annual', 'p_pit_learning_team_monthly'], 'labels' => ['default' => 'Number of subscribed periods : %s €', 'fr_FR' => 'Nombre de périodes souscrites : %s €']],
		'p_pit_turnkey' => ['definition' => 'inline', 'type' => 'product', 'variants' => ['p_pit_turnkey_annual', 'p_pit_turnkey_monthly'], 'labels' => ['default' => 'Number of subscribed periods : %s €', 'fr_FR' => 'Nmbre de périodes souscrites : %s €']],
	],
	
	'products' => [
		'p_pit_learning' =>[
			
			'card' => [
				'logo' => ['src' => ['default' => '/img/p-pit/learning-petit.jpg'], 'alt' => ['default' => 'Image P-Pit Learning'], 'class' => 'img-fluid z-depth-0'],
				'description' => ['default' => '
<h5 class="mt-3">
	<strong>P-Pit Learning</strong>
</h5>
<p class="text-muted">
	Visual and audio learning content<br>
	Evaluation tests
</p>
				'],
				'solo' => 15,
				'team' => 50,
			],
	
			'form' => [
				'introduction' => [
					'default' => '<p class="text-center">Discounts applies automatically at payment stage according to the number of subscribed modules.</p>',
					'fr_FR' => '<p class="text-center">Des réductions s’appliquent automatiquement au moment du paiement en fonction du nombre de modules souscrits.</p>',
				],
				'rows' => [
					[
						'class' => 'row mb-3',
						'cols' => [
							['class' => 'col-md-6', 'inputId' => 'solo'],
							['class' => 'col-md-6', 'inputId' => 'team'],
						],
					],
					[
						'class' => 'row mb-3',
						'cols' => [
							['class' => 'col-md-6', 'inputId' => 'annual'],
							['class' => 'col-md-6', 'inputId' => 'monthly'],
						],
					],
					[
						'class' => 'row mb-3',
						'cols' => [
							['class' => 'col-md-6', 'inputId' => 'p_pit_learning'],
						],
					],
				],
			]
		],
	],
	
	'cart' => [
		'title' => ['labels' => ['default' => 'Your basket', 'fr_FR' => 'Votre panier']],
		'including_options_amount' => ['labels' => ['default' => 'Total subscribed', 'fr_FR' => 'Total souscrit']],
		'total_amount' => ['labels' => ['default' => 'Total payable', 'fr_FR' => 'Total à payer']],
		'navigation' => [
			'submit' => ['labels' => ['default' => 'Finalize the order', 'fr_FR' => 'Terminer la commande']],
			'paymentSystem' => null, // To be defined
			'return' => ['labels' => ['default' => 'Return to catalogue', 'fr_FR' => 'Revenir au catalogue']],
		],
	],

	'complete' => [
		'recipient_properties' => [
			'n_last' => ['definition' => 'core_account/generic/property/n_last'],
			'n_first' => ['definition' => 'core_account/generic/property/n_first'],
			'birth_date' => ['definition' => 'core_account/generic/property/birth_date', 'mask' => ['default' => 'né(e) le %s']],
			'email' => ['definition' => 'core_account/generic/property/email'],
			'tel_cell' => ['definition' => 'core_account/generic/property/tel_cell'],
		],
		'documents' => [
			['src' => ['default' => ''], 'labels' => ['default' => 'Download the Terms Of Sales', 'Télécharger les Conditions Générales de Ventes']]
		],
		'navigation' => [
			'submit' => ['labels' => ['default' => 'Finalize the subscription', 'fr_FR' => 'Terminer l’inscription']],
			'return' => ['labels' => ['default' => 'Cancel and return to catalogue', 'fr_FR' => 'Annuler et revenir au catalogue']],
		],
	],
	
	'commitment' => [
		'caption' => ['labels' => ['default' => 'Your online subscription of %s', 'fr_FR' => 'Votre souscription en ligne du %s']],
		'terms' => [
			'whole' => ['caption' => ['default' => 'Paiement comptant']],
			'scheduled' => [
				'1st' => ['type' => 'deposit', 'caption' => ['default' => 'Cash with order', 'fr_FR' => 'Paiement à la commande'], 'share' => 1],
			],
		],
	],
	
	'invoiceList' => [
		'title' => ['default' => 'Your invoices', 'fr_FR' => 'Vos factures'],
		'navigation' => [
			'download' => ['labels' => ['default' => 'Download the invoice', 'fr_FR' => 'Télécharger la facture']],
			'return' => ['labels' => ['default' => 'Close', 'fr_FR' => 'Fermer']],
		],
	]
]);
