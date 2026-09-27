[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Allocated transaction) - GET](../items-allocated-transaction/GET.md)

# Item (Allocated transaction) - PATCH

Update the allocated transaction, the table below details the fields and their data type

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}`

**Method** : `PATCH`

**Fields** :

Name | Type | Description
---|---|---
name | string | The name of the transaction
description | string | A optional description of the transaction
effective_date | date / yyyy-mm-dd | The effective date for the transaction
publish_after | date / yyyy-mm-dd | Optionally, set a publish after date
currency_id | string | The currency id, must be one of the allowed values
total | decimal | The total amount of the transaction
percentage | integer | An optional percentage of total to allocate, defaults to 100
transaction_type | string | Whether the transaction is an `expense` or `income`

## Responses

### Success

**Code** : `204`

**No Content**

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "Transaction can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Transaction can't be found or is not accessible to you."
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
        "transaction_type": {
            "errors": [
                "The selected transaction type is invalid."
            ]
        }
    }
}
```
