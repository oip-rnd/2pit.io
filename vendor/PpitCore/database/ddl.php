<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "2pit";

$commands = array(
		
	// core_community
	"DROP TABLE IF EXISTS `core_community`;",
	"CREATE TABLE `core_community` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `instance_id` int(11) DEFAULT NULL,
	  `next_credit_consumption_date` date DEFAULT NULL,
	  `last_credit_consumption_date` date DEFAULT NULL,
	  `status` varchar(255) DEFAULT NULL,
	  `place_id` int(11) DEFAULT NULL,
	  `identifier` varchar(255) DEFAULT NULL,
	  `name` varbinary(255) NOT NULL DEFAULT '',
	  `contact_1_id` int(11) DEFAULT NULL,
	  `contact_1_status` varchar(255) DEFAULT NULL,
	  `contact_2_id` int(11) DEFAULT NULL,
	  `contact_2_status` varchar(255) DEFAULT NULL,
	  `contact_3_id` int(11) DEFAULT NULL,
	  `contact_3_status` varchar(255) DEFAULT NULL,
	  `contact_4_id` int(11) DEFAULT NULL,
	  `contact_4_status` varchar(255) DEFAULT NULL,
	  `contact_5_id` int(11) DEFAULT NULL,
	  `contact_5_status` varchar(255) DEFAULT NULL,
	  `origine` varchar(255) DEFAULT NULL,
	  `root_document_id` int(11) DEFAULT NULL,
	  `audit` text,
	  `creation_time` datetime DEFAULT NULL,
	  `creation_user` int(11) DEFAULT NULL,
	  `update_time` datetime DEFAULT NULL,
	  `update_user` int(11) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;",
	"INSERT INTO `core_community` VALUES (1,0,'2016-10-01',NULL,'new',NULL,NULL,'D\Èmosth\Ëne, Jean',7294,NULL,0,'main',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3514,'[]','2016-08-24 21:15:59',3349,'2016-08-24 21:53:00',3349),(2,0,'2017-02-28',NULL,'new',0,NULL,'D\Èmosth\Ëne, H\Ël\Ëne',7295,NULL,0,'main',0,NULL,0,NULL,0,NULL,NULL,3516,'[]','2016-08-24 21:17:52',3349,'2017-01-28 14:00:17',76),(3,0,'2016-10-01',NULL,'new',NULL,NULL,'Legrand, Alexandre',7296,NULL,0,'main',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3518,'[]','2016-08-24 21:52:38',3349,'2016-08-24 21:52:38',3349)",

	// core_credit
	"DROP TABLE IF EXISTS `core_credit`;",
	"CREATE TABLE `core_credit` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `instance_id` int(11) DEFAULT NULL,
	  `status` varchar(255) DEFAULT NULL,
	  `type` varchar(255) DEFAULT NULL,
	  `quantity` int(11) DEFAULT NULL,
	  `activation_date` date DEFAULT NULL,
	  `audit` mediumtext,
	  `creation_time` datetime DEFAULT NULL,
	  `creation_user` int(11) DEFAULT NULL,
	  `update_time` datetime DEFAULT NULL,
	  `update_user` int(11) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",

	// core_instance
	"DROP TABLE IF EXISTS `core_instance`;",
	"CREATE TABLE `core_instance` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `status` varchar(255) DEFAULT NULL,
	  `fqdn` varchar(255) DEFAULT NULL,
	  `default_locale` varchar(255) DEFAULT NULL,
	  `caption` varchar(255) DEFAULT NULL,
	  `sponsor_instance_caption` varchar(255) DEFAULT NULL,
	  `is_active` tinyint(1) DEFAULT NULL,
	  `ethical_charter` mediumtext,
	  `home_page` varchar(255) DEFAULT NULL,
	  `specifications` mediumtext,
	  `legal_notices` mediumtext,
	  `audit` text,
	  `creation_time` datetime DEFAULT NULL,
	  `creation_user` int(11) DEFAULT NULL,
	  `update_time` datetime DEFAULT NULL,
	  `update_user` int(11) DEFAULT NULL,
	  `credits` int(11) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
	"INSERT INTO `core_instance` VALUES (0,NULL,'','fr_FR','D√©mo','',1,NULL,NULL,'{\"ppitDocument\":{\"dropboxCredential\":\"9CRvqMLXD40AAAAAAAAAw01AjFizF3WrpmnYhseJ8RXng1QXiokyfjKqLczV5aQ6\"}}',NULL,NULL,'2015-08-21 23:41:13',1,'2015-08-21 23:41:13',1,NULL)",

	"DROP TABLE IF EXISTS `core_place`;",
	"CREATE TABLE `core_place` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `instance_id` int(11) DEFAULT NULL,
	  `status` varchar(255) DEFAULT NULL,
	  `identifier` varchar(255) DEFAULT NULL,
	  `caption` varchar(255) DEFAULT NULL,
	  `opening_date` date DEFAULT NULL,
	  `closing_date` date DEFAULT NULL,
	  `tax_regime` int(11) DEFAULT NULL,
	  `reception_contact_id` int(11) DEFAULT NULL,
	  `delivery_contact_id` int(11) DEFAULT NULL,
	  `logo_src` varchar(255) DEFAULT NULL,
	  `logo_width` int(11) DEFAULT NULL,
	  `logo_height` int(11) DEFAULT NULL,
	  `logo_href` varchar(255) DEFAULT NULL,
	  `banner_src` varchar(255) DEFAULT NULL,
	  `banner_href` varchar(255) DEFAULT NULL,
	  `legal_footer` varchar(255) DEFAULT NULL,
	  `property_1` varchar(255) DEFAULT NULL,
	  `property_2` varchar(255) DEFAULT NULL,
	  `property_3` varchar(255) DEFAULT NULL,
	  `property_4` varchar(255) DEFAULT NULL,
	  `property_5` varchar(255) DEFAULT NULL,
	  `property_6` varchar(255) DEFAULT NULL,
	  `property_7` varchar(255) DEFAULT NULL,
	  `property_8` varchar(255) DEFAULT NULL,
	  `property_9` varchar(255) DEFAULT NULL,
	  `property_10` varchar(255) DEFAULT NULL,
	  `audit` text,
	  `creation_time` datetime DEFAULT NULL,
	  `creation_user` int(11) DEFAULT NULL,
	  `update_time` datetime DEFAULT NULL,
	  `update_user` int(11) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;",
	"INSERT INTO `core_place` VALUES (1,0,'new','CHO','Cholet','2016-07-01','9999-12-31',1,0,0,'/logos/FM Sports/fmsport-logo.png',200,60,'','/logos/FM Sports/fmsport-logo.png','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'[{\"time\":\"2016-12-26 23:35:10\",\"n_fn\":\"admin58, bruno\"},{\"time\":\"2016-12-29 8:17:03\",\"n_fn\":\"admin58, bruno\"},{\"time\":\"2017-01-24 22:25:16\",\"n_fn\":\"admin58, bruno\"}]',NULL,NULL,'2017-01-24 22:25:16',153);",
	
	// core_vcard
	"DROP TABLE IF EXISTS `core_vcard`;",
	"CREATE TABLE `core_vcard` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `instance_id` int(11) DEFAULT NULL,
	  `applications` text,
	  `last_credit_consumption_date` date DEFAULT NULL,
	  `community_id` int(11) DEFAULT NULL,
	  `n_title` varchar(255) DEFAULT NULL,
	  `n_first` varchar(255) DEFAULT NULL,
	  `n_last` varchar(255) DEFAULT NULL,
	  `n_fn` varchar(255) DEFAULT NULL,
	  `org` varchar(255) DEFAULT NULL,
	  `tel_work` varchar(255) DEFAULT NULL,
	  `tel_cell` varchar(255) DEFAULT NULL,
	  `email` varchar(255) DEFAULT NULL,
	  `adr_street` varchar(255) DEFAULT NULL,
	  `adr_extended` varchar(255) DEFAULT NULL,
	  `adr_post_office_box` varchar(255) DEFAULT NULL,
	  `adr_zip` varchar(255) DEFAULT NULL,
	  `adr_city` varchar(255) DEFAULT NULL,
	  `adr_state` varchar(255) DEFAULT NULL,
	  `adr_country` varchar(255) DEFAULT NULL,
	  `sex` char(1) DEFAULT NULL,
	  `birth_date` date DEFAULT NULL,
	  `place_of_birth` varchar(255) DEFAULT NULL,
	  `nationality` varchar(255) DEFAULT NULL,
	  `photo_link_id` varchar(255) DEFAULT NULL,
	  `origine` varchar(255) DEFAULT NULL,
	  `roles` text,
	  `perimeters` text,
	  `locale` char(10) DEFAULT NULL,
	  `is_notified` tinyint(1) DEFAULT NULL,
	  `is_demo_mode_active` tinyint(1) DEFAULT NULL,
	  `creation_time` datetime DEFAULT NULL,
	  `creation_user` int(11) DEFAULT NULL,
	  `update_time` datetime DEFAULT NULL,
	  `update_user` int(11) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;",
	"INSERT INTO `core_vcard` VALUES (1,0,'{\"p-pit-studies\":\"null\"}',NULL,0,'M.','Antoine','D√©moadmin','D√©moadmin, Antoine','Athens','04 11 11 11 11','06 66 66 66 66','postmaster@p-pit.fr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'[\"admin\",\"manager\",\"business_owner\"]','[]','fr_FR',1,1,'2015-06-12 12:36:36',0,'2015-06-12 12:36:36',0),(2,0,'[]',NULL,0,NULL,'H√É¬©l√É¬®ne','D√É¬©mosth√É¬®ne','D√É¬©mosth√É¬®ne, H√É¬©l√É¬®ne',NULL,'',NULL,'helene.demosthene@athens.fr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'[]','[]','fr_FR',NULL,NULL,'2017-01-28 13:56:43',76,'2017-01-28 13:56:43',76);",
);

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

foreach ($commands as $sql) {
	if (mysqli_query($conn, $sql)) {
		echo "command executed successfully\n";
	} else {
		echo "Error creating table: " . mysqli_error($conn) . "\n";
	}
}

mysqli_close($conn);
