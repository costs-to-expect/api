[API Sections](../Sections.md)
[Auth (User)](../auth-user/GET.md)
[User tokens](../user-tokens/GET.md)

# User token - GET

Return an individual token, note the `token_id` here is a plain integer, not a hashed id like elsewhere in the API.

## Request

**URL** : `/v3/auth/user/tokens/{token_id}`

**Method** : `GET`

**Parameters** : None

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
{
    "id": 3,
    "name": "costs-to-expect-api",
    "token": "9f1c2c3b7e6d4a5f8091b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
    "created": "2026-09-01 10:00:00",
    "last_used_at": null
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

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "The requested resource does not exist."
}
```
