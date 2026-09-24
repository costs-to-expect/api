[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)

# Items (Allocated expense) - POST

Create a new allocated expense, the table below details the fields and their data type.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | Yes | The name of the expense
description | string | No | A optional description of the expense
effective_date | date / yyyy-mm-dd | Yes | The effective date for the expense
publish_after | date / yyyy-mm-dd | No | Optionally, set a publish after date
currency_id | string | Yes | The currency id, must be one of the allowed values
total | decimal | Yes | The total amount of the expense
percentage | integer | No | An optional percentage of total to allocate, defaults to 100

## Responses

### Success

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

### Validation error

**Code** : `422 OK`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "name": {
            "errors": [
                "The name must be a string."
            ]
        },
        "total": {
            "errors": [
                "The total must be of the format 0.00."
            ]
        }
    }
}
```