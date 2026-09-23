[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)

# Resource - DELETE

Delete the requested resource, you are not able to delete a resource until every item below it has been removed.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}`

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
    "message": "The resource can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Resource can't be found or is not accessible to you."
}
```
