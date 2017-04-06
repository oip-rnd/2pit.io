<?php

return array(
	'specifications' => array(
			'mailTo' => 'support@p-pit.fr', // Overrides the real email if not NULL (for test purposes)
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
