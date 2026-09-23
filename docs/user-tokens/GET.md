[API Sections](../Sections.md)
[Auth (User)](../auth-user/GET.md)

# User tokens - GET

View your active Bearer tokens. The `token` field is a hash of the actual token, not the token itself, the plain text value is only ever returned once, when it's created.

## Request

**URL** : `/v3/auth/user/tokens`

**Method** : `GET`

**Parameters** : None

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": 3,
        "name": "costs-to-expect-api",
        "token": "9f1c2c3b7e6d4a5f8091b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
        "created": "2026-09-01 10:00:00",
        "last_used_at": null
    }
]
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Authentication required, please try again with a Bearer."
}
```
