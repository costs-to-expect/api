[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)

# Items (Budget) - POST

Create a new Budget item definition.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | Yes | The name of the budget item
account | string(UUID) | Yes | The UUID of the account the expense should be deducted from
target_account | string(UUID) | No | The UUID of the account the savings should be added to
description | string | No | A optional description of the budget item
amount | decimal | Yes | The amount for the budget item
currency_id | string | Yes | The currency id, must be one of the allowed values
category | string | Yes | The category for the budget item, allowable values income, fixed, flexible or savings
start_date | date | Yes | The date from which the budget item is relevant
end_date | date | No | The date from which the budget item is no longer relevant
disabled | boolean | No | Whether the budget item is disabled
frequency | json | Yes | The frequency definition for the budget item, how often it repeats

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "OkzK5yp36b",
    "name": "Council Tax",
    "account": "ebb5c735-0308-4e3c-9aea-8a270aebfe15",
    "target_account": null,
    "description": "Council tax for the year",
    "amount": "163.00",
    "currency": {
        "id": "epMqeYqPkL",
        "name": "Sterling",
        "code": "GBP",
        "uri": "/v3/currencies/epMqeYqPkL"
    },
    "category": "fixed",
    "start_date": "2022-04-01",
    "end_date": "2023-03-21",
    "disabled": false,
    "frequency": {
        "day": 10,
        "type": "monthly",
        "exclusions": [
            2,
            3
        ]
    },
    "created": "2022-09-22 21:17:14",
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
        "frequency": {
            "errors": [
                "The frequency should be a valid JSON string."
            ]
        }
    }
}
```