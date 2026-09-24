[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Budget) - GET](../items-budget/GET.md)

# Item (Budget) - DELETE

Delete the requested item.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}`

**Method** : `DELETE`

## Responses

### Success

**Code** : `204`

**No Content**

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "The budget item can't be found."
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
