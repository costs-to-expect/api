[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items (Game) - GET](../items-game/GET.md)

# Item (Game) - DELETE

Delete the requested item, when you delete the item, key value data and log data will be removed as well.

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
