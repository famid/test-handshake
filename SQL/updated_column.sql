-- Add has_multi_account column to users table --

ALTER TABLE `users` ADD `has_multi_account` INT(2) NOT NULL DEFAULT '0' AFTER `user_type`;

-- Add nid_front,nid_back column to users table --

ALTER TABLE `users` ADD `nid_front` VARCHAR(255) NULL DEFAULT NULL AFTER `avatar`, ADD `nid_back` VARCHAR(255) NULL DEFAULT NULL AFTER `nid_front`;

-- Add package_duration, package_image column to packages_table --
ALTER TABLE `packages` ADD `package_duration` INT(20) NOT NULL AFTER `daraz_sync_limit`;
ALTER TABLE `packages` ADD `package_image` VARCHAR(255) NULL DEFAULT NULL AFTER `package_duration`;
ALTER TABLE `packages` ADD `status` TINYINT(2) NOT NULL DEFAULT '0' AFTER `package_image`;
ALTER TABLE `package_users` CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;

-- Add foreign key to packages_users table --
ALTER TABLE `package_users` ADD FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE; ALTER TABLE `package_users` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Add package_type to packages table --
ALTER TABLE `packages` ADD `package_type` VARCHAR(255) NOT NULL AFTER `status`;

-- Add url_slug to users table --
ALTER TABLE `users` ADD `url_slug` VARCHAR(255) NULL DEFAULT NULL AFTER `remaining_uploads`;

-- Add additional_package to packages table --
ALTER TABLE `packages` ADD `additional_packages` LONGTEXT NULL AFTER `package_image`;

-- Add user_type to package_users table --
ALTER TABLE `package_users` ADD `user_type` VARCHAR(255) NULL DEFAULT NULL AFTER `user_id`;

-- Change the table name of daraz_intregations to daraz_integration --
ALTER TABLE daraz_intregations
    RENAME TO daraz_integrations;

-- add column to daraz_integration table --
ALTER TABLE daraz_integrations
    ADD COLUMN user_code VARCHAR(255) NOT NULL AFTER `user_id`,
    ADD COLUMN user_info JSON NULL DEFAULT NULL AFTER `refresh_token`;

ALTER TABLE daraz_integrations
    ADD COLUMN account_status (255) NOT NULL AFTER `user_id`,
    ADD COLUMN user_info JSON NULL DEFAULT NULL AFTER `refresh_token`;

-- add column to users  table --
ALTER TABLE `users` ADD COLUMN daraz_account_status ENUM('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'INACTIVE';

-- add column to daraz_integrations table --
ALTER TABLE daraz_integrations
    ADD COLUMN daraz_account_email VARCHAR(255) NULL;

-- Add More column to transaction table --
ALTER TABLE `transactions` ADD `transaction_id` VARCHAR(255) NOT NULL AFTER `user_id`, ADD `transaction_amount` INT(20) NOT NULL AFTER `transaction_id`, ADD `refrence_id` INT(11) NULL DEFAULT NULL AFTER `transaction_amount`, ADD `type` VARCHAR(255) NOT NULL AFTER `refrence_id`, ADD `note` VARCHAR(255) NULL DEFAULT NULL AFTER `type`;
ALTER TABLE `transactions` ADD `user_type` VARCHAR(255) NOT NULL AFTER `user_id`;
