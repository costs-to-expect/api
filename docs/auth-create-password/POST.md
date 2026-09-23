[API Sections](../Sections.md)

# Auth (Create password) - POST

Set a password for a newly registered account, using the token emailed after registration.

## Request

**URL** : `/v3/auth/create-password?email={email}&token={token}`

**Method** : `POST`

**Parameters** :

Parameter | Type | Required | Description
---|---|---|---
email | string | Yes | The account email address
token | string | Yes | The token emailed after registration

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

**Code** : `401`

**Content** : 
```json
{
    "message": "Sorry, the email and or token you supplied are invalid"
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
        "password": {
            "errors": [
                "The password field is required."
            ]
        }
    }
}
```
