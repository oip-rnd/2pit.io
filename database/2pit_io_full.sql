-- phpMyAdmin SQL Dump
-- version 4.6.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 18, 2018 at 09:08 PM
-- Server version: 5.7.9
-- PHP Version: 5.6.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `2pit_io`
--

-- --------------------------------------------------------

--
-- Table structure for table `commitment`
--

CREATE TABLE `commitment` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `credit_status` varchar(255) DEFAULT NULL,
  `next_credit_consumption_date` date DEFAULT NULL,
  `last_credit_consumption_date` date DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `description` varchar(2047) DEFAULT NULL,
  `product_identifier` varchar(255) DEFAULT NULL,
  `product_brand` varchar(255) DEFAULT NULL,
  `product_caption` varchar(255) DEFAULT NULL,
  `quantity` float DEFAULT NULL,
  `unit_price` decimal(14,4) DEFAULT NULL,
  `amount` decimal(14,4) DEFAULT NULL,
  `taxable_1_amount` decimal(14,4) DEFAULT NULL,
  `taxable_2_amount` decimal(14,4) DEFAULT NULL,
  `taxable_3_amount` decimal(14,4) DEFAULT NULL,
  `options` text,
  `including_options_amount` decimal(14,4) DEFAULT NULL,
  `taxable_1_total` decimal(14,4) DEFAULT NULL,
  `taxable_2_total` decimal(14,4) DEFAULT NULL,
  `taxable_3_total` decimal(14,4) DEFAULT NULL,
  `cgv` mediumtext,
  `renewable` tinyint(1) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `quotation_identifier` varchar(255) DEFAULT NULL,
  `invoice_identifier` varchar(255) DEFAULT NULL,
  `credit_identifier` varchar(255) DEFAULT NULL,
  `commitment_date` date DEFAULT NULL,
  `retraction_limit` date DEFAULT NULL,
  `retraction_date` date DEFAULT NULL,
  `expected_shipment_date` date DEFAULT NULL,
  `shipment_date` date DEFAULT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `expected_commissioning_date` date DEFAULT NULL,
  `commissioning_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `credit_date` date DEFAULT NULL,
  `expected_settlement_date` date DEFAULT NULL,
  `settlement_date` date DEFAULT NULL,
  `order_form_id` int(11) DEFAULT NULL,
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
  `property_11` varchar(255) DEFAULT NULL,
  `property_12` varchar(255) DEFAULT NULL,
  `property_13` varchar(255) DEFAULT NULL,
  `property_14` varchar(255) DEFAULT NULL,
  `property_15` varchar(255) DEFAULT NULL,
  `property_16` varchar(255) DEFAULT NULL,
  `property_17` varchar(255) DEFAULT NULL,
  `property_18` varchar(255) DEFAULT NULL,
  `property_19` varchar(255) DEFAULT NULL,
  `property_20` varchar(255) DEFAULT NULL,
  `property_21` varchar(255) DEFAULT NULL,
  `property_22` varchar(255) DEFAULT NULL,
  `property_23` varchar(255) DEFAULT NULL,
  `property_24` varchar(255) DEFAULT NULL,
  `property_25` varchar(255) DEFAULT NULL,
  `property_26` varchar(255) DEFAULT NULL,
  `property_27` varchar(255) DEFAULT NULL,
  `property_28` varchar(255) DEFAULT NULL,
  `property_29` varchar(255) DEFAULT NULL,
  `property_30` varchar(255) DEFAULT NULL,
  `audit` text,
  `excluding_tax` decimal(14,4) DEFAULT NULL,
  `tax_regime` varchar(255) DEFAULT NULL,
  `tax_1_amount` decimal(14,4) DEFAULT NULL,
  `tax_2_amount` decimal(14,4) DEFAULT NULL,
  `tax_3_amount` decimal(14,4) DEFAULT NULL,
  `tax_amount` decimal(10,2) DEFAULT NULL,
  `tax_inclusive` decimal(10,2) DEFAULT NULL,
  `commitment_message_id` int(11) DEFAULT NULL,
  `change_message_id` int(11) DEFAULT NULL,
  `confirmation_message_id` int(11) DEFAULT NULL,
  `shipment_message_id` int(11) DEFAULT NULL,
  `delivery_message_id` int(11) DEFAULT NULL,
  `commissioning_message_id` int(11) DEFAULT NULL,
  `invoice_message_id` int(11) DEFAULT NULL,
  `credit_message_id` int(11) DEFAULT NULL,
  `settlement_message_id` int(11) DEFAULT NULL,
  `notification_time` datetime DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `customer_identifier` varchar(255) DEFAULT NULL,
  `customer_invoice_name` varchar(255) DEFAULT NULL,
  `customer_n_fn` varchar(255) DEFAULT NULL,
  `customer_adr_street` varchar(255) DEFAULT NULL,
  `customer_adr_extended` varchar(255) DEFAULT NULL,
  `customer_adr_post_office_box` varchar(255) DEFAULT NULL,
  `customer_adr_zip` varchar(255) DEFAULT NULL,
  `customer_adr_city` varchar(255) DEFAULT NULL,
  `customer_adr_state` varchar(255) DEFAULT NULL,
  `customer_adr_country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commitment_event`
--

CREATE TABLE `commitment_event` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `begin_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `location` text,
  `title` text,
  `content` mediumtext,
  `image` text,
  `criteria` text,
  `target` text,
  `audit` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commitment_message`
--

CREATE TABLE `commitment_message` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `authentication_token` varchar(255) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `direction` char(1) DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `content` longtext,
  `http_status` varchar(255) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commitment_notification`
--

CREATE TABLE `commitment_notification` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `criteria` text,
  `title` varchar(255) DEFAULT NULL,
  `content` mediumtext,
  `image` text,
  `attachment_type` varchar(255) DEFAULT NULL,
  `attachment_label` varchar(255) DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `target` text,
  `begin_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `audit` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commitment_subscription`
--

CREATE TABLE `commitment_subscription` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `product_identifier` varchar(255) DEFAULT NULL,
  `description` varchar(2047) DEFAULT NULL,
  `delivered_quantity` float DEFAULT NULL,
  `invoiced_quantity` float DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `opening_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `audit` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commitment_term`
--

CREATE TABLE `commitment_term` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `commitment_id` int(11) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `settlement_date` date DEFAULT NULL,
  `collection_date` date DEFAULT NULL,
  `amount` decimal(14,4) DEFAULT NULL,
  `means_of_payment` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `comment` varchar(2047) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `audit` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commitment_year`
--

CREATE TABLE `commitment_year` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `digits` int(11) DEFAULT NULL,
  `next_value` int(11) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contact_contract`
--

CREATE TABLE `contact_contract` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `customer_community_id` int(11) DEFAULT NULL,
  `customer_bill_contact_id` int(11) DEFAULT NULL,
  `customer_properties` text,
  `customer_roles` text,
  `supplyer_community_id` int(11) DEFAULT NULL,
  `supplyer_properties` text,
  `supplyer_roles` text,
  `services` text,
  `order_types` text,
  `order_status` text,
  `order_properties` text,
  `opening_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contact_event`
--

CREATE TABLE `contact_event` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `description` varchar(2047) DEFAULT NULL,
  `comment` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contact_message`
--

CREATE TABLE `contact_message` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `to` text,
  `cc` text,
  `cci` text,
  `subject` text,
  `from_mail` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `body` longtext,
  `emission_time` datetime DEFAULT NULL,
  `volume` int(11) DEFAULT NULL,
  `cost` float DEFAULT NULL,
  `accepted` text,
  `rejected` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contact_vcard_property`
--

CREATE TABLE `contact_vcard_property` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `vcard_id` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `text_value` varchar(255) DEFAULT NULL,
  `blob_value` blob,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_account`
--

CREATE TABLE `core_account` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `basket` varchar(255) DEFAULT NULL,
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
  `customer_community_id` int(11) DEFAULT NULL,
  `customer_bill_contact_id` int(11) DEFAULT NULL,
  `supplier_community_id` int(11) DEFAULT NULL,
  `opening_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `callback_date` date DEFAULT NULL,
  `first_activation_date` date DEFAULT NULL,
  `priority` varchar(255) DEFAULT NULL,
  `origine` varchar(255) DEFAULT NULL,
  `next_meeting_date` datetime DEFAULT NULL,
  `next_meeting_confirmed` datetime DEFAULT NULL,
  `contact_history` mediumtext,
  `terms_of_sales` mediumtext,
  `notification_time` datetime DEFAULT NULL,
  `terms_of_use_validation_time` datetime DEFAULT NULL,
  `charter_validation_time` datetime DEFAULT NULL,
  `opt_out_time` datetime DEFAULT NULL,
  `availability_begin` date DEFAULT NULL,
  `availability_end` date DEFAULT NULL,
  `availability_exceptions` mediumtext,
  `availability_constraints` text,
  `credits` varchar(255) DEFAULT NULL,
  `default_means_of_payment` varchar(255) DEFAULT NULL,
  `transfer_order_id` varchar(255) DEFAULT NULL,
  `transfer_order_date` date DEFAULT NULL,
  `bank_identifier` varchar(255) DEFAULT NULL,
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
  `property_11` varchar(255) DEFAULT NULL,
  `property_12` varchar(255) DEFAULT NULL,
  `property_13` varchar(255) DEFAULT NULL,
  `property_14` varchar(255) DEFAULT NULL,
  `property_15` varchar(255) DEFAULT NULL,
  `property_16` varchar(255) DEFAULT NULL,
  `json_property_1` mediumtext,
  `json_property_2` mediumtext,
  `json_property_3` mediumtext,
  `json_property_4` mediumtext,
  `json_property_5` mediumtext,
  `comment_1` text,
  `comment_2` text,
  `comment_3` text,
  `comment_4` text,
  `notification_ids` text,
  `audit` text,
  `authentication_token` varchar(255) DEFAULT NULL,
  `currently_updated_by` varchar(255) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `core_account`
--

INSERT INTO `core_account` (`id`, `instance_id`, `status`, `type`, `place_id`, `identifier`, `name`, `basket`, `contact_1_id`, `contact_1_status`, `contact_2_id`, `contact_2_status`, `contact_3_id`, `contact_3_status`, `contact_4_id`, `contact_4_status`, `contact_5_id`, `contact_5_status`, `customer_community_id`, `customer_bill_contact_id`, `supplier_community_id`, `opening_date`, `closing_date`, `callback_date`, `first_activation_date`, `priority`, `origine`, `next_meeting_date`, `next_meeting_confirmed`, `contact_history`, `terms_of_sales`, `notification_time`, `terms_of_use_validation_time`, `charter_validation_time`, `opt_out_time`, `availability_begin`, `availability_end`, `availability_exceptions`, `availability_constraints`, `credits`, `default_means_of_payment`, `transfer_order_id`, `transfer_order_date`, `bank_identifier`, `property_1`, `property_2`, `property_3`, `property_4`, `property_5`, `property_6`, `property_7`, `property_8`, `property_9`, `property_10`, `property_11`, `property_12`, `property_13`, `property_14`, `property_15`, `property_16`, `json_property_1`, `json_property_2`, `json_property_3`, `json_property_4`, `json_property_5`, `comment_1`, `comment_2`, `comment_3`, `comment_4`, `notification_ids`, `audit`, `authentication_token`, `currently_updated_by`, `creation_time`, `creation_user`, `update_time`, `update_user`) VALUES
(21085, 32, 'active', 'pbc', 61, '3', 'ADMIN, Admin', '', 23726, 'main', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2018-04-22', '9999-12-31', NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, NULL, '2018-10-10 00:00:00', '2018-10-10 00:00:00', NULL, NULL, '9999-12-31', '[]', '[]', '{\r\n    "earned": 5\r\n}', NULL, NULL, NULL, NULL, NULL, '', 'E', 'true', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RESG', 'Val', '[]', '[]', '[]', '[]', '[]', NULL, NULL, NULL, NULL, NULL, '[]', '76aef8501b9a3defd7ca9c82680bfba0', 'LARTILLOT, Bruno', '2018-04-22 23:33:44', NULL, '2018-10-17 20:59:33', 83),
(22690, 32, 'active', 'pbc', 61, '22690', 'Organizer, Manh', '', 24065, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2018-05-12', '9999-12-31', NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, NULL, '2018-10-10 00:00:00', '2018-10-10 00:00:00', NULL, NULL, '9999-12-31', '[]', '[]', '{\n    "earned": 5\n}', NULL, NULL, NULL, NULL, 'contributor,requestor', '21085', 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '[]', '[]', '[]', '[]', NULL, NULL, NULL, NULL, NULL, '[]', 'd68a215c20b1c9bfdf1a20ded8a89fe7', NULL, '2018-05-12 07:51:57', 83, '2018-10-18 16:21:49', 83);

-- --------------------------------------------------------

--
-- Table structure for table `core_app`
--

CREATE TABLE `core_app` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specification` longtext COLLATE utf8mb4_unicode_ci,
  `audit` mediumtext COLLATE utf8mb4_unicode_ci,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `core_community`
--

CREATE TABLE `core_community` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `activation_date` date DEFAULT NULL,
  `next_credit_consumption_date` date DEFAULT NULL,
  `last_credit_consumption_date` date DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `authentication_token` varchar(255) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `specifications` longtext,
  `home_title` text,
  `home_description` text,
  `external_links` text,
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
  `vcard_properties` text,
  `authorized_roles` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_config`
--

CREATE TABLE `core_config` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `content` longtext,
  `previous_content` longtext,
  `audit` mediumtext,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_credit`
--

CREATE TABLE `core_credit` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `activation_date` date DEFAULT NULL,
  `audit` mediumtext,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_document`
--

CREATE TABLE `core_document` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `locale_1` varchar(255) DEFAULT NULL,
  `locale_2` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `acl` text,
  `summary` text,
  `summary_locale_1` text,
  `summary_locale_2` text,
  `image` text,
  `image_locale_1` text,
  `image_locale_2` text,
  `first_part_id` int(11) DEFAULT NULL,
  `mime` varchar(255) DEFAULT NULL,
  `url` varchar(2047) DEFAULT NULL,
  `properties` text,
  `properties_locale_1` text,
  `properties_locale_2` text,
  `audit` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `community_id` int(11) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `lock` tinyint(4) DEFAULT NULL,
  `properties_en_us` text,
  `properties_fr_fr` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_document_part`
--

CREATE TABLE `core_document_part` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `content` longtext,
  `content_locale_1` longtext,
  `content_locale_2` longtext,
  `image` varchar(255) DEFAULT NULL,
  `image_locale_1` varchar(255) DEFAULT NULL,
  `image_locale_2` varchar(255) DEFAULT NULL,
  `next_part_id` int(11) DEFAULT NULL,
  `audit` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `saved_content` text,
  `is_undone` tinyint(1) DEFAULT NULL,
  `lock` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_event`
--

CREATE TABLE `core_event` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `subcategory` varchar(255) DEFAULT NULL,
  `locale_1` varchar(255) DEFAULT NULL,
  `locale_2` varchar(255) DEFAULT NULL,
  `community_id` int(11) DEFAULT NULL,
  `vcard_id` int(11) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `caption_locale_1` varchar(255) DEFAULT NULL,
  `caption_locale_2` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `description_locale_1` mediumtext,
  `description_locale_2` mediumtext,
  `begin_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `day_of_week` tinyint(4) DEFAULT NULL,
  `day_of_month` tinyint(4) DEFAULT NULL,
  `exception_dates` text,
  `begin_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `time_zone` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `matched_accounts` varchar(255) DEFAULT NULL,
  `matching_log` mediumtext,
  `rewards` mediumtext,
  `feedbacks` mediumtext,
  `value` decimal(14,4) DEFAULT NULL,
  `comments` mediumtext,
  `modality_1` varchar(255) DEFAULT NULL,
  `value_1` float DEFAULT NULL,
  `modality_2` varchar(255) DEFAULT NULL,
  `value_2` float DEFAULT NULL,
  `modality_3` varchar(255) DEFAULT NULL,
  `value_3` float DEFAULT NULL,
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
  `property_11` varchar(255) DEFAULT NULL,
  `property_12` varchar(255) DEFAULT NULL,
  `property_13` varchar(255) DEFAULT NULL,
  `property_14` varchar(255) DEFAULT NULL,
  `property_15` varchar(255) DEFAULT NULL,
  `property_16` varchar(255) DEFAULT NULL,
  `property_17` varchar(255) DEFAULT NULL,
  `property_18` varchar(255) DEFAULT NULL,
  `property_19` varchar(255) DEFAULT NULL,
  `property_20` varchar(255) DEFAULT NULL,
  `property_21` mediumtext,
  `property_22` mediumtext,
  `property_23` mediumtext,
  `property_24` mediumtext,
  `property_25` mediumtext,
  `property_26` mediumtext,
  `property_27` mediumtext,
  `property_28` mediumtext,
  `property_29` mediumtext,
  `property_30` mediumtext,
  `audit` mediumtext,
  `authentication_token` varchar(255) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `core_event`
--

INSERT INTO `core_event` (`id`, `instance_id`, `status`, `type`, `place_id`, `account_id`, `category`, `subcategory`, `locale_1`, `locale_2`, `community_id`, `vcard_id`, `identifier`, `caption`, `caption_locale_1`, `caption_locale_2`, `description`, `description_locale_1`, `description_locale_2`, `begin_date`, `end_date`, `day_of_week`, `day_of_month`, `exception_dates`, `begin_time`, `end_time`, `time_zone`, `location`, `latitude`, `longitude`, `matched_accounts`, `matching_log`, `rewards`, `feedbacks`, `value`, `comments`, `modality_1`, `value_1`, `modality_2`, `value_2`, `modality_3`, `value_3`, `property_1`, `property_2`, `property_3`, `property_4`, `property_5`, `property_6`, `property_7`, `property_8`, `property_9`, `property_10`, `property_11`, `property_12`, `property_13`, `property_14`, `property_15`, `property_16`, `property_17`, `property_18`, `property_19`, `property_20`, `property_21`, `property_22`, `property_23`, `property_24`, `property_25`, `property_26`, `property_27`, `property_28`, `property_29`, `property_30`, `audit`, `authentication_token`, `creation_time`, `creation_user`, `update_time`, `update_user`) VALUES
(4070, 32, 'completed', 'request', 61, 21085, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Demo - My urgent request', NULL, NULL, NULL, NULL, NULL, NULL, '9999-12-31', 0, 0, '[]', NULL, NULL, 0, NULL, 0, 0, '', '[]', '[]', '[]', '0.0000', '[]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'communication,marketing', 'troubleshooting', 'Having already solved sale cases in SG context', '2018-10-08', '2h', 'London', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Help! I wan\'t somebody\'s help! Would you please help me!', 'I have to do something...\r\nI first tried...', NULL, NULL, NULL, NULL, NULL, '[]', NULL, '2018-09-28 16:09:14', 83, '2018-10-10 09:14:42', 83),
(4071, 32, 'completed', 'event', 61, 21085, 'innovation', NULL, NULL, NULL, NULL, 0, NULL, 'Demo - My innovation event', NULL, NULL, 'The coolest event of the innovation week', NULL, NULL, '2018-11-05', '2018-11-05', 0, 0, '[]', '10:00:00', '12:00:00', 0, 'Tower Hill', 0, 0, '', '[]', '[]', '[]', '6.0000', '[]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Everybody', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Come soon to have a place', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, '2018-09-28 16:18:27', 83, '2018-10-10 09:19:45', 83),
(4072, 32, 'connected', 'event', 61, 22690, 'innovation', NULL, NULL, NULL, NULL, 0, 'ABCD', 'Demo event', NULL, NULL, 'A description of the event', NULL, NULL, '2018-11-06', '2018-11-06', 0, 0, '[]', '10:00:00', '12:00:00', 0, 'Tower Hill', 0, 0, '21085', '[]', '[]', '[]', '5.0000', '[]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, '2018-09-28 16:43:23', 83, '2018-10-17 20:59:33', 83);

-- --------------------------------------------------------

--
-- Table structure for table `core_instance`
--

CREATE TABLE `core_instance` (
  `id` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `fqdn` varchar(255) DEFAULT NULL,
  `default_locale` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `default_place_id` int(11) DEFAULT NULL,
  `sponsor_instance_caption` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `validated_ethical_charter_id` int(11) DEFAULT NULL,
  `applications` varchar(255) DEFAULT NULL,
  `home_page` varchar(255) DEFAULT NULL,
  `specifications` longtext,
  `legal_notices` mediumtext,
  `audit` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `currency` char(3) DEFAULT NULL,
  `currency_symbol` char(1) DEFAULT NULL,
  `community_id` int(11) DEFAULT NULL,
  `header_code` text,
  `pdf_header_code` text,
  `ethical_charter` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `core_instance`
--

INSERT INTO `core_instance` (`id`, `status`, `fqdn`, `default_locale`, `caption`, `default_place_id`, `sponsor_instance_caption`, `is_active`, `credits`, `validated_ethical_charter_id`, `applications`, `home_page`, `specifications`, `legal_notices`, `audit`, `creation_time`, `creation_user`, `update_time`, `update_user`, `currency`, `currency_symbol`, `community_id`, `header_code`, `pdf_header_code`, `ethical_charter`) VALUES
(32, 'accepted', 'localhost', 'fr_FR', 'probonocorpo', 61, NULL, 1, NULL, NULL, '{"synapps":true,"p-pit-admin":false}', 'landing/template2', '{\r\n    "credit": {\r\n        "unlimitedCredits": true\r\n    },\r\n    "styleSheet": {\r\n        "navbar": "navbar-default navbar-fixed-top",\r\n        "panelHeadingBackground": "#006179",\r\n        "panelHeadingColor": "#FFFFFF"\r\n    },\r\n    "bootstrap4": {\r\n        "logo": "PBC-logo-web-fleur.png",\r\n        "logo-height": "40"\r\n    },\r\n    "headerParams": {\r\n        "background-color": "#79CCF3",\r\n        "shift": 0,\r\n        "anchor": {\r\n            "type": "route",\r\n            "route": "public\\/home",\r\n            "params": []\r\n        },\r\n        "logo": "PBC-logo-fleur-texte.png",\r\n        "logo-height": 40,\r\n        "logo-href": "https:\\/\\/www.2pit.io",\r\n        "signature": null,\r\n        "advert": "probonocorpo.png",\r\n        "advert-width": 40,\r\n        "self-powered": true\r\n    },\r\n    "menus\\/synapps": {\r\n        "entries": {\r\n            "contact": {\r\n                "route": "account\\/indexAlt",\r\n                "params": {\r\n                    "entry": "contact",\r\n                    "type": "pbc",\r\n                    "app": "synapps"\r\n                },\r\n                "glyphicon": "glyphicon-user",\r\n                "label": {\r\n                    "default": "All the contacts",\r\n                    "fr_FR": "Tous contacts"\r\n                }\r\n            },\r\n            "account": {\r\n                "route": "account\\/indexAlt",\r\n                "params": {\r\n                    "entry": "account",\r\n                    "type": "pbc",\r\n                    "app": "synapps"\r\n                },\r\n                "glyphicon": "glyphicon-user",\r\n                "label": {\r\n                    "default": "Active",\r\n                    "fr_FR": "Actifs"\r\n                }\r\n            },\r\n            "group": {\r\n                "route": "account\\/indexAlt",\r\n                "params": {\r\n                    "entry": "group",\r\n                    "type": "group",\r\n                    "app": "synapps"\r\n                },\r\n                "glyphicon": "glyphicon-user",\r\n                "label": {\r\n                    "default": "Groups",\r\n                    "fr_FR": "Groupes"\r\n                }\r\n            },\r\n            "request": {\r\n                "route": "event\\/indexAlt",\r\n                "params": {\r\n                    "type": "request",\r\n                    "category": "request",\r\n                    "app": "synapps"\r\n                },\r\n                "label": {\r\n                    "en_US": "Requests",\r\n                    "fr_FR": "Demandes"\r\n                }\r\n            },\r\n            "survey_profile": {\r\n                "route": "event\\/indexAlt",\r\n                "params": {\r\n                    "type": "course_test",\r\n                    "category": "survey_profile",\r\n                    "app": "synapps"\r\n                },\r\n                "label": {\r\n                    "en_US": "Course tests",\r\n                    "fr_FR": "Tests parcours"\r\n                }\r\n            },\r\n            "ux_design": {\r\n                "route": "event\\/indexAlt",\r\n                "params": {\r\n                    "type": "survey",\r\n                    "category": "ux_design",\r\n                    "app": "synapps"\r\n                },\r\n                "label": {\r\n                    "en_US": "Interviews",\r\n                    "fr_FR": "Interviews"\r\n                }\r\n            },\r\n            "email": {\r\n                "route": "event\\/index",\r\n                "params": {\r\n                    "type": "email",\r\n                    "app": "synapps"\r\n                },\r\n                "label": {\r\n                    "en_US": "Emails",\r\n                    "fr_FR": "Emails"\r\n                }\r\n            }\r\n        },\r\n        "labels": {\r\n            "default": "Back-office"\r\n        }\r\n    },\r\n    "instance\\/charter": {\r\n        "default": "To be completed"\r\n    },\r\n    "manageable_roles": [\r\n        "admin",\r\n        "sales_manager"\r\n    ],\r\n    "landing_account_type": "pbc",\r\n    "core_account\\/generic\\/property\\/property_1": {\r\n        "mandatory": false,\r\n        "definition": "inline",\r\n        "type": "select",\r\n        "multiple": true,\r\n        "modalities": {\r\n            "contributor": {\r\n                "default": "Contributor",\r\n                "fr_FR": "Contributeur"\r\n            },\r\n            "requestor": {\r\n                "default": "Requestor",\r\n                "fr_FR": "Demandeur"\r\n            }\r\n        },\r\n        "labels": {\r\n            "default": "Role",\r\n            "fr_FR": "R\\u00f4le"\r\n        }\r\n    },\r\n    "core_account\\/generic\\/property\\/property_2": {\r\n        "mandatory": false,\r\n        "definition": "inline",\r\n        "type": "select",\r\n        "multiple": true,\r\n        "modalities": {\r\n            "available": {\r\n                "default": "Available for a phone call",\r\n                "fr_FR": "Disponible pour un point t\\u00e9l\\u00e9phonique"\r\n            }\r\n        },\r\n        "labels": {\r\n            "default": "Availability",\r\n            "fr_FR": "Disponibilit\\u00e9"\r\n        }\r\n    },\r\n    "core_account\\/generic\\/property\\/comment_1": {\r\n        "definition": "inline",\r\n        "type": "textarea",\r\n        "labels": {\r\n            "default": "Offered skills",\r\n            "fr_FR": "Comp\\u00e9tences offertes"\r\n        },\r\n        "max_length": 65535\r\n    },\r\n    "core_account\\/generic\\/property\\/comment_2": {\r\n        "definition": "inline",\r\n        "type": "textarea",\r\n        "labels": {\r\n            "default": "Requested skills",\r\n            "fr_FR": "Comp\\u00e9tences demand\\u00e9es"\r\n        },\r\n        "max_length": 65535\r\n    },\r\n    "core_account\\/sendMessage": {\r\n        "themes": {\r\n            "theme_1": {\r\n                "definition": "customization\\/pbc\\/send-message\\/theme_1"\r\n            },\r\n            "theme_2": {\r\n                "definition": "customization\\/pbc\\/send-message\\/theme_2"\r\n            }\r\n        },\r\n        "signature": {\r\n            "definition": "inline",\r\n            "body": {\r\n                "default": "\\n<hr>\\n<div><a href=\\"https:\\/\\/www.2pit.io\\"><img src=\\"http:\\/\\/img.probonocorpo.com\\/PBC-logo-fleur-texte.png\\" width=\\"300\\" height=\\"79\\" \\/><\\/a><\\/div>\\n<br \\/>The <strong>Pro bono<\\/strong> team\\n<br \\/><a href=\\"mailto:contact@2pit.io\\">contact@2pit.io<\\/a>\\n\\t\\t\\t\\t\\t",\r\n                "fr_FR": "\\n<hr>\\n<div><a href=\\"https:\\/\\/www.2pit.io\\"><img src=\\"http:\\/\\/img.probonocorpo.com\\/PBC-logo-fleur-texte.png\\" width=\\"300\\" height=\\"79\\" \\/><\\/a><\\/div>\\n<br \\/>L\'&Eacute;quipe <strong>Pro bono<\\/strong>\\n<br \\/><a href=\\"mailto:contact@2pit.io\\">contact@2pit.io<\\/a>\\n"\r\n            }\r\n        }\r\n    },\r\n    "flow\\/tests": {\r\n        "test_request": "test_request\\/probonocorpo",\r\n        "survey_profile": "survey_profile\\/probonocorpo"\r\n    },\r\n    "mailTo": "contact@2pit.io"\r\n}', NULL, '[]', '2017-12-04 00:21:45', 83, '2017-12-04 00:21:45', 83, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `core_interaction`
--

CREATE TABLE `core_interaction` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `direction` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `content` longtext,
  `http_status` varchar(255) DEFAULT NULL,
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
  `property_11` varchar(255) DEFAULT NULL,
  `property_12` varchar(255) DEFAULT NULL,
  `property_13` varchar(255) DEFAULT NULL,
  `property_14` varchar(255) DEFAULT NULL,
  `property_15` varchar(255) DEFAULT NULL,
  `property_16` varchar(255) DEFAULT NULL,
  `property_17` varchar(255) DEFAULT NULL,
  `property_18` varchar(255) DEFAULT NULL,
  `property_19` varchar(255) DEFAULT NULL,
  `property_20` varchar(255) DEFAULT NULL,
  `property_21` varchar(255) DEFAULT NULL,
  `property_22` varchar(255) DEFAULT NULL,
  `property_23` varchar(255) DEFAULT NULL,
  `property_24` varchar(255) DEFAULT NULL,
  `property_25` varchar(255) DEFAULT NULL,
  `property_26` varchar(255) DEFAULT NULL,
  `property_27` varchar(255) DEFAULT NULL,
  `property_28` varchar(255) DEFAULT NULL,
  `property_29` varchar(255) DEFAULT NULL,
  `property_30` varchar(255) DEFAULT NULL,
  `audit` mediumtext,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_link`
--

CREATE TABLE `core_link` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `uploaded_time` datetime DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_object`
--

CREATE TABLE `core_object` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `boo_immutable` tinyint(1) DEFAULT '0',
  `ref_count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_page`
--

CREATE TABLE `core_page` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specification` longtext COLLATE utf8mb4_unicode_ci,
  `audit` mediumtext COLLATE utf8mb4_unicode_ci,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `core_place`
--

CREATE TABLE `core_place` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `opening_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `communities` mediumtext,
  `tax_regime` int(11) DEFAULT NULL,
  `reception_vcard_id` int(11) DEFAULT NULL,
  `delivery_vcard_id` int(11) DEFAULT NULL,
  `support_email` varchar(255) DEFAULT NULL,
  `logo_src` varchar(255) DEFAULT NULL,
  `logo_width` int(11) DEFAULT NULL,
  `logo_height` int(11) DEFAULT NULL,
  `logo_href` varchar(255) DEFAULT NULL,
  `banner_src` varchar(255) DEFAULT NULL,
  `banner_width` int(11) DEFAULT NULL,
  `legal_footer` varchar(255) DEFAULT NULL,
  `legal_footer_2` varchar(255) DEFAULT NULL,
  `next_account_identifier` int(11) DEFAULT NULL,
  `config` longtext,
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
  `audit` mediumtext,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `banner_href` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `core_place`
--

INSERT INTO `core_place` (`id`, `instance_id`, `status`, `identifier`, `caption`, `opening_date`, `closing_date`, `communities`, `tax_regime`, `reception_vcard_id`, `delivery_vcard_id`, `support_email`, `logo_src`, `logo_width`, `logo_height`, `logo_href`, `banner_src`, `banner_width`, `legal_footer`, `legal_footer_2`, `next_account_identifier`, `config`, `property_1`, `property_2`, `property_3`, `property_4`, `property_5`, `property_6`, `property_7`, `property_8`, `property_9`, `property_10`, `audit`, `creation_time`, `creation_user`, `update_time`, `update_user`, `banner_href`) VALUES
(61, 32, 'new', 'probonocorpo', 'Pro Bono', '2014-09-03', '9999-12-31', '[]', 1, 0, 0, '0', '', 0, 0, '', '', 0, 'Pro Bono – StartUp - Adresse', NULL, 316, '[]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2017-04-09 11:03:15', 83, '2018-06-21 15:20:57', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `core_product`
--

CREATE TABLE `core_product` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `description` text,
  `is_available` tinyint(4) DEFAULT NULL,
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
  `variants` mediumtext,
  `tax_1_share` decimal(10,9) DEFAULT NULL,
  `tax_2_share` decimal(10,9) DEFAULT NULL,
  `tax_3_share` decimal(10,9) DEFAULT NULL,
  `prices` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `community_id` int(11) DEFAULT NULL,
  `product_category_id` int(11) DEFAULT NULL,
  `criteria` text,
  `properties` text,
  `package` varchar(255) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_product_option`
--

CREATE TABLE `core_product_option` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `description` varchar(2047) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT NULL,
  `variants` mediumtext,
  `vat_id` varchar(255) DEFAULT NULL,
  `prices` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_user`
--

CREATE TABLE `core_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `vcard_id` int(11) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `password_init_token` varchar(255) DEFAULT NULL,
  `password_init_validity` date DEFAULT NULL,
  `authentication_token` varchar(255) DEFAULT NULL,
  `authentication_validity` datetime DEFAULT NULL,
  `nb_trials` int(11) DEFAULT NULL,
  `state` smallint(5) UNSIGNED DEFAULT NULL,
  `requires_notifications` tinyint(1) DEFAULT NULL,
  `locale` char(5) DEFAULT NULL,
  `applications` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `core_user`
--

INSERT INTO `core_user` (`user_id`, `instance_id`, `username`, `vcard_id`, `password`, `password_init_token`, `password_init_validity`, `authentication_token`, `authentication_validity`, `nb_trials`, `state`, `requires_notifications`, `locale`, `applications`, `creation_time`, `creation_user`, `update_time`, `update_user`) VALUES
(83, 33, 'admin', 23726, '$2y$14$aUDFotRfZAqDW7IAx.sfyOUU4EJxsTL7Mlg6s2UyKwpypwKsZixr2', NULL, NULL, 'cb4e50362ce977063f8b423a21b7036b', '2018-08-18 00:16:47', 0, 1, 1, 'fr_FR', '[]', '2015-08-25 23:21:24', 82, '2018-10-18 16:11:23', NULL),
(2208, 32, 'organizer', 24065, '$2y$14$O7OHnO8NIyH/QPfypEACieFKLG3mymNSrfJEYNTPc2u9tfErm1v9u', NULL, NULL, NULL, NULL, 0, 1, 0, NULL, 'null', '2018-05-28 19:11:55', 83, '2018-10-18 16:08:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `core_user_contact`
--

CREATE TABLE `core_user_contact` (
  `id` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vcard_id` int(11) DEFAULT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `core_user_contact`
--

INSERT INTO `core_user_contact` (`id`, `status`, `user_id`, `vcard_id`, `instance_id`, `creation_time`, `creation_user`, `update_time`, `update_user`) VALUES
(1186, 'new', 83, 23726, 32, '2016-03-20 19:53:31', 17, '2016-03-20 19:53:31', 17),
(1189, 'new', 2208, 24065, 32, '2018-05-28 19:11:55', 83, '2018-05-28 19:11:55', 83);

-- --------------------------------------------------------

--
-- Table structure for table `core_user_token`
--

CREATE TABLE `core_user_token` (
  `id` int(10) UNSIGNED NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `vcard_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `validity` datetime DEFAULT NULL,
  `authorized_route` varchar(255) DEFAULT NULL,
  `authorized_param` varchar(255) DEFAULT NULL,
  `authorized_id` int(11) DEFAULT NULL,
  `locale` char(5) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `core_vcard`
--

CREATE TABLE `core_vcard` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `applications` text,
  `communities` mediumtext,
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
  `gender` char(1) DEFAULT NULL,
  `birth_date` varchar(255) DEFAULT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `photo_link_id` varchar(255) DEFAULT NULL,
  `origine` varchar(255) DEFAULT NULL,
  `tiny_1` varchar(255) DEFAULT NULL,
  `tiny_2` varchar(255) DEFAULT NULL,
  `tiny_3` varchar(255) DEFAULT NULL,
  `tiny_4` varchar(255) DEFAULT NULL,
  `tiny_5` varchar(255) DEFAULT NULL,
  `tiny_6` varchar(255) DEFAULT NULL,
  `tiny_7` varchar(255) DEFAULT NULL,
  `tiny_8` varchar(255) DEFAULT NULL,
  `tiny_9` varchar(255) DEFAULT NULL,
  `tiny_10` varchar(255) DEFAULT NULL,
  `roles` text,
  `perimeters` text,
  `locale` char(10) DEFAULT NULL,
  `is_notified` tinyint(1) DEFAULT NULL,
  `is_demo_mode_active` tinyint(1) DEFAULT NULL,
  `specifications` longtext,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `properties` text,
  `last_credit_consumption_date` date DEFAULT NULL,
  `community_id` int(11) DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `core_vcard`
--

INSERT INTO `core_vcard` (`id`, `instance_id`, `status`, `applications`, `communities`, `n_title`, `n_first`, `n_last`, `n_fn`, `org`, `tel_work`, `tel_cell`, `email`, `adr_street`, `adr_extended`, `adr_post_office_box`, `adr_zip`, `adr_city`, `adr_state`, `adr_country`, `gender`, `birth_date`, `place_of_birth`, `nationality`, `photo_link_id`, `origine`, `tiny_1`, `tiny_2`, `tiny_3`, `tiny_4`, `tiny_5`, `tiny_6`, `tiny_7`, `tiny_8`, `tiny_9`, `tiny_10`, `roles`, `perimeters`, `locale`, `is_notified`, `is_demo_mode_active`, `specifications`, `creation_time`, `creation_user`, `update_time`, `update_user`, `properties`, `last_credit_consumption_date`, `community_id`, `sex`) VALUES
(23726, 32, 'basic', '{"synapps":true,"frontOffice":false,"p-pit-admin":false}', '[]', NULL, 'Admin', 'ADMIN', 'ADMIN, Admin', NULL, '+33 6 11 11 11 11', NULL, 'admin@2pit.io', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'no-photo.png', NULL, 'RESG', 'business_model,design_thinking', 'anglais', 'Val', NULL, NULL, NULL, NULL, NULL, NULL, '{"operational_management":"operational_management","admin":"admin"}', '[]', 'en_US', NULL, 0, '[]', '2018-05-09 12:21:44', NULL, '2018-10-17 20:59:33', 83, NULL, NULL, 0, NULL),
(24065, 32, 'new', '[]', '[]', NULL, 'Manh', 'Organizer', 'Organizer, Manh', NULL, NULL, NULL, 'manh.organizer@probono.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'no-photo.png', NULL, NULL, 'business_model,marketing', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{"ambassador":"ambassador"}', '[]', 'en_US', NULL, 0, '[]', '2018-05-12 07:51:57', 83, '2018-10-18 16:21:49', 83, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `locale_1` varchar(255) DEFAULT NULL,
  `locale_2` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `acl` text,
  `summary` text,
  `summary_locale_1` text,
  `summary_locale_2` text,
  `image` text,
  `image_locale_1` text,
  `image_locale_2` text,
  `first_part_id` int(11) DEFAULT NULL,
  `mime` varchar(255) DEFAULT NULL,
  `url` varchar(2047) DEFAULT NULL,
  `properties` text,
  `properties_locale_1` text,
  `properties_locale_2` text,
  `audit` text,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `community_id` int(11) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `lock` tinyint(4) DEFAULT NULL,
  `properties_en_us` text,
  `properties_fr_fr` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `document_part`
--

CREATE TABLE `document_part` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `content` mediumtext,
  `content_en_us` mediumtext,
  `content_fr_fr` mediumtext,
  `saved_content` text,
  `is_undone` tinyint(1) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_en_us` varchar(255) DEFAULT NULL,
  `image_fr_fr` varchar(255) DEFAULT NULL,
  `next_part_id` int(11) DEFAULT NULL,
  `audit` text,
  `lock` tinyint(4) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `creation_user` int(11) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `emetteur` varchar(100) DEFAULT NULL,
  `dest_centre` varchar(100) DEFAULT NULL,
  `dest_sport` varchar(100) DEFAULT NULL,
  `dest_categorie` varchar(100) DEFAULT NULL,
  `dest_classe` varchar(100) DEFAULT NULL,
  `dest_formule` varchar(100) DEFAULT NULL,
  `dest_eleve` varchar(100) DEFAULT NULL,
  `espace` char(20) DEFAULT 'Flash-info',
  `objet` varchar(100) DEFAULT NULL,
  `texte` text,
  `piece_jointe` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `user_modif` int(11) DEFAULT NULL,
  `date_modif` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commitment`
--
ALTER TABLE `commitment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `commitment_event`
--
ALTER TABLE `commitment_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commitment_message`
--
ALTER TABLE `commitment_message`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `commitment_notification`
--
ALTER TABLE `commitment_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commitment_subscription`
--
ALTER TABLE `commitment_subscription`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commitment_term`
--
ALTER TABLE `commitment_term`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commitment_year`
--
ALTER TABLE `commitment_year`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_contract`
--
ALTER TABLE `contact_contract`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_event`
--
ALTER TABLE `contact_event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `contact_message`
--
ALTER TABLE `contact_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_vcard_property`
--
ALTER TABLE `contact_vcard_property`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_account`
--
ALTER TABLE `core_account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_app`
--
ALTER TABLE `core_app`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_community`
--
ALTER TABLE `core_community`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_config`
--
ALTER TABLE `core_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_credit`
--
ALTER TABLE `core_credit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_document`
--
ALTER TABLE `core_document`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_document_part`
--
ALTER TABLE `core_document_part`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_event`
--
ALTER TABLE `core_event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `core_instance`
--
ALTER TABLE `core_instance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_interaction`
--
ALTER TABLE `core_interaction`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `core_link`
--
ALTER TABLE `core_link`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_object`
--
ALTER TABLE `core_object`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_page`
--
ALTER TABLE `core_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_place`
--
ALTER TABLE `core_place`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_product`
--
ALTER TABLE `core_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_product_option`
--
ALTER TABLE `core_product_option`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_user`
--
ALTER TABLE `core_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `core_user_contact`
--
ALTER TABLE `core_user_contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_user_token`
--
ALTER TABLE `core_user_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_vcard`
--
ALTER TABLE `core_vcard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_part`
--
ALTER TABLE `document_part`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `commitment`
--
ALTER TABLE `commitment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `commitment_event`
--
ALTER TABLE `commitment_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `commitment_message`
--
ALTER TABLE `commitment_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `commitment_notification`
--
ALTER TABLE `commitment_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `commitment_subscription`
--
ALTER TABLE `commitment_subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `commitment_term`
--
ALTER TABLE `commitment_term`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `commitment_year`
--
ALTER TABLE `commitment_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `contact_contract`
--
ALTER TABLE `contact_contract`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `contact_event`
--
ALTER TABLE `contact_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `contact_message`
--
ALTER TABLE `contact_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `contact_vcard_property`
--
ALTER TABLE `contact_vcard_property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_account`
--
ALTER TABLE `core_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27722;
--
-- AUTO_INCREMENT for table `core_app`
--
ALTER TABLE `core_app`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_community`
--
ALTER TABLE `core_community`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_config`
--
ALTER TABLE `core_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `core_credit`
--
ALTER TABLE `core_credit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_document`
--
ALTER TABLE `core_document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_document_part`
--
ALTER TABLE `core_document_part`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_event`
--
ALTER TABLE `core_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4078;
--
-- AUTO_INCREMENT for table `core_instance`
--
ALTER TABLE `core_instance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `core_interaction`
--
ALTER TABLE `core_interaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_link`
--
ALTER TABLE `core_link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_object`
--
ALTER TABLE `core_object`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_page`
--
ALTER TABLE `core_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `core_place`
--
ALTER TABLE `core_place`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT for table `core_product`
--
ALTER TABLE `core_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_product_option`
--
ALTER TABLE `core_product_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_user`
--
ALTER TABLE `core_user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2320;
--
-- AUTO_INCREMENT for table `core_user_contact`
--
ALTER TABLE `core_user_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1303;
--
-- AUTO_INCREMENT for table `core_user_token`
--
ALTER TABLE `core_user_token`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_vcard`
--
ALTER TABLE `core_vcard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33817;
--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `document_part`
--
ALTER TABLE `document_part`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
