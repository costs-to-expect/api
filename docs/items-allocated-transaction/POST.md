[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)

# Items (Allocated transaction) - POST

Create a new allocated transaction, the table below details the fields and their data type.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | Yes | The name of the transaction
description | string | No | A optional description of the transaction
effective_date | date / yyyy-mm-dd | Yes | The effective date for the transaction
publish_after | date / yyyy-mm-dd | No | Optionally, set a publish after date
currency_id | string | Yes | The currency id, must be one of the allowed values
total | decimal | Yes | The total amount of the transaction
percentage | integer | No | An optional percentage of total to allocate, defaults to 100
transaction_type | string | Yes | Whether the transaction is an `expense` or `income`

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "aX35eXV3oR",
    "name": "Salary",
    "description": null,
    "currency": {
        "id": "epMqeYqPkL",
        "code": "GBP",
        "name": "Sterling"
    },
    "total": "2500.00",
    "percentage": 100,
    "actualised_total": "2500.00",
    "transaction_type": "income",
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
        "transaction_type": {
            "errors": [
                "The transaction type field is required."
            ]
        }
    }
}
```
