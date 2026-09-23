[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Items](../items-game/GET.md)
[Item](../item-game/GET.md)
[Keyed Data](../item-keyed-data-collection/GET.md)

# Keyed Data - PATCH

Update the value at the requested key.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}`

**Method** : `PATCH`

**Fields** :

Name | Type | Description
---|---|---
value | json | The value for the keyed data

## Responses

### Success

**Code** : `204`

**No Content**

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "Data can't be found."
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
                "The game value must be valid json"
            ]
        }
    }
}
```
