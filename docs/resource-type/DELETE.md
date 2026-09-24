[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)

# Resource type - DELETE

Delete the requested resource type, you are not able to delete a resource type until every 
item below it has been removed.

## Request

**URL** : `/v3/resource-types/{resource_type_id}`

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
    "message": "The resource type can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Resource type can't be found or is not accessible to you."
}
```
