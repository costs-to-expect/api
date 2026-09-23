[API Sections](../Sections.md)

# Auth (Update password) - POST

Update the password for your account.

## Request

**URL** : `/v3/auth/update-password`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
password | string | Yes | Your new password, minimum 12 characters
password_confirmation | string | Yes | Must match password

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

### Validation error

**Code** : `422`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "password_confirmation": {
            "errors": [
                "The password confirmation does not match."
            ]
        }
    }
}
```
