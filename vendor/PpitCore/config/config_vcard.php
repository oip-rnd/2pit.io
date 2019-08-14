<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

define('CONFIG_VCARD', [

	'vcard/properties' => array(
		'status' => array(
			'definition' => 'inline',
			'type' => 'select',
			'modalities' => array(
				'new' => array('default' => 'New', 'fr_FR' => 'Nouveau'),
				'interested' => array('default' => 'Interested', 'fr_FR' => 'Intéressé'),
				'active' => array('default' => 'Active', 'fr_FR' => 'Actif'),
				'gone' => array('en_US' => 'Gone', 'fr_FR' => 'Parti'),
			),
			'labels' => array(
				'default' => 'Status',
				'fr_FR' => 'Statut',
			),
		),
		'n_title' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Title',
				'fr_FR' => 'Civilité',
			),
		),
		'n_first' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'First name',
				'fr_FR' => 'Prénom',
			),
		),
		'n_last' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Last name',
				'fr_FR' => 'Nom',
			),
		),
		'n_fn' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Name',
				'fr_FR' => 'Nom',
			),
		),
		'org' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Organization',
				'fr_FR' => 'Organisation',
			),
		),
		'tel_work' => array(
			'definition' => 'inline',
			'type' => 'phone',
			'labels' => array(
				'default' => 'Phone',
				'fr_FR' => 'Téléphone',
			),
		),
		'tel_cell' => array(
			'definition' => 'inline',
			'type' => 'phone',
			'labels' => array(
				'default' => 'Cellular',
				'fr_FR' => 'Mobile',
			),
		),
		'email' => array(
			'definition' => 'inline',
			'type' => 'email',
			'labels' => array(
				'default' => 'Email',
				'fr_FR' => 'Email',
			),
		),
		'adr_street' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Address - street',
				'fr_FR' => 'Adresse - rue',
			),
		),
		'adr_extended' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Address - extended',
				'fr_FR' => 'Adresse - complément',
			),
		),
		'adr_post_office_box' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Address - post office box',
				'fr_FR' => 'Adresse - boîte postale',
			),
		),
		'adr_zip' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Address - ZIP',
				'fr_FR' => 'Adresse - Code postal',
			),
		),
		'adr_city' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Address - city',
				'fr_FR' => 'Adresse - ville',
			),
		),
		'adr_state' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Address - state',
				'fr_FR' => 'Adresse - état',
			),
		),
		'adr_country' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Address - country',
				'fr_FR' => 'Adresse - pays',
			),
		),
		'gender' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Gender',
				'fr_FR' => 'Genre',
			),
		),
		'birth_date' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Birth date',
				'fr_FR' => 'Date de naissance',
			),
		),
		'place_of_birth' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Place of birth',
				'fr_FR' => 'Lieu de naissance',
			),
		),
		'nationality' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Nationality',
				'fr_FR' => 'Nationalité',
			),
		),
		'origine' => array(
			'definition' => 'inline',
			'type' => 'input',
			'maxSize' => 255,
			'labels' => array(
				'default' => 'Origine',
				'fr_FR' => 'Origine',
			),
		),
		'locale' => array(
			'definition' => 'inline',
			'type' => 'select',
			'modalities' => array(
				'default' => array('default' => 'en_US'),
				'fr_FR' => array('default' => 'fr_FR'),
			),
			'labels' => array(
				'default' => 'Locale',
				'fr_FR' => 'Traduction',
			),
		),
	
		'update_time' => array(
			'definition' => 'inline',
			'type' => 'datetime',
			'labels' => array(
				'default' => 'Update time',
				'fr_FR' => 'Heure de mise à jour',
			),
		),
	),

	'vcard/search' => [
		'title' => ['default' => 'Contacts', 'fr_FR' => 'Contacts'],
		'properties' => [
			'status' => [],
			'n_fn' => [],
			'email' => [],
			'tel_work' => [],
			'tel_cell' => [],
			'adr_zip' => [],
			'adr_city' => [],
			'adr_country' => [],
		],
	],
	
	'vcard/list' => [
		'properties' => [
			'status' => [],
			'n_fn' => [],
			'tel_work' => [],
			'tel_cell' => [],
			'update_time' => [],
		],
	],
	
	'vcard/detail' => [
		'properties' => [
			'status' => ['mandatory' => true],
			'n_title' => [],
			'n_last' => [],
			'n_first' => [],
			'org' => [],
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
			'gender' => [],
			'birth_date' => [],
			'place_of_birth' => [],
			'nationality' => [],
			'photo_link_id' => [],
			'origine' => [],
		],
	],
	
	'vcard/groupUpdate' => [
		'properties' => [
			'status' => [],
		],
	],
	
	'vcard/export' => [
		'properties' => [
			'status' => 				['column' => 'A'],
			'n_title' => 				['column' => 'B'],
			'n_last' => 				['column' => 'C'],
			'n_first' => 				['column' => 'D'],
			'org' => 					['column' => 'E'],
			'email' => 					['column' => 'F'],
			'tel_work' => 				['column' => 'G'],
			'tel_cell' => 				['column' => 'H'],
			'adr_street' => 			['column' => 'I'],
			'adr_extended' => 			['column' => 'J'],
			'adr_post_office_box' => 	['column' => 'K'],
			'adr_zip' => 				['column' => 'L'],
			'adr_city' => 				['column' => 'M'],
			'adr_state' => 				['column' => 'N'],
			'adr_country' => 			['column' => 'O'],
			'gender' => 				['column' => 'P'],
			'birth_date' => 			['column' => 'Q'],
			'place_of_birth' => 		['column' => 'R'],
			'nationality' => 			['column' => 'S'],
			'photo_link_id' => 			['column' => 'T'],
			'origine' => 				['column' => 'U'],
		],
	],
]);
