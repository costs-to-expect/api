[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Categories](../categories/GET.md)
[Category](../category/GET.md)
[Subcategories](../subcategories/GET.md)

# Subcategory - DELETE

Delete the requested subcategory, you are not able to delete a subcategory until it is no longer associated with any items.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}`

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
    "message": "The subcategory can't be found."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "Subcategory can't be found or is not accessible to you."
}
```
