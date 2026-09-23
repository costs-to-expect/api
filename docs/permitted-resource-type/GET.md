[API Sections](../Sections.md)
[Auth (User)](../auth-user/GET.md)
[Permitted resource types](../permitted-resource-types/GET.md)

# Permitted resource type - GET

Return an individual resource type you have access to.

## Request

**URL** : `/v3/auth/user/permitted-resource-types/{permitted_resource_type_id}`

**Method** : `GET`

**Parameters** : None

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
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
    "message": "The requested `Resource Type` does not exist."
}
```
