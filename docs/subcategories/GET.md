[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Categories](../categories/GET.md)
[Category](../category/GET.md)

# Subcategories - GET

View the available subcategories.

Subcategories allow additional categorisation below the category level.

## Request

**URL** : `/v3/resource_types/{resource_type_id}/categories/{category__id}/subcategories`

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
        "id": "OwAnNrnBvj",
        "name": "Clothes & Food etc.",
        "description": "Those things that nobody can do without.",
        "created": "2018-09-06 22:04:47"
    },
    {
        "id": "bqNzrjz1W5",
        "name": "Education",
        "description": "The essentials that are required by a compulsory education.",
        "created": "2018-09-06 22:04:48"
    },
    {
        "id": "meqz5Ln4V0",
        "name": "Furniture",
        "description": "The things that make a house a home but that we can’t do without.",
        "created": "2018-09-06 22:04:48"
    },
    {
        "id": "bqNzr0jR1W",
        "name": "Grooming and Toiletries",
        "description": "The essential cost of keeping clean, neat and tidy.",
        "created": "2019-08-31 21:28:43"
    },
    {
        "id": "LDkz8E62EP",
        "name": "Health, Safety & Wellbeing",
        "description": "The things required both by law and of every protective parent.",
        "created": "2018-09-06 22:04:48"
    },
    {
        "id": "WQdzP2RjNM",
        "name": "Legal",
        "description": "All those dastardly necessary legal costs.",
        "created": "2019-05-25 11:38:30"
    },
    {
        "id": "WQdzPlkzjN",
        "name": "Technology",
        "description": "All things geeky including batteries.",
        "created": "2020-04-20 14:08:06"
    },
    {
        "id": "VXjRBDgz02",
        "name": "Travel",
        "description": "Travel",
        "created": "2019-11-04 10:39:54"
    }
]
```
