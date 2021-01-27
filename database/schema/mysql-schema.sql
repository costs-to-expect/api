

DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `password` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `remember_token` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_type;
CREATE TABLE IF NOT EXISTS `item_type` (
    `id` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(25) COLLATE utf8mb4_unicode_ci NOT NULL,
    `friendly_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `example` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `item_type_name_index` (`name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_subtype;
CREATE TABLE IF NOT EXISTS `item_subtype` (
    `id` TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_type_id` INT(10) UNSIGNED NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `friendly_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `item_subtype_name_index` (`name`),
    KEY `item_subtype_item_type_id_foreign` (`item_type_id`),
    CONSTRAINT `item_subtype_item_type_id_foreign` FOREIGN KEY (`item_type_id`) REFERENCES `item_type` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS currency;
CREATE TABLE IF NOT EXISTS `currency` (
    `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `code` char(3) COLLATE utf8mb4_unicode_ci NOT NULL,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS resource_type;
CREATE TABLE IF NOT EXISTS `resource_type` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `public` TINYINT(1) NOT NULL DEFAULT '0',
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `resource_type_public_index` (`public`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS resource;
CREATE TABLE IF NOT EXISTS `resource` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_type_id` BIGINT(20) UNSIGNED NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `effective_date` DATE NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `resource_resource_type_id_name_unique` (`resource_type_id`,`name`),
    CONSTRAINT `resource_resource_type_id_foreign` FOREIGN KEY (`resource_type_id`) REFERENCES `resource_type` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS permitted_user;
CREATE TABLE IF NOT EXISTS `permitted_user` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_type_id` BIGINT(20) UNSIGNED NOT NULL,
    `user_id` BIGINT(20) UNSIGNED NOT NULL,
    `added_by` BIGINT(20) UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `permitted_users_resource_type_id_foreign` (`resource_type_id`),
    KEY `permitted_users_user_id_foreign` (`user_id`),
    KEY `permitted_user_added_by_foreign` (`added_by`),
    CONSTRAINT `permitted_user_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`),
    CONSTRAINT `permitted_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    CONSTRAINT `permitted_users_resource_type_id_foreign` FOREIGN KEY (`resource_type_id`) REFERENCES `resource_type` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS category;
CREATE TABLE IF NOT EXISTS `category` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_type_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '1',
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `category_name_resource_type_id_unique` (`name`,`resource_type_id`),
    KEY `category_resource_type_id_foreign` (`resource_type_id`),
    CONSTRAINT `category_resource_type_id_foreign` FOREIGN KEY (`resource_type_id`) REFERENCES `resource_type` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS sub_category;
CREATE TABLE IF NOT EXISTS `sub_category` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` BIGINT(20) UNSIGNED NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `sub_category_category_id_name_unique` (`category_id`,`name`),
    CONSTRAINT `sub_category_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item;
CREATE TABLE IF NOT EXISTS `item` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_id` BIGINT(20) UNSIGNED NOT NULL,
    `created_by` BIGINT(20) UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_by` BIGINT(20) UNSIGNED DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `item_resource_id_foreign` (`resource_id`),
    KEY `created_by` (`created_by`),
    KEY `updated_by` (`updated_by`),
    CONSTRAINT `item_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
    CONSTRAINT `item_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
    CONSTRAINT `item_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_category;
CREATE TABLE IF NOT EXISTS `item_category` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` BIGINT(20) UNSIGNED NOT NULL,
    `category_id` BIGINT(20) UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `item_category_item_id_category_id_unique` (`item_id`,`category_id`),
    KEY `item_category_category_id_foreign` (`category_id`),
    CONSTRAINT `item_category_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
    CONSTRAINT `item_category_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_sub_category;
CREATE TABLE IF NOT EXISTS `item_sub_category` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_category_id` BIGINT(20) UNSIGNED NOT NULL,
    `sub_category_id` BIGINT(20) UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `item_sub_category_item_category_id_sub_category_id_unique` (`item_category_id`,`sub_category_id`),
    KEY `item_sub_category_sub_category_id_foreign` (`sub_category_id`),
    CONSTRAINT `item_sub_category_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_category` (`id`),
    CONSTRAINT `item_sub_category_sub_category_id_foreign` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_type_allocated_expense;
CREATE TABLE IF NOT EXISTS `item_type_allocated_expense` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` BIGINT(20) UNSIGNED NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8mb4_unicode_ci,
    `effective_date` DATE NOT NULL,
    `publish_after` DATE DEFAULT NULL,
    `currency_id` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
    `total` DECIMAL(13,2) NOT NULL,
    `percentage` TINYINT(3) NOT NULL,
    `actualised_total` DECIMAL(13,2) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `effective_date` (`effective_date`),
    KEY `publish_after` (`publish_after`),
    KEY `item_id` (`item_id`),
    KEY `item_type_allocated_expense_currency_id_foreign` (`currency_id`),
    CONSTRAINT `item_type_allocated_expense_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
    CONSTRAINT `item_type_allocated_expense_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_type_game;
CREATE TABLE IF NOT EXISTS `item_type_game` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` BIGINT(20) UNSIGNED NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `game` LONGTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `statistics` LONGTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `winner` CHAR(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `score` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `complete` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `item_type_game_item_id_foreign` (`item_id`),
    CONSTRAINT `item_type_game_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_type_simple_expense;
CREATE TABLE IF NOT EXISTS `item_type_simple_expense` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` BIGINT(20) UNSIGNED NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8mb4_unicode_ci,
    `currency_id` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
    `total` DECIMAL(13,2) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `item_id` (`item_id`),
    KEY `item_type_simple_expense_currency_id_foreign` (`currency_id`),
    CONSTRAINT `item_type_simple_expense_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
    CONSTRAINT `item_type_simple_expense_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_type_simple_item;
CREATE TABLE IF NOT EXISTS `item_type_simple_item` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id` BIGINT(20) UNSIGNED NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8mb4_unicode_ci,
    `quantity` INT(10) UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `item_type_simple_item_item_id_foreign` (`item_id`),
    CONSTRAINT `item_type_simple_item_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_transfer;
CREATE TABLE IF NOT EXISTS `item_transfer` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_type_id` BIGINT(20) UNSIGNED NOT NULL,
    `from` BIGINT(20) UNSIGNED NOT NULL,
    `to` BIGINT(20) UNSIGNED NOT NULL,
    `item_id` BIGINT(20) UNSIGNED NOT NULL,
    `transferred_by` BIGINT(20) UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `item_transfer_resource_type_id_foreign` (`resource_type_id`),
    KEY `item_transfer_from_foreign` (`from`),
    KEY `item_transfer_to_foreign` (`to`),
    KEY `item_transfer_item_id_foreign` (`item_id`),
    KEY `item_transfer_transferred_by_foreign` (`transferred_by`),
    CONSTRAINT `item_transfer_from_foreign` FOREIGN KEY (`from`) REFERENCES `resource` (`id`) ON DELETE CASCADE,
    CONSTRAINT `item_transfer_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE,
    CONSTRAINT `item_transfer_resource_type_id_foreign` FOREIGN KEY (`resource_type_id`) REFERENCES `resource_type` (`id`) ON DELETE CASCADE,
    CONSTRAINT `item_transfer_to_foreign` FOREIGN KEY (`to`) REFERENCES `resource` (`id`) ON DELETE CASCADE,
    CONSTRAINT `item_transfer_transferred_by_foreign` FOREIGN KEY (`transferred_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS item_partial_transfer;
CREATE TABLE IF NOT EXISTS `item_partial_transfer` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_type_id` BIGINT(20) UNSIGNED NOT NULL,
    `from` BIGINT(20) UNSIGNED NOT NULL,
    `to` BIGINT(20) UNSIGNED NOT NULL,
    `item_id` BIGINT(20) UNSIGNED NOT NULL,
    `percentage` TINYINT(3) UNSIGNED NOT NULL,
    `transferred_by` BIGINT(20) UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_item_partial_transfer` (`resource_type_id`,`from`,`item_id`),
    KEY `item_partial_transfer_from_foreign` (`from`),
    KEY `item_partial_transfer_to_foreign` (`to`),
    KEY `item_partial_transfer_item_id_foreign` (`item_id`),
    KEY `item_partial_transfer_transferred_by_foreign` (`transferred_by`),
    CONSTRAINT `item_partial_transfer_from_foreign` FOREIGN KEY (`from`) REFERENCES `resource` (`id`) ON DELETE CASCADE,
    CONSTRAINT `item_partial_transfer_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE,
    CONSTRAINT `item_partial_transfer_resource_type_id_foreign` FOREIGN KEY (`resource_type_id`) REFERENCES `resource_type` (`id`) ON DELETE CASCADE,
    CONSTRAINT `item_partial_transfer_to_foreign` FOREIGN KEY (`to`) REFERENCES `resource` (`id`) ON DELETE CASCADE,
    CONSTRAINT `item_partial_transfer_transferred_by_foreign` FOREIGN KEY (`transferred_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS request_error_log;
CREATE TABLE IF NOT EXISTS `request_error_log` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `method` CHAR(8) COLLATE utf8mb4_unicode_ci NOT NULL,
    `source` VARCHAR(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'api',
    `debug` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `expected_status_code` SMALLINT(5) UNSIGNED NOT NULL,
    `returned_status_code` SMALLINT(5) UNSIGNED NOT NULL,
    `request_uri` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `request_error_log_source_index` (`source`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS resource_item_subtype;
CREATE TABLE IF NOT EXISTS `resource_item_subtype` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_id` BIGINT(20) UNSIGNED NOT NULL,
    `item_subtype_id` TINYINT(3) UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `resource_item_subtype_resource_id_item_subtype_id_unique` (`resource_id`,`item_subtype_id`),
    KEY `resource_item_subtype_item_subtype_id_foreign` (`item_subtype_id`),
    CONSTRAINT `resource_item_subtype_item_subtype_id_foreign` FOREIGN KEY (`item_subtype_id`) REFERENCES `item_subtype` (`id`),
    CONSTRAINT `resource_item_subtype_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS resource_item_subtype;
CREATE TABLE IF NOT EXISTS `resource_item_subtype` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_type_id` BIGINT(20) UNSIGNED DEFAULT NULL,
    `item_type_id` INT(3) UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `resource_type_id` (`resource_type_id`),
    KEY `item_type_id` (`item_type_id`),
    CONSTRAINT `resource_type_item_type_ibfk_1` FOREIGN KEY (`resource_type_id`) REFERENCES `resource_type` (`id`),
    CONSTRAINT `resource_type_item_type_ibfk_2` FOREIGN KEY (`item_type_id`) REFERENCES `item_type` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS cache;
CREATE TABLE IF NOT EXISTS `cache` (
    `key` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value` MEDIUMTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `expiration` INT(11) NOT NULL,
    UNIQUE KEY `cache_key_unique` (`key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS sessions;
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id` BIGINT(20) UNSIGNED DEFAULT NULL,
    `ip_address` VARCHAR(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `user_agent` TEXT COLLATE utf8mb4_unicode_ci,
    `payload` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `last_activity` INT(11) NOT NULL,
    UNIQUE KEY `sessions_id_unique` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS error_log;
CREATE TABLE IF NOT EXISTS `error_log` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `message` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `file` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `line` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `trace` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS failed_jobs;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `connection` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `queue` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `payload` LONGTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `exception` LONGTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS jobs;
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `payload` LONGTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `attempts` TINYINT(3) UNSIGNED NOT NULL,
    `reserved_at` INT(10) UNSIGNED DEFAULT NULL,
    `available_at` INT(10) UNSIGNED NOT NULL,
    `created_at` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS migrations;
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `batch` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS password_creates;
CREATE TABLE IF NOT EXISTS `password_creates` (
    `email` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `token` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `password_creates_email_index` (`email`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS password_resets;
CREATE TABLE IF NOT EXISTS `password_resets` (
    `email` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `token` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    KEY `password_resets_email_index` (`email`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
