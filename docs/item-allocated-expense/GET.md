[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Allocated expense) - GET](../items-allocated-expense/GET.md)

# Item (Allocated expense) - GET

Return an individual allocated expense.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}`

**Method** : `GET`

**Parameters** : 

Parameter | Type | Default | Description | Example
---|---|---|---|---
include-categories | boolean | false | Optionally include assigned categories | include-categories=true
include-subcategories | boolean | false | Optionally include assign subcategories | include-subcategories=true

## Response

**Code** : `200 OK`

**Content** : 
```json
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
}
```
