[API Sections](../Sections.md)

# Auth (Register) - POST

Create an account. This only creates the user record, the account has no password yet, use the returned `create-password` link to set one. Registration can be disabled entirely on the server, in which case this route doesn't exist. This route can only be called by a trusted internal service and requires the `X-Internal-Api-Key` header.

## Request

**URL** : `/v3/auth/register`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
name | string | Yes | Your name
email | string | Yes | Your email address, must be unique
registered_via | string | No | Where the registration came from, defaults to `api`

## Responses

### Success

**Code** : `201`

**Content** : 
```json
{
    "message": "Account created, please check you email for information on how to create your password",
    "uris": {
        "create-password": {
            "uri": "/v3/auth/create-password?token=8fK2pR9xN0aQeYtLmZ5v&email=jane@example.com",
            "method": "POST",
            "parameters": {
                "token": "8fK2pR9xN0aQeYtLmZ5v",
                "email": "jane@example.com"
            }
        }
    }
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "This route can only be called by a trusted internal service."
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
                "The email has already been taken."
            ]
        }
    }
}
```
