[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Allocated expense) - GET](../items-allocated-expense/GET.md)

# Item (Allocated expense) - PATCH

Update the allocated expense, the table below details the fields and their data type

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}`

**Method** : `PATCH`

**Fields** :

Name | Type | Description
---|---|---
name | string | The name of the expense
description | string | A optional description of the expense
effective_date | date / yyyy-mm-dd | The effective date for the expense
publish_after | date / yyyy-mm-dd | Optionally, set a publish after date
currency_id | string | The currency id, must be one of the allowed values
total | decimal | The total amount of the expense
percentage | integer | An optional percentage of total to allocate, defaults to 100

## Responses

### Success

**Code** : `204`

**No Content**

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "Expense can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Expense can't be found or is not accessible to you."
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
                "The name must be a string."
            ]
        },
        "description": {
            "errors": [
                "The description field is required."
            ]
        }
    }
}
```
