[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Transfers - GET

View the transfers recorded for a resource type. A transfer moves an item permanently from one resource to another. This endpoint is only supported for the allocated expense item type.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/transfers`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
collection | boolean | false | Override pagination and return the entire collection | collection=true
limit | integer | 10 | Limit the collection | limit=10
offset | integer | 0 | Limit offset | offset=0
item | string | | Show results for the requested item only | item=Eq9g6BgJL0

## Response

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "3ZY2Kx91mN",
        "from": {
            "id": "Eq9g6BgJL0",
            "name": "Household"
        },
        "to": {
            "id": "d185Q15grY",
            "name": "Personal"
        },
        "item": {
            "id": "a56kbWV82n"
        },
        "transferred": {
            "at": "2019-04-07 11:26:10",
            "user": {
                "id": "OqZwKX16bW",
                "name": "Dean Blackborough"
            }
        }
    }
]
```
