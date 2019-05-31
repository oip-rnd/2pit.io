<?php

define('COMMITMENT_LEARNING', [

	'commitment/learning/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('default' => 'A confirmer'),
			'confirmed' => array('default' => 'Confirmé'),
			'settled' => array('default' => 'Réglé'),
			'invoiced' => array('default' => 'Facturé'),
			'registered' => array('default' => 'Comptabilisé'),
		),
		'labels' => array(
			'default' => 'Statut',
		),
	),

	'commitment/learning/property/invoice_date' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array(
			'en_US' => 'Quotation date',
			'fr_FR' => 'Date de devis',
		),
	),
	
	'commitment/learning/property/property_1' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'A1' => array('default' => 'A1 - Entreprise - Formation salarié hors professionalisation'),
			'A1p' => array('default' => 'A1\' - Entreprise - Formation salarié professionalisation'),
			'A2a' => array('default' => 'A2a - Collecteur paritaire agréé - Plan de formation'),
			'A2b' => array('default' => 'A2b - Collecteur paritaire agréé - Professionnalisation'),
			'A2c' => array('default' => 'A2c - Collecteur paritaire agréé - Compte personnel de formation'),
			'A2d' => array('default' => 'A2d - Collecteur paritaire agréé - Congé individuel de formation'),
			'A2e' => array('default' => 'A2e - Fonds assurance formation de non-salariés'),
			'A3a' => array('default' => 'A3a - Pouvoirs publics - Agents'),
			'A3b' => array('default' => 'A3b - Pouvoirs publics - Spécifique instances européennes'),
			'A3c' => array('default' => 'A3c - Pouvoirs publics - Spécifique états'),
			'A3d' => array('default' => 'A3d - Pouvoirs publics - Spécifique conseils régionaux'),
			'A3e' => array('default' => 'A3e - Pouvoirs publics - Spécifique Pôle emploi'),
			'A3f' => array('default' => 'A3f - Pouvoirs publics - Spécifique Autres ressources publiques'),
			'A4' => array('default' => 'A4 - Contrat conclus avec particuliers'),
			'A5' => array('default' => 'A4 - Contrat conclus avec autres organismes de formation'),
		),
		'labels' => array(
			'default' => 'Origine produits (BF)',
		),
	),
	
	'commitment/learning/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'A1a' => array('default' => 'A1a - Salariés financement employeur hors professionnalisation'),
			'A1b' => array('default' => 'A1b - Salariés financement employeur professionnalisation'),
			'A2' => array('default' => 'A2 - Demandeurs d\'emploi financement public'),
			'A3' => array('default' => 'A3 - Particuliers à leurs propres frais'),
			'A4' => array('default' => 'A4 - Autres stagiaires'),
		),
		'labels' => array(
			'default' => 'A - Type stagiaires',
		),
	),
	'commitment/learning/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'B1' => array('default' => 'B1 - Formés par votre organisme pour son propre compte'),
			'B2' => array('default' => 'B1 - Formés par votre organisme pour le compte d\'un autre organisme'),
			'B3' => array('default' => 'B1 - Confiés par votre organisme à un autre organisme de formation'),
		),
		'labels' => array('default' => 'B - Activité propre'),
	),
	
	'commitment/learning/property/property_4' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'C1a' => array('default' => 'C1a - Certification enregistrée au RNCP - Niveau I et II'),
			'C1b' => array('default' => 'C1b - Certification enregistrée au RNCP - Niveau III'),
			'C1c' => array('default' => 'C1c - Certification enregistrée au RNCP - Niveau IV'),
			'C1d' => array('default' => 'C1d - Certification enregistrée au RNCP - Niveau V'),
			'C2' => array('default' => 'C2 - Autres formations professionnelles continues'),
			'C3a' => array('default' => 'C3 - Prestations d\'orientation et d\'accompagnement - Hors bilan'),
			'C3b' => array('default' => 'C3 - Prestations d\'orientation et d\'accompagnement - Bilan'),
		),
		'labels' => array('default' => 'C - Objectif formation'),
	),
	
	'commitment/learning/property/property_5' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array('default' => 'Spécialités formation'),
	),
	
	'commitment/learning/property/property_10' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array('default' => 'Référence du dossier'),
	),

	'commitment/learning/property/property_11' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array('default' => 'Trainee name', 'fr_FR' => 'Nom du stagiaire'),
	),

	'commitment/learning/property/property_12' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array('default' => 'Training', 'fr_FR' => 'Formation'),
	),

	'commitment/learning/property/property_13' => array(
		'definition' => 'inline',
		'type' => 'date',
		'labels' => array('default' => 'Training start date', 'fr_FR' => 'Date début de formation'),
	),

	'commitment/learning/property/property_14' => array(
		'definition' => 'inline',
		'type' => 'input',
		'labels' => array('default' => 'Original commitment', 'fr_FR' => 'Engagement d’origine'),
	),
		
	'commitment/learning' => array(
		'tax' => 'excluding',
		'currencySymbol' => '€',
		'properties' => array(
			'status', 'year', 'place_id', 'account_id', 'account_name', 'invoice_n_fn',
			'product_caption', 'quantity', 'unit_price', 'amount', 'caption', 'description',
			'property_1', 'property_2', 'property_3', 'property_4', 'property_5',
			'property_10', 'property_11', 'property_12', 'property_13', 'property_14',
			'including_options_amount',
			'default_means_of_payment', 'invoice_date', 'update_time',
		),
		'todo' => array(
			'sales_manager' => array(
			),
		),
	),

	'commitment/index/learning' => array(
		'title' => array('default' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	
	'commitment/search/learning' => array(
		'title' => array('default' => 'Formations'),
		'todoTitle' => array('default' => 'actifs'),
		'properties' => array(
			'status' => 'select',
			'year' => 'contains',
			'account_name' => 'contains',
			'property_11' => 'contains',
			'property_12' => 'contains',
			'property_1' => 'select',
			'property_2' => 'select',
			'property_3' => 'select',
			'property_4' => 'select',
			'property_5' => 'contains',
		),
	),
	
	'commitment/list/learning' => array(
		'properties' => array(
			'place_id' => [],
			'year' => [],
			'status' => [],
			'account_name' => [],
			'property_11' => [],
			'property_12' => [],
			'caption' => [],
			'quantity' => [],
			'unit_price' => [],
			'including_options_amount' => [],
			'update_time' => [],
		),
	),
	
	'commitment/update/learning' => array(
		'year' => array('mandatory' => true),
		'invoice_date' => array('mandatory' => true),
		'account_id' => array('mandatory' => true),
		'property_10' => array('mandatory' => false),
		'property_11' => array('mandatory' => false),
		'property_12' => array('mandatory' => false),
		'property_13' => array('mandatory' => false),
		'caption' => array('mandatory' => true),
		'description' => array('mandatory' => false),
		'property_1' => array('mandatory' => false),
		'property_2' => array('mandatory' => false),
		'property_3' => array('mandatory' => false),
		'property_4' => array('mandatory' => false),
		'property_5' => array('mandatory' => false),
		'property_11' => array('mandatory' => false),
	),

	'commitment/group/learning' => array(
		'status' => [],
		'caption' => [],
		'description' => [],
	),
	
	'commitment/export/learning' => array(
		'year' => 'A',
		'invoice_identifier' => 'B',
		'invoice_date' => 'C',
		'account_name' => 'D',
		'invoice_n_fn' => 'E',
		'property_10' => 'F',
		'property_11' => 'G',
		'property_12' => 'H',
		'property_13' => 'I',
		'caption' => 'J',
		'description' => 'K',
		'product_caption' => 'L',
		'unit_price' => 'M',
		'quantity' => 'N',
		'amount' => 'O',
		'including_options_amount' => 'P',
		'tax_amount' => 'Q',
		'tax_inclusive' => 'R',
		'default_means_of_payment' => 'S',
		'property_1' => 'T',
		'property_2' => 'U',
		'property_3' => 'V',
		'property_4' => 'W',
		'property_5' => 'X',
	),

	'commitment/invoice/learning' => array(
		'header' => array(
			array(
				'format' => array('default' => '%s'),
				'params' => array('account_name'),
			),
		),
		'description' => array(
			array(
				'left' => array('default' => 'Référence'),
				'right' => array('default' => '%s'),
				'params' => array('property_10'),
			),
			array(
				'left' => array('default' => 'Nom de l’étudiant'),
				'right' => array('default' => '%s'),
				'params' => array('property_11'),
			),
			array(
				'left' => array('default' => 'Formation'),
				'right' => array('default' => '%s'),
				'params' => array('property_12'),
			),
			array(
				'left' => array('default' => 'Début de formation'),
				'right' => array('default' => '%s'),
				'params' => array('property_13'),
			),
		),
		'terms' => true,
	),
]);
