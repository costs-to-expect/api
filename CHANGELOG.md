# Changelog

The complete changelog for the Costs to Expect REST API, follows the format defined at https://keepachangelog.com/en/1.0.0/

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
