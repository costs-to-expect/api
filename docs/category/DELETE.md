[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Categories](../categories/GET.md)

# Category - DELETE

Delete the requested category, you are not able to delete a category until every subcategory below it has been removed and it is no longer associated with any items.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/categories/{category_id}`

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
    "message": "The category can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Category can't be found or is not accessible to you."
}
```
