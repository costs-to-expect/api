[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Categories - POST

Create a new category, the table below details the fields and their data type

## Request

**URL** : `/v3/resource-types/{resource_type_id}/categories`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | Yes | The name of the category
description | string | Yes | A description of the category

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "98WLap7Bx3",
    "name": "Essential",
    "description": "The costs that we see as absolutely necessary in raising a child and in essence, keeping them healthy and well, alive.",
    "created": "2018-09-06 22:04:39",
    "resource_type": {
        "uri": "/v3/resource-types/d185Q15grY",
        "id": "d185Q15grY",
        "name": "The Blackborough boys"
    },
    "subcategories": {
        "uri": "/v3/resource-types/d185Q15grY/categories/98WLap7Bx3/subcategories",
        "count": 8
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