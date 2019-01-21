ALTER TABLE `core_account` 
ADD `date_1` DATE NULL DEFAULT NULL AFTER `first_activation_date`, 
ADD `date_2` DATE NULL DEFAULT NULL AFTER `date_1`, 
ADD `date_3` DATE NULL DEFAULT NULL AFTER `date_2`, 
ADD `date_4` DATE NULL DEFAULT NULL AFTER `date_3`, 
ADD `date_5` DATE NULL DEFAULT NULL AFTER `date_4`;

ALTER TABLE `core_event` 
CHANGE `matched_accounts` `matched_accounts` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `commitment_term` 
ADD `bank_name` VARCHAR(255) NULL DEFAULT NULL AFTER `means_of_payment`,
ADD `invoice_n_last` VARCHAR(255) NULL DEFAULT NULL AFTER `bank_name`,
ADD `type` VARCHAR(255) NULL DEFAULT NULL AFTER `status`,
ADD `invoice_account_id` INT NULL DEFAULT NULL AFTER `commitment_id`,
ADD `quantity` DECIMAL(14,4) NULL DEFAULT NULL AFTER `collection_date`, 
ADD `unit_price` DECIMAL(14,4) NULL DEFAULT NULL AFTER `quantity`;

ALTER TABLE `commitment_term` 
ADD `tiny_1` VARCHAR(255) NULL DEFAULT NULL AFTER `invoice_id`, 
ADD `tiny_2` VARCHAR(255) NULL DEFAULT NULL AFTER `tiny_1`, 
ADD `tiny_3` VARCHAR(255) NULL DEFAULT NULL AFTER `tiny_2`, 
ADD `tiny_4` VARCHAR(255) NULL DEFAULT NULL AFTER `tiny_3`, 
ADD `tiny_5` VARCHAR(255) NULL DEFAULT NULL AFTER `tiny_4`;

ALTER TABLE `core_account` 
ADD `shopping_cart` VARCHAR(255) NULL DEFAULT NULL AFTER `credits`;

ALTER TABLE `core_product_option` ADD `category` VARCHAR(255) NULL DEFAULT NULL AFTER `type`;