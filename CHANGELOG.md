# Changelog

The complete changelog for the Costs to Expect REST API, our changelog follows the format defined at https://keepachangelog.com/en/1.0.0/

## [v3.12.0] - 2023-06-21
### Added
- Added a route for budget to budget-pro migrations, copies over all the budget item and resource (accounts) data.
- Added an OPTIONS request test for the new migrate route.

## [v3.11.1] - 2023-06-13
### Fixed
- The account deleting jobs are now all aware of the 'budget-pro' item type.
- Added missing "not supported" responses for the 'budget-pro' item type.

## [v3.11.0] - 2023-05-26
### Added
- Added support for the 'budget-pro' item type. For now the 'budget-pro' item type is a duplicate of the 'budget' item type but that will change in the future.
- Added additional tests for the resource controllers.
- Added tests for the item action controller.
- Added tests for the item view controller.
- Added a second phpunit.xml file, this file allows you to run tests in your IDE without Docker setup, the default phpunit.xml file is for the command line.
- Added a test helper to create a new user.
### Changed
- Reviewed the existing test, updated the test to match the App structure.
- Renamed the "Manage" controllers, now "Action".
- Updated the tests section of the README, simpler layout to separate action and view tests.
- Updated the resource type tests, they create data rather then assuming it already exists.
### Removed
- Removed anything related to Bootstrap, we are now using Tailwind.
### Fixed
- Invalid field check missing in 'game' item update.
- The 'game' schema file was incorrect, two fields can be null.
- Referenced lang file incorrect for item type 'game' patch fields.

## [v3.10.0] - 2023-05-11
### Changed
- New landing page, switched to Tailwind

## [v3.09.0] - 2023-01-30
### Changed
- Split all route files based on 'entity' rather than 'visibility'.
- Split the authentication controller into view and manage.
- Split controllers based on function, view, manage and summary.
- Updated the back-end and front-end dependencies.

### Fixed
- Fixed all authentication actions, validation message format updated to match the rest of the API. 

## [v3.08.0] - 2023-01-16
### Added
- Added a `/status` endpoint to return the environment and cache status for the API. 
### Fixed
- The forgot-password endpoint will return an encrypted token, not the actual token.
- Fixed the config file for the forgot password options response.
- Corrected the error message when forgot password can't find the account.

## [v3.07.1] - 2022-12-19
### Fixed
- Content correction for landing page.
- Minor test fixes.
- Query incorrect for fetching permitted user.
- Minor cache tweaks.
- Switch from using request->all in validation.
- Corrected config files.

## [v3.07.0] - 2022-11-15
### Added 
- I have added four more currencies to the API, CAD, AUD, NZD & INR.
### Changed
- Minor content updates to the landing page.
### Fixed
- I have added cache clear requests to the "delete" jobs added in v3.06.0. Data was being deleted but the cache was remaining.

## [v3.06.2] - 2022-11-01
### Fixed
- Item data and item log do not support the `budget` item type yet.

## [v3.06.1] - 2022-10-31
### Changed
- Adjusted cache invalidation, switched to synchronous cache clear

## [v3.06.0] - 2022-10-21
### Added
- I have added multiple routes unto `/auth/` to list permitted resource types and the corresponding permitted resources, these are to enable delete requests.
- I have added a `DeleteResource` job, this job attempts to delete the requested resource and all associated data.
- I have added a `DeleteResourceType` job, this job attempts to delete the requested resource type and all associated data.
- I have added a `DeleteAccount` job, this job attempts to delete your account and all associated data.
- I have added a `v3/auth/user/request-delete` POST endpoint, you can request the full deletion of your account.
- I have added a `v3/auth/user/permitted-resource-types/{permitted_resource_type_id}/request-delete` POST endpoint, you can request the full deletion of a resource type.
- I have added a `v3/auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}/request-delete` POST endpoint, you can request the full deletion of a resource.
### Changed
- Adjusted cache invalidation for create resource type and switch to synchronous cache clear, permitted resource types need to be cleared immediately.
- I have reviewed the cache system and attempted to simplify it.
- I have reviewed and refactored the authentication controller.
### Fixed
- Updated the route descriptions for multiple routes, spelling issues.
- Removed duplicated routes in the `auth` routes file.
- Corrected OPTIONS response for multiple auth routes, not returning authentication status.

## [v3.05.1] - 2022-10-06
### Fixed
- Config file referencing the incorrect language file.
- `target_account` is not a required field for the "budget" item type.
- `amount` and `name` should be sorting options for the "budget" item type collection.
- `description` and `end_date` are nullable fields for the "budget" item type.
- `name` should be a sortable field for the "budget" item type.

## [v3.05.0] - 2022-09-11
### Added
- Added full support for the `budget` item type, all endpoints.

### Changed
- Updated the landing page image for the 'Budget' App.
- Updated the descriptions for all `item` OPTIONS endpoint responses, the descriptions are now specific to the item type`.
- Updated the README, added additional Apps and improved the content in general.

### Fixed
- Corrected PATCH and POST validation for `items`, execution will stop and return immediately.
- Corrected an indentation issue in the CHANGELOG.

## [v3.04.0] - 2022-08-27
### Added
- Added the "Yatzy" item subtype to support the new Yatzy game scorer.
### Changed
- Updated the landing page, added "Expense" and "Yatzy".
### Fixed
- Corrected a return type in a model, possible to be NULL.
- Corrections to the landing page

## [v3.03.0] - 2022-08-13
### Added
- Added a new landing page, mirrors the new "Budget" and "Yahtzee" landing pages.
- Added "Logging" for items - new route `v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log`. Log entries can only be added and will be deleted along with the relevant item.

### Changed
- Switched to Bootstrap 5.
- We have updated the README to detail other Apps which use the API.

### Fixed
- Added a missing PATCH route to the README.
- Corrected the description in an OPTIONS request.

### Removed
- JQuery removed from dependencies.

## [v3.02.1] - 2022-08-04
### Fixed
- Corrected the route prefix for cache invalidation, JSON schema files and the README.

## [v3.02.0] - 2022-08-04
### Added
- We have added the "budget" `item-type`, required for the Budging app.
### Changed
- Updated out tests to be aware of the new `item-type`.
- Updated all out back-end dependencies.

## [v3.01.1] - 2022-08-03
### Changed
- Updated the README to document the `X-Skip-Cache` header.
### Fixed
- Corrected the case for the `X-Skip-Cache` header.

## [v3.01.0] - 2022-07-31
### Changed
- Updated the limit for category assignments. 

## [v3.00.3] - 2022-07-21
### Fixed
- The `complete` filter parameter for games can return in progress games, was limited to be a positive option only.

## [v3.00.2] - 2022-07-20
### Fixed
- The `player_id` should be the `category_id`, not the `item_category_id`

## [v3.00.1] - 2022-07-19
### Fixed
- Corrected the route prefix for cache invalidation, JSON schema files and the README.

## [v3.00.0] - 2022-07-17
### Added
- We have added a keyed data endpoint, allows us to store arbitrary data for games, later, we will add support for keyed data below the `allocated-expense` item-type.
- We have added an `include-players` parameter for the items collection and show requests when fetching games.

### Changed
- The validation error for a non distinct category is not based on the resource type/item type combination. For expenses the message refers to categories, for the game item type the message refers to players.

### Fixed
- The fallback route returns a 404 status code along with the existing message.
- Added a lang file for `parameters-show` for allocated expenses.
- We have updated model calls in the manage controllers, using `$viewable_resource_types` when they should be using `$permitted_resource_types`.
- Minor refactoring and clean-up.

### Removed
- We have removed the `simple-expense` and `simple-item` item types. Simple expense are covered by Allocated expenses and Simple items don't have a place inside the service at the present time.

## [v2.26.0] - 2022-07-06
### Added
- We have added an `item-type` filter to the resource type collection, you can limit what resource types to return.
- We have added an `item-subtype` filter to the resource collection, you can limit what resources to return.
- We have added the 'Yahtzee' item subtype.

### Changed
- Lots of refactoring.
- We have updated our backend dependencies.

### Fixed
- Renamed a lang file, the parameters-show config file should be using the parameters-show lang file.

## [v2.25.0] - 2022-06-20
### Added
- We allow the `collection` parameter for `simple-item` item-type collections.
- We have added tests for subcategory management, found another bug, whoopee!
- We have added tests for item type responses.
- We have added an option response tests for the resource types collection and a resource type.
- We have added additional resource type tests and created/updated the json-schema files as necessary.
- We have added a catch-all route for non-matching routes.

### Changed
- We have renamed the tests directory and corrected the namespaces.
- We are continuing to update out routes to named routes.
- We have moved additional responses to the response class.
- We have updated more response, if a collection is included in a response a `uri` field will contain the relative URI to the relevant collection.
- We have adjusted the layout of the test section in the README and added a note explaining the meaning of 'Non yet'.
- We have updated the response when attempting to delete an item with category assignments, rather than return a generic foreign key error, we specifically mention that there are category assignments that need to be removed first.
- We have cleaned up the response description lang file.

### Fixed
- Removed an output in a test.
- Updated the route middleware, invalid decodes should return a 403 for the route.
- Added a unique validation rule for emails, don't leave the check to the database.
- Corrected the descriptions in the OPTIONS requests for `item` summaries.
- The allowed values for `winner_id` should be a category assigned to the item, not all the categories assigned to the resource type.

## [v2.24.0] - 2022-06-13
### Added
- We have added our first schema files for OPTIONS responses and started working on the tests.
- We have added tests for category management, found one bug when creating the tests.

### Changed
- We have updated our response class for OPTIONS responses, we now allow parameters to be defined for POST requests. One example of where we need this is the create password POST request, `password` and `password_confirmation` are required fields, however, `token` and `email` are required parameters. Before this update, you had to parse the returned error of read the OPTIONS request description.
- We have started splitting config files, a config file should be for one purpose.
- We have spent quite a bit of time reviewing the API structure and refactoring. We have removed unnecessary complexity, renamed classes and methods to describe intent more clearly and removed pointless base classes.
- We have reworked how allowed values are generated for the different item types, allowed values for fields and parameters have been split, and we have removed all abstraction.
- We have removed some route validation files which didn't do anything useful after all the item type work.
- We have reworked the responses class, removed exception parameters when not necessary, pass in an exception if thrown and now delegated responsibility to the responses class to decide if the exception should be returned in the response.
- We have upgraded the API to Laravel 9 and PHP 8.1.

###Fixed
- Options request incorrect for the `auth.register` endpoint (Test added).
- Options requests returning response twice.
- Type corrected in OPTIONS response, authentication status/requirements now a boolean, not a string.
- Minor correction to the description of two POST endpoints.
- Corrected a type in the OPTIONS response for the month parameter.
- Corrected the `partial-transfer` JSON schema file.
- Allowed values not showing for `category` on GET endpoints.
- Inconsistent usage of the responses helper.
- Category validator allowed duplicate names due to incorrect params, caught by model.

### Removed
- We have removed the `ItemType` base class and all the child classes.
- We have removed a redundant validation class and moved the response method into the main response class.

## [v2.23.1] - 2022-04-18
### Changed
- We have updated the partial transfers collection and show route, the partial transfer object includes the URI to the relevant entity.
- We have switched additional routes in our routes files to named routes.
- We have updated the json schema file for partial transfers.

## [v2.23.0] - 2022-04-12
### Added
- We have updated the `/auth/user` route, the route will now show any active created tokens.
- We have added `device_name` as an optional field on sign-in, if set, the generated token will be prefixed with the device name.
- We have added an `include-permitted-users` parameter when requesting a resource type, you will be able to see all the permitted users without having to go down the tree.
- If an API response includes a related object, the first field should be the URI to the relevant collection or resource, we have started updating responses.
- We have added a `auth/user/tokens` route to show the active tokens, you can view an individual token as well as delete a token.
- We have added a notification for failed jobs, if the `ClearCache` job fails we will get an email, luckily, it doesn't ever fail :)
- We have added the ability to assign  permitted users, if you have access to a resource type you can assign a known user to the resource type.
- We have added a view permitted user endpoint.
- We have added the ability to delete a permitted user, you can delete any permitted user with access to the resource type, including yourself.
- We have added initial tests for the permitted user routes.

### Changed
- We have updated sign-in to clear tokens that have not been used for a year.
- We have added additional validation to `/auth/login` to match the create password routes.
- We have removed additional references to our `item-type` entity class, keep code in the individual item type namespaces.
- We have converted out `Mailables` to `Notifications` and they get send via the queue.
- We have updated the `partial-transfers` route to use methods per item types, this way we can correctly return a 405 when an item doesn't support partial transfers.
- We have updated the `transfers` route to use methods per item types, this was we can correctly return a 405 when an item doesn't support transfers.
- We have localised all response messages in the Authentication controller to match the rest of the API.

### Fixed
- We have fixed our Authentication tests, we no longer overwrite the initial user, additionally, we have updated three tests to return success on a 422, not a 401.
- We have corrected a couple of parameter conversions, two parameters not correctly being converted to Booleans.
- Unable to delete an `allocated-expense`, need to clear the partial transfers table.

## [v2.22.0] - 2022-01-26
After being away from the code for a while I've made some changes. I've reduced the complexity around different items types because things had started to get a little complex and I know what is coming next so want to clear out as much unnecessary code as possible. This is just a first pass, I'm sure there will be more but I have many other planned tickets to get on with.

### Added
- We have added additional tests for the `ResourceManage` controller.
- We have added tests for the `ResourceTypeView` controller.
- We have added a logout route.
- We have added an OPTIONS request for `/auth/create-new-password`.
- We have added an OPTIONS request for `/auth/create-password`.
- We have added an OPTIONS request for `/auth/forgot-password`.
- We have added an OPTIONS request for `/auth/login`.
- We have added an OPTIONS request for `/auth/register`.
- We have added an OPTIONS request for `/auth/update-password`.
- We have added an OPTIONS request for `/auth/update-profile`.
- We have added an OPTIONS request for `/auth/user`.
- We have added an OPTIONS request for `/auth/check`.

### Changed
- We have made a couple of minor changes to the Docker setup.
- We have updated the README because of minor Docker changes and corrected the table layouts in the README file.
- We have updated all front-end and back-end dependencies.
- We have updated the copyright, we are now in 2022.
- We have added additional feature tests and removed some duplication in the tests, the README details the current test status.
- General refactoring, switched to method injection and logging exception messages.
- We are switching to named routes and have updated some route files, more will be updated as additional tests are created.
- We have done a quick review of each of the model classes and fixed a few tiny issues.
- We have reviewed all the `ItemType` classes, improved organisation with additional namespaces, renamed classes and methods, all with the goal being to try and make everything clearer.
- We have reviewed all item based controllers and switched to methods per item type rather than hiding all the logic in larger item classes. There is slightly more duplication but this will allow us to more easily customise each item type as new ones are added, I'm looking at you forecasting.
- We have updated the item/categories routes and will return a 405 when a category is not supported for the item type.
- We have updated the item/subcategories routes and will return a 405 when a subcategory is not supported for the item type.
- The Authentication controller no longer extends from the base app controller, it was doing some unnecessary work.

### Fixed
- We have fixed the `delete_resource_type_success` test, wrong route.
- The `notFoundOrNotAccessible` response will optionally return a 403 if not accessible and not a 404.

### Removed
- We have removed a few files not used by the API.

## [v2.21.0] - 2021-04-29
### Changed
- We have updated our password requirements; the minimum length must now be 12 characters.
- We have updated our back-end dependencies.
- Cache clear jobs are dispatched immediately; we no longer delay items in the queue.

## [v2.20.0] - 2021-02-15
### Added
- We have added a JSON `data` field to resource types. The `data` field can be used to store any optional data specific to your resource type.
- We have added a JSON `data` field to resources. The `data` field can be used to store any optional data specific to your resource.
- We have added additional tests.

### Changed
- We have updated our dev dependencies and switched to a new faker library.
- We have moved the `Header` class to the `App\Response` namespace.
- We have tweaked our `Option` classes; we no longer use the same data array to generate Post and Get responses.

### Fixed
- We have corrected our resource-type and resources schemas; the required properties nesting is correct.

### Removed
- We have removed the `effective_date` field from resources; the new `data` field will take over responsibility for storing this data as necessary.
- We have removed the `Response\Header\Headers` class; the class is mostly duplicated code and serves no purpose.

## [v2.19.1] - 2021-02-12
### Added
- We have added additional `resource-type` tests.
- We have started work on `resource` tests.

### Changed
- The cache setting for the API can now be set in `.env`.
- We have updated phpunit.xml; the local cache will be disabled for tests.
- We have updated our Docker setup; we have switched from MySQL 5.7 to 8.0 and PHP 7.4 to PHP 8.0.
- We have moved our Cache classes into `App\Cache`.
- We have updated our `ConvertRouteParameters` middleware; our middleware now returns a 404 for invalid route parameters.

### Fixed
- We have updated some of our responses; the response no longer call `exit()` and are therefore testable.
- We have fixed our `ResourceTypeName` validator; our validator will no longer allow duplicate names.

## [v2.19.0] - 2021-02-08
### Added
- We have started transferring our Postman response tests to local feature tests.
- We have added tests for the `Authentication` controller.
- We have started writing tests for the `ResourceTypeManage` controller.
- We have updated `/auth/forgot-password` and `/auth/register`, both now support a `send` GET parameter, if defined, no email will be issued.

### Changed
- We are tweaking the first install; we have squashed all the migrations and tweaked the Docker setup. We have added an `initial-install.sql` file, this includes the required data for the API.
- We have made minor changes to how we return validation errors; we were calling `exit` and stalling our tests.
- We have updated the responses for `/auth/forgot-password` and `/auth/register`; responses include the required follow-on URIs and parameters.
- We have updated the README, we have added a `Tests` section and updated the setup steps.

## [v2.18.0] - 2021-01-27
### Added
- We have opened up registration on the API; you can register, login, and use all the expected authentication features.
- We have added notification emails for registration and forgot password requests.

### Changed
- We have switched to Laravel Sanctum and removed all references to Laravel Passport, Sanctum makes more sense for our API.
- We have updated to Laravel version 8.
- We have tweaked our Docker setup and removed composer and phpunit.
- Content updates

## [v2.17.1] - 2020-11-28
### Changed
- We have added the `X-Last-Updated` header to the `resource-types`, `resources`, `categories`, `subcategories`, `items` and `resource items` collection routes.
- We have added the `X-Last-Updated` header to additional summary routes; the header was missing, and we are going to use it.
- We have increased the coverage of our request test suite.
- We have relocated our `Transformer` classes; we have moved them out of the `Models` namespace.

### Fixed
- We have updated the way we calculated the value for `X-Last-Updated`. We are using the max of the `created at` and `updated at`, not just looking at the `created at` time.

## [v2.17.0] - 2020-11-22
### Added 
- We have added a `complete` parameter for the `game` item-type; when the parameter is included and set to true, only complete games will be returned in collections and summaries.

### Changed
- We have added item-type based response classes for all item collections and summaries. Item and resource type items are unique; there are no shared dependencies. The shared dependencies were a result of the first two item-types being similar, with the addition of the game item-type, we have learnt our lesson.
- We have tweaked the TTL for permitted, and viewable resource types. The TTL for public viewable resource types is higher than for private users.
- With the addition of more item-type classes, we have tweaked our collection TTLs for public and private users.
- We have moved our 'Method' classes; it doesn't make sense for them to sit inside the 'Option' namespace.
- We have moved our 'AllowedValue' classes; it doesn't make sense for them to sit inside the 'Option' namespace.
- We have reorganised all the item-type classes; we are keeping all the classes for each item-type together.
- We have tweaked our response classes; we will do slightly less work when reading from the cache.

### Removed
- We have removed all our interfaces; the interfaces were not useful, and we are going a slightly different way with the item-type classes, interfaces will return.

## [v2.16.2] - 2020-11-07
### Added
- We are now locally caching the permitted and viewable resource types; this change means we can skip a more expensive query per API request whilst the response is cached, we are experimenting with the TTL.

### Changed
- We have added item `name` and `description` fields to the partial-transfers collection.
- We have updated the schema for partial-transfers.
- We have updated the game item-type; the `winner` field will now be null or an object, the object will have an `id` and a `name`.
- We have updated the OPTIONS request for the `game` item-type; the allowed values for the winner field will display if necessary.
- We have tweaked our middleware; we use our Hash class rather than duplicating the effort.
- We have created several new classes to generate the allowed values data; these new classes are specific to each of our supported item-type. This change will speed up the OPTIONS requests for the non `allocated-expense` item type as we will no longer query the database when we know there will be no results.
- We have added a check to limit access to the partial-transfers route. The 'allocated-expense' item-type supports the partial-transfers feature, partial-transfers don't make sense for the other item-types.
- We have drastically simplified route validation. The API controls access to `resources` at the resource type level; we have updated all route checks to validate the requested resource type rather than validate specific access to the request entity.

### Fixed
- We have corrected several configuration file calls; our calls were looking at transfers, not partial transfers.
- We have added localisation files for the `simple-item` and `game` item-type, several were missing.

## [v2.16.1] - 2020-10-18
### Changed
- We have updated the `friendly_name` for item types; the updated names provide more information and make customising the App simpler.
- We have updated the `item-type` object included within the `resource type` object; the `item-type` object will include the `friendly_name` field.
- We have updated the description for our `game` item type; we are going to support dice games.
- We have removed the duplicated `includeUnpublished` functions and added a reusable method to our `Clause` class.
- We have disabled sessions for our web routes.

### Fixed
- We have corrected an issue with our cache; we were incorrectly creating public cache entries rather than private cache entries. No data is leaking because the cache for resource types controls access and the resource type cache is correct.
- We have renamed the method which adds the clause to include unpublished costs. By default, the method excludes unpublished expenses, so we renamed the method to make the intention clear.

## [v2.16.0] - 2020-10-15
### Added
- We have added a migration to create the `game` item-type table.
- We have added the configuration for the `game` item-types.
- We have added a schema for the `game` item-type.
- We have added a schema for the `game` resource type item-type.
- We have updated the item and resource type item collections; the collections are aware of the new `game` item-type.
- We have updated the summary routes; the item and resource type item summaries are aware of the new `game` item-type.

### Changed
- We are upgrading summaries; the new `game` summaries include much more information than other summaries. We will upgrade all the summaries a little bit at a time.

### Fixed
- We have removed a rogue validation rule present in the POST request for the `allocated-expense` item type.
- We have updated the item category and subcategory assignment routes. Category and subcategory assignment routes can show more than one item in the collection if the item-type configuration allows.

## [v2.15.0] - 2020-10-08
### Added
- We have added an `item-subtype` table; the subtypes will allow us to customise individual `item-types` within the Costs to Expect App.
- We have added a migration for the new `item-type` and the subtypes supported by the `item-type`.
- We have updated all resource collection and item response, we will include the selected `item-subtype` in the response.
- We have added an `item-subtype` schema.
- We have added an `assigned-category` schema for category assignments.
- We have added an `assigned-subcategory` schema for subcategory assignments.

### Changed
- We have modified the unique indexes on the `item_category` and `item_sub_category` table; we need to remove the unique index to allow multiple category assignments per `item`.
- We have updated create resource; we need you to define the `item-subtype` when creating a resource.
- We have added comments to the `allocated-expense` and `simple-expense` models. We left join to the category and subcategory tables knowing there will only ever be at most one category. For later `item-types` multiple categories will get assigned to an item, we will need to come up with an alternative solution.
- We have updated the clear cache calls for delete requests; we no longer add a job to the queue, we clear the cache synchronously.

### Fixed
- We have updated create resource type; we didn't start a transaction.
- We have updated the returned response after creating a resource type; the chosen item type will now show in the response.

## [v2.14.0] - 2020-09-27
### Added
- We have updated the API to support multiple currencies, we are starting with GBP, USD and EUR.
- We have added a `/currencies` route to detail the supported currencies.
- We have increased the scope of our development test suite, specifically with regards to summaries.
- We have added a `/queue` route to show the number of jobs in the queue.

### Changed
- We have updated our item collections. If the `item type` includes a monetary value, a currency object will be part of the response.
- We have updated the relevant item POSTs, `currency_id` is now required.
- We have updated the relevant item PATCHes, `currency_id is an allowable field.
- We have updated our item summaries, the format of the response summary objects supports multiple currencies if necessary.
- We have updated our resource type item summaries, the format of the response summary objects supports multiple currencies if necessary.

## [v2.13.1] - 2020-09-17
### Changed
- We have changed the cache which gets cleared when we create or delete a resource.
- We have added a delay for the job which clears the cache on creation or deletion of a resource type and resource.

### Fixed
- We have corrected a type error; the permitted user check fails because of a type error.

## [v2.13.0] - 2020-09-15
### Added
- We have added support for queues; we clear all cache via queues.

### Changed
- We have updated all our management controllers, we add a job to the queue rather than clearing the necessary cache synchronously.
- We have added the Postman collection link to the menu and renamed the documentation button.
- We have updated our README and included details on how to start the queue.

### Fixed
- We have corrected a couple of minor coding issues, unused parameters etc.
- We have updated our changelog, small spelling error.
- We have updated our controllers and added missing return statements.

## [v2.12.2] - 2020-09-12
### Added
- We have added additional tests to our POSTMAN test collection to ensure allowed values exist where expected.
- We have updated our OPTIONS responses for summary controllers; where relevant, and we show the allowed values for a parameter or field.

### Changed
- We have updated our back-end dependencies.
- We have updated our OPTIONS requests; in some cases, we were not showing allowed values for POST fields and GET parameters.

### Fixed
- We have tweaked our cache query; we use UNIX_TIMESTAMP() for comparison.
- We have removed a unique index from the `resource_type` table.
- We have updated the OPTIONS response for the `resource-types` collection; we show the allowed values for the `item_type_id.`

## [v2.12.1] - 2020-09-09
### Added
- We have reworked our item configuration; we are moving away from multiple item type classes and moving towards a configuration based setup.

### Changed
- We have updated the web.config; our server will not serve static JSON files.
- We have updated our back-end dependencies.
- We will no longer send request error mails for 404s; the number of emails is getting out of hand.
- We have updated our cache manager; some endpoints will only ever have a public cache, never a private cache.

### Fixed
- We have fixed a small bug when creating items of type 'simple-item' and 'simple-expense'; we are not setting a date for 'created_at`.
- We have tweaked our cache management system; our system will not create a private cache for authenticated users when they are looking at public endpoints for which they have no permissions.
- We have updated the allowed values for some OPTIONS requests; the allowed values are sometimes not displaying.
- We have made a minor tweak to the query for selecting cache keys.

## [v2.12.0] - 2020-07-19
### Added
- We have added the ability to exclude public resource types; to exclude public resource types include `exclude-public=1` in your request URIs.
- We have added support for database transactions; if we are modifying more than one table, we use database transactions.
- We have added a route to show the number of cached keys for the authenticated user and then optionally delete.
- We have included the schema files for the API. The schema files are accessible at `/api/schema/[type]`.

### Changed
- We have reworked our Option responses; we have moved the code from the controllers ready for the creation of a new package/library.
- We have removed some duplication in our controllers, fetching dynamic data for Options requests and validation responses is simpler.
- We have updated all manage controllers; we make use of the existing`user_id` property if we need a `user_id`.
- We have reworked how we are clearing cache; we clear the cache for all permitted users when any user makes a cache clearing change.

### Fixed
- We have fixed a couple of instances where we are not passing ids through our hashers.
- We have fixed the included category and subcategory objects in the resource type items collection.
- We have fixed a couple of transformers; we were not correctly formatting totals.
- We have corrected not found calls; in some cases, we were showing error messages on our live environment that we don't want to show.
- We have fixed the allowed values subcategories array; when we show the allowed values array with a validation error, the collection will have values.
- We have reworked all our deletes; in some cases, we were possibly creating null references.
- We have moved a call to fetch a config value; the function call is inside a loop which is a performance issue.
- We have updated the item type models': the `updated_at` and `created_at` come from the relevant item type model.

### Removed
- We have removed all API request logging; the request logging isn't adding any value data that we can't get via other means.

## [v2.11.6] - 2020-07-01
### Added
- We have added support for an `X-Skip-Cache` request header; If you include the header with your request we will skip response caching and fetch live values, please use this with care.

### Changed
- We have added separate links for the documentation and example page and the postman collection.
- We have simplified our `\Model\Transformer` classes and made it possible to alter the returned data format.
- We have added `public` as a sorting option for resource types.
- We have reworked our pagination class; we have moved it to a new workspace and also improved how it works.
- We have moved our `Hash` class; the `Hash` class now lives in the `Request` namespace.
- We have moved our `ModelUtility` class: the `ModelUtility` class now lives in the `Models` namespace.
- We have updated the indexes in our `Hash` request class; the indexes are consistent with the rest of the app.

### Fixed
- We have updated our pagination helper to include any defined filtering parameters.
- We have corrected pagination calls in all our controllers; we now include all possible request parameters.
- We have corrected calls to clear public caches; we were comparing different types.

## [v2.11.5] - 2020-06-24
### Added 
- We have added a documentation page; the documentation page links to the API documentation and includes a couple of examples.

### Changed
- We have updated the example ENV file.
- We have renamed a couple of our helper conversion/validation classes and moved them to a new namespace.
- We have made a minor content tweak on the landing page; the documentation button is in another section.
- We have updated and relocated our validation classes; the validation classes are now part of the `App\Request\Validate` namespace.
- We have reworked our summary controllers; we have removed some code duplication and added additional error checking.

### Fixed
- Incorrectly assuming the result will be an array with at least one value.
- We have fixed an error in the changelog; we jump a couple of minor versions.

## [v2.11.4] - 2020-06-19
### Changed
- We have switched to Redis for our cache.

## [v2.11.3] - 2020-06-19
### Added 
- We have added an application cache for summaries; we include the ETag header however we are not yet returning a 304.
### Changed
- We have updated the caching system to respect the `cache` config setting.

## [v2.11.2] - 2020-06-17
### Changed
- We have split calls to clear public and private cache keys.
- We only clear public cache keys when modifying a public resource type.
- We have updated the web.config; the web.config file was referring to PHP7.3.
- Added removed class type hints now that we are running PHP7.4.

## [v2.11.1] - 2020-06-16
### Added 
- We have added an application cache for collections; we include the ETag header however we are not yet returning a 304.

### Changed
- We have moved our route validators; the route validators sit inside the `App\Request` namespace.
- We have moved our `Header` utility class; the `Header` class sits inside the `App\Response` namespace.
- We have moved our `RoutePermission` utility class; the `Permission` class sits inside the `App\Request` namespace.
- We have moved and simplified our `Parameter` classes; the `Parameter` classes sit inside the `App\Request` namespace.
- We have moved and renamed the `RequestUtility` class; the `BodyValidation` class sits in the `App\Request` namespace.

## [v2.11.0] - 2020-06-06
### Added
- We have added search to the categories summary endpoint.
- We have added search to the subcategories summary endpoint.
- We have added search to the resources summary endpoint.
- We have added search to the resource-types summary endpoint.
- We have updated the transfers collection; you can filter the results by an `item` ID.
- We have updated the partial transfers collection; you can filter the results by an `item` ID.
- We have added additional filtering options for collections. We have added `total` and `actualised_total` range filtering for the `allocated-expense` item type and `total` range filtering for the `simple-expense` item type.

### Changed
- We have added the documentation URI to the README and API; the documentation for the API is work in progress; it is almost complete.
- We have added a version query parameter to the CSS include.
- We have removed a unique index on the `item_transfer` table; the index was limiting the ability to transfer an item multiple times.
- We have altered the format of the category and subcategory object for item relationships; the returned object is now a closer match to a category object.
- We have updated the URIs for item category and subcategory assignments; the URI was singular for a collection.
- We have updated the item delete endpoint; we did not return a conflict error before removing relationships.
- We have tweaked the `type` for expense fields; we highlight that the supplied value should be a decimal string.

### Fixed
- The `name` field now displays as a required field in the items collection OPTIONS request.
- The `filterable` array shows in the OPTIONS response for resource type items.
- We have updated the namespace in a model.
- We have corrected the case of a model name.
- We have added the migrations for the original item-types, missing from the import.
- We have added a database migration to update the `friendly_name` and `examples` data for `item-types`.
- We have updated the delete item request; we delete the transfer log entries for the item.
- We have updated two `item-type` summary endpoints to show they are sortable by `name`.

### Removed
- We have removed the category and subcategory item assigned URIs and replaced them with correctly named URIs for collections.

## [v2.10.4] - 2020-05-23
### Fixed
- We have adjusted the lottery value to reduce session clears.
- We have updated to v3.5.1 of Jquery, v3.5.0 was bugged.

## [v2.10.3] - 2020-05-09
### Fixed
- We have updated the delete resource type action; we have added additional checks before we attempt to delete, it was possible to remove relationship values which made the resource type inaccessible.

## [v2.10.2] - 2020-04-30
### Changed
- We have updated all item endpoints to return `updated`; this is the date and time an item was updated, not its category assignments.
- We have updated item collection and show endpoints; we are going to allow the possibility of items not having categories and subcategories. When you add the `include-categories` and `include-subcategories` parameters to a request, we will not exclude items without category assignments.
- We have updated the API to the latest release of Laravel 7.
- We have updated the front end dependencies for the welcome page.
- We have updated the `item-types` route to show additional information on each tracking method.
- We have updated all decimal fields to 13,2 rather than 10,2.
- We have updated all description fields; we have switched all the description fields from varchar(255) to text.

### Fixed
- We have corrected a bad link on the landing page.
- We have corrected a typo on the landing page.
- We have switched the table we look at to return created at for an item; we should be using the sub table, not the base item table.
- We have corrected the `/resource-types/` OPTIONS request; `public` is not a required field.

## [v2.10.1] - 2020-04-03
### Changed
- We have tweaked our Docker setup to allow a local API and App/Website; the ports have been changed and a network has been created.

## [v2.10.0] - 2020-04-01
### Added 
- We have added a new route, `/resource_types/[id]/resources/[id]/items/[id]/partial-transfer`; A partial transfer allows you to transfer a percentage of the `total` for an item from one resource to another.
- We have added an `item_transfer` table; the table will log which items were transferred and by whom.
- We have added a partial transfers collection; the route is `/resource_types/[id]/partial-transfers`.
- We have added a partial transfers item view; the route is `/resource_types/[id]/partial-transfers/[id]`.
- We have added a transfers collection; the route is `/resource_types/[id]/transfers`.
- We have added a transfers item view; the route is `/resource_types/[id]/transfers/[id]`.
- We have added a delete endpoint for partial transfers.

### Changed
- We have reformatted the validation rules in the configuration files; easier to read and simpler to add additional rules.
- We have switched the HTTP status code for a "Constraint error" from 500 to 409.
- We have tweaked the description for the resource field in the `/resource_types/[id]/resources/[id]/items/[id]/transfer` OPTIONS request.
- We have renamed the third parameter of the route validation methods; we changed the name from `$manage` to `$write`.
- We have renamed a response helper method; it was not clear from the name that the method is used for updates and delete.

### Fixed
- It is possible to set the quantity for a `simple-item` item as zero.
- It is possible to clear optional values in a PATCH request.

## [v2.09.4] - 2020-03-25
### Changed
- When a response includes additional data via the include parameters, we include the URI fragment for that data.

## [v2.09.3] - 2020-03-24
### Changed
- We have updated the `resource-types/[id]/resources/[id]/items/[id]` GET endpoint; the `include-categories` and `include-subcategories` can be included in the request for 'allocated-expense' and 'simple-expense' type items.

## [v2.09.2] - 2020-03-20
### Changed
- We have switched to a new font; the font is more legible at small screen sizes, and, it looks cool.
- We have reviewed our HTTP headers; Content-Language missing along with other expected headers.
- We log the id of the user that added a user to the 'permitted_user' table; this is to help later with permitted user management.
- We have updated the API to the most recent version of Laravel 6.

### Fixed
- The `description` field for the `simple-item` type should be nullable.
- Card data missing from head.
- Google analytics missing.

## [v2.09.1] - 2020-03-14
### Changed
- We have renamed the existing Interfaces, more straightforward names.
- We have added additional Interfaces interfaces for the summary models.
- We have refactored several model classes to again, simplify the naming.
- We have corrected multiple summary config files, unnecessary structure.
- We have unified the parameters for related item methods.

## [v2.09.0] - 2020-03-05
### Added
- We have added range filtering to the `items` collection; initially, we have added support for filtering for the `effective_date` of `allocated expense` items.
- We have added range filtering to the resource type `items` collection; as above, we have added `effective_date` filtering.
- We have added support for range filtering to the `items` and `resource type item` summary routes.
- We have added an X-Filter header to show the valid filters applied to a collection.
- We have added a link to Costs to Expect blog; the blog is a central repository for all product updates and somewhere to talk about our products.
- We have added a `FilterParameters` class to fetch any filter parameters from the URI and validate the format and values.

### Changed
- We have refreshed the landing page, we have added updated text for each of the products within the service.
- We have tweaked the stying for the landing page.
- We have renamed the data methods in the `Option` subclasses, the conditional prefix is confusing.
- We have added an `interface` for the item model classes.
- We have added an `interface` for the resource type item model classes.

### Fixed
- We have updated the `Option/Get` class, the `sort`, `search` and `filter` parameters will only display if there are viable parameters.

### Removed
- We have removed the layout file, not used as there is only one view file for the API.

## [v2.08.1] - 2020-02-27
### Fixed
- Select the correct year when validating the min and max year for year validation.
- The logo on the welcome page will redirect you to the API, not the app dashboard.

## [v2.08.0] - 2020-02-26
### Added 
- We have added a new item type, `simple item`. We intend that the 'simple item' type is useful for managing collections. The API will now allow you to list, add, edit and delete them.
- We have updated the summary routes to calculate the correct summaries for the `simple item` type.

### Changed
- We have removed the effective date from simple expenses. Our intention is simple expenses are bucket based, not time-based.
- We have updated the copyright for the API. 
- We have moved additional methods in the base `item` classes to reduce configuration code duplication.
- We have simplified the validation classes for `item` create and update requests.
- We have renamed some of the methods on the `item` class to make the intent of the method names more clear.
- We have renamed the item interface class.
- We have moved the `resource type item` classes; they are below the`item` class; there is no need for them to be so low in the structure.
- We have moved the factory class for `resource type` items.
- We have moved the validation classes.
- We have moved the `item` model classes; we have grouped all classes of the same type.

## [v2.07.0] - 2020-02-01
### Added
- We have added a GET 'auth/check' endpoint; faster check for the Costs to Expect App.

### Changed 
- We have updated the dependencies for the API.
- We have enabled URL compression.
- We now return the user id on sign-in, saves a second request for the Costs to Expect App.
- We have updated the README, adding links to the App `readme` and `changelog`.
- We have tweaked two middleware classes to improve performance slightly.

### Fixed
- The HTTP verb was incorrect for the 'auth/user' endpoint.

## [v2.06.0] - 2020-01-18
### Added 
- We have added a configuration option to control API registrations.

### Changed
- We have made adjustments to the registration process; we return status code 204. Please sign-in to get your Bearer.
- We have updated the maintenance message to include a link to the status page for the Costs to Expect service.
- Dependencies updated.
- We have added a new landing page; the design matches the rest of the service.

### Fixed
- Correction to a migration, `updated_by` field nullable.
- Correction to a migration file, a data type was incorrect.

## [v2.05.0] - 2019-12-28
### Added 
- We have added an X-Last-Updated header to the item summaries. It is only relevant and applied to the summaries which return a single item. Summaries that return a collection do not include the header.

### Changed
- We have updated the X-Total-Count header for summaries which return a single item; the header is now the total number of 'items' in the result.
- We have tweaked the server config and no longer return some default server headers.

## [v2.04.3] - 2019-12-17
### Changed
- We have added links to the Costs to Expect app.
- We have added 'app' as a source for the request log and error log.
- We have removed three traits from the base controller.
- We have removed the exception code for the failed request entry.

## [v2.04.2] - 2019-12-12
### Changed
- We have switched to database session driver.
- Removed routes we aren't using.
- Switched composer autoloader optimisation.

## [v2.04.1] - 2019-12-12
### Changed
- We have switched to database caching.
- We have updated the parameters helper, it now throws away more invalid requests.
- Dependencies updated.
- We have updated the landing page content.
- We have corrected the response for login, 201 (Bearer created) rather than 200.

## [v2.04.0] - 2019-11-11
### Added
- We have added a GitHub callout to the top right corner of the API landing page, courtesy of https://github.com/tholman/github-corners.

### Changed
- We have updated the code to ensure the data arrays in the config files get used whenever possible; minor data arrays for parameters defined in two locations.
- We have added additional summary models.
- We have moved the item and resource type item models, now organised by namespace, not the class filename.
- We have moved the item and resource type item transformers, now organised by namespace, not the class filename.
- We have moved all the `existsToUser` methods out of the `item` models and into a `PermittedUser` model.
- We have updated the `item` and `resource type item` summary routes; they respect the chosen resource type (Allocated expenses and Simple expenses) and provide the relevant summary.

### Fixed
- We have corrected several more class names, incorrect capitalisation.

## [v2.03.0] - 2019-10-27
### Added
- We have added a new route, `item-types`, the route shows the item types supported by the API.

### Changed
- We have updated the landing page; the focus was previously on the website; the API is the backbone of the entire service; the site is ancillary.
- We have moved the summary controllers; we should use namespaces to organise the code, not filenames.
- We have moved the summary transformers; we should use namespaces to organise the code, not filenames.
- We have started to move the summary models; we should use namespaces to organise the code, not filenames.
- We have merged the authorised and general API routes section, the README details the API routes and the API summary routes in two tables.
- We have moved some configuration files. We thought it was odd how some were outside of folders; additionally, config files should only be in folders if there can be multiple files for the 'section'.
- We have moved localisation files to match their config partners.
- We have updated the allowed values for the `year` GET parameter; we derive the values from the data; the limit for Jack should be 2013 to 2019, for Niall te limit should be 2019.
- We have updated the GET parameter validator; supplied values validated against values defined in the OPTIONS request.

### Fixed
- We have corrected the category summary routes in the README.
- We have corrected the name of a few transformers, incorrect capitalisation.
- We have corrected the name of a few controllers, incorrect capitalisation.
- We have set the salt for the item type hasher.
- For uncaught exceptions, we return the trace when the API is in development mode.
- We have updated the code to generate conditional GET parameters; the item type defines which parameters exist.

## [v2.02.0] - 2019-10-07
### Added
- We have updated the create resource type route. It is now possible to set the item type that you want to use. There are two expense types, "allocated expense" and "simple expense". An allocated expense allows you to allocated a percentage of the total cost; a simple expense only has a total, no allocation rate.
- The supported item types exposed in the resource types OPTIONS request, additional item types will be added over time.
- We have added an item type hasher to hash the ids for item types.
- We have added support for simple expenses, create a simple expense resource type and off you go, "simple expenses" instead of "allocated expenses".

### Changed
- We have added an interface to use when interacting with items; it works out the item type by looking at the resource and then returns the relevant models and configuration data.
- We have updated numerous sections of the API to dynamically expose the relevant fields, sort parameters and search parameters etc. based on the current item type.
- We have moved several `item` based configuration files into the relevant item type folder.

### Fixed
- Localisation missing for allocated expense name.

## [v2.01.2] - 2019-09-25
### Added
- We have added an `X-Parameters` header; it contains the validated parameters for the request.

### Changed
- We have updated the README; we have added a Headers section detailing the purpose of each Header.
- Requests are now only logged when the app is not in development mode.

### Fixed
- The `required` value is incorrectly set to `true` for fields in the PATCH section of the options request.
- We have corrected the queries for the total counts.

## [v2.01.1] - 2019-09-24
### Changed
- The `description` field in the `item_type` tables is now nullable.
- We have updated the `item_type` tables; all descriptions are now null.
- We have upgraded to version 5.8 of the Laravel framework.

### Fixed
- The subcategory parameter is only passed through to the relevant controller if the category parameter is also valid.

## [v2.01.0] - 2019-09-22
### Added
- We have added a `Header` utility class that can be used to generate the expected headers for collection and item requests.
- We have added a `RoutePermission` class which returns the view and manage permission for each route.

### Changed
- We have updated all routes to use the new `Header`, utility class.
- We have updated all the `Options` requests to use the new `RoutePermissions` class.

### Fixed
- We have fixed the item subcategory route; the field name for POST was incorrect, still had the underscore.
- We have corrected the query for the request/access-log, rather than return the entire collection and count the results, probably easier to let MySQL count the results, doh!
- Unable to delete a subcategory because an instance was not being returned from the model.

### Removed
- We have removed the category routes; they are now available below resource types.
- We have removed the category summary routes; they are now available below the resource types summary.
- We have removed the subcategory routes; they are now available below resource types.
- We have removed the subcategory summary routes; they are now available below the resource types summary.

## [v2.00.1] - 2019-09-17
### Fixed
- Links on welcome page incorrect.

## [v2.00.0] - 2019-09-17
### Added
- We have added `updated_by` to the `item` table, records the user who was last to update a record.
- We have added a `permitted_users` table; this is used to link users and resource types.
- A permitted user record created/removed on addition/removal of a resource type.
- We have added a custom validator `ResourceTypeName`; it checks the given name is unique for the user based on the resource types they are permitted to modify.
- We have added two useful properties to the base controller, `permitted_resource_types` and `include_public`.
- There are now two item types, allocated expense and simple expense. We have split the item table; there is the base item data and then the data for the specific item type.

### Changed
- We have updated the `item` table, `user_id` field has been changed to `created_by`.
- We have updated the validation rules for resource types; now aware of permitted users.
- We have renamed the `private` field in the `resource_type` table; it is now `public`, the flag is no longer used to hide the data so it should be named based on the goal.
- We have modified all requests to fetch resource types; the queries now take into account the public setting the resource types you are permitted to manage.
- We have reworked the `resource type`, `resource`, `item`, `category`, `subcategory`, `item category` and `item subcategory` route validators. The validators check your permitted resource types, your intended action and the existence of the `item` based on your permissions.
- We have added additional messages into the language files; API is multi-lingual friendly.
- We have renamed the route validation helper methods, the class is called `Route`, we don't also need `route` in the name.
- We have renamed any incorrectly spelt subcategory variables, the space between `sub` and `category` needed to go; models, classes and controllers, later.
- We have updated the authentication field in the OPTIONS requests; we now show if authentications required for the HTTP verb and what your current authentication status is.
- We have added a base class for the `Option` classes to remove code duplication.
- We have updated the `Option` classes; they now return the current authentication status for the current request.
- We have renamed the `sub_category` field for the `GET/resource-types/[resource-type]/resources/[resource]/items/[item]/category[category]/subcategory` collection and item.
- We have recreated the migration files for new installs. NOTE: you cannot upgrade from v1 to v2 with the included migrations. I have an upgrade SQL file if you need help converting to the new schema.

### Fixed
- We now return a more friendly error message for unauthenticated requests.

### Removed
- We have removed `include_private` and `resource_type_private` from the code, replaced by `include_public` and `permitted_resources_types`.

## [v1.23.0] - 2019-09-05
### Added 
- We have added a new summary route, `/summary/categories`.
- We have added a new summary route, `/summary/categories/{category_id}/subcategories`.

### Changed
- We have added `X-Count` headers to several endpoints from which they were missing.
- Content corrections in the README.

### Fixed 
- Minor corrections after the creation of additional Postman monitors.

## [v1.22.2] - 2019-09-03
### Added
- We have added an error log database table, initially, for capturing 500 errors.
- We have added an `InternalError` event and listener. After writing the error to the database, we send an email with the error.

### Changed
- We have added string length validation for hashed id values; all should be ten characters.
- We have reduced the `request/access-log` limit to 25, from 50.
- We have renamed the `CaptureAndSend` listener; it is specific to request errors so the name should be `CaptureAndSendRequestError`.

## [v1.22.1] - 2019-09-01
### Changed
- We have updated the domain for Mailgun, now mail.costs-to-expect.com rather than the temp domain.
- We have updated the OPTIONS requests, they now show additional validation data if necessary.
- We have continued to unify information names in the OPTIONS requests; we use dashes instead of underscores.
- We have updated the from setting for emails so 'on behalf of' doesn't show for sent emails.

### Fixed
- `PATCH` missing from web.config, we have also corrected the PHP version number.
- String length validation rules missing from validation checks.

## [v1.22.0] - 2019-08-25
### Added
- We have added PATCH support for categories; if authenticated, you can update the selected category.
- We have added PATCH support for subcategories; if authenticated, you can update the selected subcategory.
- We have added PATCH support for resource types; if authenticated, you can update the selected resource type.
- We have added PATCH support for resources; if authenticated, you can update the selected resource.
- We have added a Request utility class with helper methods for POST and PATCH request validation.

### Changed
- Updated the copyright, should be G3D Development Limited, not me personally.
- We have removed the protected `areThereInvalidFieldsInRequest` method from the base controller.
- We have removed the protected `isThereAnythingToPatchInRequest` method from the base controller.
- We have removed the protected `returnValidationErrors` method from the base controller.
- We have updated the README and added Mailgun.

### Fixed 
- We have corrected some data; the assigned categories and subcategories were missing for six expenses.

## [v1.21.1] - 2019-08-20
### Added
- We have added a `debug` field to the request error log; you can optionally provide information that may be useful in tracking down the error.

## [v1.21.0] - 2019-08-19
### Added
- We have added an `OptionPatch` class to help generate the PATCH data array for the OPTIONS requests.
- We have added an `OptionDelete` class to help generate the DELETE data array for the OPTIONS requests.
- We have added an `Event` on request error log entries.
- We have added a `Listener` to capture the request error log event; the Listener sends a mail.

### Changed
- We have updated all controllers to use the new `Option` classes to generate the OPTIONS requests for collections.
- We have updated the `ItemYearTransformer` class.
- We have updated the `ItemCategoryTransformer` class.
- We have updated the `ItemSubCategoryTransformer` class.

### Removed
- Removed the `generateOptionsForIndex()` method from the base controller.
- Removed the `generateOptionsForShow()` method from the base controller.

### Fixed
- We have corrected a call to the subcategory entity when a not found error is returned.

## [v1.20.0] - 2019-08-14
### Added
- We have added `X-Sort` and `X-Search` headers to the `/v1/resource-types/[resource-type]/items` collection.
- We have added `source` to the `/v1/request/error-log` collection.
- We have added listeners to revoke and prune old/unnecessary access tokens.
- We have added an `OptionGet` class to help generate the GET data array for the OPTIONS requests.
- We have added an `OptionPost` class to help generate the POST data array for the OPTIONS requests.

### Changed
- The `/v1/categories` OPTIONS request is generated using the new Option classes.

### Fixed
- Sorting the `/v1/resource-type/[resource-type]/items` collection by `description` generates an error, table name missing from sort field causing ambiguity.

## [v1.19.1] - 2019-08-09
### Added
- We have added a `collection` parameter which is available to some collections. When the `collection` parameter is defined and `true`, it overrides the pagination limits, and we return the entire collection.

### Changed
- We have updated the pagination helper class to check to ensure that the `collection` parameter is allowable for the request.

## [v1.19.0] - 2019-08-08
### Added
- We have added an `X-Sort` header to responses, displays the valid sort options, the applied sort options may differ to the requested sort options.
- We have added an `X-Search` header to responses, displays the valid search options, the applied search options may differ to the requested search options.

### Changed
- We have eliminated some code duplication around allowable search and sort options.
- We have eliminated some code duplication in the models relating to the way search parameters get added to the queries.

### Fixed
- Sorting the `/v1/resource-type/[resource-type]/resources/[resource]/items` collection by `description` generates an error, table name missing from sort field causing ambiguity.

## [v1.18.0] - 2019-08-07
### Added
- We have added sorting to the `/v1/resource-types` collection; you can sort on `name`, `description` and date created.
- We have added sorting to the `/v1/resource-types/[resource-type]/resources` collection; you can sort on `name`, `description`, `effective date` and date created.
- We have added sorting to the `/v1/categories` collection; you can sort on `name`, `description` and date created. 
- We have added sorting to the `/v1/categories/[category]/subcategories` collection; you can sort on `name`, `description` and date created.

### Changed
- We have been busy refactoring again, mainly in the models this time.
- We have increased the Postman test coverage; we are not yet at full coverage; however, we are approaching full coverage with every release.

### Fixed
- We have removed a redundant query after the create category request.

## [v1.17.0] - 2019-08-06
### Added 
- The `v1/summary/resource-types/items` summary supports all the same features as the main `items` summary; you can make a filtered request and even include a search term.
- We have added pagination to the `/v1/categories` GET endpoint.
- We have added pagination to the `/v1/categories/[category]/subcategories` GET endpoint.
- We have added pagination to the `/v1/resource-tyes` GET endpoint.
- We have added pagination to the `/v1/resource-types/[resource-type]/resources` GET endpoint.
- We have added search to the `/v1/categories` GET endpoint; you can search on `name` and `description`.
- We have added search to the `/v1/resource-types` GET endpoint; you can search on `name` and `description`.
- We have added search to the `/v1/categories/[category]/subcategories` GET endpoint; you can search on `name` and `description`.
- We have added search to the `/v1/resource-types/[resource-type]/resources` GET endpoint; you can search on `name` and `description`.

### Changed
- We have modified the year GET parameter to include "next" year, we may have unpublished items for next year and don't want to prohibit summarising the data. (The `year` validation should limit based on the existing data, an issue has been created to find a better solution).
- We have removed unnecessary clauses in the base validation switch statement.
- We have altered the format of the included resources and subcategories when the `include-resources` or `include-subcategories` parameters exist in the request.

### Fixed
- The resource type items summaries are not using the `include-unpublished` parameter.
- The response for a summary request that returns no results returns a 200, not a 404, the endpoint is correct it just no longer returns results because of the GET parameters.
- Transformers should not call models, only transform data, corrected the ResourceType transformer.

### Removed
- Removed the `include-resources` parameter from the resource type collection, not useful at the collection level and causes unnecessary SQL requests, remains an option when requesting a single resource type.
- Removed the `include-subcategories` parameter from the categories collection, not useful at the collection level and causes unnecessary SQL requests, remains an option when requesting a single category.

## [v1.16.5] - 2019-07-29
### Added
- Validation added to validators, check to ensure the required indexes set.

### Changed
- The filtered summary (`v1/summary/resource-types/[resource-type]/resources/[resource]/items`) includes search terms; previously, the request silently dropped them.
- Minor rework of the validator classes, I added abstract methods to the base class.
- There is no need to pass `$request` around.
- Removed a query that is executed after `create item`, unnecessary DB request.
- Refactored the `ItemController` and `Item` model, attempting to get it into shape before the system grows.

## [v1.16.4] - 2019-07-22
### Changed 
- I have reworked the `/summary/resource-types/[resource-type]/resources/[resource]/items` summary. Previously if you defined a time-based filter parameter,  category and subcategory parameters are ignored. 

### Fixed
- Corrected dates in the CHANGELOG, the last two releases were not two years ago.

## [v1.16.3] - 2019-07-17
### Fixed
- Search terms generate invalid SQL.

## [v1.16.2] - 2019-07-16
### Changed
- Tweak to the Docker setup, read values from the .env file. At this time, Docker is for development, that may change in the future; obviously, the passwords in the .env file were not real passwords.
- Moved Xdebug into Docker.

## [v1.16.1] - 2019-07-10
### Added 
- `/summary/request/access-log` summary route to replace `/summary/request/access-log/monthly`.
- `v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer` route to replace `v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/move`
- `source` is a required field for the error log.

### Changed
- Tweaks to Passport/OAuth.
- Tweaks to the Docker setup.
- Moved composer into Docker.
- Upgraded to PHP 7.3.

### Removed
- The `/summary/request/access-log/monthly` route removed; there isn't a corresponding API route, the route shouldn't have `monthly` at the end.
- The `v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/move` route, transfer matches the process better than move and also makes sense for future endpoints, in this case partial-transfer.

### Fixed
- Heading in the CHANGELOG.
- Split the `request/error-log` and `request/access-log` configuration and localisation files.

## [v1.16.0] - 2019-06-30
### Added
- The landing page for the API is no longer affected by maintenance mode.
- We have customised the API response when in maintenance mode.
- Added an alert to the landing page when the API is in maintenance mode.
- The resource type items collection supports sorting via the `sort` parameter.
- The resource type items collection supports searching via the `search` parameter.
- Added an `include-unpublished` parameter to the items collection and the items summary.
- Added an `include-unpublished` parameter to the resource type items collection and summary.
- Added the ability to move items to another resource within the resource type.

### Changed
- The `sortable` and `searchable` parameters for the OPTIONS request are automatically assigned if they are relevant.
- The `sortable` and `searchable` parameters are set to `false` if not relevant for the endpoint.
- Tweaked the exception handler, throw more friendly error messages in production.

### Fixed 
- All item collection and summary queries need to apply the `publish_after` clause via a closure otherwise the braces don't get added correctly in the query.
- Delete item endpoint, `delete()` is called on an array, not a model instance.
- Corrected references to localisation file.
- Fixed a spelling error in the responses localisation file.

## [v1.15.3] - 2019-06-21
### Fixed
- Database error when `include-categories` and `category` parameters both set for an item collection.

## [v1.15.2] - 2019-06-10
### Fixed
- Hasher decode call unable to decode subcategory id.

## [v1.15.1] - 2019-06-09
### Fixed
- Override defaults in number_format.

## [v1.15.0] - 2019-06-09
### Added
- `publish_after` field added to item, POST and PATCH updated.
- Tests for POSTMAN, now up to 280 tests.
- /resource-types/[resource-type]/resources/[resource]/items collection updated to not include unpublished items.
- /resource-types/[resource-type]/items collection updated to not include unpublished items.
- /summary/resource-types/[resource-type]/resources/[resource]/items updated to not include unpublished items.
- /summary/resource-types/[resource-type]/items updated to not include unpublished items.
- search support added to /summary/resource-types/[resource-type]/resources/[resource]/items

### Changed
- `description` added to /summary/resource-types/[resource-type]/resources/[resource]/items?categories=true summary
- `description` added to /summary/resource-types/[resource-type]/items?categories=true summary
- `description` added to /summary/resource-types/[resource-type]/resources/[resource]/items?category=[category]&subcategories=true
- `description` added to /summary/resource-types/[resource-type]/items?category=[category]&subcategories=true
- Category, ItemCategorySummary and ItemSubCategorySummary transformers updated to new setup.
- Up the throttle limit.
- Don't format numbers in output, leave that to the apps.
- Moved fields validators.
- Moved route validators.
- Moved the class used to fetch GET parameters.
- Refactored the sort parameters code, added validation and the code now resides in its own class, now reusable.
- General refactoring, never happy with the design.

### Fixed
- Collection parameters not being passed through to the category transformer. 
- Header indents incorrect for the v1.14.3 changelog entry.
- Description missing from /categories collection.
- Pagination helper not hashing subcategory value before adding to URI.
- Sort and search conditions added to pagination URIs.
- Section value cleared in changelog parser when new release found.

## [v1.14.3] - 2019-06-02
### Added
- Added Twitter social card summary to the landing page.
- Added `include-categories` and `include-subcategories` parameters to /resource-types/[resource-type]/resources/[resource]/items collection.

### Changed
- `postman` is now a supported value for the `source` parameter.

## [v1.14.2] - 2019-05-31
### Added 
- Offset and Limit X headers added to collections.
- If X-Source exists in request header it is saved with the request so we can start tracking sources.
- Capture the id of the user that created an item.
- `source` filter added to /request/access-log.
- `source` filter added to /summary/request/access-log/monthly.

### Changed
- Upgraded `RequestLog` Transformer, switching to a new system.

## [v1.14.1] - 2019-05-01
### Fixed
- Item subcategory collection and single item returning incorrect results.

## [v1.14.0] - 2019-04-30
### Added
- New route /summary/resource-types.
- New route /summary/resource-types/[resource-type]/resources.
- Sorting options added to /resource-types/[resource-type]/resources/[resource]/items collection.

### Changed
- Content updates to the README, CHANGELOG and the landing page.
- GET parameter changed to include-resources for the resource-types route.
- GET parameter changed to include-subcategories for the categories routes.

### Fixed
- Item category and Item subcategory collections were not returning a collection.

## [v1.13.1] - 2019-04-23
### Changed
- Content update, there are now multiple children.

## [v1.13.0] - 2019-04-17
### Added 
- New route /resource-types/[resource-type]/items.
- Added `include-categories` and `include-subcategories` parameters to /resource-types/[resource-type]/items route.
- Added `year`, `month`, `category` and `subcategory` parameters to /resource-types/[resource-type]/items route.
- New route /summary/resource-types/[resource-type]/items.
- Added `resources`, `years`, `year`, `months`, `month`, `categories`, `category`, `subcategories` and `subcategory` parameters to /summary/resource-types/[resource-type]/items route.
- Added favicons for landing page.  

### Changed
- Updated the API landing page to point to the new website.
- Updated the README to include the new website URL.

## [v1.12.1] - 2019-04-10
### Fixed
- Private routes not updated correctly.
- Changelog endpoint not showing changes after format change.

## [v1.12.0] - 2019-04-09
### Changed
- Renamed the route files, filenames relate to access, not the middleware that runs.
- Added flag to allow turning pagination on and off for collection OPTIONS requests.
- /request/log route now /request/access-log.
- /request/log/monthly-requests route now summary/request/access-log/monthly.
- /resource_types/[resource-type]/resources/[resource]/summary/tco now /summary/resource_types/[resource-type]/resources/[resource]/items.
- /resource_types/[resource-type]/resources/[resource]/summary/categories now /summary/resource_types/[resource-type]/resources/[resource]/items?category=all.
- /resource_types/[resource-type]/resources/[resource]/summary/categories/[category] now /summary/resource_types/[resource-type]/resources/[resource]/items?category=[category].
- /resource_types/[resource-type]/resources/[resource]/summary/categories/[category]/sub_categories now /summary/resource_types/[resource-type]/resources/[resource]/items?category=[category]&subcategory=all.
- /resource_types/[resource-type]/resources/[resource]/summary/categories/[category]/sub_categories/[sub-category] now /summary/resource_types/[resource-type]/resources/items?category=[category]&subcategory=[subcategory].
- /resource_types/[resource_type]/resources/[resource]/items/[item]/category/[item-category]/sub_category now /resource_types/[resource-type]/resources/[resource]/items/[item]/category/[item-category]/subcategory.
- /resource_types/[resource_type]/resources/[resource]/items/[item]/category/[item-category]/sub_category/[sub-category] now /resource_types/[resource-type]/resources/[resource]/items/[item]/category/[item-category]/sub_category/[subcategory].
- /categories/[category]/sub_categories now /categories/[category]/subcategories. 
- /categories/[category]/sub_categories/[subcategory] now /categories/[category]/subcategories/[subcategory].
- resource_type switched to resource-types in all URIs 

### Fixed
- OPTIONS request failure when collections do not support POST.
- OPTIONS request showing DELETE when not always valid.
- OPTIONS request methods now all use the same helper methods.
- Validation incorrect for categories, wrong rule being used.
- Unique index incorrect in category table.

### Removed
- Removed /request/log, see changed. 
- Removed /request/log/monthly-requests, see changed.
- Removed /resource_types/[resource-type]/resources/[resource]/summary/categories, see changed.
- Removed /resource_types/[resource-type]/resources/[resource]/summary/categories/[category], see changed.
- Removed /resource_types/[resource-type]/resources/[resource]/summary/categories/[category]/sub_categories, see changed.
- Removed /resource_types/[resource_type]/resources/[resource]/items/[item]/category/[item-category]/sub_category/[subcategory], see changed
- Removed /categories/[category]/sub_categories now /categories/[category]/subcategories, see changed.
- Removed /categories/[category]/sub_categories, see changed.
- Removed /categories/[category]/sub_categories/[subcategory], see changed.
- Removed /resource_types/[resource-type]/resources/[resource]/summary/tco, see changed.
- Removed /resource_types/[resource-type]/resources/{[resource]/expanded_summary/categories. 

## [v1.11.0] - 2019-04-02
### Added 
- All messages and strings (route descriptions, fields in OPTIONS requests, error messages) localised.

### Changed
- Added additional helper methods to the Utilities\Response class.
- Non 200/201 responses returned via the Utilities\Response class.
- Upgraded to the latest version of Laravel 5.7.
- Not found responses not consistent with their error messages.
- Removed route descriptions config file, no longer necessary.
- Updated format of changelog, follows the format defined at https://keepachangelog.com/en/1.0.0/

### Fixed
- Utilities\Response class name incorrect.

## [v1.10.0]  - 2019-03-19
### Fixed
- Categories linked to private resource types don't display if not authenticated.
- Moved some configuration values into `.env`, no need for them to exist in config directly.
- Removed some rogue `use` statements.

### Changed
- Updated copyright, now 2018-2019.
- Added `declare(strict_types=1)` to all non framework files.
- Added missing return types and method param hints.
- Reviewed PATCH support for items, made a few minor changes to catch more errors.
- Moved helper redirects from base controller to utility class.
- Moved transformer classes, now sit below the models.

## [v1.09.3] - 2018-11-18
### Changed
- Updated how OPTIONS are generated for show requests, easier to define all route options.
- Updated README, setup and planned development.

## [v1.09.2] - 2018-11-14
### Fixed
- Correction to query, not using the params passed into the method.

## [v1.09.1] - 2018-11-14
### Changed
- Altered the query for the expanded summary, now begins at subcategories and shows categories without items.
- OPTIONS request not showing PATCH options.

## [v1.09.0] - 2018-11-12
### Added
- Added an expanded-summary categories route.

## [v1.08.0] - 2018-11-10
### Added
- Initial support for PATCHing, to start /resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}.
- Added initial support for private resource types, if a valid bearer exists private resource types are shown.

### Changed
- Updates to README around expected responses.
- Renamed route validation helper methods, now clearer when shown in context.

## [v1.07.2] - 2018-11-03
### Added 
- New route, request/log/monthly-requests number of logged requests per month.

### Changed
- Category name needs to be unique for the resource type.

### Fixed
- Route corrections, some routes using unnecessary middleware.
- Total count missing from HEAD for changelog.
- Validation errors nesting fields property twice.
- Allowed values methods renamed without updating calls in create methods, throwing an error.

## [v1.07.1] - 2018-10-31
### Changed
- Validator base class not set correctly.

## [v1.7.0] - 2018-10-31
### Added
- Added resource_type GET parameter to /categories route to filter results.
- Two options for changelog, markdown on GitHub and via API.
- Added Google Analytics to the landing page.

### Changed
 - Corrected CHANGELOG dates, I'm not from the future.
- Split routes up based on the middleware they require.
- Reworked how OPTIONS are generated, can now set `authenticated`, new method is more expandable as new verbs are supported.
- Moved request validators classes, no sit alongside route validators. 

### Fixed
- POST to categories was not setting the selected resource type, using the default value.

## [v1.06.0] - 2018-10-27
### Changed
- Updated database, a category is now a child of a resource type, not global.
- Updated categories collection and category, shows the resource type that category is assigned to.
- POST/resource_types/.../item/[item_id]/category updated to look at resource type.
- POST/categories now requires the resource_type_id to be set.
- Request log and Request error log now show created times.
- Minor updates to models.

## [v1.05.0] - 2018-10-22
### Added
- Added the ability to POST a request error to the API.
- Added request/error-log route.
- Added request/log route.

### Changed
- Reworked pagination utility class.
- Modified HEADER links for pagination.
- Removed all code referencing PATCH and update, not ready to implement yet and may modify the design.
- Minor refactoring, the order of method params etc.

## [v1.04.3] - 2018-10-14
### Fixed
- Catch routing error, incorrectly return a 200.
- Corrected link to API from landing page.

### Added
- Added link to CHANGELOG on the landing page.
- Added latest release and version number to the landing page.

### Changed
- Minor change to README.

## [v1.04.2] - 2018-10-11
### Added
- Added a /changlog route, parses and displays CHANGELOG.md.
- Added a landing page, links to the root of the API and GitHub.

### Changed
- Simplify generating a basic OPTIONS request.
- Added Utility\Request helper class.
- Added Utility\Pagination helper class.
- Added Utility\General helper class.

## [v1.04.1] - 2018-10-08
### Fixed
- Corrected routes displayed in the root of the API.
- Booleans not being checked correctly.

### Changed
- Split Hashids middleware, now ConvertGetParameters and ConvertRouteParameters.
- Added App\Http\Parameters\Get class to validate GET parameters, moved code from base controller.
- Added App\Http\Parameters\Route\Validate and child classes to validator route parameters.
- Updated controllers to use new App\Http\Parameters\* classes.

## [v104.0] - 2018-09-25
### Added
- Added sub category parameter to /resource_types/{resource_type_id}/resources/{resource_id}/items.

### Changed
- GET parameters are now validated, invalid values are silently removed.
- Minor refactoring.

## [v1.03.1] - 2018-09-23
### Changed
- Added a helper method to the base controller to easily set usable GET parameters for collections.
- Updated the methods/logic for setting allowed values for GET parameters, it is capable of setting more than just the allowed values so all references have been updated to reflect intended usage.
- Updated the methods/logic for setting allowed values for POST parameters, it is capable of setting more than just the allowed values so all references have been updated to reflect intended usage. 

## [v1.03.0] - 2018-09-22
### Added
- Added ability to set GET parameters in OPTIONS requests.
- Added year parameter to /resource_types/{resource_type_id}/resources/{resource_id}/items.
- Added month parameter to /resource_types/{resource_type_id}/resources/{resource_id}/items.
- Added category parameter to /resource_types/{resource_type_id}/resources/{resource_id}/items.

### Changed
- ConvertHashIds middleware now automatically converts query params.
- /resource_types/{resource_type_id}/resources/{resource_id}/items route total count not using resource type and resources ids.

### Fixed
- Parameters not being passed through to pagination links.
- Pagination links incorrect URIs.

## [v1.02.0] - 2018-09-21
### Added
- Added a summary/years route for a resource.
- Added a summary/years/{year} route for a resource.
- Added a summary/years/{year}/month route for a resource.
- Added a summary/years/{year}/month/{month} route for a resource.

### Changed
- Override exceptions, always return json.

## [v1.01.1] - 2018-09-19
### Fixed
- Correct sub category queries, joins incorrect.

## [v1.01.0] - 2018-09-10
### Added
- Added four summary routes for a resource, categories collection, category, subcategories collection and subcategory.

### Changed
- Minor updates to config files.
- Categories and subcategories endpoints now returns results in alphabetical order.
- Code style updates.

## [v1.00.0] - 2018-09-07
### Added
- Added summary/tco route for a resource.
- Added planned development section to README.
- Added summary route section to README.
- Added URL for the live site to README.
- Modified the prefix for routes, simplify to v1, will be hosted on an API subdomain.
- Set default values in env.example.
- Added web.config for Azure.
- Added include_sub_categories GET param for categories collection and single show.

### Changed
- Collections return data showing newest first.
- OPTIONS requests no longer shows PATCH request fields, not yet implemented.
- Show action/Options show action will return resource not found for invalid final ids.
- GET Parameters can now be set for collections and items.
- Other minor fixes and updates.
- Log requests to the API, GET and OPTIONS only.
- Disable register route. 
- Updated README, routes layout.

## [v0.99.0] - 2018-08-08
### Added
- API feature complete for release, not being released yet, real life in the way.
- Added initial pagination to item controller.
- Added Hash utility class to centralise all the encoding and decoding.
- Added Http/Route/Validator classes to validate route params in controllers.
- Reworked relationship for item subcategory and relevant controller updates. 
- Added delete endpoints.
- All route params are validated for each request.
- Updated ConvertHashIds middleware, returns 'nill' for values which can't be decoded, useful for later checks.
- Updated OPTIONS requests, added required Yes/No flags.
- Added ability to define allowed values for fields.
- Updated validation errors, now the same format as OPTIONS requests and shows allowed values if defined.
- Removed response envelopes.
- Non-2xx responses are more consistent.
- Added a responses section to the README that details the expected responses and formats.
- Reworked the id hashing, added middleware and slightly improved encoding although I need to DRY the code, looking at you, transformers, validators and base controller.  
- Moved API config files into a subfolder to separate them from Laravel.
- Added a version config, for post-release, includes the API prefix.
- Redirect client if they request /.
- Reworked the validation helpers.
- Added initial get parameters code.
- Added validation helpers.
- Added model transformers.
- Added migrations and models.
- Added initial pagination HEADERS.
- Moved definition of fields, validation rules, validation message etc. into config/routes.
- Added generic code to create OPTIONS requests 
- Mocked the Category, Sub Category, Resource type, Resource and Item controllers.
- Added Passport.
- Initial setup, git housekeeping etc. 
