# Costs to expect

## Overview

Costs to Expect is a service original intended to track and forecast expenses. 
This API is the backbone of the service and is not limited to tracking expenses, over the years we have 
made it flexible enough to record almost anything, including dice games.

## Documentation

The documentation for the Costs to Expect API can be found at 
[postman.costs-to-expect.com](https://postman.costs-to-expect.com?version=latest). 

The documentation is slowly being moved to a repository on 
[GitHub](https://github.com/costs-to-expect/api-docs). It is taking a while to rewrite and update the 
documentation, it will however be complete before the official of 'Budget'.

## Apps

The API is used by the following Apps;

- [Budget](https://budget.costs-to-expect.com) Our free and Open Source Budget tool
- Budget Pro (Coming soon) Our commercial version of Budget
- [Expense](https://app.costs-to-expect.com) Our free and Open Source expense tracker
- Expense Pro (Coming soon) Our commercial version of Expense
- [Social Experiment](https://www.costs-to-expect.com) How much does it cost to raise a child to adulthood in the UK?
- [Yahtzee Game Scorer](https://yahtzee.game-score.com) Our Yahtzee Game Scorer, free for all to use
- [Yatzy Game Scorer](https://yatzu.game-score.com) Our Yatzy Game Scorer, free for all to use

## Set up

I'm going to assume you are using Docker, if not, sorry. You should be able to work out what you need to 
do for your development setup from the steps below.

Go to the project root directory and run the below.

* $ `docker network create costs.network` *
* $ `docker compose build`
* $ `docker compose up -d`

*We include a network for local development purposes, I need a local copy of the API to communicate with 
my local App. You probably don't need this so remove the network section from the docker compose file and 
don't create the network.*

You now have a working environment, so need to set up the API. There are two 
Docker services, `api` and `mysql`, we will need to exec into the `api` service to 
set up our app.

Firstly, we need to check we are trying to access the right location, 
execute `docker compose exec costs.api.app ls`. You should see a list of the files and 
directories at the project root. 

Next, we need to configure the API by setting out local .ENV file our .env, 
installing all dependencies and running our migrations.

* Copy the `.env.example` file and name the copy `.env`. Set all the empty values, all 
drivers have been set to our defaults, sessions, cache, and the queue default to the database driver.
* `docker compose exec costs.api.app php artisan key:generate`
* `docker compose exec costs.api.app php artisan migrate`)
* `docker compose exec costs.api.app php artisan queue:work`
* Run an OPTIONS request on `http://[your.domail.local:8080]/v3/resource_types`, you will see an OPTIONS response, 
alternatively a GET request to `http://[your.domail.local:8080]/v1` will show all the defined routes.
* You can create a user by POSTing to `http://[your.domail.local:8080]/v3/auth/register`. 
* You create a password by POSTing a password and password_confirmation to the URI register response. 
* You can sign-in by posting to `http://[your.domail.local:8080]/v3/auth/login` - you will need a bearer for all the routes that require authentication.
* Our API defaults to Mailgun, populate `MAILGUN_DOMAIN` and `MAILGUN_SECRET` with the relevant values from your account, 
you will also need to set `MAIL_FROM_ADDRESS` and `MAIL_TO_ADDRESS`. You may need to set `Authorized Recipients` in Mailgun. 

## Responses

* On success, collections will return an array and a 200.
* On success, show requests will return a single object and a 200, no response envelope.
* Successful POST requests will return a single object and a 201, there are minor exceptions where we return a 204.
* Successful PATCH requests will return 204.
* Successful DELETE requests will return a 204.
* Non 2xx results will return an object with a message field and optionally a fields array. When we 
return a validation error, the response will be 422 and include a fields array which will contain all validation errors.

## Caching

The API caches responses per authenticated user and also a public cache.

You can skip reading from the cache for a specific request by adding the `X-Skip-Cache` header, any value will do, 
the existence of the header is all that matters.

Cache is cleared when we detect a change to relevant data, currently, there is a minor delay before cache is cleared, 
we are planning to clear cache immediately, we want to review the performance impact before making the change.

## Headers

Responses will include multiple headers, the table below details the intention behind each of our custom headers.

| Header          | Purpose                                             |
|:----------------|:----------------------------------------------------|
| X-Total-Count   | Pagination: Total number of results for the request |
| X-Count         | Pagination: Number of results returned              |
| X-Limit         | Pagination: Limit value applied to request          |
| X-Offset        | Pagination: Offset value applied to request         |
| X-Link-Previous | Pagination: URI for previous result set if relevant |
| X-Link-Next     | Pagination: URI for next result set if relevant     |
| X-Last-Updated  | The last time the collection was updated            |
| X-Sort          | Sort options applied to the request                 |
| X-Search        | Search options applied to the request               |
| X-Skip-Cache    | Return collection, bypassing the cache              |
| X-Parameters    | Request parameters applied to request               |
| X-Filter        | Filter options applied to the request               |

## Routes

Access to a route is limited based upon a users permitted resource types. When a user creates a resource type they 
have full access to everything below it, additionally, the same is true if you are assigned as a permitted user to a 
resource type.

Public resources types provide READ access to everyone, WRITE access is limited to the permitted users.

| HTTP Verb(s) | Route                                                                                                                                          |
|:-------------|:-----------------------------------------------------------------------------------------------------------------------------------------------|
| GET/HEAD     | v3/                                                                                                                                            |
| OPTIONS      | v3/                                                                                                                                            | 
| GET/HEAD     | v3/auth/check                                                                                                                                  |
| OPTIONS      | v3/auth/check                                                                                                                                  |
| OPTIONS      | v3/auth/create-password                                                                                                                        |
| POST         | v3/auth/create-password                                                                                                                        |
| OPTIONS      | v3/auth/create-new-password                                                                                                                    |
| POST         | v3/auth/create-new-password                                                                                                                    |
| OPTIONS      | v3/auth/forgot-password                                                                                                                        |
| POST         | v3/auth/forgot-password                                                                                                                        |
| OPTIONS      | v3/auth/login                                                                                                                                  |
| POST         | v3/auth/login                                                                                                                                  |
| GET          | v3/auth/logout                                                                                                                                 |
| OPTIONS      | v3/auth/register                                                                                                                               |
| POST         | v3/auth/register                                                                                                                               |
| OPTIONS      | v3/auth/update-password                                                                                                                        |
| POST         | v3/auth/update-password                                                                                                                        |
| OPTIONS      | v3/auth/update-profile                                                                                                                         |
| POST         | v3/auth/update-profile                                                                                                                         |
| GET/HEAD     | v3/auth/user                                                                                                                                   |
| OPTIONS      | v3/auth/user                                                                                                                                   |
| GET/HEAD     | v3/auth/permitted-resource-types                                                                                                               |
| OPTIONS      | v3/auth/permitted-resource-types                                                                                                               |
| GET/HEAD     | v3/auth/permitted-resource-types/{permitted_resource_type_id}                                                                                  |
| OPTIONS      | v3/auth/permitted-resource-types/{permitted_resource_type_id}                                                                                  |
| OPTIONS      | v3/auth/permitted-resource-types/{permitted_resource_type_id}/request-delete                                                                   |
| POST         | v3/auth/permitted-resource-types/{permitted_resource_type_id}/request-delete                                                                   |
| GET/HEAD     | v3/auth/permitted-resource-types/{permitted_resource_type_id}/resources                                                                        |
| OPTIONS      | v3/auth/permitted-resource-types/{permitted_resource_type_id}/resources                                                                        |
| GET/HEAD     | v3/auth/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}                                                          |
| OPTIONS      | v3/auth/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}                                                          |
| OPTIONS      | v3/auth/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}/request-delete                                           |
| POST         | v3/auth/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}/request-delete                                           |
| GET/HEAD     | v3/auth/user/tokens                                                                                                                            |
| OPTIONS      | v3/auth/user/tokens                                                                                                                            |
| DELETE       | v3/auth/user/tokens/{token_id}                                                                                                                 |
| GET/HEAD     | v3/auth/user/tokens/{token_id}                                                                                                                 |
| OPTIONS      | v3/auth/user/tokens/{token_id}                                                                                                                 |
| GET/HEAD     | v3/changelog                                                                                                                                   |
| OPTIONS      | v3/changelog                                                                                                                                   |
| GET/HEAD     | v3/currencies                                                                                                                                  |
| OPTIONS      | v3/currencies                                                                                                                                  |
| GET/HEAD     | v3/currencies/{currency_id}                                                                                                                    |
| OPTIONS      | v3/currencies/{currency_id}                                                                                                                    |
| GET/HEAD     | v3/item-types                                                                                                                                  |
| OPTIONS      | v3/item-types                                                                                                                                  |
| GET/HEAD     | v3/item-types/{item_type_id}                                                                                                                   |
| OPTIONS      | v3/item-types/{item_type_id}                                                                                                                   |
| GET/HEAD     | v3/item-types/{item_type_id}/item-subtypes                                                                                                     |
| OPTIONS      | v3/item-types/{item_type_id}/item-subtypes                                                                                                     |
| GET/HEAD     | v3/item-types/{item_type_id}/item-subtypes/{item_subtype_id}                                                                                   |
| OPTIONS      | v3/item-types/{item_type_id}/item-subtypes/{item_subtype_id}                                                                                   |
| GET/HEAD     | v3/resource-types                                                                                                                              |
| OPTIONS      | v3/resource-types                                                                                                                              |
| POST         | v3/resource-types                                                                                                                              |
| GET/HEAD     | v3/resource-types/{resource_type_id}                                                                                                           |
| OPTIONS      | v3/resource-types/{resource_type_id}                                                                                                           |
| PATCH        | v3/resource-types/{resource_type_id}                                                                                                           |
| DELETE       | v3/resource-types/{resource_type_id}                                                                                                           |
| GET/HEAD     | v3/resource-types/{resource_type_id}/categories                                                                                                |
| OPTIONS      | v3/resource-types/{resource_type_id}/categories                                                                                                |
| POST         | v3/resource-types/{resource_type_id}/categories                                                                                                |
| PATCH        | v3/resource-types/{resource_type_id}/categories/{category_id}                                                                                  |
| DELETE       | v3/resource-types/{resource_type_id}/categories/{category_id}                                                                                  |
| GET/HEAD     | v3/resource-types/{resource_type_id}/categories/{category_id}                                                                                  |
| OPTIONS      | v3/resource-types/{resource_type_id}/categories/{category_id}                                                                                  |
| GET/HEAD     | v3/resource-types/{resource_type_id}/categories/{category_id}/subcategories                                                                    |
| OPTIONS      | v3/resource-types/{resource_type_id}/categories/{category_id}/subcategories                                                                    |
| POST         | v3/resource-types/{resource_type_id}/categories/{category_id}/subcategories                                                                    |
| GET/HEAD     | v3/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}                                                   |
| OPTIONS      | v3/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}                                                   |
| PATCH        | v3/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}                                                   |
| DELETE       | v3/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}                                                   |
| GET/HEAD     | v3/resource-types/{resource_type_id}/items                                                                                                     |
| OPTIONS      | v3/resource-types/{resource_type_id}/items                                                                                                     |
| GET/HEAD     | v3/resource-types/{resource_type_id}/partial-transfers                                                                                         |
| OPTIONS      | v3/resource-types/{resource_type_id}/partial-transfers                                                                                         |
| GET/HEAD     | v3/resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}                                                              |
| OPTIONS      | v3/resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}                                                              |
| DELETE       | v3/resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}                                                              |
| POST         | v3/resource-types/{resource_type_id}/permitted-users                                                                                           |
| GET/HEAD     | v3/resource-types/{resource_type_id}/permitted-users                                                                                           |
| OPTIONS      | v3/resource-types/{resource_type_id}/permitted-users                                                                                           |
| GET/HEAD     | v3/resource-types/{resource_type_id}/permitted-users/{permitted_user_id}                                                                       |
| OPTIONS      | v3/resource-types/{resource_type_id}/permitted-users/{permitted_user_id}                                                                       |
| DELETE       | v3/resource-types/{resource_type_id}/permitted-users/{permitted_user_id}                                                                       |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources                                                                                                 |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources                                                                                                 |
| POST         | v3/resource-types/{resource_type_id}/resources                                                                                                 |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}                                                                                   |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}                                                                                   |
| PATCH        | v3/resource-types/{resource_type_id}/resources/{resource_id}                                                                                   |
| DELETE       | v3/resource-types/{resource_type_id}/resources/{resource_id}                                                                                   |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items                                                                             |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items                                                                             |
| POST         | v3/resource-types/{resource_type_id}/resources/{resource_id}/items                                                                             |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}                                                                   |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}                                                                   |
| PATCH        | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}                                                                   |
| DELETE       | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}                                                                   |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories                                                        |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories                                                        |
| POST         | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories                                                        |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}                                     |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}                                     |
| DELETE       | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}                                     |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories                       |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories                       |
| POST         | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories                       |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id} |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id} |
| DELETE       | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id} |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data                                                              |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data                                                              |
| POST         | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data                                                              |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}                                                        |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}                                                        |
| PATCH        | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}                                                        |
| DELETE       | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}                                                        |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log                                                               |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log                                                               |
| POST         | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log                                                               |
| GET/HEAD     | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log/{item_data_id}                                                |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log/{item_data_id}                                                |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer                                                  |
| POST         | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer                                                  |
| OPTIONS      | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer                                                          |
| POST         | v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer                                                          |
| GET/HEAD     | v3/resource-types/{resource_type_id}/transfers                                                                                                 |
| OPTIONS      | v3/resource-types/{resource_type_id}/transfers                                                                                                 |
| GET/HEAD     | v3/resource-types/{resource_type_id}/transfers/{item_transfer_id}                                                                              |
| OPTIONS      | v3/resource-types/{resource_type_id}/transfers/{item_transfer_id}                                                                              |
| GET/HEAD     | v3/request/error-log                                                                                                                           |
| OPTIONS      | v3/request/error-log                                                                                                                           |
| POST         | v3/request/error-log                                                                                                                           |
| GET/HEAD     | v3/tools/cache                                                                                                                                 |
| OPTIONS      | v3/tools/cache                                                                                                                                 |
| DELETE       | v3/tools/cache                                                                                                                                 |

## Summary routes

Eventually, there will be a summary route for every API collection GET endpoint. Until 
that point, the summary routes that exists are detailed below. Some allow GET 
parameters to break down the data, one example being 
`v3/summary/resource-types/{resource_type_id}/items`. 

Review the OPTIONS request for each summary route to see the supported parameters, these should 
largely match the matching non-summary route.

| HTTP Verb(s) | Route                                                                               |
|:-------------|:------------------------------------------------------------------------------------|
| GET/HEAD     | v3/summary/resource-types                                                           |
| OPTIONS      | v3/summary/resource-types                                                           |
| GET/HEAD     | v3/summary/resource-types/{resource_type_id}/categories                             |
| OPTIONS      | v3/summary/resource-types/{resource_type_id}/categories                             |
| GET/HEAD     | v3/summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories |
| OPTIONS      | v3/summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories |
| GET/HEAD     | v3/summary/resource-types/{resource_type_id}/items                                  |
| OPTIONS      | v3/summary/resource-types/{resource_type_id}/items                                  |
| GET/HEAD     | v3/summary/resource-types/{resource_type_id}/resources                              |
| OPTIONS      | v3/summary/resource-types/{resource_type_id}/resources                              |
| GET/HEAD     | v3/summary/resource-types/{resource_type_id}/resources/{resource_id}/items          |
| OPTIONS      | v3/summary/resource-types/{resource_type_id}/resources/{resource_id}/items          |

## Tests

We are in the process of moving our feature tests from Postman, we are moving them locally and using PHPUnit.

You can see our progress in the table below. We are hoping to add tests in each new release. We are 
not too concerned about missing anything as we still have all our tests in Postman, we won't disable our test monitor until 
our local test suite is as complete as the Postman request tests.

| Controller                          | Progress |
|:------------------------------------|:---------|
| Authentication (Actions)            | 35 Tests |
| Authentication (Responses)          | 2 Tests  |
| CategoryManage (Actions)            | 11 Tests |
| ItemCategoryManage (Actions)        | Non yet* |
| ItemManage (Actions)                | Non yet* |
| ItemPartialTransferManage (Actions) | Non yet* |
| ItemSubcategoryManage (Actions)     | Non yet* |
| ItemTransferManage (Actions)        | Non yet* |
| ItemTypeView (Responses)            | 7 tests  |
| PermittedUserManage (Actions)       | 4 Tests  |
| PermittedUserView (Responses)       | 2 Tests  |
| RequestManage (Actions)             | Non yet* |
| ResourceManage (Actions)            | 14 Tests |
| ResourceTypeManage (Actions)        | 14 Tests |
| ResourceTypeView (Responses)        | 11 Tests |
| SubcategoryManage (Actions)         | 12 Tests |
| ToolManage (Actions)                | Non yet* |
| Summaries (Responses)               | Non yet* |
| **Total tests**                     | **112**  |

*Non yet does not mean there are no tests, it just means there are no PHPUnit tests. There are over 2000 tests in 
a private Postman collection, I'm slowing transferring them locally.
