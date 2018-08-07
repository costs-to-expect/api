# Changelog

Full changelog for the costs to expect REST API.

## Pre release changes

## Additional development and fine tuning

* Added Http/Route/Validator classes to validate route params in controllers.
* Reworked relationship for item sub category and relevant controller updates. 
* Added delete end points.
* All route params are validated for each request.
* Updated ConvertHashIds middleware, returns 'nill' for values which can't be decode, useful for later checks.
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

## Initial development

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
