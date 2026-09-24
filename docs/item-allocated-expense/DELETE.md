[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Allocated expense) - GET](../items-allocated-expense/GET.md)

# Item (Allocated expense) - DELETE

Delete the requested item, you are not able to delete an item which has categories assigned to it, however, key value data and log data will be removed as that is specific to the item itself.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}`

**Method** : `DELETE`

## Responses

### Success

**Code** : `204`

**No Content**

### Constraint error

**Code** : `409`

**Content** : 
```json
{
    "message": "Unable to handle your request, dependent data exists or foreign key error."
}
```

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "The expense can't be found."
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
