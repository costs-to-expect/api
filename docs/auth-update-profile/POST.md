[API Sections](../Sections.md)

# Auth (Update profile) - POST

Update your name and/or email address. Provide at least one field.

## Request

**URL** : `/v3/auth/update-profile`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | No | Your name
email | string | No | Your email address

## Responses

### Success

**Code** : `204`

**No Content**

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Authentication required, please try again with a Bearer."
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
        "email": {
            "errors": [
                "The email must be a valid email address."
            ]
        }
    }
}
```
