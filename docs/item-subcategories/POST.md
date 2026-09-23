[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Item (Allocated expense) - GET](../item-allocated-expense/GET.md)
[Item categories](../item-categories/GET.md)
[Item category](../item-category/GET.md)

# Item subcategories - POST

Assign a subcategory to an item category. An item category can have one subcategory assigned. This endpoint is only supported for the allocated expense item type, game items cannot have subcategories assigned.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
subcategory_id | string | Yes | The subcategory to assign, must belong to the item category's category

## Responses

### Success

**Code** : `201`

**Content** : 
```json
{
    "id": "OwAnNrnBvj",
    "subcategory": {
        "id": "bqNzrjz1W5",
        "name": "Education",
        "description": "The essentials that are required by a compulsory education."
    },
    "created": "2019-04-07 11:26:10"
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "The requested `Assigned Category` does not exist or is not accessible with your permissions."
}
```

### Assignment limit reached

**Code** : `400`

**Content** : 
```json
{
    "message": "Unable to handle your request, the number of allowable subcategory assignments reached",
    "limit": 1
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
        "subcategory_id": {
            "errors": [
                "The subcategory id field is required or the provided value could not be decoded"
            ]
        }
    }
}
```
