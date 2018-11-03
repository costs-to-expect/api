# Costs to expect

## Overview

What does it costs to raise a child in the UK?

Costs to Expect is a long-term project, my wife and I are tracking the expenses to raise our child to 
adulthood, 18.

### Why?

There are two core reasons as to why I/we are doing this. I love data, and over the last twenty years, 
it appears to have become accepted knowledge that it costs £250k to raise a child in the UK. 

If you think about the number, it becomes apparent quickly that it can't be right, on average over 
£10k a year?

This API will show the costs to raise my child; obviously, every family is different, these costs only 
relate to my family, more details will appear on https://www.costs-to-expect.com as the site develops.

## The API

This Laravel app is the RESTful API for the Costs to Expect service, the API will be consumed by 
the Costs to Expect website and iOS app which I'm creating to assist my wife with data input.

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

* Copy the `.env.example` file and name the copy `.env`, set your environment settings
* `composer install`
* `docker-compose exec app php artisan migrate`
* `docker-compose exec app php artisan passport:install`
* Run an OPTIONS request on `http://api.local/v1/resource_types`, you should see a nice OPTIONS request, 
alternatively a GET request to `http://api.local/v1` will show all the routes.
* You can add a development user by POSTing to `http://api.local/v1/auth/register` and then get a bearer by 
POSTing to `http://api.local/v1/auth/login` - you will need a bearer for all the routes that require authentication.

## Responses

* Collections will return an array and 200.
* Items will return a single object and a 200.
* Successful POST requests will return a single object and a 201.
* Successful DELETE requests will return a 204.
* Non 2xx results will return an object with a message field and optionally a fields array, in the 
case of a validator error, 422, the fields array will contain the validation errors.

## Pagination

You will notice that pagination links and total counts are in the HEADERS, I don't return an 
envelope with counts etc. Specifically, X-Total-Count, X-Link-Previous and X-Link-Next. 
X-Link-Previous and X-Link-Next can be null.

## Data routes

| HTTP Verb(s) | Route |
| :--- | :--- |
| GET/HEAD | v1/ |
| OPTIONS  | v1/ | 
| POST     | v1/auth/login |
| POST     | v1/auth/register (Removed in production) |
| OPTIONS  | v1/categories |
| GET/HEAD | v1/categories |
| OPTIONS  | v1/categories/{category_id} |
| GET/HEAD | v1/categories/{category_id} |
| OPTIONS  | v1/categories/{category_id}/sub_categories |
| GET/HEAD | v1/categories/{category_id}/sub_categories |
| OPTIONS  | v1/categories/{category_id}/sub_categories/{sub_category_id} |
| GET/HEAD | v1/categories/{category_id}/sub_categories/{sub_category_id} |
| GET/HEAD | v1/resource_types |
| OPTIONS  | v1/resource_types |
| GET/HEAD | v1/resource_types/{resource_type_id} |
| OPTIONS  | v1/resource_types/{resource_type_id} | 
| GET/HEAD | v1/resource_types/{resource_type_id}/resources |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id} |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/items |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/items |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category/{sub_category_id} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category/{sub_category_id} |

## Summary routes

| HTTP Verb(s) | Route |
| :--- | :--- |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/tco |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/tco |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/categories |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/categories |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id} |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}/sub_categories |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}/sub_categories |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}/sub_categories/{sub_category_id} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}/sub_categories/{sub_category_id} |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/years |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/years |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year} |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}/months |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}/months |
| GET/HEAD | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}/months/{month} |
| OPTIONS  | v1/resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}/months/{month} |

## Misc routes

| HTTP Verb(s) | Route |
| :--- | :--- |
| GET/HEAD | v1/changelog |
| OPTIONS  | v1/changelog |
| GET/HEAD | v1/request/error-log |
| OPTIONS  | v1/request/error-log |
| POST     | v1/request/error-log |
| GET/HEAD | v1/request/log |
| OPTIONS  | v1/request/log |
| GET/HEAD | v1/request/log/monthly-requests |
| OPTIONS  | v1/request/log/monthly-requests |

## Management routes

These routes require authorisation.

| HTTP Verb(s) | Route |
| :--- | :--- |
| GET/HEAD | v1/auth/user |
| POST     | v1/categories |
| DELETE   | v1/categories/{category_id} |
| POST     | v1/categories/{category_id}/sub_categories |
| DELETE   | v1/categories/{category_id}/sub_categories/{sub_category_id} |
| POST     | v1/resource_types |
| DELETE   | v1/resource_types/{resource_type_id} |
| POST     | v1/resource_types/{resource_type_id}/resources |
| DELETE   | v1/resource_types/{resource_type_id}/resources/{resource_id} |
| POST     | v1/resource_types/{resource_type_id}/resources/{resource_id}/items |
| DELETE   | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| POST     | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category |
| DELETE   | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id} |
| POST     | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category |
| DELETE   | v1/resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category/{sub_category_id} |

## Planned development

* PATCH support.
* PUT support.
* Upgrade Laravel to 5.7.
* Move the user model.
* Dev setting to show generated queries.
* Switch to Money class.
* Create a white box version of API.
* Add limits on POST for single item collections.
