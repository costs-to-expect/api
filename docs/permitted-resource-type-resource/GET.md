[API Sections](../Sections.md)
[Auth (User)](../auth-user/GET.md)
[Permitted resource types](../permitted-resource-types/GET.md)
[Permitted resource type](../permitted-resource-type/GET.md)
[Permitted resource type resources](../permitted-resource-type-resources/GET.md)

# Permitted resource type resource - GET

Return an individual resource for a resource type you have access to.

## Request

**URL** : `/v3/auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}`

**Method** : `GET`

**Parameters** : None

## Responses

### Success

**Code** : `200 OK`

**Content** : 
```json
{
    "id": "a56kbWV82n",
    "name": "Niall",
    "description": "Niall James Blackborough, born on the 22nd April 2019 at 17:46, these are all the expenses we have recorded for him.",
    "data": {
        "birth": "2019-04-22"
    },
    "created": "2019-04-07 11:26:10",
    "item_subtype": {
        "uri": "/v3/item-types/OqZwKX16bW/item-subtypes/a56kbWV82n",
        "id": "a56kbWV82n",
        "name": "default",
        "description": "Default behaviour for the allocated-expense type"
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
    "message": "The requested `Resource` does not exist."
}
```
