[API Sections](../Sections.md)

# Auth (Create new password) - POST

Set a new password using the token emailed by the forgot password request.

## Request

**URL** : `/v3/auth/create-new-password?email={email}&encrypted_token={encrypted_token}`

**Method** : `POST`

**Parameters** :

Parameter | Type | Required | Description
---|---|---|---
email | string | Yes | The account email address
encrypted_token | string | Yes | The encrypted token emailed by the forgot password request

**Fields** :

Name | Type | Required | Description
---|---|---|---
password | string | Yes | Your new password, minimum 12 characters
password_confirmation | string | Yes | Must match password

## Responses

### Success

**Code** : `204`

**No Content**

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "Sorry, the email and or token you supplied are invalid"
}
```

### Validation error

**Code** : `422`

**Content** : 
```json
{
    "message": "Validation error.",
    "fields": {
        "password": {
            "errors": [
                "The password field is required."
            ]
        }
    }
}
```
