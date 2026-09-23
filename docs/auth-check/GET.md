[API Sections](../Sections.md)

# Auth (Check) - GET

Check whether the current request is authenticated. Useful for clients that want to check a stored Bearer token is still valid without triggering an authentication error.

## Request

**URL** : `/v3/auth/check`

**Method** : `GET`

**Parameters** : None

## Response

**Code** : `200 OK`

**Content** : 
```json
{
    "auth": true
}
```
