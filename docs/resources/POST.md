[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Resources - POST

Create a new resource, the table below details the fields and their data type

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | Yes | The name of the resource
description | string | Yes | A description of the resource
data | json | No | An optional json object
item_subtype_id | string | Yes | The item subtype to use, must be one of the allowed values

## Responses

### Success

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
},
```

### Validation error

**Code** : `422 OK`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "name": {
            "errors": [
                "The name must be a string."
            ]
        },
        "description": {
            "errors": [
                "The description field is required."
            ]
        }
    }
}
```