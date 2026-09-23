[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Categories](../categories/GET.md)
[Category](../category/GET.md)

# Subcategories - POST

Create a new subcategory, the table below details the fields and their data type

## Request

**URL** : `/v3/resource-types/{resource_type_id}/categories/{category_ud}/subcategories`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | Yes | The name of the subcategory
description | string | Yes | A description of the subcategory

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "OwAnNrnBvj",
    "name": "Clothes & Food etc.",
    "description": "Those things that nobody can do without.",
    "created": "2018-09-06 22:04:47"
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