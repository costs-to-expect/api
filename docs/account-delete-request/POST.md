[API Sections](../Sections.md)
[Auth (User)](../auth-user/GET.md)

# Account - Request delete

Request deletion of your entire account. This queues a background job rather than deleting immediately.

## Request

**URL** : `/v3/auth/user/request-delete`

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
