[API Sections](../Sections.md)

# Resource types - POST

Create a new resource type, the table below details the fields
and their data type

## Request

**URL** : `/v3/resource-types/`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | Yes | The name of the resource type
description | string | Yes | A description of the resource type
data | json | No | An optional json object
item_type_id | string | Yes | The item type to use, must be one of the allowed values
public | boolean | No | Should the resource type be publicly visible, defaults to false

## Responses

### Success

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