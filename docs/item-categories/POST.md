[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Item (Allocated expense) - GET](../item-allocated-expense/GET.md)

# Item categories - POST

Assign a resource type category to the item. Allocated expense items can have one category assigned, game items can have up to ten.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
category_id | string | Yes | The category to assign to the item, must be one of the resource type's categories

## Responses

### Success

**Code** : `201`

**Content** : 
```json
{
    "id": "3ZY2Kx91mN",
    "category": {
        "id": "98WLap7Bx3",
        "name": "Essential",
        "description": "The costs that we see as absolutely necessary in raising a child."
    },
    "created": "2019-04-07 11:26:10"
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "The requested `Resource` does not exist or is not accessible with your permissions."
}
```

### Assignment limit reached

**Code** : `400`

**Content** : 
```json
{
    "message": "Unable to handle your request, the number of allowable category assignments reached",
    "limit": 1
}
```

### Validation error

**Code** : `422`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "category_id": {
            "errors": [
                "The category id field is required or the provided value could not be decoded"
            ]
        }
    }
}
```
