# Changelog

Full changelog for the costs to expect REST API.

## 2019-xx-xx - v1.xx-x

* Corrected routes displayed in root of API.
* Split Hashids middleware, now ConvertGetParameters and ConvertRouteParameters.
* Added App\Http\Parameters\Get class to validate GET parameters, moved code from base controller.
* Added App\Http\Parameters\Route\Validate and child classes to validator route parameters.
* Updated controllers to use new App\Http\Parameters\* classes.

## 2019-09-25 - v1.04.0

* GET parameters are now validated, invalid values are silently removed.
* Added sub category parameter to /resource_types/{resource_type_id}/resources/{resource_id}/items.
* Minor refactoring.

## 2018-09-23 - v1.03.1

* Added helper method to base controller to easily set usable GET parameters for collections.
* Updated the methods/logic for setting allowed values for GET parameters, it is capable of setting more than just 
the allowed values so all references have been updated to reflect intended usage.
* Updated the methods/logic for setting allowed values for POST parameters, it is capable of setting more than just 
the allowed values so all references have been updated to reflect intended usage. 

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
* Added four summary routes for a resource, categories collection, category, 
sub categories collection and sub category.
* Code style updates.

## 2018-09-07 - v1.00.0 

Official release

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

## 2018-08-08 - v1.00 (pre release)

* API feature complete for release, not being released yet, real life in the way.

## Pre release changes

### Additional development and fine tuning

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
* Reworked the id hashing, added middleware and slightly improved encoding although I need to DRY the code, 
looking at you, transformers, validators and base controller.  
* Moved API config files into a sub folder to separate them from Laravel.
* Added a version config, for post release, includes the api prefix.
* Redirect client if they request /.
* Reworked the validation helpers.

### Initial development

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
