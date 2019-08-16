<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

define('CONFIG_DOCUMENT', [

	'document/generic/property/type' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'blog' => array('default' => 'Blog', 'fr_FR' => 'Blog'),
			'binary' => array('default' => 'Binary', 'fr_FR' => 'Binaire'),
		),
		'labels' => array(
			'default' => 'Type',
			'fr_FR' => 'Type',
		),
	),
	
	'document/generic/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('default' => 'New', 'fr_FR' => 'Nouveau'),
			'archived' => array('default' => 'Archived', 'fr_FR' => 'Archivé'),
		),
		'labels' => array(
			'default' => 'Status',
			'fr_FR' => 'Statut',
		),
	),
	
	'document/generic/property/place_id' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'2pit' => array('fr_FR' => 'P-PIT', 'en_US' => '2PIT'),
		),
		'labels' => array(
			'en_US' => 'Place',
			'fr_FR' => 'Établissement',
		),
	),

	'document/generic/property/account_id' => array(
		'definition' => 'inline',
		'type' => 'select',
		'labels' => array(
			'en_US' => 'Account',
			'fr_FR' => 'Compte',
		),
	),
	
	'document/generic/property/folder' => array(
		'definition' => 'inline',
		'type' => 'input',
		'maxSize' => 255,
		'labels' => array(
			'default' => 'Folder',
			'fr_FR' => 'Dossier',
		),
	),

	'document/generic/property/identifier' => array(
		'definition' => 'inline',
		'type' => 'input',
		'maxSize' => 255,
		'labels' => array(
			'default' => 'Identifier',
			'fr_FR' => 'Identifiant',
		),
	),

	'document/generic/property/name' => array(
		'definition' => 'inline',
		'type' => 'input',
		'maxSize' => 255,
		'labels' => array(
			'default' => 'Name',
			'fr_FR' => 'Nom',
		),
	),

	'document/generic/property/mime' => array(
		'definition' => 'inline',
		'type' => 'input',
		'maxSize' => 255,
		'labels' => array(
			'default' => 'Mime type',
			'fr_FR' => 'Type mime',
		),
	),

	'document/generic/property/update_time' => array(
		'definition' => 'inline',
		'type' => 'datetime',
		'labels' => array(
			'en_US' => 'Update time',
			'fr_FR' => 'Heure de mise à jour',
		),
	),
	
	'document/generic' => array(
		'properties' => array('type', 'status', 'place_id', 'account_id', 'folder', 'identifier', 'name', 'mime', 'update_time'),
	),

	'document/search/generic' => [
		'title' => ['default' => 'Documents', 'fr_FR' => 'Documents'],
		'properties' => [
			'status' => [],
			'name' => [],
			'mime' => [],
		],
	],
	
	'document/list/generic' => [
		'properties' => [
			'name' => [],
			'mime' => [],
			'update_time' => [],
		],
	],
	
	'document/detail/generic' => [
		'properties' => [
			'status' => ['mandatory' => true],
			'place_id' => [],
			'identifier' => [],
			'name' => ['mandatory' => true],
			'mime' => ['readonly' => true],
		],
	],
	
	'document/groupUpdate/generic' => [
		'properties' => [
			'status' => [],
		],
	],
	
	'document/export/generic' => [
		'properties' => [
			'status' => 				['column' => 'A'],
			'type' => 					['column' => 'B'],
			'folder' => 				['column' => 'C'],
			'place_id' => 				['column' => 'D'],
			'account_id' => 			['column' => 'E'],
			'identifier' => 			['column' => 'F'],
			'name' => 					['column' => 'G'],
			'mime' => 					['column' => 'H'],
		],
	],
]);
