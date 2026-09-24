[API Sections](../Sections.md)
[Auth (User)](../auth-user/GET.md)

# Permitted resource types - GET

View the resource types you have access to, either because you own them or because another user has granted you access.

## Request

**URL** : `/v3/auth/user/permitted-resource-types`

**Method** : `GET`

**Parameters** : None

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
[
    {
        "id": "Eq9g6BgJL0",
        "name": "Personal",
        "description": "Personal resource type",
        "data": null,
        "created": "2026-09-01 10:00:00",
        "public": false,
        "item_type": {
            "uri": "/v3/item-types/OqZwKX16bW",
            "id": "OqZwKX16bW",
            "name": "allocated-expense",
            "friendly_name": "Allocated Expense",
            "description": "Track and allocate expenses"
        }
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
