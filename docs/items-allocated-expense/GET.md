[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)

# Items (Allocated expense) - GET

Return the items collection for the given resource.

The `allocated-expense` item type is used to track expenses, you can allocated a percentage of the cost of an expense, set future public dates and set categories and subcategories for grouping.

Expenses can be transferred among resources and additionally, a percentage of the expense can be allocated to other resources below the resource type.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
limit | integer | 25 | Limit the collection | limit=25
offset | integer | 0 | Limit offset | offset=0
search | string | | Search collection fields | search=field1:partial_search_term|field2:partial_search_term
sort | string | | Sort collection | sort=field1:asc|field2:desc
filter | string | | Filter the collection | filter=field:from:to|field2:from:to
include-categories | boolean | false | Optionally include categories assigned to each item | include-categories=true
include-subcategories | boolean | false | Optionally include subcategories assigned to each item | include-subcategories=true
include-unpublished | boolean | false | Optionally include unpublished expenses in the collection | include-unpublished=true
category | string | | Filter by category | category=2AP1axw6L7
subcategory | string | | Filter by subcategory | subcategory=2AP1axw6L7
year | integer | | Filter by year | year=2018
month | integer | | Filter by month | month=1

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "aX35eXV3oR",
        "name": "Ice cream",
        "description": null,
        "currency": {
            "id": "epMqeYqPkL",
            "code": "GBP",
            "name": "Sterling"
        },
        "total": "2.50",
        "percentage": 100,
        "actualised_total": "2.50",
        "effective_date": "2022-08-28",
        "categories": [],
        "created": "2022-08-28 11:26:07",
        "updated": null
    },
    {
        "id": "OkDRMYpz71",
        "name": "School trousers and PE tops",
        "description": null,
        "currency": {
            "id": "epMqeYqPkL",
            "code": "GBP",
            "name": "Sterling"
        },
        "total": "20.00",
        "percentage": 100,
        "actualised_total": "20.00",
        "effective_date": "2022-08-26",
        "categories": [],
        "created": "2022-08-26 19:44:47",
        "updated": null
    },
    ...
]
```
