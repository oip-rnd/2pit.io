<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
	'defaultRoute' => 'flowEvent',
	'mailAdmin' => 'no-reply@probonocorpo.com',
	'nameAdmin' => 'Pro bono corpo',
	'mailTo' => 'contact@probonocorpo.com', // Overrides the real email if not NULL (for test purposes)
	'isTraceActive' => true,
	'isDemoModeActive' => true,
	'isDemoAccountUpdatable' => false,
	'defaultInstanceId' => 1,
	'defaultPlaceId' => 2,
	'document/ethicalCharter' => array('id' => 1),
	'maxUploadSize' => 2048000,
	'compressGifPngToJpg' => false,
	'formExpiration' => 1800, // Duration in seconds of a form validity (against CSRF)
	'specificationMode' => 'config', // 'config' vs 'database'
	'locales' => array(
			'en_US' => 'en_US',
			'fr_FR' => 'fr_FR',
	),
		
	'specifications' => array(
			'mailTo' => 'contact@probonocorpo.com', // Overrides the real email if not NULL (for test purposes)
			'credit' => array(
					'unlimitedCredits' => true,
			),
			'ppitDocument' => array(
					'dropbox' => array(
							'credential' => '9CRvqMLXD40AAAAAAAAAw01AjFizF3WrpmnYhseJ8RXng1QXiokyfjKqLczV5aQ6',
							'clientIdentifier' => 'P-PIT',
							'root' => 'https://www.dropbox.com/home/Applications/docs.confianceit.net', 
							'mode' => 'real',
							'folders' => array(
							),
					),
					'pages' => array(
							'blog' => array(
									'2016-12-18' => array(),
									'2016-12-03	' => array(),
									'2016-06-19	' => array(),
							),
							'news' => array(
									'last-new' => array(
									)
							),
							'resources' => array(
									'legal-mentions' => array(),
							),
					),
					'home' => array(
							'title' => array(
									'en_US' => '2pit project - Make applications interact',
							),
							'description' => array(
									'en_US' => 'Keep business applications that are useful and make them interacts. By exploiting all kind of available APIs, importing or exporting datas, sending and receiving web-services and so on...',
							),
							'contactUs' => array(
									array(
											'title' => array('en_US' => 'Contact us'),
											'glyphicon' => 'earphone',
											'href' => 'tel:+33629879002',
											'text' => array('en_US' => '+33 629 879 002'),
									),
									array(
											'title' => array('en_US' => 'Nous contacter'),
											'glyphicon' => 'send',
											'href' => 'mailto:postmaster@2pit.io',
											'text' => array('en_US' => 'Send an email'),
									),
							),
							'jumbotron' => array(
									'directory' => 'news',
									'name' => 'last-new',
							),
							'frontProducts' => array(
							),
							'legalNotices' => array(
									'directory' => 'resources',
									'name' => 'legal-notices',
							),
					),
			),
	),
);
