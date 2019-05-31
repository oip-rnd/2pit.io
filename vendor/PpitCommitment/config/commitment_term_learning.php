<?php

define('COMMITMENT_TERM_LEARNING', [

	// Properties joined from commitment
	'commitmentTerm/learning/property/commitment_property_9' => array('definition' => 'commitment/learning/property/property_9'),
	'commitmentTerm/learning/property/commitment_property_10' => array('definition' => 'commitment/learning/property/property_10'),
	'commitmentTerm/learning/property/commitment_property_11' => array('definition' => 'commitment/learning/property/property_11'),
	'commitmentTerm/learning/property/commitment_property_12' => array('definition' => 'commitment/learning/property/property_12'),
	'commitmentTerm/learning/property/commitment_property_13' => array('definition' => 'commitment/learning/property/property_13'),
	'commitmentTerm/learning/property/commitment_property_14' => array('definition' => 'commitment/learning/property/property_14'),
	
	'commitmentTerm/search/learning' => array(
		'title' => array('en_US' => 'Terms', 'fr_FR' => 'Echéances'),
		'todoTitle' => array('en_US' => 'todo list', 'fr_FR' => 'todo list'),
		'properties' => array(
			'place_id' => ['multiple' => true],
			'name' => [],
			'invoice_account_id' => [],
			'status' => ['multiple' => true],
			'account_status' => ['multiple' => true],
			'due_date' => [],
			'collection_date' => [],
			'means_of_payment' => [],
			'amount' => [],
			'reference' => [],
			'comment' => [],
		),
	),
	'commitmentTerm/list/learning' => array(
		'properties' => array(
			'name' => [],
			'invoice_account_id' => [],
			'status' => [],
			'due_date' => [],
			'collection_date' => [],
			'amount' => [],
		),
	),
	
	'commitmentTerm/update/learning' => array(
		'invoice_account_id' => [],
		'status' => ['mandatory' => true],
		'caption' => ['mandatory' => true],
		'due_date' => ['mandatory' => true],
		'settlement_date' => [],
		'collection_date' => [],
//		'quantity' => ['readonly' => true],
//		'unit_price' => ['mandatory' => true],
		'amount' => ['mandatory' => true],
		'means_of_payment' => [],
		'bank_name' => [],
		'invoice_n_last' => [],
		'reference' => [],
		'commitment_property_10' => ['readonly' => true],
		'commitment_property_11' => ['readonly' => true],
		'commitment_property_12' => ['readonly' => true],
		'commitment_property_13' => ['readonly' => true],
		'commitment_property_14' => ['readonly' => true],
		'comment' => [],
		'document' => [],
	),

	'commitmentTerm/export/learning' => array(
		'name' => 'A',
		'invoice_account_id' => 'B',
		'commitment_caption' => 'C',
		'status' => 'D',
		'caption' => 'E',
		'due_date' => 'F',
		'settlement_date' => 'G',
		'collection_date' => 'H',
		'amount' => 'I',
		'means_of_payment' => 'J',
		'bank_name' => 'K',
		'invoice_n_last' => 'L',
		'reference' => 'M',
		'commitment_property_10' => 'N',
		'commitment_property_11' => 'O',
		'commitment_property_12' => 'P',
		'commitment_property_13' => 'Q',
		'commitment_property_14' => 'R',
		'comment' => 'S',
		'document' => 'T',
	),

	'commitmentTerm/invoice/generic' => array(
		'header' => array(),
		'description' => array(
			array(
				'left' => array('en_US' => 'Caption', 'fr_FR' => 'Libellé'),
				'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
				'params' => array('caption'),
			),
			array(
				'left' => array('en_US' => 'Invoice date', 'fr_FR' => 'Date de facture'),
				'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
				'params' => array('date'),
			),
			array(
				'left' => array('en_US' => 'Place', 'fr_FR' => 'Centre'),
				'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
				'params' => array('place_id'),
			),
			array(
				'left' => array('en_US' => 'Invoicing period', 'fr_FR' => 'Période de facturation'),
				'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
				'params' => array('tiny_1'),
			),
			array(
				'left' => array('en_US' => 'File reference', 'fr_FR' => 'Référence dossier'),
				'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
				'params' => array('commitment_property_10'),
			),
			array(
				'left' => array('en_US' => 'Training name', 'fr_FR' => 'Nom de la formation'),
				'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
				'params' => array('commitment_property_12'),
			),
			array(
				'left' => array('en_US' => 'Training start date', 'fr_FR' => 'Date de début de formation'),
				'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
				'params' => array('commitment_property_13'),
			),
			array(
				'left' => array('en_US' => 'Mentor name', 'fr_FR' => 'Nom de l’entreprise'),
				'right' => array('en_US' => '%s', 'fr_FR' => '%s'),
				'params' => array('commitment_property_14'),
			),
		),
		'terms' => true,
	),
	
	// CommitmentMessage
	
	'commitmentMessage/property/status' => array(
		'definition' => 'inline',
		'type' => 'select',
		'modalities' => array(
			'new' => array('default' => 'New', 'fr_FR' => 'Nouveau'),
			'submitted' => array('default' => 'Submitted', 'fr_FR' => 'Emis'),
			'rejected' => array('default' => 'Rejected', 'fr_FR' => 'Rejeté'),
			'canceled' => array('default' => 'Canceled', 'fr_FR' => 'Annulé'),
			'registered' => array('default' => 'Registered', 'fr_FR' => 'Comptabilisé'),
		),
		'labels' => array(
			'en_US' => 'Status',
			'fr_FR' => 'Statut',
		),
	),
	
	'commitmentMessage' => array(
			'importMaxRows' => 100,
			'importTypes' => array('csv' => array('en_US' => 'CSV file', 'fr_FR' => 'Fichier CSV')),
			'inputMessages' => array(
					'order' => array(
							'action' => '',
							'format' => 'Web-service - json',
							'description' => array(
									
									// Generic
									'message_identifier',
									'issue_date',
									
									// Specific
									'order_number',
									'buyer_party',
									'seller_party',
									'product_identifier',
									'quantity',
							)
					),
			),
			'outputMessages' => array(
					'commissioning' => array(
							'action' => 'commission',
							'format' => 'Web-service - json',
							'description' => array(
									'message_identifier' => array('source' => 'this', 'property' => 'id'),
									'order_number' => array('source' => 'commitment_message', 'property' => 'order_number'),
									'issue_date' => array('source' => 'system', 'property' => 'now'),
									'commissioning_date' => array('source' => 'commitment', 'property' => 'commissioning_date'),
									'buyer_party' => array('source' => 'commitment_message', 'property' => 'buyer_party'),
									'seller_party' => array('source' => 'commitment_message', 'property' => 'seller_party'),
									'product_identifier' => array('source' => 'commitment_message', 'property' => 'product_identifier'),
									'quantity' => array('source' => 'commitment_message', 'property' => 'quantity'),
							)
					),
			),
	),
	'commitment/accountList' => array(
			'title' => array('en_US' => 'Commitments', 'fr_FR' => 'Engagements'),
			'properties' => array(
				'caption' => 'text',
				'property_1' => 'text',
			),
			'anchors' => array(
/*				'document' => array(
						'type' => 'nav',
						'labels' => array('en_US' => 'Documents', 'fr_FR' => 'Documents'),
						'entries' => array(
						),
				),*/
			),
	),

		'core_account/sendMessage' => array(
				'templates' => array(
						'generic' => array('definition' => 'core_account/sendMessage/generic'),
				),
				'signature' => array(
					'definition' => 'inline',
					'body' => array(
						'en_US' => 'To be translated',
						'fr_FR' => '
<hr>
<div><a href="https://www.p-pit.fr"><img src="http://img.p-pit.fr/P-Pit/p-pit-advert.png" width="400" alt="P-Pit logo" /></a></div>
Le support P-Pit<br>
support@p-pit.fr
'
					),
				),
		),

		'core_account/sendMessage/generic' => array(
				'labels' => array(
						'en_US' => 'Generic',
						'fr_FR' => 'Générique',
				),
				'cci' => 'contact@p-pit.fr',
				'from_mail' => 'contact@p-pit.fr',
				'from_name' => 'noreply@p-pit.fr',
				'subject' => array('default' => 'Important message from P-Pit', 'fr_FR' => 'Message important de P-Pit'),
				'text' => array(
					'default' => '
<p>Hello %s,</p>
<p>We hope that our services are giving you satisfaction. Please send your requests or questions to the P-Pit support: support@p-pit.fr.</p>
<p>Best regards,</p>
<p>The P-Pit staff</p>
',
					'fr_FR' => '
<p>Bonjour %s,</p>
<p>Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-Pit : support@p-pit.fr.</p>
<p>Bien cordialement,</p>
<p>L\'équipe P-Pit</p>
',
				),
				'params' => array('n_first'),
				'body' => '
<style>
        @font-face {
        font-family: "League Gothic";
        src: url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.eot");
        src: url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.eot?#iefix") format("embedded-opentype"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff2") format("woff2"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff") format("woff"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.ttf") format("truetype"), url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.svg#league_gothicregular") format("svg");
        font-weight: normal;
        font-style: normal;
    }
    
    @media only screen and (max-width: 480px) {
        @font-face {
            font-family: "League Gothic";
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff2") format("woff2"),
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.woff") format("woff"),
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.ttf") format("truetype"),
            url("https://s3-eu-west-1.amazonaws.com/lrqdo-ppr-news-images/design-templates/leaguegothic-regular-webfont.svg#league_gothicregular") format("svg");
            font-weight: normal;
            font-style: normal;
        }

</style>
<table border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td>
                <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" style="font-family: arial, helvetica, sans-serif;" width="760">
                    <tbody>
                        <tr>
                            <td width="40">&nbsp;</td>
                            <td width="680">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" valign="top" width="680">
                                    <tbody>
                                        <tr>
                                            <td colspan="3" height="40">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" bgcolor="#ffffff" valign="top" width="40">&nbsp;</td>
                                            <td bgcolor="#ffffff" valign="top" width="600">
                                                <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600">
                                                    <tbody>
                                                        <tr>
                                                            <td style="line-height:24px;text-align:justify;font-size:16px; font-family: Georgia, Times New Roman, Times, serif; color:rgb(45,40,70);">
																%s
																%s
															</td>
                                                        </tr>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td align="right" bgcolor="#ffffff" valign="top" width="38">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="10">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" bgcolor="#eeeeee" colspan="3" height="15" width="682"><font style="color: rgb(51, 51, 51); font-family: arial, sans-serif; font-size: 10px; font-weight: normal;">P-Pit SAS - 25, rue du Faubourg du Temple - B&acirc;timent C - 75010 Paris<br /></font></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="10">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="40">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
                <p>&nbsp;</p>
            </td>
        </tr>
    </tbody>
</table>',
		),

		'core_account/notification' => array(
				'definition' => 'inline',
				'template' => array('definition' => 'core_account/notification/template'),
				'from_mail' => 'support@p-pit.fr',
				'from_name' => 'noreply@p-pit.fr',
				'signature' => array('definition' => 'customisation/esi/send-message/signature'),
		),
		
		'core_account/notification/template' => array(
				'subject' => array('en_US' => 'Current registration request', 'fr_FR' => 'Demande d\'inscription en cours'),
				'body' => array(
						'en_US' => '<p>Hello,</p>
<p>You have initiated a registration request on the web site %s that you have not been able to complete. We propose you to resume it by following this link: %s</p>
<p>Best regards,</p>
',
						'fr_FR' => '<p>Bonjour,</p>
<p>Vous avez initié une demande d\'inscription sur le site %s que vous n\'avez pas pu finaliser. Nous vous proposons de la reprendre en suivant ce lien : %s</p>
<p>Cordialement,</p>
',
				),
		),

	'commitment/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),
	'commitment/subscribe/rental' => array(
//			'due_date' => array('mandatory' => false, 'disabled' => true),
	),
	'commitmentMessage/index' => array(
			'title' => array('en_US' => 'P-PIT Commitments', 'fr_FR' => 'P-PIT Engagements'),
	),

	'journal/accountingChart/sale' => array(
			'rental' => array(
					'settlement' => array(
							'512' => array(
									'direction' => -1,
									'source' => 'tax_inclusive',
							),
							'44571' => array(
									'direction' => 1,
									'source' => 'tax_amount',
							),
							'706' => array(
									'direction' => 1,
									'source' => 'excluding_tax',
							),
					),
			),
			'service' => array(
					'registration' => array(
							'411' => array(
									'direction' => -1,
									'source' => 'tax_inclusive',
							),
							'44587' => array(
									'direction' => 1,
									'source' => 'tax_amount',
							),
							'706' => array(
									'direction' => 1,
									'source' => 'excluding_tax',
							),
					),
					'settlement' => array(
							'411' => array(
									'direction' => 1,
									'source' => 'tax_inclusive',
							),
							'512' => array(
									'direction' => -1,
									'source' => 'tax_inclusive',
							),
							'44587' => array(
									'direction' => -1,
									'source' => 'tax_amount',
							),
							'44571' => array(
									'direction' => 1,
									'source' => 'tax_amount',
							),
					),
			),
	),
		
	'demo' => array(
			'core_account/search/title' => array(
					'en_US' => '
<h4>Account list</h4>
<p>As a default, all the accounts with a <em>Active</em> status are presented in the list.</p>
<p>As soon as a criterion below is specified, the list switch in search mode.</p>
',
					'fr_FR' => '
<h4>Liste des comptes</h4>
<p>Par défaut, tous les comptes dont le statut est <em>Actif</em> sont présentés dans la liste.</p>
<p>Dès lors qu\'un des critères ci-dessous est spécifié, le mode de recherche est automatiquement activé.</p>
',
			),
			'core_account/search/x' => array(
					'en_US' => '
<h4>Return in default mode</h4>
<p>The <code>x</code> button reinitializes all the search criteria and reset the list filtered in todo-list mode.</p>
',
					'fr_FR' => '
<h4>Retour au mode par défaut</h4>
<p>Le bouton <code>x</code> réinitialise tous les critères de recherche et ré-affiche la liste filtrée en mode todo-list.</p>
',
			),
			'core_account/search/export' => array(
					'en_US' => '
<h4>List export</h4>
<p>The list can be exported to Excel as it is presented: defaulting list or list resulting of a multi-criteria search.</p>
',
					'fr_FR' => '
<h4>Export de la liste</h4>
<p>La liste peut être exportée sous Excel telle que présentée : liste par défaut ou liste résultant d\'une recherche multi-critère.</p>
',
			),
			'core_account/list/ordering' => array(
					'en_US' => '
<h4>Ordering</h4>
<p>The list can be sorted according to each column in ascending or descending order.</p>
',
					'fr_FR' => '
<h4>Classement</h4>
<p>La liste peut être triée selon chaque colonne en ordre ascendant ou descendant.</p>
',
			),
			'core_account/list/checkAll' => array(
					'en_US' => '
<h4>Check all</h4>
<p>This check-box allows to check at one time all the items of the list.</p>
					',
					'fr_FR' => '
<h4>Tout sélectionner</h4>
<p>Cette case à cocher permet de sélectionner d\'un coup tous les éléments de la liste.</p>
',
			),
			'core_account/list/groupedActions' => array(
					'en_US' => '
<h4>Grouped actions</h4>
<p>The group action button operates along with the individual or global checkboxes on the left column.</p>
<p>It opens a new panel proposing actions to apply to each student who has previously been checked in the list.</p>
<p>For example you can send an emailing by checking the target accounts and then send the email in a grouped way.</p>
					',
					'fr_FR' => '
<h4>Actions groupées</h4>
<p>Le bouton d\'actions groupées agit conjointement avec les cases à cocher individuelles ou globales en colonne de gauche de la liste.</p>
<p>Il ouvre un nouveau panneau proposant des actions à appliquer à chaque compte qui a préalablement été sélectionné dans la liste.</p>
<p>Par exemple vous pouvez envoyer un emailing en cochant dans la liste les comptes à cibler puis émettre l\'email de façon groupée.</p>
					',
			),
			'core_account/list/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un compte</h4>
<p>Le bouton + permet l\'ajout d\un nouveau compte.</p>
<p>Les engagements liés à ce compte seront créés dans un second temps.</p>
<p>On peut ainsi gérer un regroupement des engagements par compte.</p>
					',
			),
			'core_account/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un compte</h4>
<p>Lors de la création d\'un compte les données principales sont renseignées.</p>
	<ul>
		<li>Identification</li>
		<li>Données de contact</li>
		<li>période de validité du compte (seule la date d\'ouverture est obligatoire)</li>
		<li>Le statut (pour mémoire, le statut <em>Actif</em> conditionne la sélection du compte dans la liste par défaut)</li>
	</ul>
					',
			),
			'core_account/list/detail' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Détail d\'un compte</h4>
<p>Le bouton zoom permet d\'accéder au détail d\'un compte et aux engagements associés.</p>
					',
			),
			'core_account/business' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Gestion des données du compte</h4>
<p>L\'accès au détail d\'un compte permet de consulter et éventuellement en rectifier les données.</p>
<p>Il donne également accès à l\'onglet de gestion du contact de facturation.</p>
<p>Il donne enfin un accès centralisé, en ajout ou modification, aux engagements associés à ce compte.</p>
					',
			),
			'commitment/accountList/add' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Ajout d\'un engagement</h4>
<p>Le bouton + permet l\'ajout d\un nouvel engagement pour ce compte.</p>
					',
			),
			'commitment/accountList/documents' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Documents</h4>
<p>Quatre documents pré-formatés sont disponibles au niveau du dossier d\'inscription annuelle :</p>
	<ul>
		<li>L\'accusé de réception</li>
		<li>La confirmation d\'inscription</li>
		<li>L\'engagement de prise en charge</li>
		<li>L\'attestation scolaire</li>
	</ul>
<p>Ces documents sont générés au format Word et peuvent être complétés manuellement après téléchargement, par exemple si besoin d\'ajouter une mention spécifique.</p>
',
			),

			'commitment/search/title' => array(
					'en_US' => '
<h4>Commitment list</h4>
<p>As a default, all the active commitments are presented in the list.</p>
<p>As soon as a criterion below is specified, the list switch in search mode.</p>
',
					'fr_FR' => '
<h4>Liste des engagements</h4>
<p>Par défaut, tous les engagements actifs sont présentés dans la liste.</p>
<p>Dès lors qu\'un des critères ci-dessous est spécifié, le mode de recherche est automatiquement activé.</p>
',
			),
			'commitment/search/x' => array(
					'en_US' => '
<h4>Return in default mode</h4>
<p>The <code>x</code> button reinitializes all the search criteria and reset the list filtered on active commitments.</p>
',
					'fr_FR' => '
<h4>Retour au mode par défaut</h4>
<p>Le bouton <code>x</code> réinitialise tous les critères de recherche et ré-affiche la liste filtrée sur les engagements actifs.</p>
',
			),
			'commitment/search/export' => array(
					'en_US' => '
<h4>List export</h4>
<p>The list can be exported to Excel as it is presented: defaulting list or list resulting of a multi-criteria search.</p>
',
					'fr_FR' => '
<h4>Export de la liste</h4>
<p>La liste peut être exportée sous Excel telle que présentée : liste par défaut ou liste résultant d\'une recherche multi-critère.</p>
',
			),
			'commitment/list/ordering' => array(
					'en_US' => '
<h4>Ordering</h4>
<p>The list can be sorted according to each column in ascending or descending order.</p>
',
					'fr_FR' => '
<h4>Classement</h4>
<p>La liste peut être triée selon chaque colonne en ordre ascendant ou descendant.</p>
',
			),
			'commitment/list/detail' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Détail d\'un engagement</h4>
<p>Le bouton zoom permet d\'accéder au détail d\'un engagement et aux données de facturation et d\'échéancier associées.</p>
					',
			),
			'commitment/update' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Gestion des données de l\'engagement</h4>
<p>L\'accès au détail d\'un engagement permet de consulter et éventuellement en rectifier les données.</p>
<p>Il donne également accès au détail de facturation :</p>
	<ul>
		<li>Le produit souscrit</li>
		<li>Les différentes options souscrites</li>
	</ul>
<p>Il donne enfin accès à l\'échéancier associé à cet engagement.</p>
					',
			),
			'commitment/invoice' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Facture</h4>
<p>Une facture comptable est disponible en téléchargement, ainsi qu\'une facture simplifiée, dite proforma (TTC sans données de TVA).</p>
					',
			),

			'commitmentTerm/search/title' => array(
					'en_US' => '
<h4>Term list</h4>
<p>As a default, all the current terms (to be settled or collected) are presented in the list.</p>
<p>As soon as a criterion below is specified, the list switch in search mode.</p>
',
					'fr_FR' => '
<h4>Liste des échéances</h4>
<p>Par défaut, toutes les échéances en cours (à régler ou encaisser) sont présentées dans la liste.</p>
<p>Dès lors qu\'un des critères ci-dessous est spécifié, le mode de recherche est automatiquement activé.</p>
',
			),
			'commitmentTerm/search/x' => array(
					'en_US' => '
<h4>Return in default mode</h4>
<p>The <code>x</code> button reinitializes all the search criteria and reset the list filtered on current terms.</p>
',
					'fr_FR' => '
<h4>Retour au mode par défaut</h4>
<p>Le bouton <code>x</code> réinitialise tous les critères de recherche et ré-affiche la liste filtrée sur les échéances en cours.</p>
',
			),
			'commitmentTerm/search/export' => array(
					'en_US' => '
<h4>List export</h4>
<p>The list can be exported to Excel as it is presented: defaulting list or list resulting of a multi-criteria search.</p>
',
					'fr_FR' => '
<h4>Export de la liste</h4>
<p>La liste peut être exportée sous Excel telle que présentée : liste par défaut ou liste résultant d\'une recherche multi-critère.</p>
',
			),
			'commitmentTerm/list/ordering' => array(
					'en_US' => '
<h4>Ordering</h4>
<p>The list can be sorted according to each column in ascending or descending order.</p>
',
					'fr_FR' => '
<h4>Classement</h4>
<p>La liste peut être triée selon chaque colonne en ordre ascendant ou descendant.</p>
',
			),
			'commitmentTerm/list/detail' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Détail d\'une échéance</h4>
<p>Le bouton zoom permet d\'accéder au détail d\'une échéance.</p>
					',
			),
			'commitmentTerm/update' => array(
					'en_US' => '',
					'fr_FR' => '
<h4>Gestion du statut et des attributs de l\'échéance</h4>
<p>L\'accès au détail d\'une échéance permet de consulter et éventuellement en rectifier les données.</p>
<p>Il permet également d\'en actualiser la statut et y associer une pièce jointe (ex. scan de chèque).</p>
					',
			),
	),
]);
