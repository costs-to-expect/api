# Costs to expect

## Overview

Costs to Expect is a service which focuses on tracking and forecasting expenses. 
The Costs to Expect API is the backbone of the service and is not going to be 
limited to expenses; however, we figured that is was an excellent place to start. 

### The App
The [alpha](https://app.costs-to-expect.com) for the service is online, we are 
 hoping to release the public alpha at the start of April 2020. Please check 
 the [app.costs-to-expect.com/roadmap](https://app.costs-to-expect.com/roadmap) 
 and changelog to see how we are getting on 
 [app.costs-to-expect.com/changelog](https://app.costs-to-expect.com/changelog) 

### The Website
A small part of the service is tracking the costs to raise a child in the UK, 
more detail can be found at [Costs to Expect](https://www.costs-to-expect.com).

## Set up

I'm going to assume you are using Docker, if not you should be able to work out 
what you need to run for your development setup, go to the project root 
directory and run the below.

### Environment

* Run `docker-compose build`
* Run `docker-compose up`

### API

We now have a working environment, lets set up the app. There are two Docker 
services, `api` and `mysql`, we need to exec into the `api` service to set up 
our app.

First, let us check we are trying to access the right place, 
run `docker-compose exec api ls`. You should see a list of the files and 
directories at the root of our project, if you can see artisan, you are in 
the right place, otherwise see where you are and adjust accordingly.

Now we need to set up the app by setting our .env, installing our dependencies 
and then running any migrations and install Passport.

* Copy the `.env.example` file and name the copy `.env`, set your environment settings
* `docker-compose exec api composer install`
* `docker-compose exec api php artisan key:generate`
* `docker-compose exec api php artisan migrate`
* `docker-compose exec api php artisan passport:install`
* Run an OPTIONS request on `http://[your.domail.local]/v2/resource_types`, you should see a nice OPTIONS request, 
alternatively a GET request to `http://[your.domail.local]/v1` will show all the routes.
* You can add a development user by POSTing to `http://[your.domail.local]/v2/auth/register` and then get a bearer by 
POSTing to `http://[your.domail.local]/v2/auth/login` - you will need a bearer for all the routes that require authentication.
* The API is setup to use Mailgun by default, populate `MAILGUN_DOMAIN` and `MAILGUN_SECRET` with values from your account, 
you will also need to set `MAIL_FROM_ADDRESS` and `MAIL_TO_ADDRESS`. You may need to set `Authorized Recipients` in Mailgun. 

## Responses

* Collections will return an array and a 200.
* Items will return a single object and a 200.
* Successful POST requests will return a single object and a 201.
* Successful PATCH requests will return 204.
* Successful DELETE requests will return a 204.
* Non 2xx results will return an object with a message field and optionally a fields array, in the 
case of a validation error, 422, the fields array will contain the validation errors.

## Headers

Responses will include multiple headers, the table details the purpose behind 
some of the custom headers.

| Header | Purpose |
| :--- | :--- |
| X-Total-Count | Pagination: Total number of result |
| X-Count | Pagination: Number of results returned |
| X-Limit | Pagination: Limit value applied to request after validation |
| X-Offset | Pagination: Offset value applied to request after validation |
| X-Offset | Pagination: Offset value applied to request after validation |
| X-Link-Previous | Pagination: URI for previous result set if relevant |
| X-Link-Next | Pagination: URI for next result set if relevant |
| X-Link-Next | Pagination: URI for next result set if relevant |
| X-Sort | Sort options applied to request after validation |
| X-Search | Search options applied to request after validation |
| X-Parameters | Request parameters applied to request after validation |

## Routes

Access to a route will be limited based upon your permitted resource types. 
When you create a resource type you have full access to everything below, 
additionally, the same is true if you are assigned to a resource type.

| HTTP Verb(s) | Route |
| :--- | :--- |
| GET/HEAD | v2/ |
| OPTIONS  | v2/ | 
| POST     | v2/auth/login |
| POST     | v2/auth/register  |
| GET/HEAD | v2/auth/check |
| GET/HEAD | v2/auth/user |
| GET/HEAD | v2/changelog |
| OPTIONS  | v2/changelog |
| GET/HEAD | v2/item-types |
| OPTIONS  | v2/item-types |
| GET/HEAD | v2/item-types/{item_type_id} |
| OPTIONS  | v2/item-types/{item_type_id} |
| GET/HEAD | v2/resource-types |
| OPTIONS  | v2/resource-types |
| POST     | v2/resource-types |
| GET/HEAD | v2/resource-types/{resource_type_id} |
| OPTIONS  | v2/resource-types/{resource_type_id} |
| PATCH    | v2/resource-types/{resource_type_id} |
| DELETE   | v2/resource-types/{resource_type_id} |
| GET/HEAD | v2/resource-types/{resource_type_id}/categories |
| OPTIONS  | v2/resource-types/{resource_type_id}/categories |
| POST     | v2/resource-types/{resource_type_id}/categories |
| PATCH    | v2/resource-types/{resource_type_id}/categories/{category_id} |
| DELETE   | v2/resource-types/{resource_type_id}/categories/{category_id} |
| GET/HEAD | v2/resource-types/{resource_type_id}/categories/{category_id} |
| OPTIONS  | v2/resource-types/{resource_type_id}/categories/{category_id} |
| GET/HEAD | v2/resource-types/{resource_type_id}/categories/{category_id}/subcategories |
| OPTIONS  | v2/resource-types/{resource_type_id}/categories/{category_id}/subcategories |
| POST     | v2/resource-types/{resource_type_id}/categories/{category_id}/subcategories |
| GET/HEAD | v2/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id} |
| OPTIONS  | v2/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id} |
| PATCH    | v2/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id} |
| DELETE   | v2/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id} |
| GET/HEAD | v2/resource-types/{resource_type_id}/items |
| OPTIONS  | v2/resource-types/{resource_type_id}/items |
| OPTIONS  | v2/resource-types/{resource_type_id}/permitted-users |
| GET/HEAD | v2/resource-types/{resource_type_id}/resources |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources |
| POST     | v2/resource-types/{resource_type_id}/resources |
| GET/HEAD | v2/resource-types/{resource_type_id}/resources/{resource_id} |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources/{resource_id} |
| PATCH    | v2/resource-types/{resource_type_id}/resources/{resource_id} |
| DELETE   | v2/resource-types/{resource_type_id}/resources/{resource_id} |
| GET/HEAD | v2/resource-types/{resource_type_id}/resources/{resource_id}/items |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources/{resource_id}/items |
| POST     | v2/resource-types/{resource_type_id}/resources/{resource_id}/items |
| GET/HEAD | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| PATCH    | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| DELETE   | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id} |
| GET/HEAD | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category |
| POST     | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category |
| GET/HEAD | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id} |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id} |
| DELETE   | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id} |
| GET/HEAD | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory |
| POST     | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category |
| GET/HEAD | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory/{item_subcategory_id} |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory/{item_subcategory_id} |
| DELETE   | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category/{item_subcategory_id} |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer |
| POST     | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer |
| OPTIONS  | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer |
| POST     | v2/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer |
| GET/HEAD | v2/request/error-log |
| OPTIONS  | v2/request/error-log |
| POST     | v2/request/error-log |
| GET/HEAD | v2/request/access-log |
| OPTIONS  | v2/request/access-log |

## Summary routes

Eventually, there will be a summary route for every API GET endpoint. Until 
that point, the summary routes that exists are detailed below. Some use GET 
parameters to breakdown the data, one example being 
`v2/summary/resource-types/{resource_type_id}/items`. Review the OPTIONS 
request for each summary route to see the supported parameters, these should 
largely match the non summary route.

| HTTP Verb(s) | Route |
| :--- | :--- |
| GET/HEAD | v2/summary/request/access-log |
| OPTIONS  | v2/summary/request/access-log |
| GET/HEAD | v2/summary/resource-types |
| OPTIONS  | v2/summary/resource-types |
| GET/HEAD | v2/summary/resource-types/{resource_type_id}/categories |
| OPTIONS  | v2/summary/resource-types/{resource_type_id}/categories |
| GET/HEAD | v2/summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories |
| OPTIONS  | v2/summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories |
| GET/HEAD | v2/summary/resource-types/{resource_type_id}/items |
| OPTIONS  | v2/summary/resource-types/{resource_type_id}/items |
| GET/HEAD | v2/summary/resource-types/{resource_type_id}/resources |
| OPTIONS  | v2/summary/resource-types/{resource_type_id}/resources |
| GET/HEAD | v2/summary/resource-types/{resource_type_id}/resources/{resource_id}/items |
| OPTIONS  | v2/summary/resource-types/{resource_type_id}/resources/{resource_id}/items |
