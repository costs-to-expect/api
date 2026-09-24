[API Sections](../Sections.md)
[Resource types](../resource-types/GET.md)
[Resource type](../resource-type/GET.md)
[Partial transfers](../partial-transfers/GET.md)

# Partial transfer - DELETE

Delete a partial transfer record. This only removes the allocation record, it has no effect on the item itself.

## Request

**URL** : `/v3/resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}`

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
    "message": "The requested `Item Partial Transfer` does not exist."
}
```

### Not authourised

**Code** : `403`

**Content** : 
```json
{
    "message": "The requested `Item Partial Transfer` does not exist or is not accessible with your permissions."
}
```
