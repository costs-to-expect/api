# Changelog

Full changelog for the Costs to Expect REST API.

## 2019-xx-xx - v1.10.0

* Updated copyright, now 2018-2019.
* Added `declare(strict_types=1)` to non framework files.
* Added missing return types and method param hints.

## 2018-11-18 - v1.09.3

* Updated how OPTIONS are generated for show requests, easier to define all route options.
* Updated README, setup and planned development.

## 2018-11-14 - v1.09.2

* Correction to query, not using the params passed into the method.

## 2018-11-14 - v1.09.1

* Altered the query for the expanded summary, now begins at subcategories and shows categories without items.
* OPTIONS request not showing PATCH options.

## 2018-11-12 - v1.09.0

* Added an expanded-summary categories route.

## 2018-11-10 - v1.08.0

* Initial support for PATCHing, to start /resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}.
* Added initial support for private resource types, if a valid bearer exists private resource types are shown.
* Updates to README around expected responses.
* Renamed route validation helper methods, now clearer when shown in context.

## 2018-11-03 - v1.07.2

* Route corrections, some routes using unnecessary middleware.
* Total count missing from HEAD for changelog.
* Validation errors nesting fields property twice.
* Allowed values methods renamed without updating calls in create methods, throwing an error.
* Category name needs to be unique for resource type.
* New route, request/log/monthly-requests number of logged requests per month.

## 2018-10-31 - v1.07.1

* Validator base class not set correctly.

## 2018-10-31 - v1.07.0

* Added resource_type GET parameter to /categories route to filter results.
* Two options for changelog, markdown on github and via API.
* Added Google Analytics to landing page.
* Corrected CHANGELOG dates, I'm not from the future.
* POST to categories was not setting the selected resource type, using default value.
* Split routes up based on the middleware they require.
* Reworked how OPTIONS are generated, can now set `authenticated`, new method is more expandable as new verbs are supported.
* Moved request validators classes, no sit alongside route validators. 

## 2018-10-27 - v1.06.0

* Updated database, a category is now a child of a resource type, not global.
* Updated categories collection and category, shows the resource type that category is assigned to.
* POST/resource_types/.../item/[item_id]/category updated to look at resource type.
* POST/categories requires the resource_type_id to be set.
* Request log and Request error log now show created times.
* Minor updates to models.

## 2018-10-22 - v1.05.0

* Added the ability to POST a request error to the API.
* Added request/error-log route.
* Added request/log route.
* Reworked pagination utility class.
* Modified HEADER links for pagination.
* Removed all code referencing PATCH and update, not ready to implement yet and may modify design.
* Minor refactoring, order of method params etc.

## 2018-10-14 - v1.04.3

* Catch routing error, incorrectly return a 200.
* Corrected link to API from landing page.
* Added link to CHANGELOG on landing page.
* Added latest release and version number to landing page.
* Minor change to README.

## 2018-10-11 - v1.04.2

* Added a /changlog route, parses and displays CHANGELOG.md.
* Added a landing page, links to the root of the API and GitHub.
* Simplify generating a basic OPTIONS request.
* Added Utility\Request helper class.
* Added Utility\Pagination helper class.
* Added Utility\General helper class.

## 2018-10-08 - v1.04.1

* Corrected routes displayed in root of API.
* Split Hashids middleware, now ConvertGetParameters and ConvertRouteParameters.
* Added App\Http\Parameters\Get class to validate GET parameters, moved code from base controller.
* Added App\Http\Parameters\Route\Validate and child classes to validator route parameters.
* Updated controllers to use new App\Http\Parameters\* classes.
* Minor bug fix, booleans not being checked correctly.

## 2018-09-25 - v1.04.0

* GET parameters are now validated, invalid values are silently removed.
* Added sub category parameter to /resource_types/{resource_type_id}/resources/{resource_id}/items.
* Minor refactoring.

## 2018-09-23 - v1.03.1

* Added helper method to base controller to easily set usable GET parameters for collections.
* Updated the methods/logic for setting allowed values for GET parameters, it is capable of setting more than just the allowed values so all references have been updated to reflect intended usage.
* Updated the methods/logic for setting allowed values for POST parameters, it is capable of setting more than just the allowed values so all references have been updated to reflect intended usage. 

## 2018-09-22 - v1.03.0

* Added ability to set GET parameters in OPTIONS requests.
* Added year parameter to /resource_types/{resource_type_id}/resources/{resource_id}/items.
* Added month parameter to /resource_types/{resource_type_id}/resources/{resource_id}/items.
* Added category parameter to /resource_types/{resource_type_id}/resources/{resource_id}/items.
* ConvertHashIds middleware now automatically converts query params.
* /resource_types/{resource_type_id}/resources/{resource_id}/items route total count not using resource type and resources ids.
* Parameters not being passed through to pagination links.
* Pagination links incorrect URIs.

## 2018-09-21 - v1.02.0

* Added a summary/years route for a resource.
* Added a summary/years/{year} route for a resource.
* Added a summary/years/{year}/month route for a resource.
* Added a summary/years/{year}/month/{month} route for a resource.
* Override exceptions, always return json.

## 2018-09-19 - v1.01.1

* Correct sub category queries, joins incorrect.

## 2018-09-10 - v1.01.0

* Minor updates to config files.
* Categories and sub categories end points now returns results in alphabetical order.
* Added four summary routes for a resource, categories collection, category, sub categories collection and sub category.
* Code style updates.

## 2018-09-07 - v1.00.0 (Official release)

* Added summary/tco route for a resource.
* Added planned development section to README.
* Summary route section to README.
* Added URL for live site to README.
* Modified the prefix for routes, simplify to v1, my will be hosted on an api sub domain.
* Set default values in env.example.
* Disable register route. 
* Updated README, routes layout.
* Added web.config for Azure.
* Log requests to the API, GET and OPTIONS only.
* Added include_sub_categories GET param for categories collection and single show.
* Collections return data showing newest first.
* OPTIONS requests no longer shows PATCH request fields, not yet implemented.
* Show action/Options show action will return resource not found for invalid final ids.
* GET Parameters can now be set for collections and items.
* Other minor fixes and updates.

## 2018-08-08 - v1.00.0 (pre release)

* API feature complete for release, not being released yet, real life in the way.
* Added initial pagination to item controller.
* Added Hash utility class to centralise all the encoding and decoding.
* Added Http/Route/Validator classes to validate route params in controllers.
* Reworked relationship for item sub category and relevant controller updates. 
* Added delete end points.
* All route params are validated for each request.
* Updated ConvertHashIds middleware, returns 'nill' for values which can't be decoded, useful for later checks.
* Updated OPTIONS requests, added required Yes/No flags.
* Added ability to define allowed values for fields.
* Updated validation errors, now the same format as OPTIONS requests and shows allowed values if defined.
* Removed response envelopes.
* Non-2xx responses more consistent.
* Added a responses section to the README that details the expected responses and formats.
* Reworked the id hashing, added middleware and slightly improved encoding although I need to DRY the code, looking at you, transformers, validators and base controller.  
* Moved API config files into a sub folder to separate them from Laravel.
* Added a version config, for post release, includes the api prefix.
* Redirect client if they request /.
* Reworked the validation helpers.
* Added initial get parameters code.
* Added validation helpers.
* Added model transformers.
* Added migrations and models.
* Added initial pagination HEADERS.
* Moved definition of field, validation rules, validation message etc. into config/routes.
* Added generic code to create OPTIONS requests 
* Mocked the Category, Sub Category, Resource type, Resource and Item controllers.
* Added Passport.
* Initial setup, git housekeeping etc. 
