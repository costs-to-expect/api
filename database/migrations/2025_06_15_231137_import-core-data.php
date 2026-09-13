<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ImportCoreData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::insert("
            INSERT INTO 
            `currency` (`id`, `code`, `name`, `created_at`, `updated_at`)
            VALUES
            (1, 'GBP', 'Sterling', '2020-09-28 10:27:34', NULL),
            (2, 'USD', 'US Dollar', '2020-09-28 10:27:34', NULL),
            (3, 'EUR', 'Euro', '2020-09-28 10:27:34', NULL),
            (4, 'CAD', 'Canadian Dollar', '2022-11-15 14:46:05', NULL),
            (5, 'AUD', 'Australian Dollar', '2022-11-15 14:46:05', NULL),
            (6, 'NZD', 'New Zealand Dollar', '2022-11-15 14:46:05', NULL),
            (7, 'INR', 'Indian Rupee', '2022-11-15 14:46:05', NULL),
            (8, 'NOK', 'Norwegian Krone', '2024-09-01 15:20:15', NULL)
        ");

        DB::insert("
            insert 
            into `item_type`(`id`,`name`,`friendly_name`,`description`,`example`,`created_at`,`updated_at`) values
            (1,'allocated-expense','Create an expense chronological tracker','Track expenses over time, additionally, an expense can be partially allocated to another tracker.','Examples include, the cost to raise a child and start-up expenses for your business.','2019-09-18 12:47:07',NULL),
            (4,'game','Track a game','Track your board, card and dice game sessions.','Check the item_subtype collection, more added on request','2020-10-08 09:33:25',NULL),
            (5, 'budget', 'Budgeting', 'Plan your budgets', 'Annual Personal Budget, Business Budget, Savings plan...', '2022-08-04 15:55:36', NULL),
            (6, 'budget-pro', 'Budgeting', 'Plan your budgets', 'Annual Personal Budget, Business Budget, Savings plan...', '2023-06-01 15:20:43', NULL)
        ");

        DB::insert("
            INSERT
            INTO
            `item_subtype`(`id`, `item_type_id`, `name`, `friendly_name`, `description`, `created_at`, `updated_at`)
            VALUES
            (1, 1, 'default', 'Default behaviour', 'Default behaviour for the allocated-exense type', '2020-10-08 09:33:24', NULL),
            (4, 4, 'carcassonne', 'Carcassonne board games', 'Track your Carcassonne games, wins and losses', '2020-10-08 09:33:25', NULL),
            (5, 4, 'scrabble', 'Scrabble board games', 'Track your Scrabble games, wins and losses', '2020-10-08 09:33:25', NULL),
            (6, 4, 'yahtzee', 'Yahtzee', 'Track your Yahtzee games, wins and losses', '2022-07-06 12:42:47', NULL),
            (7, 5, 'default', 'Default behaviour', 'Default behaviour for the budget item type', '2022-08-04 15:55:36', NULL),
            (8, 4, 'yatzy', 'Yatzy', 'Track your Yatzy games, wins and losses', '2022-08-27 15:26:51', NULL),
            (9, 6, 'default', 'Default behaviour', 'Default behaviour for the budget pro item type', '2023-06-01 15:20:43', NULL)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // No down
    }
}
