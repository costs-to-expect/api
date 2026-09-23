[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Categories - GET

View the available categories.

Categories usage depends on the item type but typically they are used to group items together. For the allocated expenses item type, an item can have zero or more categories, for the game item type, categories can be thought of as a synonym for players.

Categories are defined for a resource type and are this shareable among all the items of that resource type.

## Request

**URL** : `/v3/resource_types/{resource_type_id}/categories/`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
collection | boolean | false | Override pagination and return the entire collection | collection=true
limit | integer | 25 | Limit the collection | limit=25
offset | integer | 0 | Limit offset | offset=0
search | string | | Search collection fields | search=field1:partial_search_term|field2:partial_search_term
sort | string | | Sort collection | sort=field1:asc|field2:desc

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "98WLap7Bx3",
        "name": "Essential",
        "description": "The costs that we see as absolutely necessary in raising a child and in essence, keeping them healthy and well, alive.",
        "created": "2018-09-06 22:04:39",
        "resource_type": {
            "uri": "/v3/resource-types/d185Q15grY",
            "id": "d185Q15grY",
            "name": "The Blackborough boys"
        },
        "subcategories": {
            "uri": "/v3/resource-types/d185Q15grY/categories/98WLap7Bx3/subcategories",
            "count": 8
        }
    },
    {
        "id": "Gwg7zgL316",
        "name": "Hobbies and Interests",
        "description": "All the fun extras.",
        "created": "2018-09-06 22:04:39",
        "resource_type": {
            "uri": "/v3/resource-types/d185Q15grY",
            "id": "d185Q15grY",
            "name": "The Blackborough boys"
        },
        "subcategories": {
            "uri": "/v3/resource-types/d185Q15grY/categories/Gwg7zgL316/subcategories",
            "count": 10
        }
    },
    {
        "id": "RjXM5VJDw6",
        "name": "Non-Essential",
        "description": "The optional costs of raising a child and those which spark lively debate between Mummy and Daddy.",
        "created": "2018-09-06 22:04:39",
        "resource_type": {
            "uri": "/v3/resource-types/d185Q15grY",
            "id": "d185Q15grY",
            "name": "The Blackborough boys"
        },
        "subcategories": {
            "uri": "/v3/resource-types/d185Q15grY/categories/RjXM5VJDw6/subcategories",
            "count": 22
        }
    }
]
```
