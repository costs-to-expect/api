[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Item (Allocated expense) - GET](../item-allocated-expense/GET.md)

# Item (Allocated expense) - Transfer

Move the item to a different resource within the same resource type. The item is moved permanently, a record of the transfer is kept and can be viewed via the transfers endpoints. This action is only supported for the allocated expense item type.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
resource_id | string | Yes | The resource to transfer the item to, must belong to the same resource type and be different to the item's current resource

## Responses

### Success

**Code** : `204`

**No Content**

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "The requested `Item` does not exist or is not accessible with your permissions."
}
```

### Not supported

**Code** : `405`

**Content** : 
```json
{
    "message": "The requested route is not supported for the item type"
}
```

### Validation error

**Code** : `422`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "resource_id": {
            "errors": [
                "The provided resource does not belong to the current resource type or you are trying to transfer the item to the same resource."
            ]
        }
    }
}
```
