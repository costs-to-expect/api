-- Initial data for the API
-- Supported currencies
INSERT INTO `currency` (`id`, `code`, `name`, `created_at`, `updated_at`)
VALUES
(1, 'GBP', 'Sterling', '2020-09-28 10:27:34', NULL),
(2, 'USD', 'US Dollar', '2020-09-28 10:27:34', NULL),
(3, 'EUR', 'Euro', '2020-09-28 10:27:34', NULL);

-- Supported item types
insert  into `item_type`(`id`,`name`,`friendly_name`,`description`,`example`,`created_at`,`updated_at`) values
(1,'allocated-expense','Create an expense chronological tracker','Track expenses over time, additionally, an expense can be partially allocated to another tracker.','Examples include, the cost to raise a child and start-up expenses for your business.','2019-09-18 12:47:07',NULL),
(4,'game','Track a game','Track your board, card and dice game sessions.','Check the item_subtype collection, more added on request','2020-10-08 09:33:25',NULL);

-- Supported subtypes
INSERT
INTO
    `item_subtype`(`id`, `item_type_id`, `name`, `friendly_name`, `description`, `created_at`, `updated_at`)
VALUES
(1, 1, 'default', 'Default behaviour', 'Default behaviour for the allocated-exense type', '2020-10-08 09:33:24', NULL),
(4, 4, 'carcassonne', 'Carcassonne board games', 'Track your Carcassonne games, wins and losses', '2020-10-08 09:33:25', NULL),
(5, 4, 'scrabble', 'Scrabble board games', 'Track your Scrabble games, wins and losses', '2020-10-08 09:33:25', NULL);
