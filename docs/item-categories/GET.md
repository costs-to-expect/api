[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Item (Allocated expense) - GET](../item-allocated-expense/GET.md)

# Item categories - GET

View the categories assigned to an item. This is distinct from the resource type categories, this endpoint lists which of those categories have been assigned to a specific item.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "3ZY2Kx91mN",
        "category": {
            "id": "98WLap7Bx3",
            "name": "Essential",
            "description": "The costs that we see as absolutely necessary in raising a child."
        },
        "created": "2019-04-07 11:26:10"
    }
]
```
