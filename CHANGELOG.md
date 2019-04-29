# Changelog

The complete changelog for the Costs to Expect REST API, follows the format defined at https://keepachangelog.com/en/1.0.0/

## [v1.14.0] - 2019-05-xx
### Added
- New route /summary/resource-types.
- New route /summary/resource-types/[resource-type]/resources

### Changed
- Content updates, README, CHANGELOG and the landing page.
- GET parameter changed to include-resources for resource-types route.

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
- Added Google Analytics to then landing page.

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
