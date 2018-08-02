# Changelog

Full changelog for the costs to expect REST API.

## Pre release changes

## Additional development and fine tuning

* Reworked the id hashing, added middle and slightly improved encoding although I need to DRY the code, 
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
