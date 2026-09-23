[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Item (Allocated expense) - GET](../item-allocated-expense/GET.md)

# Item (Allocated expense) - Partial transfer

Record that a percentage of the item's value is attributed to another resource within the same resource type. Unlike a transfer, the item itself is not moved. This action is only supported for the allocated expense item type.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
resource_id | string | Yes | The resource to attribute a percentage of the item's value to, must belong to the same resource type and be different to the item's current resource
percentage | integer | Yes | The percentage of the item's value to attribute to the given resource, must be between 1 and 99

## Responses

### Success

**Code** : `201`

**Content** : 
```json
{
    "id": "3ZY2Kx91mN",
    "from": {
        "uri": "/v3/resource-types/d185Q15grY/resources/Eq9g6BgJL0",
        "id": "Eq9g6BgJL0",
        "name": "Household"
    },
    "to": {
        "uri": "/v3/resource-types/d185Q15grY/resources/OqZwKX16bW",
        "id": "OqZwKX16bW",
        "name": "Personal"
    },
    "item": {
        "uri": "/v3/resource-types/d185Q15grY/resources/Eq9g6BgJL0/items/a56kbWV82n",
        "id": "a56kbWV82n",
        "name": "New TV",
        "description": "55 inch OLED"
    },
    "percentage": 30,
    "transferred": {
        "at": "2019-04-07 11:26:10",
        "user": {
            "id": "98WLap7Bx3",
            "name": "Dean Blackborough"
        }
    }
}
```

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
                "The provided resource does not belong to the current resource type or you are trying to partially transfer the item to the same resource."
            ]
        },
        "percentage": {
            "errors": [
                "The percentage must be between 1 and 99."
            ]
        }
    }
}
```
