[API Sections](../Sections.md)

# Auth (Logout) - GET

Sign out, this deletes the Bearer token used for the request.

## Request

**URL** : `/v3/auth/logout`

**Method** : `GET`

**Parameters** : None

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
{
    "message": "Account signed out"
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Authentication required, please try again with a Bearer."
}
```
