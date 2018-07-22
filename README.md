# Costs to expect

## The API

This Laravel app is the RESTful API for costs-to-expect, the API will be consumed by the 
costs to expect website and iOS app which I'm creating to assist the wife with data input.

Current API Routes, for fields and parameters, check the OPTIONS request. 

| HTTP Verb | Route |
| :--- | :--- |
| GET/HEAD | / 
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
