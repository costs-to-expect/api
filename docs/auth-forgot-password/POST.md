[API Sections](../Sections.md)

# Auth (Forgot password) - POST

Request a password reset email. This route can only be called by a trusted internal service and requires the `X-Internal-Api-Key` header.

## Request

**URL** : `/v3/auth/forgot-password`

**Method** : `POST`

**Fields** :

Name | Type | Required | Description
---|---|---|---
email | string | Yes | Your account email address

## Responses

### Success

**Code** : `201`

**Content** : 
```json
{
    "message": "Request received, please check your email for instructions on how to create your new password",
    "uris": {
        "create-new-password": {
            "uri": "/v3/auth/create-new-password?encrypted_token=eyJpdiI6...&email=jane@example.com",
            "method": "POST",
            "parameters": {
                "encrypted_token": "eyJpdiI6...",
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

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "Unable to find your account, please try again later"
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
                "The email field is required."
            ]
        }
    }
}
```
