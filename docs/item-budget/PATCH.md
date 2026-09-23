[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Budget) - GET](../items-budget/GET.md)

# Item (Budget) - PATCH

Update the budget item definition, the table below details the fields and their data type

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}`

**Method** : `PATCH`

**Fields** :

Name | Type | Description
---|---|---
name | string | The name of the budget item
account | string(UUID) | The UUID of the account the expense should be deducted from
target_account | string(UUID) | The UUID of the account the savings should be added to
description | string | A optional description of the budget item
amount | decimal | The amount for the budget item
category | string | The category for the budget item, allowable values income, fixed, flexible or savings
start_date | date | The date from which the budget item is relevant
end_date | date | The date from which the budget item is no longer relevant
disabled | boolean | Whether the budget item is disabled
frequency | json | The frequency definition for the budget item, how often it repeats

## Responses

### Success

**Code** : `204`

**No Content**

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "Budget item can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Budget item can't be found or is not accessible to you."
}
```

### No body

**Code** : `400`

**Content** : 
```json
{
    "message": "Unable to handle your request, please include a request body."
}
```

### Validation error

**Code** : `422`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "name": {
            "errors": [
                "The frequency value must be valid json"
            ]
        }
    }
}
```
