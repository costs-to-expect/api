[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Categories](../categories/GET.md)

# Category - PATCH

Update the category, the table below details the fields and their data type

## Request

**URL** : `/v3/resource-types/{resource_type_id}`

**Method** : `PATCH`

**Fields** :

Name | Type | Description
---|---|---
name | string | The name of the category
description | string | A description of the category

## Responses

### Success

**Code** : `204`

**No Content**

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "Category can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Category can't be found or is not accessible to you."
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
