[API Sections](../Sections.md)

# Auth (Login) - POST

Exchange an email and password for a Bearer token. The token is only ever returned once, store it securely, there is no way to retrieve the plain text token again.

## Request

**URL** : `/v3/auth/login`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
email | string | Yes | Your account email address
password | string | Yes | Your account password
device_name | string | No | An optional name for the device, used to help identify the token later

## Responses

### Success

**Code** : `201`

**Content** : 
```json
{
    "id": "Eq9g6BgJL0",
    "type": "Bearer",
    "token": "3|G7k2pR9xN0aQeYtLmZ5vC1sJhU8bWdKfXo6"
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
                "These credentials do not match our records."
            ]
        }
    }
}
```
