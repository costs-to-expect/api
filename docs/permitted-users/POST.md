[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)

# Permitted users - POST

Grant an existing user access to a resource type. You must already have write access to the resource type to add another permitted user, and the user being added must already have an account and not already have access.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/permitted-users`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
email | string | Yes | The email address of the user to grant access to, they must already have an account and not already be a permitted user for this resource type

## Responses

### Success

**Code** : `204`

**No Content**

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "The requested `Resource Type` does not exist or is not accessible with your permissions."
}
```

### Validation error

**Code** : `422`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "email": {
            "errors": [
                "The given user cannot be assigned to the resource type, they either don't exist to us or are already assigned to the resource type"
            ]
        }
    }
}
```
