[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)

# Resource type - GET

Return an individual resource type

## Request

**URL** : `/v3/resource-types/{resource_type_id}`

**Method** : `GET`

**Parameters** :

Parameter | Type | Default | Description | Example
---|---|---|---|---
include-resources | boolean | false | Include all resources in the response | include-resources=1
include-permitted-users | boolean | false | Include the permitted users in the response | include-permitted-users=1

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "d185Q15grY",
    "name": "Blackborough boys",
    "description": "The Blackborough children, Jack and Niall",
    "data": null,
    "created": "2018-09-06 22:04:23",
    "public": true,
    "item_type": {
        "uri": "/v3/item-types/OqZwKX16bW",
        "id": "OqZwKX16bW",
        "name": "allocated-expense",
        "description": "Expenses with an allocation rate"
    },
    "resources": {
        "uri": "/v3/resource-types/Z9YJMxawpG/resources",
        "count": 2
    }
}
```
