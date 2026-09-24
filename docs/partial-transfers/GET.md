[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Partial transfers - GET

View the partial transfers recorded for a resource type. Unlike a full transfer, a partial transfer doesn't move the item, it records that a percentage of the item's value is attributed to another resource. This endpoint is only supported for the allocated expense item type.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/partial-transfers`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
collection | boolean | false | Override pagination and return the entire collection | collection=true
limit | integer | 10 | Limit the collection | limit=10
offset | integer | 0 | Limit offset | offset=0
item | string | | Show results for the requested item only | item=a56kbWV82n

## Response

**Code** : `200 OK`

**Content** : 
```json
[
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
]
```
