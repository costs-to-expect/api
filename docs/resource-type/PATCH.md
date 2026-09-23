[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)

# Resource type - PATCH

Update the resource type, the table below details the fields and their data type

## Request

**URL** : `/v3/resource-types/{resource_type_id}`

**Method** : `PATCH`

**Fields** :

Name | Type | Description
---|---|---
name | string | The name of the resource type
description | string | A description of the resource type
data | json | An optional json object
public | boolean | Should the resource type be publicly visible, defaults to false

## Responses

### Success

**Code** : `204`

**No Content**

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "Resource type can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Resource type can't be found or is not accessible to you."
}
```

### No body

**Code** : `400`

**Content** : 
```json
{
    "message": "Unable to handle your request, please include a request body."
}
```

### Validation error

**Code** : `422`

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
