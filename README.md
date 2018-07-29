# Costs to expect

## The API

This Laravel app is the RESTful API for costs-to-expect, the API will be consumed by the 
costs to expect website and iOS app which I'm creating to assist the wife with data input.

## Set up

I'm going to assume you are using Docker, if not you should be able to work out what you need to run for your 
development setup, go to the project root directory and run the below.

### Environment

* Run `docker-compose build`
* Create the following directories in `.docker`, `.docker/app/php`, `.docker/app/mysql` and `.docker/app/mysql/data`
* Run `docker-compose up`

### App

We now have a working environment, lets set up the app. There are two Docker services, `app` and `mysql`, we need to 
exec into the `app` service to set up our app.

First, let us check we are trying to access the right place, run `docker-compose exec app ls`. You should see a list 
of the files and directories at the root of our project, if you can see artisan, you are in the right place, 
otherwise see where you are and adjust accordingly.

Now we need to set up the app by setting our .env, installing our dependencies and then running any migrations and 
install Passport.

* Copy the `.env.example` file and name the copy `.env`, set  your environment settings
* `composer install`
* `docker-compose exec app php artisan migrate`
* `docker-compose exec app php artisan passport:install`
* Run a OPTIONS request on `http://api.local/api-v1/resource_types`, you should see a nice OPTIONS request, 
alternatively a GET request to `http://api.local/api-v1` will show all the routes.
* You can add a development user by POSTing to `http://api.local/api-v1/auth/register` and then get a bearer by 
POSTing to `http://api.local/api-v1/auth/login` - you will need a bearer for all the routes that require authentication.

## Routes

Current API Routes, for fields and parameters, check the OPTIONS request for each 
endpoint.

| HTTP Verb | Route |
| :--- | :--- |
| GET/HEAD | /                                                                                |
| OPTIONS  | /                                                                                | 
| POST     | api-v1/auth/login                                                                |
| POST     | api-v1/auth/register                                                             |
| GET/HEAD | api-v1/auth/user                                                                 |
| POST     | api-v1/categories                                                                |
| OPTIONS  | api-v1/categories                                                                |
| GET/HEAD | api-v1/categories                                                                |
| OPTIONS  | api-v1/categories/{category_id}                                                  |
| GET/HEAD | api-v1/categories/{category_id}                                                  |
| PATCH    | api-v1/categories/{category_id}                                                  |
| DELETE   | api-v1/categories/{category_id}                                                  |
| POST     | api-v1/categories/{category_id}/sub_categories                                   |
| OPTIONS  | api-v1/categories/{category_id}/sub_categories                                   |
| GET/HEAD | api-v1/categories/{category_id}/sub_categories                                   |
| OPTIONS  | api-v1/categories/{category_id}/sub_categories/{sub_category_id}                 |
| GET/HEAD | api-v1/categories/{category_id}/sub_categories/{sub_category_id}                 |
| DELETE   | api-v1/categories/{category_id}/sub_categories/{sub_category_id}                 |
| PATCH    | api-v1/categories/{category_id}/sub_categories/{sub_category_id}                 |
| POST     | api-v1/resource_types                                                            |
| GET/HEAD | api-v1/resource_types                                                            |
| OPTIONS  | api-v1/resource_types                                                            |
| GET/HEAD | api-v1/resource_types/{resource_type_id}                                         |
| PATCH    | api-v1/resource_types/{resource_type_id}                                         |
| OPTIONS  | api-v1/resource_types/{resource_type_id}                                         |
| DELETE   | api-v1/resource_types/{resource_type_id}                                         |
| POST     | api-v1/resource_types/{resource_type_id}/resources                               |
| GET/HEAD | api-v1/resource_types/{resource_type_id}/resources                               |
| OPTIONS  | api-v1/resource_types/{resource_type_id}/resources                               |
| OPTIONS  | api-v1/resource_types/{resource_type_id}/resources/{resource_id}                 |
| PATCH    | api-v1/resource_types/{resource_type_id}/resources/{resource_id}                 |
| DELETE   | api-v1/resource_types/{resource_type_id}/resources/{resource_id}                 |
| GET/HEAD | api-v1/resource_types/{resource_type_id}/resources/{resource_id}                 |
| OPTIONS  | api-v1/resource_types/{resource_type_id}/resources/{resource_id}/items           |
| POST     | api-v1/resource_types/{resource_type_id}/resources/{resource_id}/items           |
| GET/HEAD | api-v1/resource_types/{resource_type_id}/resources/{resource_id}/items           |
| GET/HEAD | api-v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| OPTIONS  | api-v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| DELETE   | api-v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
