# Costs to expect

## Overview

What does it cost to raise a child in the UK?

[Costs to Expect](https://www.costs-to-expect.com) is a long-term personal project, my wife and I are tracking 
the expenses to raise our children to, adulthood, 18.

### Why?

There are two core reasons as to why we are doing this. I love data, and over the last twenty years, 
it appears to have become accepted knowledge that it costs £250k to raise a child in the UK. 

If you think about the number, it becomes apparent quickly that it can't be right, on average over 
£10k a year?

This API will show the costs to raise our children; obviously, every family is different, these costs only 
relate to our family, more details will appear on [Costs to Expect](https://www.costs-to-expect.com) as development 
on the site continues.

## The API

This Laravel app is the RESTful API for the [Costs to Expect](https://www.costs-to-expect.com) service, 
the API will be consumed by the [Costs to Expect](https://www.costs-to-expect.com) website and the iOS app which 
I'm creating to assist my wife with data input.

## Set up

I'm going to assume you are using Docker, if not you should be able to work out what you need to run for your 
development setup, go to the project root directory and run the below.

### Environment

* Run `docker-compose build`
* Run `docker-compose up`

### App

We now have a working environment, lets set up the app. There are two Docker services, `api` and `mysql`, we need to 
exec into the `api` service to set up our app.

First, let us check we are trying to access the right place, run `docker-compose exec api ls`. You should see a list 
of the files and directories at the root of our project, if you can see artisan, you are in the right place, 
otherwise see where you are and adjust accordingly.

Now we need to set up the app by setting our .env, installing our dependencies and then running any migrations and 
install Passport.

* Copy the `.env.example` file and name the copy `.env`, set your environment settings
* `docker-compose exec api composer install`
* `docker-compose exec api php artisan key:generate`
* `docker-compose exec api php artisan migrate`
* `docker-compose exec api php artisan passport:install`
* Run an OPTIONS request on `http://[your.domail.local]/v1/resource_types`, you should see a nice OPTIONS request, 
alternatively a GET request to `http://[your.domail.local]/v1` will show all the routes.
* You can add a development user by POSTing to `http://[your.domail.local]/v1/auth/register` and then get a bearer by 
POSTing to `http://[your.domail.local]/v1/auth/login` - you will need a bearer for all the routes that require authentication.
* The API is setup to use Mailgun by default, populate `MAILGUN_DOMAIN` and `MAILGUN_SECRET` with values from your account, 
you will also need to set `MAIL_FROM_ADDRESS` and `MAIL_TO_ADDRESS`. You may need to set `Authorized Recipients` in Mailgun. 

## Responses

* Collections will return an array and 200.
* Items will return a single object and a 200.
* Successful POST requests will return a single object and a 201.
* Successful PATCH requests will return 204.
* Successful DELETE requests will return a 204.
* Non 2xx results will return an object with a message field and optionally a fields array, in the 
case of a validation error, 422, the fields array will contain the validation errors.

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
| GET/HEAD | v1/categories |
| OPTIONS  | v1/categories |
| GET/HEAD | v1/categories/{category_id} |
| OPTIONS  | v1/categories/{category_id} |
| GET/HEAD | v1/categories/{category_id}/subcategories |
| OPTIONS  | v1/categories/{category_id}/subcategories |
| GET/HEAD | v1/categories/{category_id}/subcategories/{subcategory_id} |
| OPTIONS  | v1/categories/{category_id}/subcategories/{subcategory_id} |
| GET/HEAD | v1/resource-types |
| OPTIONS  | v1/resource-types |
| GET/HEAD | v1/resource-types/{resource_type_id} |
| OPTIONS  | v1/resource-types/{resource_type_id} | 
| GET/HEAD | v1/resource-types/{resource_type_id}/items |
| OPTIONS  | v1/resource-types/{resource_type_id}/items |
| GET/HEAD | v1/resource-types/{resource_type_id}/resources |
| OPTIONS  | v1/resource-types/{resource_type_id}/resources |
| GET/HEAD | v1/resource-types/{resource_type_id}/resources/{resource_id} |
| OPTIONS  | v1/resource-types/{resource_type_id}/resources/{resource_id} |
| GET/HEAD | v1/resource-types/{resource_type_id}/resources/{resource_id}/items |
| OPTIONS  | v1/resource-types/{resource_type_id}/resources/{resource_id}/items |
| GET/HEAD | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| OPTIONS  | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| GET/HEAD | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category |
| OPTIONS  | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category |
| GET/HEAD | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id} |
| OPTIONS  | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id} |
| GET/HEAD | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory |
| OPTIONS  | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory |
| GET/HEAD | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory/{item_subcategory_id} |
| OPTIONS  | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory/{item_subcategory_id} |
| OPTIONS  | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer |

## Summary routes

Eventually there will be a summary route for every API route, till that point the summary routes 
are details below, some use GET parameters to breakdown the data, one example being items 
which allows you to provide year, month, category and subcategory.

| HTTP Verb(s) | Route |
| :--- | :--- |
| GET/HEAD | v1/summary/request/access-log |
| OPTIONS  | v1/summary/request/access-log |
| GET/HEAD | v1/summary/resource-types |
| OPTIONS  | v1/summary/resource-types |
| GET/HEAD | v1/summary/resource-types/{resource_type_id}/items |
| OPTIONS  | v1/summary/resource-types/{resource_type_id}/items |
| GET/HEAD | v1/summary/resource-types/{resource_type_id}/resources |
| OPTIONS  | v1/summary/resource-types/{resource_type_id}/resources |
| GET/HEAD | v1/summary/resource-types/{resource_type_id}/resources/{resource_id}/items |
| OPTIONS  | v1/summary/resource-types/{resource_type_id}/resources/{resource_id}/items |

## Misc routes

| HTTP Verb(s) | Route |
| :--- | :--- |
| GET/HEAD | v1/changelog |
| OPTIONS  | v1/changelog |
| GET/HEAD | v1/request/error-log |
| OPTIONS  | v1/request/error-log |
| POST     | v1/request/error-log |
| GET/HEAD | v1/request/access-log |
| OPTIONS  | v1/request/access-log |

## Private routes

These routes require authorisation.

| HTTP Verb(s) | Route |
| :--- | :--- |
| GET/HEAD | v1/auth/user |
| POST     | v1/categories |
| PATCH    | v1/categories/{category_id} |
| DELETE   | v1/categories/{category_id} |
| POST     | v1/categories/{category_id}/sub_categories |
| PATCH    | v1/categories/{category_id}/sub_categories/{sub_category_id} |
| DELETE   | v1/categories/{category_id}/sub_categories/{sub_category_id} |
| POST     | v1/resource-types |
| PATCH    | v1/resource-types/{resource_type_id} |
| DELETE   | v1/resource-types/{resource_type_id} |
| POST     | v1/resource-types/{resource_type_id}/resources |
| DELETE   | v1/resource-types/{resource_type_id}/resources/{resource_id} |
| POST     | v1/resource-types/{resource_type_id}/resources/{resource_id}/items |
| PATCH    | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| DELETE   | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| POST     | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category |
| DELETE   | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id} |
| POST     | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category |
| DELETE   | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category/{item_subcategory_id} |
| POST     | v1/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer |
