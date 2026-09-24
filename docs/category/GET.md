[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Categories](../categories/GET.md)

# Category - GET

Return an individual category

## Request

**URL** : `/v3/resource-types/{resource_type_id}/categories/{category_id}`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
include-subcategories | boolean | false | Include all subcategories | include-subcategories=1

## Response

**Code** : `200 OK`

**Content** : 
```json
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
        "count": 8,
        "collection": [
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
    }
}
```
