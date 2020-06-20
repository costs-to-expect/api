# Changelog

The complete changelog for the Costs to Expect REST API, our changelog follows the format defined at https://keepachangelog.com/en/1.0.0/

## [v2.12.0] - 2020-xx-xx
### Changed
- We have updated the example ENV file.

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
