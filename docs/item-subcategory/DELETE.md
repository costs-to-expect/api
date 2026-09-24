[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Resources](../resources/GET.md)
[Resource](../resource/GET.md)
[Item (Allocated expense) - GET](../item-allocated-expense/GET.md)
[Item categories](../item-categories/GET.md)
[Item category](../item-category/GET.md)
[Item subcategories](../item-subcategories/GET.md)

# Item subcategory - DELETE

Remove a subcategory from an item category. This only removes the assignment, it has no effect on the subcategory itself.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}`

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
    "message": "The requested `Assigned Subcategory` does not exist."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "The requested `Assigned Subcategory` does not exist or is not accessible with your permissions."
}
```
