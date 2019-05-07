<?php

define('CATALOGUE_PRODUCT_RATES', [
	'products' => [
		'p_pit_learning' => [],
		'p_pit_turnkey' => [],
	],
	
	'variants' => [
		'p_pit_learning_solo_annual' => [
			'caption' => ['default' => 'P-Pit Learning Solo - Annual', 'fr_FR' => 'P-Pit Learning Solo - Annuel'], 
			'product_id' => 'p_pit_learning', 
			'unit_price' => 180
		],
		'p_pit_learning_solo_monthly' => [
			'caption' => ['default' => 'P-Pit Learning Solo - Monthly', 'fr_FR' => 'P-Pit Learning Solo - Mensuel'],
			'product_id' => 'p_pit_learning',
			'unit_price' => 16.5
		],
		'p_pit_learning_team_annual' => [
			'caption' => ['default' => 'P-Pit Learning Team - Annual', 'fr_FR' => 'P-Pit Learning Équipe - Annuel'],
			'product_id' => 'p_pit_learning',
			'unit_price' => 600
		],
		'p_pit_learning_team_monthly' => [
			'caption' => ['default' => 'P-Pit Learning Team - Monthly', 'fr_FR' => 'P-Pit Learning Équipe - Mensuel'],
			'product_id' => 'p_pit_learning',
			'unit_price' => 55
		],
		'p_pit_turnkey_annual' => [
			'caption' => ['default' => 'P-Pit Turnkey - Annual', 'fr_FR' => 'P-Pit Clé en main - Annual'],
			'product_id' => 'p_pit_turnkey',
			'unit_price' => 600
		],
		'p_pit_turnkey_monthly' => [
			'caption' => ['default' => 'P-Pit Turnkey - Monthly', 'fr_FR' => 'P-Pit Clé en main - Mensuel'],
			'product_id' => 'p_pit_turnkey',
			'unit_price' => 55
		],
	],
	
	'options' => [
	],
	
	'discounts' => [
		'multiple_subscription' => ['caption' => ['default' => 'Réduction souscriptions multiples'], 'progressiveness' => [0, 0.05, 0.1, 0.2]],
	],
]);
