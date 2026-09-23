[API Sections](../Sections.md)
[Auth (User)](../auth-user/GET.md)
[User tokens](../user-tokens/GET.md)

# User token - DELETE

Revoke a token, only your own tokens can be deleted. If you delete the token used to make this request you will be signed out.

## Request

**URL** : `/v3/auth/user/tokens/{token_id}`

**Method** : `DELETE`

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

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "The requested resource does not exist."
}
```
