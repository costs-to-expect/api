[API Sections](../Sections.md)
[Auth (User)](../auth-user/GET.md)
[Permitted resource types](../permitted-resource-types/GET.md)
[Permitted resource type](../permitted-resource-type/GET.md)

# Permitted resource type - Request delete

Request deletion of a resource type you have access to. This queues a background job rather than deleting immediately.

## Request

**URL** : `/v3/auth/user/permitted-resource-types/{permitted_resource_type_id}/request-delete`

**Method** : `POST`

## Responses

### Success

**Code** : `201`

**Content** : 
```json
{
    "message": "The delete request has been received, a job has been added to the queue to process the request"
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
