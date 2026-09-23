[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)

# Resource - GET

Return an individual resource.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "Eq9g6BgJL0",
    "name": "Niall",
    "description": "Niall James Blackborough, born on the 22nd April 2019 at 17:46, these are all the expenses we have recorded for him.",
    "data": {
        "birth": "2019-04-22"
    },
    "created": "2019-04-07 11:26:10",
    "item_subtype": {
        "uri": "/v3/item-types/OqZwKX16bW/item-subtypes/a56kbWV82n",
        "id": "a56kbWV82n",
        "name": "default",
        "description": "Default behaviour for the allocated-exense type"
    }
}
```
