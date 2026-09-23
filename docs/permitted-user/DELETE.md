[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Permitted users](../permitted-users/GET.md)

# Permitted user - DELETE

Revoke a user's access to the resource type. You must already have write access to the resource type to remove a permitted user.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/permitted-users/{permitted_user_id}`

**Method** : `DELETE`

## Responses

### Success

**Code** : `204`

**No Content**

### Constraint error

**Code** : `409`

**Content** : 
```json
{
    "message": "Unable to handle your request, dependent data exists or foreign key error."
}
```

### Not found

**Code** : `404`

**Content** : 
```json
{
    "message": "The requested `Permitted User` does not exist."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "The requested `Permitted User` does not exist or is not accessible with your permissions."
}
```
