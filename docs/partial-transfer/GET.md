[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Partial transfers](../partial-transfers/GET.md)

# Partial transfer - GET

Return an individual partial transfer.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

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
