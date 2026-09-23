[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Item (Allocated expense) - GET](../item-allocated-expense/GET.md)
[Item categories](../item-categories/GET.md)
[Item category](../item-category/GET.md)

# Item subcategories - GET

View the subcategories assigned to an item category. This endpoint is only supported for the allocated expense item type.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "OwAnNrnBvj",
        "subcategory": {
            "id": "bqNzrjz1W5",
            "name": "Education",
            "description": "The essentials that are required by a compulsory education."
        },
        "created": "2019-04-07 11:26:10"
    }
]
```
