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
			'c01' => array('default' => 'C1 - Entreprise - Formation de leurs salariés'),
			'c02a' => array('default' => 'C2a - Collecteur ou gestionnaire paritaire - Professionnalisation'),
			'c02b' => array('default' => 'C2b - Collecteur ou gestionnaire paritaire - Congé individuel de formation'),
			'c02c' => array('default' => 'C2c - Collecteur ou gestionnaire paritaire - Compte personnel de formation'),
			'c02d' => array('default' => 'C2d - Collecteur ou gestionnaire paritaire - Recherche emploi'),
			'c02e' => array('default' => 'C2e - Collecteur ou gestionnaire paritaire - Autres dispositifs'),
			'c03' => array('default' => 'C3 - Fonds assurance formation de non-salariés'),
			'c04' => array('default' => 'C4 - Pouvoirs publics - Agents'),
			'c05' => array('default' => 'C5 - Pouvoirs publics spécifiques - Instances européennes'),
			'c06' => array('default' => 'C6 - Pouvoirs publics spécifiques - Etats'),
			'c07' => array('default' => 'C7 - Pouvoirs publics spécifiques - Conseils régionaux'),
			'c08' => array('default' => 'C8 - Pouvoirs publics spécifiques - Pôle emploi'),
			'c09' => array('default' => 'C9 - Pouvoirs publics spécifiques - Autres ressources publiques'),
			'c10' => array('default' => 'C10 - Personnes individuelles à leurs frais'),
			'c11' => array('default' => 'C11 - Autres organismes de formation'),
			'c12' => array('default' => 'C12 - Vente d’outils pédagogiques'),
			'c13' => array('default' => 'C13 - Formation professionnelle continue'),
		),
		'labels' => array(
			'default' => 'C. Origine du produit',
		),
	),
	
	'commitment/learning/property/property_2' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'f1a' => array('default' => 'F1a - Salarié financement employeur OPCA ou OPACIF'),
			'f1b' => array('default' => 'F1b - Demandeur d’emploi financement public'),
			'f1c' => array('default' => 'F1c - Demandeur d’emploi financement OPCA'),
			'f1d' => array('default' => 'F1d - Particulier à ses propres frais'),
			'f1e' => array('default' => 'F1e - Autre stagiaire'),
		),
		'labels' => array(
			'default' => 'F-1. Type de stagiaire',
		),
	),
	'commitment/learning/property/property_3' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'f2a' => array('default' => 'F2a - Pour son propre compte'),
			'f2b' => array('default' => 'F2b - Pour le compte d’un autre organisme'),
		),
		'labels' => array('default' => 'F-2. Activité en propre'),
	),
	
	'commitment/learning/property/property_4' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'f3a12' => array('default' => 'F3a - RNCP niveau I et II'),
			'f3a3' => array('default' => 'F3a - RNCP niveau III'),
			'f3a4' => array('default' => 'F3a - RNCP niveau IV'),
			'f3a5' => array('default' => 'F3a - RNCP niveau V'),
			'f3b' => array('default' => 'F3b - CQP'),
			'f3c' => array('default' => 'F3c - CNCP'),
			'f3d' => array('default' => 'F3d - Autre formation professionnelle continue'),
			'f3e' => array('default' => 'F3e - Bilan de compétence'),
			'f3f' => array('default' => 'F3f - Accompagnement de la VAE'),
		),
		'labels' => array('default' => 'F-3. Objectif de la prestation'),
	),
	
	'commitment/learning/property/property_5' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'f4a' => array('default' => 'Enseignement formation'),
			'f4b' => array('default' => 'Développement capacités organisation'),
			'f4c' => array('default' => 'Développement capacités orientation / insertion'),
		),
		'labels' => array('default' => 'F-4. Spécialité de la formation'),
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
		'type' => 'date',
		'labels' => array('default' => 'Training end date', 'fr_FR' => 'Date fin de formation'),
	),

	'commitment/learning/property/property_15' => array(
		'definition' => 'inline',
		'type' => 'number',
		'minValue' => 0,
		'maxValue' => 9999,
		'labels' => array('default' => 'Number of hours', 'fr_FR' => 'Nombre d’heures'),
	),
	
	'commitment/learning' => array(
		'tax' => 'excluding',
		'currencySymbol' => '€',
		'properties' => array(
			'status', 'year', 'place_id', 'account_id', 'account_name', 'invoice_n_fn',
			'product_caption', 'quantity', 'unit_price', 'amount', 'caption', 'description',
			'property_1', 'property_2', 'property_3', 'property_4', 'property_5',
			'property_10', 'property_11', 'property_12', 'property_13', 'property_14', 'property_15',
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
		'property_14' => array('mandatory' => false),
		'property_15' => array('mandatory' => false),
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
				'left' => array('default' => 'Nom du stagiaire'),
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
			array(
				'left' => array('default' => 'Fin de formation'),
				'right' => array('default' => '%s'),
				'params' => array('property_14'),
			),
			array(
				'left' => array('default' => 'Nombre d’heures de formation'),
				'right' => array('default' => '%s'),
				'params' => array('property_15'),
			),
		),
		'terms' => true,
	),
]);
