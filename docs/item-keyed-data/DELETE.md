[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items](../items-game/GET.md)
[Item](../item-game/GET.md)
[Keyed Data](../item-keyed-data-collection/GET.md)

# Keyed Data - DELETE

Delete the requested item, when you delete the item, key value data and log data will be removed as well.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}`

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
    "message": "The data can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Data can't be found or is not accessible to you."
}
```
