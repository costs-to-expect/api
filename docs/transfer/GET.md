[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Transfers](../transfers/GET.md)

# Transfer - GET

Return an individual transfer.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/transfers/{item_transfer_id}`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
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
```
